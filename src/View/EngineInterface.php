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
	 * Return a canonical string representation of the view name
	 *
	 * @param  string  $view
	 * @return string
	 */
	public function canonical( $view );

	/**
	 * Render the first view that exists to a string
	 *
	 * @param  string[] $views
	 * @param  array    $context
	 * @return string
	 */
	public function render( $views, $context );
}
