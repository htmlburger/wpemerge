<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Routing\Conditions\ConditionInterface;

/**
 * Represent an object which has a route condition.
 */
interface HasConditionInterface {
	/**
	 * Get condition.
	 *
	 * @return ConditionInterface
	 */
	public function getCondition();

	/**
	 * set condition.
	 *
	 * @param  ConditionInterface $condition
	 * @return void
	 */
	public function setCondition( ConditionInterface $condition );
}
