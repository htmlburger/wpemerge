<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

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
