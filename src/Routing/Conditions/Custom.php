<?php

namespace CarbonFramework\Routing\Conditions;

class Custom implements ConditionInterface {
	/**
	 * Callable to use
	 * 
	 * @var callable
	 */
	protected $callable = null;

	/**
	 * Arguments to pass to the callable and controller
	 * 
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * Constructor
	 * 
	 * @param callable $callable
	 * @param any      ...$arguments
	 */
	public function __construct( $callable ) {
		$this->callable = $callable;
		$this->arguments = array_slice( func_get_args(), 1 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function satisfied() {
		return call_user_func_array( $this->callable, $this->arguments );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments() {
		return $this->arguments;
	}
}
