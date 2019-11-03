<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

/**
 * Interface that view engines must implement
 */
interface ViewEngineInterface extends ViewFinderInterface {
	/**
	 * Create a view instance from the first view name that exists.
	 *
	 * @param  array<string> $views
	 * @return ViewInterface
	 */
	public function make( $views );
}
