<?php

namespace CarbonFramework\Routing;

use CarbonFramework\Url;
use CarbonFramework\Support\StrictFluent;

use Exception;

class Route {
	protected $config = null;

	public function __construct( $options ) {
		$default_config = array(
			'endpoint' => '',
			'postId' => 0,
			'template' => '',
			'path' => '',
			'methods' => ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
			'to' => '',
		);
		$this->config = new StrictFluent( array_merge( $default_config, $options ) );
	}

	public function config() {
		return $this->config;
	}

	public function validate() {
		if ( empty( $this->config()->methods ) ) {
			throw new Exception( 'Route: Cannot register route with no methods.' );
		}

		try {
			$handler = $this->getHandler();
		} catch ( Exception $e ) {
			throw new Exception( 'Route: No or invalid handler provided (missed to call `to()` on route?).' );
		}
	}

	public function getHandler() {
		return new Handler( $this->config()->to );
	}

	public function matches() {
		if ( $this->config()->endpoint ) {
			$value = get_query_var( $this->config()->endpoint, null );
			if ( $value === null ) {
				return false;
			}
		}

		if ( $this->config()->postId ) {
			if ( intval( get_the_ID() ) !== intval( $this->config()->postId ) ) {
				return false;
			}
		}

		if ( $this->config()->template ) {
			$post = get_post();
			if ( ! $post ) {
				return false;
			}

			$template = get_post_meta( $post->ID, '_wp_page_template', true );
			$template = $template ? $template : 'default';
			if ( $template !== $this->config()->template ) {
				return false;
			}
		}

		if ( $this->config()->path ) {
			$path = trailingslashit( Url::getCurrentPath() );
			if ( $path !== trailingslashit( $this->config()->path ) ) {
				return false;
			}
		}

		if ( ! in_array( $_SERVER['REQUEST_METHOD'], $this->config()->methods ) ) {
			return false;
		}

		return true;
	}
}
