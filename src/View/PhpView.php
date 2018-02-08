<?php

namespace WPEmerge\View;

use Exception;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use WPEmerge\Facades\View;

/**
 * Render a view file with php.
 */
class PhpView implements ViewInterface {
	use HasNameTrait, HasContextTrait;

	/**
	 * Filepath to view.
	 *
	 * @var string
	 */
	protected $filepath = '';

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
	 * {@inheritDoc}
	 */
	public function toString() {
		if ( empty( $this->getName() ) ) {
			throw new Exception( 'View must have a name.' );
		}

		if ( empty( $this->getFilepath() ) ) {
			throw new Exception( 'View must have a filepath.' );
		}

		$context = $this->getContext();
		$this->with( ['global' => View::getGlobals()] );
		View::compose( $this );
		$this->with( $context );

		$renderer = function() {
			ob_start();
			$__context = $this->getContext();
			extract( $__context );
			include( $this->getFilepath() );
			return ob_get_clean();
		};

		return $renderer();
	}

	/**
	 * {@inheritDoc}
	 */
	public function toResponse() {
		return (new Response())
			->withHeader( 'Content-Type', 'text/html' )
			->withBody( Psr7\stream_for( $this->toString() ) );
	}
}
