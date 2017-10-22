<?php

namespace CarbonFramework\Routing\Conditions;

class PostTemplate implements ConditionInterface {
	/**
	 * Post template to check against
	 * 
	 * @var string
	 */
	protected $post_template = '';

	/**
	 * Post types to check against
	 * 
	 * @var string[]
	 */
	protected $post_types = [];

	/**
	 * Constructor
	 * 
	 * @param string          $post_template
	 * @param string|string[] $post_types
	 */
	public function __construct( $post_template, $post_types = [] ) {
		$this->post_template = $post_template;
		$this->post_types = is_array( $post_types ) ? $post_types : [$post_types];
	}

	/**
	 * {@inheritDoc}
	 */
	public function satisfied() {
		$template = get_post_meta( get_the_ID(), '_wp_page_template', true );
		$template = $template ? $template : 'default';
		return ( is_singular( $this->post_types ) && $this->post_template === $template );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments() {
		return [$this->post_template, $this->post_types];
	}
}
