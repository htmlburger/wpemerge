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
