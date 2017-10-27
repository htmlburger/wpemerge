<?php

namespace CarbonFramework\Routing\Conditions;

use CarbonFramework\Request;

/**
 * Check against the current post's type
 */
class Endpoint extends QueryVar {
	/**
	 * {@inheritDoc}
	 */
	public function satisfied( Request $request ) {
		return get_query_var( $this->query_var, null ) !== null;
	}
}
