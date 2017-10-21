<?php

namespace CarbonFramework\Input;

use Flash;
use CarbonFramework\Support\Arr;

class OldInput {
	const FLASH_KEY = '__carbonFrameworkOldInput';

	public static function getFlashKey() {
		return static::FLASH_KEY;
	}

	public static function all() {
		return Flash::peek( static::getFlashKey() );
	}

	public static function get() {
		$arguments = array_merge( [
			static::all(),
		], func_get_args() );
		return call_user_func_array( [Arr::class, 'get'], $arguments );
	}
}