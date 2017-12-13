<?php

namespace WPEmerge\View;

use View;

/**
 * Render view files with php
 */
class Php implements EngineInterface {
	/**
	 * {@inheritDoc}
	 */
	public function render( $file, $context ) {
		$__view = $file;

		$__context = array_merge(
			['global' => View::getGlobals()],
			View::compose( $file ),
			$context
		);

		$renderer = function() use ( $__view, $__context ) {
			ob_start();
			extract( $__context );
			include( $__view );
			return ob_get_clean();
		};

		return $renderer();
	}
}
