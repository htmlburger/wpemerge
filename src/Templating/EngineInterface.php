<?php

namespace CarbonFramework\Templating;

/**
 * Interface that template engines must implement
 */
interface EngineInterface {
	/**
	 * Render a template to a string
	 *
	 * @param  string $file
	 * @param  array  $context
	 * @return string
	 */
	public function render( $file, $context );
}
