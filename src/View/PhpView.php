<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use WPEmerge\Facades\View;

/**
 * Render a view file with php.
 */
class PhpView implements ViewInterface {
	use HasNameTrait, HasContextTrait;

	/**
	 * Stack of views ready to be rendered.
	 *
	 * @var array<ViewInterface>
	 */
	protected static $layout_content_stack = [];

	/**
	 * Filepath to view.
	 *
	 * @var string
	 */
	protected $filepath = '';

	/**
	 * Layout to use.
	 *
	 * @var ViewInterface|null
	 */
	protected $layout = null;

	/**
	 * Get the top-most layout content from the stack.
	 *
	 * @codeCoverageIgnore
	 * @return string
	 */
	public static function getLayoutContent() {
		$view = array_pop( static::$layout_content_stack );

		return $view->render();
	}

	/**
	 * Get filepath.
	 *
	 * @return string
	 */
	public function getFilepath() {
		return $this->filepath;
	}

	/**
	 * Set filepath.
	 *
	 * @param  string $filepath
	 * @return self   $this
	 */
	public function setFilepath( $filepath ) {
		$this->filepath = $filepath;
		return $this;
	}

	/**
	 * Get layout.
	 *
	 * @return ViewInterface|null
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * Set layout.
	 *
	 * @param  ViewInterface|null $layout
	 * @return self               $this
	 */
	public function setLayout( ViewInterface $layout ) {
		$this->layout = $layout;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @throws ViewException
	 */
	public function toString() {
		if ( empty( $this->getName() ) ) {
			throw new ViewException( 'View must have a name.' );
		}

		if ( empty( $this->getFilepath() ) ) {
			throw new ViewException( 'View must have a filepath.' );
		}

		$clone = clone $this;
		static::$layout_content_stack[] = $clone->compose();

		if ( $this->getLayout() !== null ) {
			return $this->getLayout()->toString();
		}

		return static::getLayoutContent();
	}

	/**
	 * Compose the context.
	 *
	 * @return self $this
	 */
	protected function compose() {
		$context = $this->getContext();
		$this->with( ['global' => View::getGlobals()] );
		View::compose( $this );
		$this->with( $context );
		return $this;
	}

	/**
	 * Render the view to a string.
	 *
	 * @return string
	 */
	protected function render() {
		$__context = $this->getContext();
		ob_start();
		extract( $__context, EXTR_OVERWRITE );
		include $this->getFilepath();
		return ob_get_clean();
	}

	/**
	 * {@inheritDoc}
	 * @throws ViewException
	 */
	public function toResponse() {
		return (new Response())
			->withHeader( 'Content-Type', 'text/html' )
			->withBody( Psr7\stream_for( $this->toString() ) );
	}
}
