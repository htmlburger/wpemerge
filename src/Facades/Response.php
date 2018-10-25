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
 * Provide access to the responses service.
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\Responses\ResponseService
 */
class Response extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_RESPONSE_SERVICE_KEY;
	}
}
