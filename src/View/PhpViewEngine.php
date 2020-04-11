<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

/**
 * Render view files with php.
 */
class PhpViewEngine implements ViewEngineInterface {
	/**
	 * View compose action.
	 *
	 * @var callable
	 */
	protected $compose = null;

	/**
	 * View finder.
	 *
	 * @var PhpViewFilesystemFinder
	 */
	protected $finder = null;

	/**
	 * Stack of views ready to be rendered.
	 *
	 * @var PhpView[]
	 */
	protected $layout_content_stack = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param callable                $compose
	 * @param PhpViewFilesystemFinder $finder
	 */
	public function __construct( callable $compose, PhpViewFilesystemFinder $finder ) {
		$this->compose = $compose;
		$this->finder = $finder;
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists( $view ) {
		return $this->finder->exists( $view );
	}

	/**
	 * {@inheritDoc}
	 */
	public function canonical( $view ) {
		return $this->finder->canonical( $view );
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
		$view = (new PhpView( $this ))
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
	 * @return ViewInterface|null
	 * @throws ViewNotFoundException
	 */
	protected function getViewLayout( PhpView $view ) {
		$layout_headers = array_filter( get_file_data(
			$view->getFilepath(),
			['Layout']
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

	/**
	 * Render a view.
	 *
	 * @param  PhpView $__view
	 * @return string
	 */
	protected function renderView( PhpView $__view ) {
		$__context = $__view->getContext();
		ob_start();
		extract( $__context, EXTR_OVERWRITE );
		/** @noinspection PhpIncludeInspection */
		include $__view->getFilepath();
		return ob_get_clean();
	}

	/**
	 * Push layout content to the top of the stack.
	 *
	 * @codeCoverageIgnore
	 * @param PhpView $view
	 * @return void
	 */
	public function pushLayoutContent( PhpView $view ) {
		$this->layout_content_stack[] = $view;
	}

	/**
	 * Pop the top-most layout content from the stack.
	 *
	 * @codeCoverageIgnore
	 * @return PhpView|null
	 */
	public function popLayoutContent() {
		return array_pop( $this->layout_content_stack );
	}

	/**
	 * Pop the top-most layout content from the stack, render and return it.
	 *
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getLayoutContent() {
		$view = $this->popLayoutContent();

		if ( ! $view ) {
			return '';
		}

		$clone = clone $view;

		call_user_func( $this->compose, $clone );

		return $this->renderView( $clone );
	}
}
