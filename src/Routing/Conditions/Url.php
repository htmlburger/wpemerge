<?php

namespace CarbonFramework\Routing\Conditions;

use CarbonFramework\Url as UrlUtility;

class Url implements ConditionInterface {
	protected $url = 0;

	public function __construct( $url ) {
		$url = UrlUtility::addLeadingSlash( $url );
		$url = UrlUtility::addTrailingSlash( $url );
		// TODO support parameters
		$this->url = $url;
	}

	public function satisfied() {
		$url = UrlUtility::addTrailingSlash( UrlUtility::getCurrentPath() );
		return $this->url === $url;
	}

	public function getUrl() {
		return $this->url;
	}

	public function concatenate( Url $url ) {
		return new static( UrlUtility::removeTrailingSlash( $this->getUrl() ) . $url->getUrl() );
	}
}
