<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Helpers\Handler;
use WPEmerge\Middleware\HasMiddlewareInterface;
use WPEmerge\Requests\RequestInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasConditionInterface, HasMiddlewareInterface {
	/**
	 * Get handler.
	 *
	 * @return Handler
	 */
	public function getHandler();

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

	/**
	 * Decorate route.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return void
	 */
	public function decorate( $attributes );
}
