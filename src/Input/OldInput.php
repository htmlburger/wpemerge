<?php

namespace WPEmerge\Input;

use WPEmerge\Facades\Flash;
use WPEmerge\Support\Arr;

/**
 * Provide a way to get values from the previous request.
 */
class OldInput {
	/**
	 * Key to store the flashed data with.
	 *
	 * @var string
	 */
	protected $flash_key = '';

	/**
	 * Constructor.
	 *
	 * @param string $flash_key
	 */
	public function __construct( $flash_key = '__wpemergeOldInput' ) {
		$this->flash_key = $flash_key;
	}

	/**
	 * Get whether the old input service is enabled.
	 *
	 * @return boolean.
	 */
	public function enabled() {
		return Flash::enabled();
	}

	/**
	 * Get any flashed request value for key from the previous request.
	 *
	 * @see    Arr::get()
	 * @param  string     $key
	 * @param  mixed      $default
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		return Arr::get( Flash::get( $this->flash_key, [] ), $key, $default );
	}

	/**
	 * Set the current input.
	 *
	 * @param array $input
	 */
	public function set( $input ) {
		Flash::add( $this->flash_key, $input );
	}

	/**
	 * Clear stored input from the previous request.
	 */
	public function clear() {
		Flash::clear( $this->flash_key );
	}
}
