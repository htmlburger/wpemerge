<?php

namespace WPEmerge\Flash;

use ArrayAccess;
use Exception;
use WPEmerge\Helpers\Mixed;
use WPEmerge\Support\Arr;

/**
 * Provide a way to flash data into the session for the next request.
 */
class Flash {
	/**
	 * Keys for different request contexts.
	 */
	const CURRENT_KEY = 'current';
	const NEXT_KEY = 'next';

	/**
	 * Key to store flashed data in storage with.
	 *
	 * @var string
	 */
	protected $storage_key = '';

	/**
	 * Root storage array or object implementing ArrayAccess.
	 *
	 * @var array|\ArrayAccess
	 */
	protected $root_storage = null;

	/**
	 * Flash storage array.
	 *
	 * @var array
	 */
	protected $storage = null;

	/**
	 * Constructor.
	 *
	 * @param array|\ArrayAccess $storage
	 * @param string             $storage_key
	 */
	public function __construct( &$storage, $storage_key = '__wpemergeFlash' ) {
		$this->storage_key = $storage_key;
		$this->setStorage( $storage );
	}

	/**
	 * Get whether a storage object is valid.
	 *
	 * @param  mixed   $storage
	 * @return boolean
	 */
	protected function isValidStorage( $storage ) {
		return ( is_array( $storage ) || $storage instanceof ArrayAccess );
	}

	/**
	 * Throw an exception if storage is not valid.
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function validateStorage() {
		if ( ! $this->isValidStorage( $this->storage ) ) {
			throw new Exception( 'Attempted to use Flash without an active session. Did you forget to call session_start()?' );
		}
	}

	/**
	 * Get the storage for flash messages.
	 *
	 * @return array|\ArrayAccess
	 */
	public function getStorage() {
		return $this->storage;
	}

	/**
	 * Set the storage for flash messages.
	 *
	 * @param  array|\ArrayAccess $storage
	 * @return void
	 */
	public function setStorage( &$storage ) {
		if ( ! $this->isValidStorage( $storage ) ) {
			return;
		}

		if ( ! isset( $storage[ $this->storage_key ] ) ) {
			$storage[ $this->storage_key ] = [
				static::CURRENT_KEY => [],
				static::NEXT_KEY => [],
			];
		}

		$this->root_storage = &$storage;
		$this->storage = &$storage[ $this->storage_key ];
	}

	/**
	 * Get whether the flash service is enabled.
	 *
	 * @return boolean
	 */
	public function enabled() {
		return $this->isValidStorage( $this->storage );
	}

	/**
	 * Get the entire storage or the values for a key for a request.
	 *
	 * @param  boolean     $next
	 * @param  string|null $key
	 * @param  mixed       $default
	 * @return mixed
	 */
	protected function getFromRequest( $next, $key = null, $default = null ) {
		$this->validateStorage();

		$request_key = $next ? static::NEXT_KEY : static::CURRENT_KEY;

		if ( $key === null ) {
			return $this->storage[ $request_key ];
		}

		return Arr::get( $this->storage[ $request_key ], $key, $default );
	}

	/**
	 * Add values for a key for a request.
	 *
	 * @param  boolean $next
	 * @param  string  $key
	 * @param  mixed   $new_items
	 * @return void
	 */
	protected function addToRequest( $next, $key, $new_items ) {
		$this->validateStorage();

		$request_key = $next ? static::NEXT_KEY : static::CURRENT_KEY;
		$new_items = Mixed::toArray( $new_items );
		$items = Mixed::toArray( $this->get( $key, [] ) );
		$this->storage[ $request_key ][ $key ] = array_merge( $items, $new_items );
	}

	/**
	 * Remove all values or values for a key from a request.
	 *
	 * @param  boolean     $next
	 * @param  string|null $key
	 * @return void
	 */
	protected function clearFromRequest( $next, $key = null ) {
		$this->validateStorage();

		$request_key = $next ? static::NEXT_KEY : static::CURRENT_KEY;
		$keys = $key === null ? array_keys( $storage ) : [$key];

		foreach ( $this->storage[ $request_key ] as $key => $value ) {
			unset( $this->storage[ $request_key ][ $key ] );
		}
	}

	/**
	 * Shift current storage and replace it with next storage.
	 *
	 * @return void
	 */
	public function shift() {
		$this->validateStorage();

		$this->storage[ static::CURRENT_KEY ] = $this->storage[ static::NEXT_KEY ];
		$this->storage[ static::NEXT_KEY ] = [];
	}

	/**
	 * Add values for a key for the next request.
	 *
	 * @param  string $key
	 * @param  mixed  $new_items
	 * @return void
	 */
	public function add( $key, $new_items ) {
		$this->addToRequest( true, $key, $new_items );
	}

	/**
	 * Add values for a key for the current request.
	 *
	 * @param string $key
	 * @param mixed  $new_items
	 */
	public function addNow( $key, $new_items ) {
		$this->addToRequest( false, $key, $new_items );
	}

	/**
	 * Get the entire storage or the values for a key for the current request.
	 *
	 * @param  string|null $key
	 * @param  mixed       $default
	 * @return mixed
	 */
	public function get( $key = null, $default = null ) {
		return $this->getFromRequest( false, $key, $default );
	}

	/**
	 * Get the entire storage or the values for a key for the next request.
	 *
	 * @param  string|null $key
	 * @param  mixed       $default
	 * @return mixed
	 */
	public function getNext( $key = null, $default = null ) {
		return $this->getFromRequest( true, $key, $default );
	}

	/**
	 * Clear the entire storage or the values for a key for the current request.
	 *
	 * @param  string|null $key
	 * @return void
	 */
	public function clear( $key = null ) {
		$this->clearFromRequest( false, $key );
	}

	/**
	 * Clear the entire storage or the values for a key for the next request.
	 *
	 * @param  string|null $key
	 * @return void
	 */
	public function clearNext( $key = null ) {
		$this->clearFromRequest( true, $key );
	}
}
