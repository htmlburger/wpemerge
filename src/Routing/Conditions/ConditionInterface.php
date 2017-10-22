<?php

namespace CarbonFramework\Routing\Conditions;

/**
 * Interface that condition types must implement
 */
interface ConditionInterface {
	/**
	 * Return whether the condition is satisfied
	 * 
	 * @return boolean
	 */
	public function satisfied();

	/**
	 * Return an array of arguments for use in request
	 * 
	 * @return array
	 */
	public function getArguments();
}
