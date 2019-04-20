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
 *
 * @method static void respond( \Psr\Http\Message\ResponseInterface $response )
 * @method static void sendHeaders( \Psr\Http\Message\ResponseInterface $response )
 * @method static void sendBody( \Psr\Http\Message\ResponseInterface $response )
 * @method static \Psr\Http\Message\ResponseInterface response()
 * @method static \Psr\Http\Message\ResponseInterface output( string $output )
 * @method static \Psr\Http\Message\ResponseInterface json( $data )
 * @method static \WPEmerge\Responses\RedirectResponse redirect()
 * @method static \WPEmerge\View\ViewInterface view( string|array $views )
 * @method static \Psr\Http\Message\ResponseInterface error( integer $status )
 */
class Response extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_RESPONSE_SERVICE_KEY;
	}
}
