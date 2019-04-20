<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the view service
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\View\ViewEngineInterface
 * @see \WPEmerge\View\PhpViewEngine (Default)
 *
 * @method static boolean exists( string $view )
 * @method static string canonical( string $view )
 * @method static \WPEmerge\View\ViewInterface make( array $views )
 */
class ViewEngine extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_VIEW_ENGINE_KEY;
	}
}
