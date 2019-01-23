<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Flash;

use Closure;
use WPEmerge\Facades\Flash as FlashService;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\RequestInterface;

/**
 * Store current request data and clear old request data
 */
class FlashMiddleware implements MiddlewareInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		if ( FlashService::enabled() ) {
			FlashService::shift();
			FlashService::save();
		}

		return $response;
	}
}
