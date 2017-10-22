<?php

namespace CarbonFramework\Input;

use Flash;
use CarbonFramework\Support\Arr;

/**
 * Provide a way to get values from the previous request
 */
class OldInput {
	/**
	 * Key to store the flashed data with
	 * 
	 * @var string
	 */
	const FLASH_KEY = '__carbonFrameworkOldInput';

	/**
	 * Return the flashed data key
	 * 
	 * @return string
	 */
	public static function getFlashKey() {
		return static::FLASH_KEY;
	}

	/**
	 * Return all previously flashed request data
	 * 
	 * @return array
	 */
	public static function all() {
		return Flash::peek( static::getFlashKey() );
	}

	/**
	 * Return any previously flashed request data value
	 * 
	 * @see Arr::get()
	 */
	public static function get() {
		$arguments = array_merge( [
			static::all(),
		], func_get_args() );
		return call_user_func_array( [Arr::class, 'get'], $arguments );
	}
}