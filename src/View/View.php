<?php

namespace WPEmerge\View;

use Closure;
use WPEmerge\Support\Arr;

/**
 * Render view files with php
 */
class View {
	/**
	 * Global context
	 *
	 * @var array
	 */
	protected $global_context = [];

	/**
	 * View composers
	 *
	 * @var array
	 */
	protected $composers = [];

	/**
	 * Get the global context
	 *
	 * @return array
	 */
	public function getGlobalContext() {
		return $this->global_context;
	}

	/**
	 * Add a value to the global context
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function addGlobal( $key, $value ) {
		$this->global_context[ $key ] = $value;
	}

	/**
	 * Add an array of values to the global context
	 *
	 * @param  array $globals
	 * @return void
	 */
	public function addGlobals( $globals ) {
		foreach ( $globals as $key => $value ) {
			$this->addGlobal( $key, $value );
		}
	}
}
