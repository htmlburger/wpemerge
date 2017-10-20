<?php

namespace CarbonFramework\Routing;

use ReflectionClass;
use Exception;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\ServerRequest;
use CarbonFramework\Framework;
use CarbonFramework\Response as FrameworkResponse;

class Router {
	use Routable;

	public function hook() {
		add_action( 'template_include', array( $this, 'route' ), 1000 );
	}

	public function route( $template ) {
		$routes = $this->getRoutes();
		foreach ( $routes as $route ) {
			if ( $route->satisfied() ) {
				return $this->handle( $route->getHandler() );
			}
		}
		return $template;
	}

	protected function handle( $handler ) {
		$request = ServerRequest::fromGlobals();
		$response = $handler->execute( $request );

		if ( ! is_a( $response, ResponseInterface::class ) ) {
			if ( Framework::debug() ) {
				throw new Exception( 'Response returned by controller is not valid (expectected ' . ResponseInterface::class . '; received ' . gettype( $response ) . ').' );
			}
			$response = FrameworkResponse::error( FrameworkResponse::response(), 500 );
		}

		add_filter( 'carbon_framework_response', function() use ( $response ) {
			return $response;
		} );

		return CARBON_FRAMEWORK_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'template.php';
	}

	public function condition() {
		$args = func_get_args();
		if ( count( $args ) === 0 ) {
			throw new Exception( 'No condition type specified.' );
		}

		$condition_type = $args[0];
		$arguments = array_slice( $args, 1 );

		$condition_class = Framework::resolve( 'framework.routing.conditions.' . $condition_type );
		if ( $condition_class === null ) {
			throw new Exception( 'Unknown condition type specified: ' . $condition_type );
		}

		$reflection = new ReflectionClass( $condition_class );
		$condition = $reflection->newInstanceArgs( $arguments );
		return $condition;
	}
}
