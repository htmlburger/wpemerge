<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

class MixedType {
	/**
	 * Converts a value to an array containing this value unless it is an array
	 *
	 * @param  mixed $argument
	 * @return array
	 */
	public static function toArray( $argument ) {
		if ( ! is_array( $argument ) ) {
			$argument = [$argument];
		}

		return $argument;
	}

	/**
	 * Executes a value depending on what type it is and returns the result
	 * Callable: call; return result
	 * Instance: call method; return result
	 * Class:    instantiate; call method; return result
	 * Other:    return value without taking any action
	 *
	 * @param  mixed    $entity
	 * @param  array    $arguments
	 * @param  string   $method
	 * @param  callable $instantiator
	 * @return mixed
	 */
	public static function value( $entity, $arguments = [], $method = '__invoke', $instantiator = 'static::instantiate' ) {
		if ( is_callable( $entity ) ) {
			return call_user_func_array( $entity, $arguments );
		}

		if ( is_object( $entity ) ) {
			return call_user_func_array( [$entity, $method], $arguments );
		}

		if ( static::isClass( $entity ) ) {
			return call_user_func_array( [call_user_func( $instantiator, $entity ), $method], $arguments );
		}

		return $entity;
	}

	/**
	 * Check if a value is a valid class name
	 *
	 * @param  mixed   $class_name
	 * @return boolean
	 */
	public static function isClass( $class_name ) {
		return ( is_string( $class_name ) && class_exists( $class_name ) );
	}

	/**
	 * Create a new instance of the given class.
	 *
	 * @param  string $class_name
	 * @return object
	 */
	public static function instantiate( $class_name ) {
		return new $class_name();
	}

	/**
	 * Normalize a path's slashes according to the current OS.
	 * Solves mixed slashes that are sometimes returned by WordPress core functions.
	 *
	 * @param  string $path
	 * @param  string $slash
	 * @return string
	 */
	public static function normalizePath( $path, $slash = DIRECTORY_SEPARATOR ) {
		return preg_replace( '~[' . preg_quote( '/\\', '~' ) . ']+~', $slash, $path );
	}

	/**
	 * Ensure path has a trailing slash.
	 *
	 * @param  string $path
	 * @param  string $slash
	 * @return string
	 */
	public static function addTrailingSlash( $path, $slash = DIRECTORY_SEPARATOR ) {
		$path = static::normalizePath( $path, $slash );
		$path = preg_replace( '~' . preg_quote( $slash, '~' ) . '*$~', $slash, $path );
		return $path;
	}

	/**
	 * Ensure path does not have a trailing slash.
	 *
	 * @param  string $path
	 * @param  string $slash
	 * @return string
	 */
	public static function removeTrailingSlash( $path, $slash = DIRECTORY_SEPARATOR ) {
		$path = static::normalizePath( $path, $slash );
		$path = preg_replace( '~' . preg_quote( $slash, '~' ) . '+$~', '', $path );
		return $path;
	}
}
