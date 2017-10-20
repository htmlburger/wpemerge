<?php

namespace CarbonFramework\Routing\Conditions;

use CarbonFramework\Url as UrlUtility;

class Url implements ConditionInterface {
	protected $url = 0;

	public function __construct( $url ) {
		// TODO convert to relative
		// TODO leading slash it
		// TODO support parameters
		$this->url = trailingslashit( $url );
	}

	public function satisfied() {
		$url = trailingslashit( UrlUtility::getCurrentPath() );
		return $this->url === $url;
	}

	public function getUrl() {
		return $this->url;
	}

	public function concatenate( Url $url ) {
		return new static( untrailingslashit( $this->getUrl() ) . $url->getUrl() );
	}
}
