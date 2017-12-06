<?php

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Request;

/**
 * Interface that condition types must implement
 */
interface ConditionInterface {
	/**
	 * Get whether the condition is satisfied
	 *
	 * @param  Request $request
	 * @return boolean
	 */
	public function isSatisfied( Request $request );

	/**
	 * Get an array of arguments for use in request
	 *
	 * @param  Request $request
	 * @return array
	 */
	public function getArguments( Request $request );
}
