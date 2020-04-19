<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Requests\RequestInterface;

/**
 * Represent an object which has a WordPress query filter.
 */
interface HasQueryFilterInterface {
	/**
	 * Apply the query filter, if any.
	 *
	 * @param  RequestInterface $request
	 * @param  array            $query_vars
	 * @return array
	 */
	public function applyQueryFilter( $request, $query_vars );
}
