<?php

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\Request;

/**
 * Check against a query var value.
 *
 * @codeCoverageIgnore
 */
class QueryVarCondition implements ConditionInterface {
	/**
	 * Query var name to check against.
	 *
	 * @var string|null
	 */
	protected $query_var = null;

	/**
	 * Query var value to check against.
	 *
	 * @var string|null
	 */
	protected $value = '';

	/**
	 * Constructor.
	 *
	 * @param string      $query_var
	 * @param string|null $value
	 */
	public function __construct( $query_var, $value = null ) {
		$this->query_var = $query_var;
		$this->value = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		$query_var_value = get_query_var( $this->query_var, null );

		if ( $query_var_value === null ) {
			return false;
		}

		if ( $this->value === null ) {
			return true;
		}

		return (string) $this->value === $query_var_value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->query_var, $this->value];
	}
}
