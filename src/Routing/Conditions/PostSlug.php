<?php

namespace CarbonFramework\Routing\Conditions;

class PostSlug implements ConditionInterface {
	/**
	 * Post slug to check against
	 * 
	 * @var string
	 */
	protected $post_slug = '';

	/**
	 * Constructor
	 * 
	 * @param string $post_slug
	 */
	public function __construct( $post_slug ) {
		$this->post_slug = $post_slug;
	}

	/**
	 * {@inheritDoc}
	 */
	public function satisfied() {
		$post = get_post();
		return ( $post && $this->post_slug === $post->post_name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments() {
		return [$this->post_slug];
	}
}
