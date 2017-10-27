<?php

namespace CarbonFramework\Routing\Conditions;

use CarbonFramework\Request;

/**
 * Interface that condition types must implement
 */
interface ConditionInterface {
	/**
	 * Return whether the condition is satisfied
	 *
	 * @param  Request $request
	 * @return boolean
	 */
	public function satisfied( Request $request );

	/**
	 * Return an array of arguments for use in request
	 *
	 * @param  Request $request
	 * @return array
	 */
	public function getArguments( Request $request );
}
