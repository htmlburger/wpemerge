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
 * @see \WPEmerge\View\ViewService
 *
 * @method static array getGlobals()
 * @method static void addGlobal( string $key, $value )
 * @method static void addGlobals( array $globals )
 * @method static array getComposersForView( string $view )
 * @method static void addComposer( string|array $views, string|\Closure $composer )
 * @method static void compose( \WPEmerge\View\ViewInterface $view )
 * @method static \WPEmerge\View\ViewInterface make( string|array $views )
 * @method static void triggerPartialHooks( string $name )
 */
class View extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_VIEW_SERVICE_KEY;
	}
}
