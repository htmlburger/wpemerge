<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Helpers\Url as UrlUtility;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Support\Arr;

/**
 * Check against the current url
 */
class UrlCondition implements ConditionInterface, HasUrlInterface {
	const WILDCARD = '*';

	/**
	 * URL to check against.
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * URL where.
	 *
	 * @var array<string,string>
	 */
	protected $url_where = [];

	/**
	 * Regex to detect parameters in urls.
	 *
	 * @var string
	 */
	protected $url_regex = '~
		(?:/)                     # match leading slash
		(?:\{)                    # opening curly brace
			(?P<name>[a-z]\w*)    # string starting with a-z and followed by word characters for the parameter name
			(?P<optional>\?)?     # optionally allow the user to mark the parameter as option using literal ?
		(?:\})                    # closing curly brace
		(?=/)                     # lookahead for a trailing slash
	~ix';

	/**
	 * Regex to detect valid parameters in url segments.
	 *
	 * @var string
	 */
	protected $parameter_regex = '[^/]+';

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param string $url
	 */
	public function __construct( $url ) {
		$this->setUrl( $url );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function whereIsSatisfied( RequestInterface $request ) {
		$where = $this->getUrlWhere();
		$arguments = $this->getArguments( $request );

		foreach ( $where as $parameter => $regex ) {
			$value = Arr::get( $arguments, $parameter, '' );

			if ( ! preg_match( $regex, $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		if ( $this->getUrl() === static::WILDCARD ) {
			return true;
		}

		$validation_regex = $this->getValidationRegex( $this->getUrl() );
		$url = UrlUtility::getPath( $request );
		$match = (bool) preg_match( $validation_regex, $url );

		if ( ! $match || empty( $this->getUrlWhere() ) ) {
			return $match;
		}

		return $this->whereIsSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		$validation_regex = $this->getValidationRegex( $this->getUrl() );
		$url = UrlUtility::getPath( $request );
		$matches = [];
		$success = preg_match( $validation_regex, $url, $matches );

		if ( ! $success ) {
			return []; // this should not normally happen
		}

		$arguments = [];
		$parameter_names = $this->getParameterNames( $this->getUrl() );
		foreach ( $parameter_names as $parameter_name ) {
			$arguments[ $parameter_name ] = ! empty( $matches[ $parameter_name ] ) ? $matches[ $parameter_name ] : '';
		}

		return $arguments;
	}

	/**
	 * Get the url for this condition.
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Set the url for this condition.
	 *
	 * @param  string $url
	 * @return void
	 */
	public function setUrl( $url ) {
		if ( $url !== static::WILDCARD ) {
			$url = UrlUtility::addLeadingSlash( UrlUtility::addTrailingSlash( $url ) );
		}

		$this->url = $url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUrlWhere() {
		return $this->url_where;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUrlWhere( $where ) {
		$this->url_where = $where;
	}

	/**
	 * Append a url to this one returning a new instance.
	 *
	 * @param  string $url
	 * @return static
	 */
	public function concatenateUrl( $url ) {
		if ( $this->getUrl() === static::WILDCARD || $url === static::WILDCARD ) {
			return new static( static::WILDCARD );
		}

		$leading = UrlUtility::addLeadingSlash( UrlUtility::removeTrailingSlash( $this->getUrl() ), true );
		$trailing = UrlUtility::addLeadingSlash( UrlUtility::addTrailingSlash( $url ) );

		return new static( $leading . $trailing );
	}

	/**
	 * Get parameter names as defined in the url.
	 *
	 * @param  string   $url
	 * @return string[]
	 */
	protected function getParameterNames( $url ) {
		$matches = [];
		preg_match_all( $this->url_regex, $url, $matches );
		return $matches['name'];
	}

	/**
	 * Validation regex replace callback.
	 *
	 * @param  array  $matches
	 * @param  array  $parameters
	 * @return string
	 */
	protected function replaceRegexParameterWithPlaceholder( $matches, &$parameters ) {
		$name = $matches['name'];
		$optional = ! empty( $matches['optional'] );

		$replacement = '/(?P<' . $name . '>' . $this->parameter_regex . ')';

		if ( $optional ) {
			$replacement = '(?:' . $replacement . ')?';
		}

		$hash = sha1( implode( '_', [
			count( $parameters ),
			$replacement,
			uniqid( 'wpemerge_', true ),
		] ) );
		$placeholder = '___placeholder_' . $hash . '___';
		$parameters[ $placeholder ] = $replacement;

		return $placeholder;
	}

	/**
	 * Get regex to test whether normal urls match the parameter-based one.
	 *
	 * @param  string  $url
	 * @param  boolean $wrap
	 * @return string
	 */
	public function getValidationRegex( $url, $wrap = true ) {
		$parameters = [];

		// Replace all parameters with placeholders
		$validation_regex = preg_replace_callback( $this->url_regex, function ( $matches ) use ( &$parameters ) {
			return $this->replaceRegexParameterWithPlaceholder( $matches, $parameters );
		}, $url );

		// quote the remaining string so that it does not get evaluated as regex
		$validation_regex = preg_quote( $validation_regex, '~' );

		// replace the placeholders with the real parameter regexes
		$validation_regex = str_replace( array_keys( $parameters ), array_values( $parameters ), $validation_regex );

		// match the entire url; make trailing slash optional
		$validation_regex = '^' . $validation_regex . '?$';

		if ( $wrap ) {
			$validation_regex = '~' . $validation_regex . '~';
		}

		return $validation_regex;
	}
}
