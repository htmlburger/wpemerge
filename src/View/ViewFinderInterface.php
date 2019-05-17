<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

/**
 * Interface that view finders must implement.
 */
interface ViewFinderInterface {
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
}
