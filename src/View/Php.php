<?php

namespace WPEmerge\View;

use View as ViewService;

/**
 * Render view files with php
 */
class Php implements EngineInterface {
	/**
	 * {@inheritDoc}
	 */
	public function exists( $view ) {
		$file = $this->resolveFile( $view );
		return strlen( $file ) > 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $views, $context ) {
		foreach ( $views as $view ) {
			if ( $this->exists( $view ) ) {
				$file = $this->resolveFile( $view );
				return $this->renderView( $view, $file, $context );
			}
		}

		return '';
	}

	/**
	 * Render a single view to string
	 *
	 * @param  string $view
	 * @param  string $file
	 * @param  array  $context
	 * @return string
	 */
	protected function renderView( $view, $file, $context ) {
		$__file = $file;

		$__context = array_merge(
			['global' => ViewService::getGlobals()],
			ViewService::compose( $view ),
			$context
		);

		$renderer = function() use ( $__file, $__context ) {
			ob_start();
			extract( $__context );
			include( $__file );
			return ob_get_clean();
		};

		return $renderer();
	}

	/**
	 * Resolve a view or a view array to an absolute filepath
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFile( $view ) {
		$file = locate_template( $view, false );

		if ( ! $file ) {
			// locate_template failed to find the view - test if a valid absolute path was passed
			$file = $this->resolveFileFromFilesystem( $view );
		}

		return $file;
	}

	/**
	 * Resolve the view if it exists on the filesystem
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFileFromFilesystem( $view ) {
		return file_exists( $view ) ? $view : '';
	}
}
