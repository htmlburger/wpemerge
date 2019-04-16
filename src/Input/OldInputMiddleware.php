<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Input;

use Closure;
use WPEmerge\Facades\OldInput as OldInputService;
use WPEmerge\Requests\RequestInterface;

/**
 * Store current request data and clear old request data
 */
class OldInputMiddleware {
	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next ) {
		if ( OldInputService::enabled() && $request->isPost() ) {
			OldInputService::set( $request->post() );
		}

		return $next( $request );
	}
}
