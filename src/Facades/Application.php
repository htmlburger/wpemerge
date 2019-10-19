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
 * Provide access to the application instance.
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\Application\Application
 *
 * @method static boolean debugging()
 * @method static boolean isBootstrapped()
 * @method static \Pimple\Container getContainer()
 * @method static void bootstrap( array $config = [], boolean $run = true )
 * @method static void alias( string $alias, string $facade_class )
 * @method static mixed|null resolve( string $key )
 */
class Application extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_APPLICATION_KEY;
	}
}
