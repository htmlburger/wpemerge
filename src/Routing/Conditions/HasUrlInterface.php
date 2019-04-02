<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

/**
 * Interface for conditions that accept a URL.
 */
interface HasUrlInterface {
	/**
	 * Get the URL where.
	 *
	 * @return array<string, string>
	 */
	public function getUrlWhere();

	/**
	 * Set the URL where.
	 *
	 * @param array<string, string> $where
	 * @return void
	 */
	public function setUrlWhere( $where );
}
