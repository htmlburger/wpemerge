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
	 * Filter array of middleware into a unique set.
	 *
	 * @param  array<string> $middleware
	 * @return array<string>
	 */
	public function uniqueMiddleware( $middleware ) {
		return array_values( array_unique( $middleware, SORT_REGULAR ) );
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
			$classes = array_merge(
				$classes,
				$this->expandMiddlewareItem( $item )
			);
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

		return $this->expandMiddleware( $this->middleware_groups[ $group ] );
	}

	/**
	 * Expand a middleware into a fully qualified class name.
	 *
	 * @param  string        $middleware
	 * @return array<string>
	 */
	public function expandMiddlewareItem( $middleware ) {
		if ( is_subclass_of( $middleware, MiddlewareInterface::class ) ) {
			return [$middleware];
		}

		if ( isset( $this->middleware_groups[ $middleware ] ) ) {
			return $this->expandMiddlewareGroup( $middleware );
		}

		if ( ! isset( $this->middleware[ $middleware ] ) ) {
			throw new ConfigurationException( 'Unknown middleware "' . $middleware . '" used.' );
		}

		return [$this->middleware[ $middleware ]];
	}
}
