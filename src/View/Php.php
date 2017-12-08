<?php

namespace WPEmerge\View;

/**
 * Render view files with php
 */
class Php implements EngineInterface {
	/**
	 * Global context
	 *
	 * @var array
	 */
	protected $global_context = [];

	/**
	 * Constructor
	 *
	 * @param array $global_context
	 */
	public function __construct( $global_context ) {
		$this->global_context = $global_context;
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $file, $context ) {
		$__view = $file;
		$__context = array_merge(
			['global' => $this->global_context],
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
