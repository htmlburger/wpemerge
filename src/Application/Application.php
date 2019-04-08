<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Controllers\ControllersServiceProvider;
use WPEmerge\Csrf\CsrfServiceProvider;
use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Exceptions\ExceptionsServiceProvider;
use WPEmerge\Flash\FlashServiceProvider;
use WPEmerge\Input\OldInputServiceProvider;
use WPEmerge\Kernels\KernelsServiceProvider;
use WPEmerge\Requests\RequestsServiceProvider;
use WPEmerge\Responses\ResponsesServiceProvider;
use WPEmerge\Routing\RoutingServiceProvider;
use WPEmerge\ServiceProviders\ServiceProviderInterface;
use WPEmerge\Support\AliasLoader;
use WPEmerge\View\ViewServiceProvider;

/**
 * Main communication channel with the application.
 */
class Application {
	/**
	 * Flag whether the application has been bootstrapped.
	 *
	 * @var boolean
	 */
	protected $bootstrapped = false;

	/**
	 * IoC container.
	 *
	 * @var Container
	 */
	protected $container = null;

	/**
	 * Array of application service providers.
	 *
	 * @var string[]
	 */
	protected $service_providers = [
		KernelsServiceProvider::class,
		ExceptionsServiceProvider::class,
		RequestsServiceProvider::class,
		ResponsesServiceProvider::class,
		RoutingServiceProvider::class,
		ViewServiceProvider::class,
		ControllersServiceProvider::class,
		CsrfServiceProvider::class,
		FlashServiceProvider::class,
		OldInputServiceProvider::class,
	];

	/**
	 * Constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;

		$config = isset( $container[ WPEMERGE_CONFIG_KEY ] ) ? $container[ WPEMERGE_CONFIG_KEY ] : [];
		$config = array_merge( [
			'providers' => [],
		], $config );
		$container[ WPEMERGE_CONFIG_KEY ] = $config;
	}

	/**
	 * Get whether WordPress is in debug mode.
	 *
	 * @return boolean
	 */
	public function debugging() {
		$debugging = ( defined( 'WP_DEBUG' ) && WP_DEBUG );
		$debugging = apply_filters( 'wpemerge.debug', $debugging );
		return $debugging;
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
	 * Throw an exception if the application has not been bootstrapped.
	 *
	 * @throws ConfigurationException
	 * @return void
	 */
	protected function verifyBootstrap() {
		if ( ! $this->isBootstrapped() ) {
			throw new ConfigurationException( static::class . ' must be bootstrapped first.' );
		}
	}

	/**
	 * Get the IoC container instance.
	 *
	 * @return Container
	 */
	public function getContainer() {
		return $this->container;
	}

	/**
	 * Bootstrap the application.
	 * WordPress' 'after_setup_theme' action is a good place to call this.
	 *
	 * @throws ConfigurationException
	 * @param  array $config
	 * @return void
	 */
	public function bootstrap( $config = [] ) {
		if ( $this->isBootstrapped() ) {
			throw new ConfigurationException( static::class . ' already bootstrapped.' );
		}

		$container = $this->getContainer();
		$this->loadConfig( $container, $config );
		$this->loadServiceProviders( $container );

		require_once WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'wordpress.php';
		/*require_once WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'wordpress-admin.php';*/

		$this->bootstrapped = true;
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
		$container[ WPEMERGE_CONFIG_KEY ] = array_merge(
			$container[ WPEMERGE_CONFIG_KEY ],
			$config
		);
	}

	/**
	 * Register and bootstrap all service providers.
	 *
	 * @codeCoverageIgnore
	 * @param  Container $container
	 * @return void
	 */
	protected function loadServiceProviders( Container $container ) {
		$container[ WPEMERGE_SERVICE_PROVIDERS_KEY ] = array_merge(
			$this->service_providers,
			$container[ WPEMERGE_CONFIG_KEY ]['providers']
		);

		$service_providers = array_map( function ( $service_provider ) {
			if ( ! is_subclass_of( $service_provider, ServiceProviderInterface::class ) ) {
				throw new ConfigurationException( 'The following class does not implement ServiceProviderInterface: ' . $service_provider );
			}

			return new $service_provider();
		}, $container[ WPEMERGE_SERVICE_PROVIDERS_KEY ] );

		$this->registerServiceProviders( $service_providers, $container );
		$this->bootstrapServiceProviders( $service_providers, $container );
	}

	/**
	 * Register all service providers.
	 *
	 * @param  \WPEmerge\ServiceProviders\ServiceProviderInterface[] $service_providers
	 * @param  Container                                             $container
	 * @return void
	 */
	protected function registerServiceProviders( $service_providers, Container $container ) {
		foreach ( $service_providers as $provider ) {
			$provider->register( $container );
		}
	}

	/**
	 * Bootstrap all service providers.
	 *
	 * @param  \WPEmerge\ServiceProviders\ServiceProviderInterface[] $service_providers
	 * @param  Container                                             $container
	 * @return void
	 */
	protected function bootstrapServiceProviders( $service_providers, Container $container ) {
		foreach ( $service_providers as $provider ) {
			$provider->bootstrap( $container );
		}
	}

	/**
	 * Register a facade class.
	 *
	 * @param  string $alias
	 * @param  string $facade_class
	 * @return void
	 */
	public function facade( $alias, $facade_class ) {
		AliasLoader::getInstance()->alias( $alias, $facade_class );
	}

	/**
	 * Resolve a dependency from the IoC container.
	 *
	 * @param  string     $key
	 * @return mixed|null
	 */
	public function resolve( $key ) {
		$this->verifyBootstrap();

		if ( ! isset( $this->getContainer()[ $key ] ) ) {
			return null;
		}

		return $this->getContainer()[ $key ];
	}

	/**
	 * Create and return a class instance.
	 *
	 * @throws ClassNotFoundException
	 * @param  string $class
	 * @return object
	 */
	public function instantiate( $class ) {
		$this->verifyBootstrap();

		$instance = $this->resolve( $class );

		if ( $instance === null ) {
			if ( ! class_exists( $class ) ) {
				throw new ClassNotFoundException( 'Class not found: ' . $class );
			}

			$instance = new $class();
		}

		return $instance;
	}

	/**
	 * Send output based on a response object.
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return void
	 */
	public function respond( ResponseInterface $response ) {
		$this->resolve( WPEMERGE_RESPONSE_SERVICE_KEY )->respond( $response );
	}
}
