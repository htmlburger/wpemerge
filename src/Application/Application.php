<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use Closure;
use Pimple\Container;
use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Requests\Request;
use WPEmerge\Support\Arr;

/**
 * The core WP Emerge component representing an application.
 */
class Application {
	use HasAliasesTrait;
	use LoadsServiceProvidersTrait;
	use HasContainerTrait;

	/**
	 * Flag whether to intercept and render configuration exceptions.
	 *
	 * @var boolean
	 */
	protected $render_config_exceptions = true;

	/**
	 * Flag whether the application has been bootstrapped.
	 *
	 * @var boolean
	 */
	protected $bootstrapped = false;

	/**
	 * Make a new application instance.
	 *
	 * @codeCoverageIgnore
	 * @return static
	 */
	public static function make() {
		return new static( new Container() );
	}

	/**
	 * Constructor.
	 *
	 * @param Container $container
	 * @param boolean   $render_config_exceptions
	 */
	public function __construct( Container $container, $render_config_exceptions = true ) {
		$this->setContainer( $container );
		$this->container()[ WPEMERGE_APPLICATION_KEY ] = $this;
		$this->render_config_exceptions = $render_config_exceptions;
	}

	/**
	 * Get whether the application has been bootstrapped.
	 *
	 * @return boolean
	 */
	public function isBootstrapped() {
		return $this->bootstrapped;
	}

	/**
	 * Bootstrap the application.
	 *
	 * @param  array   $config
	 * @param  boolean $run
	 * @return void
	 */
	public function bootstrap( $config = [], $run = true ) {
		if ( $this->isBootstrapped() ) {
			throw new ConfigurationException( static::class . ' already bootstrapped.' );
		}

		$this->bootstrapped = true;

		$container = $this->container();
		$this->loadConfig( $container, $config );
		$this->loadServiceProviders( $container );

		$this->renderConfigExceptions( function () use ( $run ) {
			$this->loadRoutes();

			if ( $run ) {
				$kernel = $this->resolve( WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY );
				$kernel->bootstrap();
			}
		} );
	}

	/**
	 * Load config into the service container.
	 *
	 * @codeCoverageIgnore
	 * @param  Container $container
	 * @param  array     $config
	 * @return void
	 */
	protected function loadConfig( Container $container, $config ) {
		$container[ WPEMERGE_CONFIG_KEY ] = $config;
	}

	/**
	 * Load route definition files depending on the current request.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	protected function loadRoutes() {
		if ( wp_doing_ajax() ) {
			$this->loadRoutesGroup( 'ajax' );
			return;
		}

		if ( is_admin() ) {
			$this->loadRoutesGroup( 'admin' );
			return;
		}

		$this->loadRoutesGroup( 'web' );
	}

	/**
	 * Load a route group applying default attributes, if any.
	 *
	 * @codeCoverageIgnore
	 * @param  string $group
	 * @return void
	 */
	protected function loadRoutesGroup( $group ) {
		$config = $this->resolve( WPEMERGE_CONFIG_KEY );
		$file = Arr::get( $config, 'routes.' . $group . '.definitions', '' );
		$attributes = Arr::get( $config, 'routes.' . $group . '.attributes', [] );

		if ( empty( $file ) ) {
			return;
		}

		$middleware = Arr::get( $attributes, 'middleware', [] );

		if ( ! in_array( $group, $middleware, true ) ) {
			$middleware = array_merge( [$group], $middleware );
		}

		$attributes['middleware'] = $middleware;

		$blueprint = $this->resolve( WPEMERGE_ROUTING_ROUTE_BLUEPRINT_KEY );
		$blueprint->attributes( $attributes )->group( $file );
	}

	/**
	 * Catch any configuration exceptions and short-circuit to an error page.
	 *
	 * @codeCoverageIgnore
	 * @param  Closure $action
	 * @return void
	 */
	protected function renderConfigExceptions( Closure $action ) {
		try {
			$action();
		} catch ( ConfigurationException $exception ) {
			if ( ! $this->render_config_exceptions ) {
				throw $exception;
			}

			$request = Request::fromGlobals();
			$handler = $this->resolve( WPEMERGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY );

			add_filter( 'wpemerge.pretty_errors.apply_admin_styles', '__return_false' );

			$response_service = $this->resolve( WPEMERGE_RESPONSE_SERVICE_KEY );
			$response_service->respond( $handler->getResponse( $request, $exception ) );

			wp_die();
		}
	}
}
