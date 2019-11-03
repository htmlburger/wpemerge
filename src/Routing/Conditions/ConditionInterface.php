<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\RequestInterface;

/**
 * Interface that condition types must implement
 */
interface ConditionInterface {
	/**
	 * Get whether the condition is satisfied
	 *
	 * @param  RequestInterface $request
	 * @return boolean
	 */
	public function isSatisfied( RequestInterface $request );

	/**
	 * Get an array of arguments for use in request
	 *
	 * @param  RequestInterface $request
	 * @return array
	 */
	public function getArguments( RequestInterface $request );
}
