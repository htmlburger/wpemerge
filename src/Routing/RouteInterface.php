<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Requests\Request;
use WPEmerge\Middleware\HasMiddlewareInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasMiddlewareInterface {
	/**
	 * Get whether the route is satisfied.
	 *
	 * @param  Request $request
	 * @return boolean
	 */
	public function isSatisfied( Request $request );

	/**
	 * Get a response for the given request.
	 *
	 * @param  Request                             $request
	 * @param  string                              $view
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( Request $request, $view );

	/**
	 * Get arguments.
	 *
	 * @param  Request $request
	 * @return array
	 */
	public function getArguments( Request $request );
}
