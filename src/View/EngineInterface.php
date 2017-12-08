<?php

namespace WPEmerge\View;

/**
 * Interface that view engines must implement
 */
interface EngineInterface {
	/**
	 * Render a view to a string
	 *
	 * @param  string $file
	 * @param  array  $context
	 * @return string
	 */
	public function render( $file, $context );
}
