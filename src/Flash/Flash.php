<?php

namespace CarbonFramework\Flash;

use Exception;

class Flash {
	protected $storage = null;

	protected $storage_key = '__carbonFrameworkFlash';

	public function __construct( &$storage ) {
		if ( $this->isValidStorage( $storage ) ) {
			if ( ! isset( $storage[ $this->storage_key ] ) ) {
				$storage[ $this->storage_key ] = [];
			}
			$this->storage = &$storage[ $this->storage_key ];
		}
	}

	protected function isValidStorage( $storage ) {
		return $storage !== null;
	}

	protected function validateStorage() {
		if ( ! $this->isValidStorage( $this->storage ) ) {
			throw new Exception( 'Attempted to use Flash without an active session. Did you forget to call session_start()?' );
		}
	}

	public function enabled() {
		return $this->isValidStorage( $this->storage );
	}

	public function get( $key = null ) {
		$this->validateStorage();

		$items = $this->peek( $key );
		$this->clear( $key );
		return $items;
	}

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

	public function add( $key, $new_items ) {
		$this->validateStorage();
		
		$new_items = is_array( $new_items ) ? $new_items : [$new_items];

		$items = $this->peek( $key );
		$items = array_merge( $items, $new_items );
		
		if ( $key === null ) {
			$this->storage = $items;
		} else {
			$this->storage[ $key ] = $items;
		}
	}

	public function clear( $key = null ) {
		$this->validateStorage();
		
		if ( $key === null ) {
			$this->storage = [];
		} else {
			$this->storage[ $key ] = [];
		}
	}
}