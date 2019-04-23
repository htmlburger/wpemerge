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
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Application\Application;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Facades\Response;
use WPEmerge\Middleware\HasMiddlewareDefinitionsTrait;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\HasQueryFilterInterface;
use WPEmerge\Routing\Pipeline;
use WPEmerge\Routing\Router;
use WPEmerge\Routing\SortsMiddlewareTrait;

/**
 * Describes how a request is handled.
 */
class HttpKernel implements HttpKernelInterface {
	use HasMiddlewareDefinitionsTrait;
	use SortsMiddlewareTrait;

	/**
	 * Application.
	 *
	 * @var Application
	 */
	protected $app = null;

	/**
	 * Request.
	 *
	 * @var RequestInterface
	 */
	protected $request = null;

	/**
	 * Router.
	 *
	 * @var Router
	 */
	protected $router = null;

	/**
	 * Error handler.
	 *
	 * @var ErrorHandlerInterface
	 */
	protected $error_handler = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Application           $app
	 * @param RequestInterface      $request
	 * @param Router                $router
	 * @param ErrorHandlerInterface $error_handler
	 */
	public function __construct(
		Application $app,
		RequestInterface $request,
		Router $router,
		ErrorHandlerInterface $error_handler
	) {
		$this->app = $app;
		$this->request = $request;
		$this->router = $router;
		$this->error_handler = $error_handler;
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function bootstrap() {
		// Web.
		add_action( 'request', [$this, 'filterRequest'], 1000 );
		add_action( 'template_include', [$this, 'filterTemplateInclude'], 1000 );

		// Ajax.
		add_action( 'admin_init', [$this, 'registerAjaxAction'] );

		// Admin.
		add_action( 'admin_init', [$this, 'registerAdminAction'] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, $arguments = [] ) {
		$route = $this->router->execute( $request );

		if ( $route === null ) {
			return null;
		}

		$handler = function () use ( $route ) {
			$arguments = func_get_args();
			$request = array_shift( $arguments );
			return call_user_func( [$route, 'handle'], $request, $arguments );
		};

		$response = $this->run( $request, $route->getMiddleware(), $handler, $arguments );

		$container = $this->app->getContainer();
		$container[ WPEMERGE_RESPONSE_KEY ] = $response;

		return $response;
	}

	/**
	 * {@inheritDoc}
	 */
	public function run( RequestInterface $request, $middleware, $handler, $arguments = [] ) {
		$this->error_handler->register();

		try {
			$middleware = $this->expandMiddleware( $middleware );
			$middleware = $this->uniqueMiddleware( $middleware );
			$middleware = $this->sortMiddleware( $middleware );

			$response = ( new Pipeline() )
				->pipe( $middleware )
				->to( $handler )
				->run( $request, array_merge( [$request], $arguments ) );
		} catch ( Exception $exception ) {
			$response = $this->error_handler->getResponse( $request, $exception );
		}

		$this->error_handler->unregister();

		return $response;
	}

	/**
	 * Filter the main query vars.
	 *
	 * @param  array $query_vars
	 * @return array
	 */
	public function filterRequest( $query_vars ) {
		/** @var $routes \WPEmerge\Routing\RouteInterface[] */
		$routes = $this->router->getRoutes();

		foreach ( $routes as $route ) {
			if ( ! $route instanceof HasQueryFilterInterface ) {
				continue;
			}

			if ( ! $route->isSatisfied( $this->request ) ) {
				continue;
			}

			$query_vars = $route->applyQueryFilter( $this->request, $query_vars );
			break;
		}

		return $query_vars;
	}

	/**
	 * Filter the main template file.
	 *
	 * @param  string $view
	 * @return string
	 */
	public function filterTemplateInclude( $view ) {
		/** @var $wp_query \WP_Query */
		global $wp_query;

		$response = $this->handle( $this->request, [$view] );

		if ( $response instanceof ResponseInterface ) {
			if ( $response->getStatusCode() === 404 ) {
				$wp_query->set_404();
			}

			return WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php';
		}

		return $view;
	}

	/**
	 * Register ajax action to hook into current one.
	 *
	 * @return void
	 */
	public function registerAjaxAction() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return;
		}

		$action = $this->request->post( 'action', $this->request->get( 'action' ) );
		$action = sanitize_text_field( $action );

		add_action( "wp_ajax_{$action}", [$this, 'actionAjax'] );
		add_action( "wp_ajax_nopriv_{$action}", [$this, 'actionAjax'] );
	}

	/**
	 * Act on ajax action.
	 *
	 * @return void
	 */
	public function actionAjax() {
		$response = $this->handle( $this->request, [''] );

		if ( ! $response instanceof ResponseInterface ) {
			return;
		}

		Response::respond( $response );
		wp_die( '', '', ['response' => null] );
	}

	/**
	 * Get page hook.
	 * Slightly modified version of code from wp-admin/admin.php.
	 *
	 * @return string
	 */
	protected function getAdminPageHook() {
		global $pagenow, $typenow, $plugin_page;

		$page_hook = '';
		if ( isset( $plugin_page ) ) {
			if ( ! empty( $typenow ) ) {
				$the_parent = $pagenow . '?post_type=' . $typenow;
			} else {
				$the_parent = $pagenow;
			}

			$page_hook = get_plugin_page_hook( $plugin_page, $the_parent );
		}

		return $page_hook;
	}

	/**
	 * Get admin page hook.
	 * Slightly modified version of code from wp-admin/admin.php.
	 *
	 * @param  string $page_hook
	 * @return string
	 */
	protected function getAdminHook( $page_hook ) {
		global $pagenow, $plugin_page;

		$hook_suffix = '';
		if ( ! empty( $page_hook ) ) {
			$hook_suffix = $page_hook;
		} else if ( isset( $plugin_page ) ) {
			$hook_suffix = $plugin_page;
		} else if ( isset( $pagenow ) ) {
			$hook_suffix = $pagenow;
		}

		return $hook_suffix;
	}

	/**
	 * Register admin action to hook into current one.
	 *
	 * @return void
	 */
	public function registerAdminAction() {
		global $pagenow;

		if ( $pagenow !== 'admin.php' ) {
			return;
		}

		$page_hook = $this->getAdminPageHook();
		$hook_suffix = $this->getAdminHook( $page_hook );

		add_action( "load-{$hook_suffix}", [$this, 'actionAdminLoad'] );
		add_action( $hook_suffix, [$this, 'actionAdmin'] );
	}

	/**
	 * Act on admin action load.
	 *
	 * @return void
	 */
	public function actionAdminLoad() {
		$response = $this->handle( $this->request, [''] );

		if ( ! $response instanceof ResponseInterface ) {
			return;
		}

		if ( ! headers_sent() ) {
			Response::sendHeaders( $response );
		}
	}

	/**
	 * Act on admin action.
	 *
	 * @return void
	 */
	public function actionAdmin() {
		$response = $this->app->resolve( WPEMERGE_RESPONSE_KEY );

		if ( $response === null ) {
			return;
		}

		Response::sendBody( $response );
	}
}
