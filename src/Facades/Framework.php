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
 * Provide access to the framework instance
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\Framework\Framework
 */
class Framework extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_FRAMEWORK_KEY;
	}
}
