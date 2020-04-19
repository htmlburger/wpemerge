<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Helpers\HasAttributesInterface;
use WPEmerge\Requests\RequestInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasAttributesInterface {
	/**
	 * Get whether the route is satisfied.
	 *
	 * @param  RequestInterface $request
	 * @return boolean
	 */
	public function isSatisfied( RequestInterface $request );

	/**
	 * Get arguments.
	 *
	 * @param  RequestInterface $request
	 * @return array
	 */
	public function getArguments( RequestInterface $request );
}
