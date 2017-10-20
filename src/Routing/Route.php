<?php

namespace CarbonFramework\Routing;

use Exception;
use CarbonFramework\Url;
use CarbonFramework\Routing\Conditions\ConditionInterface;
use CarbonFramework\Routing\Conditions\Url as UrlCondition;

class Route implements RouteInterface {
	protected $methods = [];

	protected $target = null;

	protected $handler = null;

	public function __construct( $methods, $target, $handler ) {
		if ( is_string( $target ) ) {
			$target = new UrlCondition( $target );
		}

		if ( ! is_a( $target, ConditionInterface::class ) ) {
			throw new Exception( 'Route target is not a valid route string or condition.' );
		}

		$this->methods = $methods;
		$this->target = $target;
		$this->handler = new Handler( $handler );
	}

	public function satisfied() {
		if ( ! in_array( $_SERVER['REQUEST_METHOD'], $this->methods) ) {
			return false;
		}
		return $this->target->satisfied();
	}

	public function getHandler() {
		return $this->handler;
	}
}
