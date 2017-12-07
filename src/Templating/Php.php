<?php

namespace WPEmerge\Templating;

/**
 * Include template files with php
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
		$__template = $file;
		$__context = array_merge(
			['global' => $this->global_context],
			$context
		);
		$renderer = function() use ( $__template, $__context ) {
			ob_start();
			extract( $__context );
			include( $__template );
			return ob_get_clean();
		};
		return $renderer();
	}
}
