<?php

namespace Obsidian\Routing\Conditions;

use Obsidian\Request;

/**
 * Check against the current post's id
 *
 * @codeCoverageIgnore
 */
class PostId implements ConditionInterface {
	/**
	 * Post id to check against
	 *
	 * @var string
	 */
	protected $post_id = '';

	/**
	 * Constructor
	 *
	 * @param string $post_id
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function satisfied( Request $request ) {
		return ( is_singular() && intval( $this->post_id ) === intval( get_the_ID() ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->post_id];
	}
}
