<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\Request;

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
