<?php

namespace WPEmerge\View;

interface HasContextInterface {
	/**
	 * Get context values.
	 *
	 * @param  string|null $key
	 * @return mixed
	 */
	public function getContext( $key = null );

	/**
	 * Add context values.
	 *
	 * @param  string|array $key
	 * @param  mixed        $value
	 * @return self         $this
	 */
	public function with( $key, $value = null );
}
