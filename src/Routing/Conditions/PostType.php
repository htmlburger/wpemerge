<?php

namespace CarbonFramework\Routing\Conditions;

/**
 * Check against the current post's type
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
	public function satisfied() {
		return $this->post_type === get_post_type();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments() {
		return [$this->post_type];
	}
}
