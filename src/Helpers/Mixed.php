<?php

namespace Obsidian\Helpers;

class Mixed {
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

	/**
	 * Executes a value depending on what type it is and returns the result
	 * Callable: call
	 * Instance: call method
	 * Class:    instantiate and call method
	 * Other:    return it
	 *
	 * @param  mixed  $entity
	 * @param  array  $arguments
	 * @param  string $method
	 * @return mixed
	 */
	public static function value( $entity, $arguments = [], $method = null ) {
		if ( is_callable( $entity ) ) {
			return call_user_func_array( $entity, $arguments );
		}

		if ( is_string( $entity ) && class_exists( $entity ) ) {
			$instance = new $entity();
			return call_user_func_array( [$instance, $method], $arguments );
		}

		return call_user_func_array( [$entity, $method], $arguments );
	}
}
