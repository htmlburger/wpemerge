<?php

namespace WPEmerge\View;

use Exception;
use View as ViewService;
use WPEmerge\Helpers\Path;

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
		$view_file = $this->resolveViewAndFile( $views );

		if ( $view_file === null ) {
			return '';
		}

		$__view = $view_file['file'];

		$__context = array_merge(
			['global' => ViewService::getGlobals()],
			ViewService::compose( $view_file['view'] ),
			$context
		);

		$renderer = function() use ( $__view, $__context ) {
			ob_start();
			extract( $__context );
			include( $__view );
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

	/**
	 * Resolve an array of views to the first existing view and it's filepath
	 *
	 * @param  string[]   $views
	 * @return array|null
	 */
	protected function resolveViewAndFile( $views ) {
		$view = '';
		$file = '';

		foreach ( $views as $current_view ) {
			if ( $this->exists( $current_view ) ) {
				$view = $current_view;
				$file = $this->resolveFile( $view );
				break;
			}
		}

		if ( ! $file ) {
			return null;
		}

		return [
			'view' => $view,
			'file' => $file,
		];
	}
}
