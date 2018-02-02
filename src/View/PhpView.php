<?php

namespace WPEmerge\View;

use Exception;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use View as ViewService;

/**
 * Render a view file with php.
 */
class PhpView implements ViewInterface {
	use HasContextTrait;

	/**
	 * View name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Filepath to view.
	 *
	 * @var string
	 */
	protected $filepath = '';

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName( $name ) {
		$this->name = $name;
		return $this;
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
	 * {@inheritDoc}
	 */
	public function toString() {
		if ( empty( $this->getName() ) ) {
			throw new Exception( 'View must have a name.' );
		}

		if ( empty( $this->getFilepath() ) ) {
			throw new Exception( 'View must have a filepath.' );
		}

		$global_context = ['global' => ViewService::getGlobals()];
		$local_context = $this->getContext();

		$this->with( $global_context );
		ViewService::compose( $this );
		$this->with( $local_context );

		$renderer = function() {
			ob_start();
			extract( $this->getContext() );
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
