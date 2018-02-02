<?php

namespace WPEmerge\View;

/**
 * Interface that view engines must implement
 */
interface ViewEngineInterface {
	/**
	 * Check if a view exists.
	 *
	 * @param  string  $view
	 * @return boolean
	 */
	public function exists( $view );

	/**
	 * Return a canonical string representation of the view name.
	 *
	 * @param  string  $view
	 * @return string
	 */
	public function canonical( $view );

	/**
	 * Create a view instance from the first view name that exists.
	 *
	 * @param  string[]      $views
	 * @param  array         $context
	 * @return ViewInterface
	 */
	public function make( $views, $context = [] );
}
