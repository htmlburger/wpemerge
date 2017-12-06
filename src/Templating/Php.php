<?php

namespace WPEmerge\Templating;

/**
 * Include template files with php
 */
class Php implements EngineInterface {
	/**
	 * {@inheritDoc}
	 */
	public function render( $file, $context ) {
		$__template = $file;
		$__context = $context;
		$renderer = function() use ( $__template, $__context ) {
			ob_start();
			extract( $__context );
			include( $__template );
			return ob_get_clean();
		};
		return $renderer();
	}
}
