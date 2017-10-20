<?php

namespace CarbonFramework;

use Exception;
use ReflectionException;
use ReflectionMethod;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use CarbonFramework\Facades\Facade;
use CarbonFramework\Support\AliasLoader;
use CarbonFramework\Routing\Router;
use CarbonFramework\Facades\Router as RouterFacade;

class Framework {
	protected static $booted = false;

	protected static $container = null;

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

	public static function boot( $bootstrap = null ) {
		if ( static::isBooted() ) {
			throw new Exception( get_called_class() . ' already booted.' );
		}
		static::$booted = true;

		static::$container = new Container();

		static::bindDefaults( static::$container );
		if ( is_callable( $bootstrap ) ) {
			call_user_func( $bootstrap, static::$container );
		}

		Facade::setFacadeApplication( static::$container );
		AliasLoader::getInstance()->register();

		\Router::hook(); // facade
	}

	protected static function bindDefaults( $container ) {
		$container['framework.router'] = function( $c ) {
			return new Router();
		};

		$container['framework.routing.conditions.custom'] = \CarbonFramework\Routing\Conditions\Custom::class;
		$container['framework.routing.conditions.url'] = \CarbonFramework\Routing\Conditions\Url::class;
		$container['framework.routing.conditions.post_id'] = \CarbonFramework\Routing\Conditions\PostId::class;

		static::facade( 'Router', RouterFacade::class );
	}

	public static function facade( $alias, $facade_class ) {
		AliasLoader::getInstance()->alias( $alias, $facade_class );
	}

	public static function resolve( $key ) {
		static::verifyBoot();

		if ( ! isset( static::$container[ $key ] ) ) {
			return null;
		}
		return static::$container[ $key ];
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
