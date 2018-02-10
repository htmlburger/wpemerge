<?php

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Facades\RouteCondition;
use WPEmerge\Requests\Request;

/**
 * Negate another condition's result.
 *
 * @codeCoverageIgnore
 */
class NegateCondition implements ConditionInterface {
	/**
	 * Condition to negate.
	 *
	 * @var ConditionInterface
	 */
	protected $condition = [];

	/**
	 * Constructor.
	 *
	 * @param mixed $condition
	 */
	public function __construct( $condition ) {
		if ( $condition instanceof ConditionInterface ) {
			$this->condition = $condition;
		} else {
			$this->condition = call_user_func( [RouteCondition::class, 'make'], func_get_args() );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		return ! $this->condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return $this->condition->getArguments( $request );
	}
}
