<?php

namespace Obsidian\Routing\Conditions;

use Obsidian\Request;

/**
 * Check against the current post's type
 *
 * @codeCoverageIgnore
 */
class PostType implements ConditionInterface {
	/**
	 * Post type to check against
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Constructor
	 *
	 * @param string $post_type
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		return ( is_singular() && $this->post_type === get_post_type() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->post_type];
	}
}
