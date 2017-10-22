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
		return Response::response();
	}

	/**
	 * @see Response::output()
	 */
	protected function output( $output ) {
		return Response::output( $this->response(), $output );
	}

	/**
	 * @see Response::template()
	 */
	protected function template( $templates, $context = array() ) {
		return Response::template( $this->response(), $templates, $context );
	}

	/**
	 * @see Response::json()
	 */
	protected function json( $data ) {
		return Response::json( $this->response(), $data );
	}

	/**
	 * @see Response::redirect()
	 */
	protected function redirect( $url, $status = 302 ) {
		return Response::redirect( $this->response(), $url, $status );
	}

	/**
	 * @see Response::error()
	 */
	protected function error( $code ) {
		return Response::error( $this->response(), $code );
	}
}
