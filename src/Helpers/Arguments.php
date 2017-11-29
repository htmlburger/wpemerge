<?php

namespace Obsidian\Helpers;

class Arguments {
	/**
	 * Converts a value to an array containing this value if needed
	 * The $check_for_callable flag is there to prevent coincidences that
	 * can happen with [$object, 'stringThatMatchesAMethodOf$object']
	 *
	 * @param  mixed   $argument
	 * @param  boolean $check_for_callable
	 * @return array
	 */
	public static function toArray( $argument, $check_for_callable = false ) {
		if ( $check_for_callable && is_array( $argument ) && is_callable( $argument ) ) {
			$argument = [$argument];
		}

		if ( ! is_array( $argument ) ) {
			$argument = [$argument];
		}

		return $argument;
	}
}
