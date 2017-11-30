<?php

namespace Obsidian\Input;

use Flash;
use Obsidian\Support\Arr;

/**
 * Provide a way to get values from the previous request
 */
class OldInput {
	/**
	 * Key to store the flashed data with
	 *
	 * @var string
	 */
	const FLASH_KEY = '__obsidianOldInput';

	/**
	 * Return all previously flashed request data
	 *
	 * @return array
	 */
	public function all() {
		return Flash::peek( static::FLASH_KEY );
	}

	/**
	 * Return any previously flashed request data value
	 *
	 * @see Arr::get()
	 */
	public function get() {
		$arguments = array_merge( [
			static::all(),
		], func_get_args() );
		return call_user_func_array( [Arr::class, 'get'], $arguments );
	}

	/**
	 * Clear previously stored input
	 */
	public function clear() {
		// @codeCoverageIgnoreStart
		if ( ! Flash::enabled() ) {
			return;
		}
		// @codeCoverageIgnoreEnd

		Flash::clear( static::FLASH_KEY );
	}

	/**
	 * Store the current input
	 *
	 * @param array $input
	 */
	public function store( $input ) {
		// @codeCoverageIgnoreStart
		if ( ! Flash::enabled() ) {
			return;
		}
		// @codeCoverageIgnoreEnd

		Flash::add( static::FLASH_KEY, $input );
	}
}
