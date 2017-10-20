<?php

namespace CarbonFramework\Routing\Conditions;

class PostId implements ConditionInterface {
	protected $post_id = 0;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	public function satisfied() {
		return intval( $this->post_id ) === intval( get_the_ID() );
	}
}
