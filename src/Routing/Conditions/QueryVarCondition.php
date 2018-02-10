<?php

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\Request;

/**
 * Check against a query var value
 *
 * @codeCoverageIgnore
 */
class QueryVarCondition implements ConditionInterface {
	/**
	 * Query var name to check against
	 *
	 * @var string
	 */
	protected $query_var = '';

	/**
	 * Query var value to check against
	 *
	 * @var string
	 */
	protected $value = '';

	/**
	 * Constructor
	 *
	 * @param string $query_var
	 * @param string $value
	 */
	public function __construct( $query_var, $value = '' ) {
		$this->query_var = $query_var;
		$this->value = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		return $this->value === get_query_var( $this->query_var, '' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->query_var, $this->value];
	}
}
