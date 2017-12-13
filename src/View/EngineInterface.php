<?php

namespace WPEmerge\View;

/**
 * Interface that view engines must implement
 */
interface EngineInterface {
	/**
	 * Check if a view exists
	 *
	 * @param  string  $view
	 * @return boolean
	 */
	public function exists( $view );

	/**
	 * Render the first view that exists to a string
	 *
	 * @param  string[] $view
	 * @param  array    $context
	 * @return string
	 */
	public function render( $views, $context );
}
