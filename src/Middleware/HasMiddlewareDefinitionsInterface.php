<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

/**
 * Provide middleware definitions.
 */
interface HasMiddlewareDefinitionsInterface {
	/**
	 * Register middleware.
	 *
	 * @codeCoverageIgnore
	 * @param  array<string, string> $middleware
	 * @return void
	 */
	public function setMiddleware( $middleware );

	/**
	 * Register middleware groups.
	 *
	 * @codeCoverageIgnore
	 * @param  array<string, array<string>> $middleware_groups
	 * @return void
	 */
	public function setMiddlewareGroups( $middleware_groups );

	/**
	 * Filter array of middleware into a unique set.
	 *
	 * @param  array<string> $middleware
	 * @return array<string>
	 */
	public function uniqueMiddleware( $middleware );

	/**
	 * Expand array of middleware into an array of fully qualified class names.
	 *
	 * @param  array<string> $middleware
	 * @return array<string>
	 */
	public function expandMiddleware( $middleware );

	/**
	 * Expand a middleware group into an array of fully qualified class names.
	 *
	 * @param  string        $group
	 * @return array<string>
	 */
	public function expandMiddlewareGroup( $group );

	/**
	 * Expand a middleware into a fully qualified class name.
	 *
	 * @param  string        $middleware
	 * @return array<string>
	 */
	public function expandMiddlewareMolecule( $middleware );
}
