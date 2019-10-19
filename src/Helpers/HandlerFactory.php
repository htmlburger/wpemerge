<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

/**
 * Handler factory.
 */
class HandlerFactory {
	/**
	 * Make a Handler.
	 *
	 * @codeCoverageIgnore
	 * @param string|\Closure $raw_handler
	 * @param string         $default_method
	 * @param string         $namespace
	 *
	 * @return Handler
	 */
	public function make( $raw_handler, $default_method = '', $namespace = '' ) {
		return new Handler( $raw_handler, $default_method, $namespace );
	}
}
