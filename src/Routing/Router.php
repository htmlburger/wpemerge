<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Requests\RequestInterface;

/**
 * Provide routing for site requests (i.e. all non-api requests).
 */
class Router implements HasRoutesInterface {
	use HasRoutesTrait {
		addRoute as traitAddRoute;
	}

	/**
	 * Current active route.
	 *
	 * @var RouteInterface
	 */
	protected $current_route = null;

	/**
	 * Get the current route.
	 *
	 * @return RouteInterface
	 */
	public function getCurrentRoute() {
		return $this->current_route;
	}

	/**
	 * Set the current route.
	 *
	 * @param  RouteInterface
	 * @return void
	 */
	public function setCurrentRoute( RouteInterface $current_route ) {
		$this->current_route = $current_route;
	}

	/**
	 * Assign and return the first satisfied route (if any) as the current one for the given request.
	 *
	 * @param  RequestInterface $request
	 * @return RouteInterface
	 */
	public function execute( $request ) {
		$routes = $this->getRoutes();

		foreach ( $routes as $route ) {
			if ( $route->isSatisfied( $request ) ) {
				$this->setCurrentRoute( $route );
				return $route;
			}
		}

		return null;
	}
}
