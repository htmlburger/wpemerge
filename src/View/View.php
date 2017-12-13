<?php

namespace WPEmerge\View;

use Closure;
use WPEmerge\Helpers\Handler;
use WPEmerge\Support\Arr;

/**
 * Render view files with php
 */
class View {
	/**
	 * Global variables
	 *
	 * @var array
	 */
	protected $globals = [];

	/**
	 * View composers
	 *
	 * @var array
	 */
	protected $composers = [];

	/**
	 * Get global variables
	 *
	 * @return array
	 */
	public function getGlobals() {
		return $this->globals;
	}

	/**
	 * Set a global variable
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function setGlobal( $key, $value ) {
		$this->globals[ $key ] = $value;
	}

	/**
	 * Set an array of global variables
	 *
	 * @param  array $globals
	 * @return void
	 */
	public function setGlobals( $globals ) {
		foreach ( $globals as $key => $value ) {
			$this->setGlobal( $key, $value );
		}
	}

	/**
	 * Get view composer
	 *
	 * @param  string       $view
	 * @return Handler|null
	 */
	public function getComposer( $view ) {
		return Arr::get( $this->composers, $view, null );
	}

	/**
	 * Set view composer
	 *
	 * @param  string         $view
	 * @param  string|Closure $composer
	 * @return void
	 */
	public function setComposer( $view, $composer ) {
		$handler = new Handler( $composer );
		$this->composers[ $view ] = $handler;
	}

	/**
	 * Get the composed context for a view.
	 * Passes all arguments to the composer.
	 *
	 * @param  string $view
	 * @param  mixed  $arguments,...
	 * @return array
	 */
	public function compose( $view ) {
		$composer = $this->getComposer( $view );
		if ( $composer === null ) {
			return [];
		}
		return call_user_func_array( [$composer, 'execute'], func_get_args() );
	}
}
