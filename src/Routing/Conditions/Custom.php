<?php

namespace CarbonFramework\Routing\Conditions;

class Custom implements ConditionInterface {
	protected $callable = 0;

	public function __construct( $callable ) {
		$this->callable = $callable;
	}

	public function satisfied() {
		return call_user_func( $this->callable );
	}
}
