<?php

namespace WPEmerge\View;

use Exception;

/**
 * Render view files with php.
 */
class PhpViewEngine implements ViewEngineInterface {
	/**
	 * {@inheritDoc}
	 */
	public function exists( $view ) {
		$file = $this->resolveFilepath( $view );
		return strlen( $file ) > 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function canonical( $view ) {
		$file = $this->resolveFilepath( $view );
		return $file;
	}

	/**
	 * {@inheritDoc}
	 */
	public function make( $views ) {
		foreach ( $views as $view ) {
			if ( $this->exists( $view ) ) {
				$filepath = $this->resolveFilepath( $view );
				return $this->makeView( $view, $filepath );
			}
		}

		throw new Exception( 'View not found for "' . implode( ', ', $views ) . '"' );
	}

	/**
	 * Create a view instance.
	 *
	 * @param  string        $name
	 * @param  string        $filepath
	 * @return ViewInterface
	 */
	protected function makeView( $name, $filepath ) {
		return (new PhpView())
			->setName( $name )
			->setFilepath( $filepath );
	}

	/**
	 * Resolve a view or a view array to an absolute filepath
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFilepath( $view ) {
		$file = locate_template( $view, false );

		if ( ! $file ) {
			// locate_template failed to find the view - try adding a .php extension
			$file = locate_template( $view . '.php', false );
		}

		if ( ! $file ) {
			// locate_template failed to find the view - test if a valid absolute path was passed
			$file = $this->resolveFilepathFromFilesystem( $view );
		}

		if ( $file ) {
			$file = realpath( $file );
		}

		return $file;
	}

	/**
	 * Resolve the view if it exists on the filesystem
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFilepathFromFilesystem( $view ) {
		return file_exists( $view ) ? $view : '';
	}
}
