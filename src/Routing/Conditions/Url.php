<?php

namespace CarbonFramework\Routing\Conditions;

use CarbonFramework\Url as UrlUtility;

class Url implements ConditionInterface {
	protected $url = '';

	protected $url_regex = '~
		(?:/)                     # require leading slash
		(?:\{)                    # require opening curly brace
			(?P<name>[a-z]\w*)    # require a string starting with a-z and followed by any number of word characters for the parameter name
			(?P<optional>\?)?     # optionally allow the user to mark the parameter as option using literal ?
			(?::(?P<regex>.*?))?  # optionally allow the user to supply a regex to match the argument against
		(?:\})                    # require closing curly brace
		(?=/)                     # lookahead for a slash but do not consume it as it may be used by the next match
	~ix';

	protected $parameter_regex = '[^/]+';

	public function __construct( $url ) {
		$url = UrlUtility::addLeadingSlash( $url );
		$url = UrlUtility::addTrailingSlash( $url );
		$this->url = $url;
	}

	public function satisfied() {
		$validation_regex = $this->getValidationRegex( $this->getUrl() );
		$url = $this->getCurrentUrl();
		return preg_match( $validation_regex, $url );
	}

	public function getArguments() {
		$validation_regex = $this->getValidationRegex( $this->getUrl() );
		$url = $this->getCurrentUrl();
		$matches = [];
		$success = preg_match( $validation_regex, $url, $matches );

		if ( ! $success ) {
			return []; // this should not normally happen
		}

		$arguments = [];
		$parameter_names = $this->getParameterNames( $this->getUrl() );
		foreach ( $parameter_names as $parameter_name ) {
			$arguments[] = $matches[ $parameter_name ];
		}
		
		return $arguments;
	}

	public function getUrl() {
		return $this->url;
	}

	protected function getCurrentUrl() {
		return UrlUtility::addTrailingSlash( UrlUtility::getCurrentPath() );
	}

	public function concatenate( Url $url ) {
		return new static( UrlUtility::removeTrailingSlash( $this->getUrl() ) . $url->getUrl() );
	}

	protected function getParameterNames( $url ) {
		$matches = [];
		preg_match_all( $this->url_regex, $url, $matches );
		return $matches['name'];
	}

	protected function getValidationRegex( $url ) {
		$parameters = [];

		// Replace all parameters with placeholders
		$validation_regex = preg_replace_callback( $this->url_regex, function( $matches ) use ( &$parameters ) {
			$name = $matches['name'];
			$optional = ! empty( $matches['optional'] );
			$regex = ! empty( $matches['regex'] ) ? $matches['regex'] : $this->parameter_regex;
			$replacement = '(?:/(?P<' . $name . '>' . $regex . '))';
			if ( $optional ) {
				$replacement .= '?';
			}

			$placeholder = '___placeholder_' . sha1( count( $parameters) . '_' . $replacement . '_' . uniqid() ) . '___';
			$parameters[ $placeholder ] = $replacement;
			return $placeholder;
		}, $url );

		// quote the remaining string so that it does not get evaluated as regex
		$validation_regex = preg_quote( $validation_regex, '~' );

		// replace the placeholders with the real parameter regexes
		$validation_regex = str_replace( array_keys( $parameters ), array_values( $parameters ), $validation_regex );

		// make sure that the regex matches the entire string
		$validation_regex = '~\A' . $validation_regex . '\z~';

		return $validation_regex;
	}
}
