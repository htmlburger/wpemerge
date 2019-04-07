<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Kernels;

use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Router;

/**
 * Describes how a request is handled.
 */
class WordPressHttpKernel implements HttpKernel {
	/**
	 * Router.
	 *
	 * @var Router|null
	 */
	protected $router = null;

	/**
	 * Middleware available to the application.
	 * TODO: implement.
	 *
	 * @var array<string, string>
	 */
	protected $middleware = [];

	/**
	 * Middleware groups.
	 * TODO: implement.
	 *
	 * @var array<string, array>
	 */
	protected $middleware_groups = [];

	/**
	 * Global middleware that will be applied to all routes.
	 *
	 * @var array
	 */
	protected $global_middleware = [];

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
	 */
	public function __construct( $router ) {
		$this->router = $router;
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
	 * @codeCoverageIgnore
	 */
	public function handle( RequestInterface $request, $view ) {
		return $this->router->execute( $request, $view );
	}
}
