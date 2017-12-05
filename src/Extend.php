<?php

namespace Obsidian;

use Exception;
use Obsidian\Support\Arr;
use Obsidian\Routing\Conditions\ConditionInterface;

/**
 * Extend built-in functionality
 */
class Extend {
	/**
	 * Dictionary of extensions
	 *
	 * @var array
	 */
	protected static $extensions = [];

	/**
	 * Get extensions for type
	 *
	 * @param  string $type
	 * @return array
	 */
	public static function get( $type ) {
		return Arr::get( static::$extensions, $type );
	}

	/**
	 * Set extension for type, with name and class name
	 *
	 * @param  string $type
	 * @param  string $name
	 * @param  string $class_name
	 * @return void
	 */
	public static function set( $type, $name, $class_name ) {
		if ( Framework::isBooted() ) {
			throw new Exception( 'Extensions must be registered before Obsidian is booted.' );
		}
		Arr::set( static::$extensions, $type . '.' . $name, $class_name );
	}

	/**
	 * Register a route condition
	 *
	 * @param  string $name
	 * @param  string $class_name
	 * @return void
	 */
	public static function routeCondition( $name, $class_name ) {
		static::set( ConditionInterface::class, $name, $class_name );
	}
}
