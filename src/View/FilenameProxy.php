<?php

namespace WPEmerge\View;

use WPEmerge;

/**
 * Render view files with different engines depending on their filename
 */
class FilenameProxy implements \WPEmerge\View\EngineInterface {
	/**
	 * Container key of default engine to use
	 *
	 * @var string
	 */
	protected $default = WPEMERGE_VIEW_ENGINE_PHP_KEY;

	/**
	 * Array of filename_suffix=>engine_container_key bindings
	 *
	 * @var array
	 */
	protected $bindings = [];

	/**
	 * Constructor
	 *
	 * @param array  $bindings
	 * @param string $default
	 */
	public function __construct( $bindings, $default = '' ) {
		$this->bindings = $bindings;

		if ( ! empty( $default ) ) {
			$this->default = $default;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists( $view ) {
		$engine_key = $this->getBindingForFile( $view );
		$engine_instance = WPEmerge::resolve( $engine_key );
		return $engine_instance->exists( $view );
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $views, $context ) {
		foreach ( $views as $view ) {
			if ( $this->exists( $view ) ) {
				$engine_key = $this->getBindingForFile( $view );
				$engine_instance = WPEmerge::resolve( $engine_key );
				return $engine_instance->render( [$view], $context );
			}
		}

		return '';
	}

	/**
	 * Get the default binding
	 *
	 * @return string $binding
	 */
	public function getDefaultBinding() {
		return $this->default;
	}

	/**
	 * Get all bindings
	 *
	 * @return array  $bindings
	 */
	public function getBindings() {
		return $this->bindings;
	}

	/**
	 * Get the engine key binding for a specific file
	 *
	 * @param  string $file
	 * @return string
	 */
	public function getBindingForFile( $file ) {
		$engine_key = $this->default;

		foreach ( $this->bindings as $suffix => $engine ) {
			if ( substr( $file, -strlen( $suffix ) ) === $suffix ) {
				$engine_key = $engine;
				break;
			}
		}

		return $engine_key;
	}
}
