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
 * Provide access to session flashing service
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\Flash\Flash
 *
 * @method static array|\ArrayAccess getStore()
 * @method static void setStore( array|\ArrayAccess &$store )
 * @method static boolean enabled()
 * @method static void add( string $key, $new_items )
 * @method static void addNow( string $key, $new_items )
 * @method static mixed get( string|null $key, $default = [] )
 * @method static mixed getNext( string|null $key, $default = [] )
 * @method static void clear( string|null $key )
 * @method static void clearNext( string|null $key )
 * @method static void shift()
 * @method static void save()
 */
class Flash extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_FLASH_KEY;
	}
}
