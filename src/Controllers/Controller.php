<?php

namespace CarbonFramework\Controllers;

use CarbonFramework\Framework;
use CarbonFramework\Response;

/**
 * Base controller class that should be extended by all controllers
 */
abstract class Controller {
	/**
	 * @see Response::response()
	 */
	protected function response() {
		return FrameworkResponse::response();
	}

	/**
	 * @see Response::output()
	 */
	protected function output( $output ) {
		return FrameworkResponse::output( $this->response(), $output );
	}

	/**
	 * @see Response::template()
	 */
	protected function template( $templates, $context = array() ) {
		return FrameworkResponse::template( $this->response(), $templates, $context );
	}

	/**
	 * @see Response::json()
	 */
	protected function json( $data ) {
		return FrameworkResponse::json( $this->response(), $data );
	}

	/**
	 * @see Response::redirect()
	 */
	protected function redirect( $url, $status = 302 ) {
		return FrameworkResponse::redirect( $this->response(), $url, $status );
	}

	/**
	 * @see Response::error()
	 */
	protected function error( $code ) {
		return FrameworkResponse::error( $this->response(), $code );
	}
}
