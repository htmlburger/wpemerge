<?php

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\Request;

/**
 * Check against the current post's id
 *
 * @codeCoverageIgnore
 */
class PostIdCondition implements ConditionInterface {
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
	public function isSatisfied( Request $request ) {
		return ( is_singular() && intval( $this->post_id ) === intval( get_the_ID() ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->post_id];
	}
}
