<?php

namespace CarbonFramework\Controllers;

use CarbonFramework\Framework;
use CarbonFramework\Response as FrameworkResponse;

abstract class Controller {
	protected function response() {
		return FrameworkResponse::response();
	}

	protected function output( $output ) {
		return FrameworkResponse::output( $this->response(), $output );
	}

	protected function template( $templates, $context = array() ) {
		return FrameworkResponse::template( $this->response(), $templates, $context );
	}

	protected function json( $data ) {
		return FrameworkResponse::json( $this->response(), $data );
	}

	protected function redirect( $url, $status = 302, $headers = array() ) {
		return FrameworkResponse::redirect( $this->response(), $url, $status, $headers );
	}

	protected function error( $code ) {
		return FrameworkResponse::error( $this->response(), $code );
	}
}
