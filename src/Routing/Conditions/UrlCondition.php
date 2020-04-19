<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
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
class UrlCondition implements ConditionInterface {
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
	 * @var array<string, string>
	 */
	protected $url_where = [];

	/**
	 * Pattern to detect parameters in urls.
	 *
	 * @suppress HtmlUnknownTag
	 * @var string
	 */
	protected $url_pattern = '~
		(?:/)                     # match leading slash
		(?:\{)                    # opening curly brace
			(?P<name>[a-z]\w*)    # string starting with a-z and followed by word characters for the parameter name
			(?P<optional>\?)?     # optionally allow the user to mark the parameter as option using literal ?
		(?:\})                    # closing curly brace
		(?=/)                     # lookahead for a trailing slash
	~ix';

	/**
	 * Pattern to detect valid parameters in url segments.
	 *
	 * @var string
	 */
	protected $parameter_pattern = '[^/]+';

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param string $url
	 * @param array<string, string> $where
	 */
	public function __construct( $url, $where = [] ) {
		$this->setUrl( $url );
		$this->setUrlWhere( $where );
	}

	/**
	 * Make a new instance.
	 *
	 * @codeCoverageIgnore
	 * @param string $url
	 * @param array<string, string> $where
	 * @return self
	 */
	protected function make( $url, $where = [] ) {
		return new self( $url, $where );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function whereIsSatisfied( RequestInterface $request ) {
		$where = $this->getUrlWhere();
		$arguments = $this->getArguments( $request );

		foreach ( $where as $parameter => $pattern ) {
			$value = Arr::get( $arguments, $parameter, '' );

			if ( ! preg_match( $pattern, $value ) ) {
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

		$validation_pattern = $this->getValidationPattern( $this->getUrl() );
		$url = UrlUtility::getPath( $request );
		$match = (bool) preg_match( $validation_pattern, $url );

		if ( ! $match || empty( $this->getUrlWhere() ) ) {
			return $match;
		}

		return $this->whereIsSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		$validation_pattern = $this->getValidationPattern( $this->getUrl() );
		$url = UrlUtility::getPath( $request );
		$matches = [];
		$success = preg_match( $validation_pattern, $url, $matches );

		if ( ! $success ) {
			return []; // this should not normally happen
		}

		$arguments = [];
		$parameter_names = $this->getParameterNames( $this->getUrl() );
		foreach ( $parameter_names as $parameter_name ) {
			$arguments[ $parameter_name ] = isset( $matches[ $parameter_name ] ) ? $matches[ $parameter_name ] : '';
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
	 * @param  string                $url
	 * @param  array<string, string> $where
	 * @return static
	 */
	public function concatenate( $url, $where = [] ) {
		if ( $this->getUrl() === static::WILDCARD || $url === static::WILDCARD ) {
			return $this->make( static::WILDCARD );
		}

		$leading = UrlUtility::addLeadingSlash( UrlUtility::removeTrailingSlash( $this->getUrl() ), true );
		$trailing = UrlUtility::addLeadingSlash( UrlUtility::addTrailingSlash( $url ) );

		return $this->make( $leading . $trailing, array_merge(
			$this->getUrlWhere(),
			$where
		) );
	}

	/**
	 * Get parameter names as defined in the url.
	 *
	 * @param  string   $url
	 * @return string[]
	 */
	protected function getParameterNames( $url ) {
		$matches = [];
		preg_match_all( $this->url_pattern, $url, $matches );
		return $matches['name'];
	}

	/**
	 * Validation pattern replace callback.
	 *
	 * @param  array  $matches
	 * @param  array  $parameters
	 * @return string
	 */
	protected function replacePatternParameterWithPlaceholder( $matches, &$parameters ) {
		$name = $matches['name'];
		$optional = ! empty( $matches['optional'] );

		$replacement = '/(?P<' . $name . '>' . $this->parameter_pattern . ')';

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
	 * Get pattern to test whether normal urls match the parameter-based one.
	 *
	 * @param  string  $url
	 * @param  boolean $wrap
	 * @return string
	 */
	public function getValidationPattern( $url, $wrap = true ) {
		$parameters = [];

		// Replace all parameters with placeholders
		$validation_pattern = preg_replace_callback( $this->url_pattern, function ( $matches ) use ( &$parameters ) {
			return $this->replacePatternParameterWithPlaceholder( $matches, $parameters );
		}, $url );

		// Quote the remaining string so that it does not get evaluated as a pattern.
		$validation_pattern = preg_quote( $validation_pattern, '~' );

		// Replace the placeholders with the real parameter patterns.
		$validation_pattern = str_replace(
			array_keys( $parameters ),
			array_values( $parameters ),
			$validation_pattern
		);

		// Match the entire url; make trailing slash optional.
		$validation_pattern = '^' . $validation_pattern . '?$';

		if ( $wrap ) {
			$validation_pattern = '~' . $validation_pattern . '~';
		}

		return $validation_pattern;
	}
}
