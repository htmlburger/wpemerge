<?php

namespace WPEmerge\View;

use View as ViewService;

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
			['global' => ViewService::getGlobals()],
			ViewService::compose( $file ),
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
