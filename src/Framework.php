<?php

namespace CarbonFramework;

use ArrayAccess;
use ReflectionException;
use ReflectionMethod;
use Exception;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use CarbonFramework\Facades\Facade;
use CarbonFramework\Support\AliasLoader;
use CarbonFramework\ServiceProviders\Routing as RoutingServiceProvider;

class Framework {
	protected static $booted = false;

	protected static $container = null;

	protected static $service_providers = [
		RoutingServiceProvider::class,
	];

	public static function debug() {
		return ( defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

	public static function isBooted() {
		return static::$booted;
	}

	public static function verifyBoot() {
		if ( ! static::isBooted() ) {
			throw new Exception( get_called_class() . ' must be booted first.' );
		}
	}

	public static function getContainer() {
		if ( static::$container === null ) {
			static::$container = new Container();
		}
		return static::$container;
	}

	public static function boot() {
		if ( static::isBooted() ) {
			throw new Exception( get_called_class() . ' already booted.' );
		}
		static::$booted = true;

		Facade::setFacadeApplication( static::getContainer() );
		AliasLoader::getInstance()->register();

		static::$service_providers = apply_filters( 'carbon_framework_service_providers', static::$service_providers );

		$service_providers = array_map( function( $service_provider ) {
			return new $service_provider();
		}, static::$service_providers );

		static::registerServiceProviders( $service_providers, static::getContainer() );
		static::bootServiceProviders( $service_providers, static::getContainer() );
	}

	protected static function registerServiceProviders( $service_providers, ArrayAccess $container ) {
		foreach ( $service_providers as $provider ) {
			$provider->register( $container );
		}
	}

	protected static function bootServiceProviders( $service_providers, ArrayAccess $container ) {
		foreach ( $service_providers as $provider ) {
			$provider->boot( $container );
		}
	}

	public static function facade( $alias, $facade_class ) {
		AliasLoader::getInstance()->alias( $alias, $facade_class );
	}

	public static function resolve( $key ) {
		static::verifyBoot();

		if ( ! isset( static::getContainer()[ $key ] ) ) {
			return null;
		}
		return static::getContainer()[ $key ];
	}

	public static function instantiate( $class ) {
		static::verifyBoot();

		$instance = static::resolve( $class );
		if ( $instance === null ) {
			try {
				$reflection = new ReflectionMethod( $class, '__construct' );

				if ( ! $reflection->isPublic() ) {
					throw new Exception( $class . '::__construct() is not public.' );
				}

				$parameters = $reflection->getParameters();

				$required_parameters = array_filter( $parameters, function( $parameter ) {
					return ! $parameter->isOptional();
				} );

				if ( ! empty( $required_parameters ) ) {
					throw new Exception( $class . '::__construct() has requird parameters but could not be resolved from container. Did you miss to define it into the container?' );
				}
			} catch ( ReflectionException $e ) {
				// __constructor is not defined so we are free to create a new instance
			}

			$instance = new $class();
		}

		return $instance;
	}

	/**
	 * @credit slimphp/slim Slim/App.php
	 */
	public static function respond( ResponseInterface $response ) {
		// Send response
        if (!headers_sent()) {
            // Status
            header(sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
            // Headers
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }
        // Body
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }
        $chunkSize = 4096;
        $contentLength = $response->getHeaderLine('Content-Length');
        if (!$contentLength) {
            $contentLength = $body->getSize();
        }
        if (isset($contentLength)) {
            $amountToRead = $contentLength;
            while ($amountToRead > 0 && !$body->eof()) {
                $data = $body->read(min($chunkSize, $amountToRead));
                echo $data;
                $amountToRead -= strlen($data);
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
        } else {
            while (!$body->eof()) {
                echo $body->read($chunkSize);
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
        }
	}
}
