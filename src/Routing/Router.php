<?php

namespace CarbonFramework\Routing;

use Exception;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\ServerRequest;
use CarbonFramework\Framework;
use CarbonFramework\Response as FrameworkResponse;

class Router {
	protected $endpoints = array();

	protected $routes = array();

	public function hook() {
		add_action( 'init', array( $this, 'registerEndpoints' ) );
		add_action( 'init', array( $this, 'validateRoutes' ) );
		add_action( 'template_include', array( $this, 'route' ) );
	}

	public function registerEndpoints() {
		foreach ( $this->endpoints as $endpoint ) {
			add_rewrite_endpoint( $endpoint['name'], $endpoint['mask'] );
		}
	}

	public function validateRoutes() {
		foreach ( $this->routes as $route ) {
			$route->validate();
		}
	}

	public function route( $template ) {
		foreach ( $this->routes as $route ) {
			if ( $route->matches() ) {
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

	protected function addRoute( $options = array() ) {
		$route = new Route( $options );
		$this->routes[] = $route;
		return $route->config();
	}

	public function addEndpoint( $name, $mask = null ) {
		$this->endpoints[ $name ] = array(
			'name' => $name,
			'mask' => $mask ? $mask : EP_ROOT,
		);
	}

	public function get() {
		return $this->addRoute( array(
			'methods' => ['GET', 'HEAD'],
		) );
	}

	public function post() {
		return $this->addRoute( array(
			'methods' => ['POST'],
		) );
	}

	public function put() {
		return $this->addRoute( array(
			'methods' => ['PUT'],
		) );
	}

	public function patch() {
		return $this->addRoute( array(
			'methods' => ['PATCH'],
		) );
	}

	public function delete() {
		return $this->addRoute( array(
			'methods' => ['DELETE'],
		) );
	}

	public function options() {
		return $this->addRoute( array(
			'methods' => ['OPTIONS'],
		) );
	}

	public function any() {
		return $this->addRoute();
	}
}
