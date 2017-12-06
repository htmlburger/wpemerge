<?php

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Request;

/**
 * Check against an array of conditions in an AND logical relationship
 */
class Multiple implements ConditionInterface {
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
			if ( is_a( $condition, ConditionInterface::class ) ) {
				return $condition;
			}
			return Factory::make( $condition );
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
		return [];
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
