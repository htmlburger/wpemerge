<?php

namespace Obsidian\Templating;

use Obsidian\Framework;

/**
 * Include template files with different engines depending on their filename
 */
class Filename implements \Obsidian\Templating\EngineInterface {
	/**
	 * Container key of default engine to use
	 *
	 * @var string
	 */
	protected $default = 'framework.templating.engine.php';

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
	public function render( $file, $context ) {
		$engine_key = $this->default;

		foreach ( $this->bindings as $suffix => $engine ) {
			if ( substr( $file, -strlen( $suffix ) ) === $suffix ) {
				$engine_key = $engine;
				break;
			}
		}

		$engine_instance = Framework::resolve( $engine_key );

		return $engine_instance->render( $file, $context );
	}
}
