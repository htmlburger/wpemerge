<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

use Closure;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Responses\RedirectResponse;

/**
 * Redirect users who do not have a capability to a specific URL.
 */
class UserCanMiddleware {
	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next, $capability = '', $object_id = '0', $url = '' ) {
		$capability = apply_filters( 'wpemerge.middleware.user.can.capability', $capability, $request );
		$object_id = apply_filters( 'wpemerge.middleware.user.can.object_id', (int) $object_id, $capability, $request );
		$args = [$capability];

		if ( $object_id !== 0 ) {
			$args[] = $object_id;
		}

		if ( call_user_func_array( 'current_user_can', $args ) ) {
			return $next( $request );
		}

		if ( empty( $url ) ) {
			$url = home_url();
		}

		$url = apply_filters( 'wpemerge.middleware.user.can.redirect_url', $url, $request );

		return (new RedirectResponse( $request ))->to( $url );
	}
}
