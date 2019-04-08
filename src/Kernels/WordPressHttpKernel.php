<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Kernels;

use Exception;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Router;

/**
 * Describes how a request is handled.
 */
class WordPressHttpKernel implements HttpKernel {
	/**
	 * Error handler.
	 *
	 * @var ErrorHandlerInterface
	 */
	protected $error_handler = null;

	/**
	 * Router.
	 *
	 * @var Router
	 */
	protected $router = null;

	/**
	 * Middleware available to the application.
	 *
	 * @var array<string, string>
	 */
	protected $middleware = [];

	/**
	 * Middleware groups.
	 *
	 * @var array<string, array<string>>
	 */
	protected $middleware_groups = [];

	/**
	 * Global middleware that will be applied to all routes.
	 *
	 * @var array
	 */
	protected $global_middleware = [
		\WPEmerge\Flash\FlashMiddleware::class,
		\WPEmerge\Input\OldInputMiddleware::class,
	];

	/**
	 * Middleware sorted in order of execution.
	 *
	 * @var array<string>
	 */
	protected $middleware_priority = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Router $router
	 * @param ErrorHandlerInterface $error_handler
	 */
	public function __construct( $router, ErrorHandlerInterface $error_handler ) {
		$this->router = $router;
		$this->error_handler = $error_handler;
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap() {
		$this->router->setMiddleware( $this->middleware );
		$this->router->setMiddlewareGroups( $this->middleware_groups );
		$this->router->setGlobalMiddleware( $this->global_middleware );
		$this->router->setMiddlewarePriority( $this->middleware_priority );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, $view ) {
		$this->error_handler->register();

		try {
			$response = $this->router->execute( $request, $view );
		} catch ( Exception $exception ) {
			$response = $this->error_handler->getResponse( $exception );
		}

		$this->error_handler->unregister();

		return $response;
	}
}
