<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

use WPEmerge\Helpers\Handler;

/**
 * Describes how a request is handled.
 */
trait ReadsHandlerMiddlewareTrait {
	/**
	 * Get middleware registered with the given handler.
	 *
	 * @param  Handler       $handler
	 * @return array<string>
	 */
	protected function getHandlerMiddleware( Handler $handler ) {
		$instance = $handler->make();

		if ( ! $instance instanceof HasControllerMiddlewareInterface ) {
			return [];
		}

		return $instance->getMiddleware( $handler->get()['method'] );
	}
}
