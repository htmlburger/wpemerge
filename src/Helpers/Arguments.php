<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

/**
 * A collection of tools dealing with urls
 */
class Arguments {
	/**
	 * Get a closure which will flip preceding optional arguments around.
	 * @example list( $argument1, $argument2 ) = Arguments::flip( $argument1, $argument2 );
	 *
	 * @return array
	 */
	public static function flip() {
		$arguments = func_get_args();
		$first_null = array_search( null, $arguments, true );

		if ( $first_null === false ) {
			return $arguments;
		}

		// Support integer keys only.
		$first_null = (int) $first_null;

		$arguments = array_values( array_merge(
			array_slice( $arguments, $first_null ),
			array_slice( $arguments, 0, $first_null )
		) );

		return $arguments;
	}
}
