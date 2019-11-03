<?php /** @noinspection PhpUnusedParameterInspection */
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Controllers;

use Psr\Http\Message\ResponseInterface;
use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\View\ViewService;

/**
 * Handles normal WordPress requests without interfering
 * Useful if you only want to add a middleware to a route without handling the output
 *
 * @codeCoverageIgnore
 */
class WordPressController {
	/**
	 * View service.
	 *
	 * @var ViewService
	 */
	protected $view_service = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ViewService $view_service
	 */
	public function __construct( ViewService $view_service ) {
		$this->view_service = $view_service;
	}

	/**
	 * Default WordPress handler.
	 *
	 * @param  RequestInterface  $request
	 * @param  string            $view
	 * @return ResponseInterface
	 */
	public function handle( RequestInterface $request, $view = '' ) {
		if ( is_admin() || wp_doing_ajax() ) {
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

		return $this->view_service->make( $view )
			->toResponse()
			->withStatus( http_response_code() );
	}
}
