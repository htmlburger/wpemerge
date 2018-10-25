<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\Request;

/**
 * Check against the current post's template
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
	public function isSatisfied( Request $request ) {
		$template = get_post_meta( (int) get_the_ID(), '_wp_page_template', true );
		$template = $template ? $template : 'default';
		return ( is_singular( $this->post_types ) && $this->post_template === $template );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->post_template, $this->post_types];
	}
}
