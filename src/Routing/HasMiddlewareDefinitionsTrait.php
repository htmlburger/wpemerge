<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Middleware\MiddlewareInterface;

/**
 * Provide middleware sorting.
 */
trait HasMiddlewareDefinitionsTrait {
	/**
	 * Middleware available to the application.
	 *
	 * @var array<string, string>
	 */
	protected $middleware = [];

	/**
	 * Middleware groups.
	 *
	 * @var array<string, array<string>>
	 */
	protected $middleware_groups = [];

	/**
	 * Global middleware that will be applied to all routes.
	 *
	 * @var array<string>
	 */
	protected $global_middleware = [];

	/**
	 * Register middleware.
	 *
	 * @codeCoverageIgnore
	 * @param  array<string, string> $middleware
	 * @return void
	 */
	public function setMiddleware( $middleware ) {
		$this->middleware = $middleware;
	}

	/**
	 * Register middleware groups.
	 *
	 * @codeCoverageIgnore
	 * @param  array<string, array<string>> $middleware_groups
	 * @return void
	 */
	public function setMiddlewareGroups( $middleware_groups ) {
		$this->middleware_groups = $middleware_groups;
	}

	/**
	 * Register global middleware.
	 *
	 * @codeCoverageIgnore
	 * @param  array<string> $middleware
	 * @return void
	 */
	public function setGlobalMiddleware( $middleware ) {
		$this->global_middleware = $middleware;
	}

	/**
	 * Expand array of middleware into an array of fully qualified class names.
	 *
	 * @param  array<string> $middleware
	 * @return array<string>
	 */
	public function expandMiddleware( $middleware ) {
		$classes = [];

		foreach ( $middleware as $item ) {
			if ( isset( $this->middleware_groups[ $item ] ) ) {
				$classes = array_merge(
					$classes,
					$this->expandMiddlewareGroup( $item )
				);
				continue;
			}

			$classes[] = $this->expandMiddlewareItem( $item );
		}

		return $classes;
	}

	/**
	 * Expand a middleware group into an array of fully qualified class names.
	 *
	 * @param  string        $group
	 * @return array<string>
	 */
	public function expandMiddlewareGroup( $group ) {
		if ( ! isset( $this->middleware_groups[ $group ] ) ) {
			throw new ConfigurationException( 'Unknown middleware group "' . $group . '" used.' );
		}

		return array_map( [$this, 'expandMiddlewareItem'], $this->middleware_groups[ $group ] );
	}

	/**
	 * Expand a middleware into a fully qualified class name.
	 *
	 * @param  string $middleware
	 * @return string
	 */
	public function expandMiddlewareItem( $middleware ) {
		if ( is_subclass_of( $middleware, MiddlewareInterface::class ) ) {
			return $middleware;
		}

		if ( ! isset( $this->middleware[ $middleware ] ) ) {
			throw new ConfigurationException( 'Unknown middleware "' . $middleware . '" used.' );
		}

		return $this->middleware[ $middleware ];
	}
}
