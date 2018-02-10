<?php

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\Request;

/**
 * Check against an array of conditions in an AND logical relationship
 */
class MultipleCondition implements ConditionInterface {
	/**
	 * Array of conditions to check
	 *
	 * @var array<ConditionInterface>
	 */
	protected $conditions = [];

	/**
	 * Constructor
	 *
	 * @param array $conditions
	 */
	public function __construct( $conditions ) {
		$this->conditions = array_map( function( $condition ) {
			if ( $condition instanceof ConditionInterface ) {
				return $condition;
			}
			return ConditionFactory::make( $condition );
		}, $conditions );
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		foreach ( $this->conditions as $condition ) {
			if ( ! $condition->isSatisfied( $request ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		$arguments = [];
		foreach ( $this->conditions as $condition ) {
			$arguments = array_merge( $arguments, $condition->getArguments( $request ) );
		}
		return $arguments;
	}

	/**
	 * Get all assigned conditions
	 *
	 * @return \WPEmerge\Routing\Conditions\ConditionInterface[]
	 */
	public function getConditions() {
		return $this->conditions;
	}
}
