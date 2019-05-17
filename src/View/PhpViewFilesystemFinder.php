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
class PhpViewFilesystemFinder implements ViewFinderInterface {
	/**
	 * Custom views directories to check first.
	 *
	 * @var array<string>
	 */
	protected $directories = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param array<string> $directories
	 */
	public function __construct( $directories = [] ) {
		$this->setDirectories( $directories );
	}

	/**
	 * Get the custom views directories.
	 *
	 * @codeCoverageIgnore
	 * @return array<string>
	 */
	public function getDirectories() {
		return $this->directories;
	}

	/**
	 * Set the custom views directories.
	 *
	 * @codeCoverageIgnore
	 * @param  array<string> $directories
	 * @return void
	 */
	public function setDirectories( $directories ) {
		$this->directories = array_filter( array_map( [MixedType::class, 'removeTrailingSlash'], $directories ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists( $view ) {
		return ! empty( $this->resolveFilepath( $view ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function canonical( $view ) {
		return $this->resolveFilepath( $view );
	}

	/**
	 * Resolve a view to an absolute filepath.
	 *
	 * @param  string $view
	 * @return string
	 */
	public function resolveFilepath( $view ) {
		$file = $this->resolveFromAbsoluteFilepath( $view );

		if ( ! $file ) {
			$file = $this->resolveFromCustomDirectories( $view );
		}

		if ( ! $file ) {
			$file = $this->resolveFromWordPress( $view );
		}

		return $file;
	}

	/**
	 * Resolve a view if it is a valid absolute filepath.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromAbsoluteFilepath( $view ) {
		$path = realpath( MixedType::normalizePath( $view ) );

		if ( ! empty( $path ) && ! is_file( $path ) ) {
			$path = '';
		}

		return $path ? $path : '';
	}

	/**
	 * Resolve a view if it exists in the custom views directories.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromCustomDirectories( $view ) {
		$directories = $this->getDirectories();

		foreach ( $directories as $directory ) {
			$file = MixedType::normalizePath( $directory . DIRECTORY_SEPARATOR . $view );

			if ( ! file_exists( $file ) ) {
				// Try adding a .php extension.
				$file .= '.php';
			}

			if ( file_exists( $file ) ) {
				return $file;
			}
		}

		return '';
	}

	/**
	 * Resolve a view if WordPress can locate it.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromWordPress( $view ) {
		$file = locate_template( $view, false );

		if ( ! $file ) {
			// Try adding a .php extension.
			$file = locate_template( $view . '.php', false );
		}

		return $this->resolveFromAbsoluteFilepath( $file );
	}
}
