<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\RequestInterface;

/**
 * Check against the current post's template.
 *
 * @codeCoverageIgnore
 */
class PostTemplateCondition implements ConditionInterface {
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
	 * @codeCoverageIgnore
	 * @param string               $post_template
	 * @param string|array<string> $post_types
	 */
	public function __construct( $post_template, $post_types = [] ) {
		$this->post_template = $post_template;
		$this->post_types = is_array( $post_types ) ? $post_types : [$post_types];
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		$template = get_post_meta( (int) get_the_ID(), '_wp_page_template', true );
		$template = $template ? $template : 'default';
		return ( is_singular( $this->post_types ) && $this->post_template === $template );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return ['post_template' => $this->post_template, 'post_types' => $this->post_types];
	}
}
