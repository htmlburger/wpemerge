<?php

namespace CarbonFramework\Templating;

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