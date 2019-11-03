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
 * Redirect non-logged in users to a specific URL.
 */
class UserLoggedInMiddleware {
	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next, $url = '' ) {
		if ( is_user_logged_in() ) {
			return $next( $request );
		}

		if ( empty( $url ) ) {
			$url = wp_login_url( $request->getUrl() );
		}

		$url = apply_filters( 'wpemerge.middleware.user.logged_in.redirect_url', $url, $request );

		return (new RedirectResponse( $request ))->to( $url );
	}
}
