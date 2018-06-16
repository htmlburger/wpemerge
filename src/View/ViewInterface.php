<?php

namespace WPEmerge\View;

use WPEmerge\Responses\ResponsableInterface;

/**
 * Represent and render a view to a string.
 */
interface ViewInterface extends HasContextInterface, ResponsableInterface {
	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Set name.
	 *
	 * @param  string $name
	 * @return self   $this
	 */
	public function setName( $name );

	/**
	 * Render the view to a string.
	 *
	 * @return string
	 */
	public function toString();
}
