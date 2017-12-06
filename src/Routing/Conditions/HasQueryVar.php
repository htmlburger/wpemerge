<?php

namespace Obsidian\Routing\Conditions;

use Obsidian\Request;

/**
 * Check if a certain query var is set
 *
 * @codeCoverageIgnore
 */
class HasQueryVar extends QueryVar {
	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		return get_query_var( $this->query_var, null ) !== null;
	}
}
