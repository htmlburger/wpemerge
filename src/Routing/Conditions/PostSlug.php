<?php

namespace CarbonFramework\Routing\Conditions;

use CarbonFramework\Request;

/**
 * Check against the current post's slug
 */
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
	public function satisfied( Request $request ) {
		$post = get_post();
		return ( $post && $this->post_slug === $post->post_name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->post_slug];
	}
}
