<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;

/**
 * Render a view file with php.
 */
class PhpView implements ViewInterface {
	use HasNameTrait, HasContextTrait;

	/**
	 * PHP view engine.
	 *
	 * @var PhpViewEngine
	 */
	protected $engine = null;

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
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param PhpViewEngine $engine
	 */
	public function __construct( PhpViewEngine $engine ) {
		$this->engine = $engine;
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
	 * @return static $this
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
	 * @return static             $this
	 */
	public function setLayout( $layout ) {
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

		$this->engine->pushLayoutContent( $this );

		if ( $this->getLayout() !== null ) {
			return $this->getLayout()->toString();
		}

		return $this->engine->getLayoutContent();
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
