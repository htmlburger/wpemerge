<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Csrf;

use Closure;
use WPEmerge\Requests\RequestInterface;

/**
 * Store current request data and clear old request data
 */
class CsrfMiddleware {
	/**
	 * CSRF service.
	 *
	 * @var Csrf
	 */
	protected $csrf = null;

	/**
	 * Constructor.
	 *
	 * @param Csrf $csrf
	 */
	public function __construct( $csrf ) {
		$this->csrf = $csrf;
	}

	/**
	 * {@inheritDoc}
	 * @throws InvalidCsrfTokenException
	 */
	public function handle( RequestInterface $request, Closure $next ) {
		if ( ! $request->isReadVerb() ) {
			$token = $this->csrf->getTokenFromRequest( $request );
			if ( ! $this->csrf->isValidToken( $token ) ) {
				throw new InvalidCsrfTokenException();
			}
		}

		$this->csrf->generateToken();

		return $next( $request );
	}
}
