<?php

namespace WPEmerge\Flash;

use ArrayAccess;
use Exception;

/**
 * Provide a way to flash data into the session for the next request
 */
class Flash {
	/**
	 * Root storage array or object implementing ArrayAccess
	 *
	 * @var array|\ArrayAccess
	 */
	protected $root_storage = null;

	/**
	 * Child storage array or object implementing ArrayAccess
	 *
	 * @var array|\ArrayAccess
	 */
	protected $storage = null;

	/**
	 * Key to store flashed data in storage with
	 *
	 * @var string
	 */
	protected $storage_key = '__wpEmergeFlash';

	/**
	 * Constructor
	 *
	 * @param array|\ArrayAccess $storage
	 */
	public function __construct( &$storage ) {
		$this->setStorage( $storage );
	}

	/**
	 * Get whether a storage object is valid
	 *
	 * @param  mixed   $storage
	 * @return boolean
	 */
	protected function isValidStorage( $storage ) {
		return ( is_array( $storage ) || is_a( $storage, ArrayAccess::class ) );
	}

	/**
	 * Throw an exception if storage is not valid
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
	 * Get the storage for flash messages
	 *
	 * @return array|\ArrayAccess
	 */
	public function getStorage() {
		return $this->root_storage;
	}

	/**
	 * Set the storage for flash messages
	 *
	 * @param  array|\ArrayAccess $storage
	 * @return void
	 */
	public function setStorage( &$storage ) {
		if ( ! $this->isValidStorage( $storage ) ) {
			return;
		}

		if ( ! isset( $storage[ $this->storage_key ] ) ) {
			$storage[ $this->storage_key ] = [];
		}

		$this->root_storage = &$storage;
		$this->storage = &$this->root_storage[ $this->storage_key ];
	}

	/**
	 * Get whether the flash service is enabled
	 *
	 * @return boolean
	 */
	public function enabled() {
		return $this->isValidStorage( $this->storage );
	}

	/**
	 * Get the entire storage or the values for a key
	 *
	 * @param  string|null $key
	 * @return array|\ArrayAccess
	 */
	public function peek( $key = null ) {
		$this->validateStorage();

		if ( $key === null ) {
			return $this->storage;
		}

		if ( isset( $this->storage[ $key ] ) ) {
			return $this->storage[ $key ];
		}

		return [];
	}

	/**
	 * Get and clear the entire storage or the values for a key
	 *
	 * @param  string|null $key
	 * @return array|\ArrayAccess
	 */
	public function get( $key = null ) {
		$this->validateStorage();

		$items = $this->peek( $key );
		$this->clear( $key );
		return $items;
	}

	/**
	 * Add values for a key
	 *
	 * @param string $key
	 * @param mixed  $new_items
	 */
	public function add( $key, $new_items ) {
		$this->validateStorage();

		$new_items = is_array( $new_items ) ? $new_items : [$new_items];

		$items = (array) $this->peek( $key );
		$items = array_merge( $items, $new_items );

		$this->storage[ $key ] = $items;
	}

	/**
	 * Clear the entire storage or the values for a key
	 *
	 * @param  string|null $key
	 * @return void
	 */
	public function clear( $key = null ) {
		$this->validateStorage();

		if ( $key === null ) {
			foreach ( $this->storage as $key => $value ) {
				$this->storage[ $key ] = [];
			}
		} else {
			$this->storage[ $key ] = [];
		}
	}
}
