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
 * Provide access to old input service
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\Input\OldInput
 *
 * @method static boolean enabled()
 * @method static mixed get( string $key, $default = null )
 * @method static void set( array $input )
 * @method static void clear()
 */
class OldInput extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_OLD_INPUT_KEY;
	}
}
