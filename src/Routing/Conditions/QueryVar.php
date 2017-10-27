<?php

namespace CarbonFramework\Routing\Conditions;

use CarbonFramework\Request;

/**
 * Check against the current post's type
 */
class QueryVar implements ConditionInterface {
	/**
	 * Query var name to check against
	 *
	 * @var string
	 */
	protected $query_var_name = '';

	/**
	 * Query var value to check against
	 *
	 * @var string
	 */
	protected $query_var = '';

	/**
	 * Constructor
	 *
	 * @param string $query_var
	 */
	public function __construct( $query_var_name, $query_var ) {
		$this->query_var_name = $query_var_name;
		$this->query_var = $query_var;
	}

	/**
	 * {@inheritDoc}
	 */
	public function satisfied( Request $request ) {
		return $this->query_var === get_query_var( $this->query_var_name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->query_var_name, $this->query_var];
	}
}
