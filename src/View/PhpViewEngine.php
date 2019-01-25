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
	 * Custom views directory to check first.
	 *
	 * @var string
	 */
	protected $directory = '';

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param string $directory
	 */
	public function __construct( $directory = '' ) {
		$this->setDirectory( $directory );
	}

	/**
	 * Get the custom views directory.
	 *
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getDirectory() {
		return $this->directory;
	}

	/**
	 * Set the custom views directory.
	 *
	 * @codeCoverageIgnore
	 * @param  string $directory
	 * @return void
	 */
	public function setDirectory( $directory ) {
		$this->directory = MixedType::removeTrailingSlash( $directory );
	}

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
		$view = $this->resolveRelativeFilepath( $view );
		$file = $this->resolveFromCustomFilepath( $view );

		if ( ! $file ) {
			$file = $this->resolveFromThemeFilepath( $view );
		}

		if ( ! $file ) {
			$file = $this->resolveFromAbsoluteFilepath( $view );
		}

		if ( $file ) {
			$file = realpath( $file );
		}

		return $file;
	}

	/**
	 * Resolve an absolute view to a relative one, if possible.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveRelativeFilepath( $view ) {
		$normalized_view = MixedType::normalizePath( $view );
		$stylesheet_path = MixedType::addTrailingSlash( STYLESHEETPATH );
		$template_path = MixedType::addTrailingSlash( TEMPLATEPATH );

		if ( substr( $normalized_view, 0, strlen( $stylesheet_path ) ) === $stylesheet_path ) {
			return substr( $normalized_view, strlen( $stylesheet_path ) );
		}

		if ( substr( $normalized_view, 0, strlen( $template_path ) ) === $template_path ) {
			return substr( $normalized_view, strlen( $template_path ) );
		}

		// Bail if we've failed to convert the view to a relative path.
		return $view;
	}

	/**
	 * Resolve a view if it exists in the custom views directory.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromCustomFilepath( $view ) {
		$directory = $this->getDirectory();

		if ( $directory === '' ) {
			return '';
		}

		// Normalize to ensure there are no doubled separators.
		$file = MixedType::normalizePath( $directory . DIRECTORY_SEPARATOR . $view );

		if ( ! file_exists( $file ) ) {
			// Try adding a .php extension.
			$file .= '.php';
		};

		return file_exists( $file ) ? $file : '';
	}

	/**
	 * Resolve a view if it exists in the current theme.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromThemeFilepath( $view ) {
		$file = locate_template( $view, false );

		if ( ! $file ) {
			// Try adding a .php extension.
			$file = locate_template( $view . '.php', false );
		}

		return $file;
	}

	/**
	 * Resolve a view if it exists on the filesystem.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromAbsoluteFilepath( $view ) {
		return file_exists( $view ) ? $view : '';
	}
}
