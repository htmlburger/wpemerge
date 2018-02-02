<?php

namespace WPEmerge\View;

use WPEmerge\Support\Arr;

trait HasContextTrait {
	/**
	 * Context.
	 *
	 * @var array
	 */
	protected $context = [];

	/**
	 * Get context values.
	 *
	 * @param  string|null $key
	 * @return mixed
	 */
	public function getContext( $key = null ) {
		if ( $key === null ) {
			return $this->context;
		}

		return Arr::get( $this->context, $key );
	}

	/**
	 * Add context values.
	 *
	 * @param  string|array $key
	 * @param  mixed        $value
	 * @return self         $this
	 */
	public function with( $key, $value = null ) {
		if ( is_array( $key ) ) {
			$this->context = array_merge( $this->getContext(), $key );
		} else {
			$this->context[ $key ] = $value;
		}
		return $this;
	}
}
