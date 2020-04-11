<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

use WPEmerge\Exceptions\ConfigurationException;

/**
 * Provide middleware definitions.
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
	 * @var array<string, string[]>
	 */
	protected $middleware_groups = [];

	/**
	 * Middleware groups that should have the 'wpemerge' and 'global' groups prepended to them.
	 *
	 * @var string[]
	 */
	protected $prepend_special_groups_to = [
		'web',
		'admin',
		'ajax',
	];

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
	 * @param  array<string, string[]> $middleware_groups
	 * @return void
	 */
	public function setMiddlewareGroups( $middleware_groups ) {
		$this->middleware_groups = $middleware_groups;
	}

	/**
	 * Filter array of middleware into a unique set.
	 *
	 * @param  array[]  $middleware
	 * @return string[]
	 */
	public function uniqueMiddleware( $middleware ) {
		return array_values( array_unique( $middleware, SORT_REGULAR ) );
	}

	/**
	 * Expand array of middleware into an array of fully qualified class names.
	 *
	 * @param  string[] $middleware
	 * @return array[]
	 */
	public function expandMiddleware( $middleware ) {
		$classes = [];

		foreach ( $middleware as $item ) {
			$classes = array_merge(
				$classes,
				$this->expandMiddlewareMolecule( $item )
			);
		}

		return $classes;
	}

	/**
	 * Expand a middleware group into an array of fully qualified class names.
	 *
	 * @param  string  $group
	 * @return array[]
	 */
	public function expandMiddlewareGroup( $group ) {
		if ( ! isset( $this->middleware_groups[ $group ] ) ) {
			throw new ConfigurationException( 'Unknown middleware group "' . $group . '" used.' );
		}

		$middleware = $this->middleware_groups[ $group ];

		if ( in_array( $group, $this->prepend_special_groups_to, true ) ) {
			$middleware = array_merge( ['wpemerge', 'global'], $middleware );
		}

		return $this->expandMiddleware( $middleware );
	}

	/**
	 * Expand middleware into an array of fully qualified class names and any companion arguments.
	 *
	 * @param  string  $middleware
	 * @return array[]
	 */
	public function expandMiddlewareMolecule( $middleware ) {
		$pieces = explode( ':', $middleware, 2 );

		if ( count( $pieces ) > 1 ) {
			return [array_merge( [$this->expandMiddlewareAtom( $pieces[0] )], explode( ',', $pieces[1] ) )];
		}

		if ( isset( $this->middleware_groups[ $middleware ] ) ) {
			return $this->expandMiddlewareGroup( $middleware );
		}

		return [[$this->expandMiddlewareAtom( $middleware )]];
	}

	/**
	 * Expand a single middleware a fully qualified class name.
	 *
	 * @param  string $middleware
	 * @return string
	 */
	public function expandMiddlewareAtom( $middleware ) {
		if ( isset( $this->middleware[ $middleware ] ) ) {
			return $this->middleware[ $middleware ];
		}

		if ( class_exists( $middleware ) ) {
			return $middleware;
		}

		throw new ConfigurationException( 'Unknown middleware "' . $middleware . '" used.' );
	}
}
