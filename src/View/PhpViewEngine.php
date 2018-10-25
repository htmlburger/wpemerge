<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use WPEmerge\Helpers\MixedType;

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
		$root = realpath( MixedType::normalizePath( get_template_directory() ) ) . DIRECTORY_SEPARATOR;
		$match_root = '/^' . preg_quote( $root, '/' ) . '/';
		return preg_replace( $match_root, '', $this->resolveFilepath( $view ) );
	}

	/**
	 * {@inheritDoc}
	 * @throws ViewNotFoundException
	 */
	public function make( $views ) {
		foreach ( $views as $view ) {
			if ( $this->exists( $view ) ) {
				$filepath = $this->resolveFilepath( $view );
				return $this->makeView( $view, $filepath );
			}
		}

		throw new ViewNotFoundException( 'View not found for "' . implode( ', ', $views ) . '"' );
	}

	/**
	 * Create a view instance.
	 *
	 * @param  string $name
	 * @param  string $filepath
	 * @return ViewInterface
	 * @throws ViewNotFoundException
	 */
	protected function makeView( $name, $filepath ) {
		$view = (new PhpView())
			->setName( $name )
			->setFilepath( $filepath );

		$layout = $this->getViewLayout( $view );

		if ( $layout !== null ) {
			$view->setLayout( $layout );
		}

		return $view;
	}

	/**
	 * Create a view instance for the given view's layout header, if any.
	 *
	 * @param  PhpView $view
	 * @return ViewInterface
	 * @throws ViewNotFoundException
	 */
	protected function getViewLayout( PhpView $view ) {
		$layout_headers = array_filter( get_file_data(
			$view->getFilepath(),
			['App Layout']
		) );

		if ( empty( $layout_headers ) ) {
			return null;
		}

		$layout_file = trim( $layout_headers[0] );

		if ( ! $this->exists( $layout_file ) ) {
			throw new ViewNotFoundException( 'View layout not found for "' . $layout_file . '"' );
		}

		return $this->makeView( $this->canonical( $layout_file ), $this->resolveFilepath( $layout_file ) );
	}

	/**
	 * Resolve a view name to an absolute filepath.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFilepath( $view ) {
		$file = locate_template( $view, false );

		if ( ! $file ) {
			// locate_template failed to find the view - try adding a .php extension.
			$file = locate_template( $view . '.php', false );
		}

		if ( ! $file ) {
			// locate_template failed to find the view - test if a valid absolute path was passed.
			$file = $this->resolveFilepathFromFilesystem( $view );
		}

		if ( $file ) {
			$file = realpath( $file );
		}

		return $file;
	}

	/**
	 * Resolve a view if it exists on the filesystem.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFilepathFromFilesystem( $view ) {
		return file_exists( $view ) ? $view : '';
	}
}
