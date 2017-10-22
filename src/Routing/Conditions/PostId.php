<?php

namespace CarbonFramework\Routing\Conditions;

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
	public function satisfied() {
		return $this->post_id === get_the_ID();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments() {
		return [$this->post_id];
	}
}
