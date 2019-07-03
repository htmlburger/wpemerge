<?php /** @noinspection PhpUnusedParameterInspection */
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Controllers;

use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Facades\View;
use WPEmerge\Requests\RequestInterface;

/**
 * Handles normal WordPress requests without interfering
 * Useful if you only want to add a middleware to a route without handling the output
 *
 * @codeCoverageIgnore
 */
class WordPressController {
	/**
	 * Default WordPress handler.
	 *
	 * @param  RequestInterface                    $request
	 * @param  string                              $view
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( RequestInterface $request, $view = '' ) {
		if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			throw new ConfigurationException(
				'Attempted to run the default WordPress controller on an ' .
				'admin or AJAX page. Did you miss to specify a custom handler for ' .
				'a route or accidentally used Route::all() during admin ' .
				'requests?'
			);
		}

		if ( empty( $view ) ) {
			throw new ConfigurationException(
				'No view loaded for default WordPress controller. ' .
				'Did you miss to specify a custom handler for an ajax or admin route?'
			);
		}

		return View::make( $view )
			->toResponse()
			->withStatus( http_response_code() );
	}
}
