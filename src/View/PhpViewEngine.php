<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

/**
 * Render view files with php.
 */
class PhpViewEngine implements ViewEngineInterface {
	/**
	 * View finder.
	 *
	 * @var PhpViewFilesystemFinder
	 */
	protected $finder = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param PhpViewFilesystemFinder $finder
	 */
	public function __construct( PhpViewFilesystemFinder $finder ) {
		$this->setFinder( $finder );
	}

	/**
	 * Get the custom views directories.
	 *
	 * @codeCoverageIgnore
	 * @return PhpViewFilesystemFinder
	 */
	public function getFinder() {
		return $this->finder;
	}

	/**
	 * Set the view finder.
	 *
	 * @codeCoverageIgnore
	 * @param  PhpViewFilesystemFinder
	 * @return void
	 */
	public function setFinder( PhpViewFilesystemFinder $finder ) {
		$this->finder = $finder;
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists( $view ) {
		return $this->getFinder()->exists( $view );
	}

	/**
	 * {@inheritDoc}
	 */
	public function canonical( $view ) {
		return $this->getFinder()->canonical( $view );
	}

	/**
	 * {@inheritDoc}
	 * @throws ViewNotFoundException
	 */
	public function make( $views ) {
		foreach ( $views as $view ) {
			if ( $this->exists( $view ) ) {
				$filepath = $this->finder->resolveFilepath( $view );
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

		return $this->makeView( $this->canonical( $layout_file ), $this->finder->resolveFilepath( $layout_file ) );
	}
}
