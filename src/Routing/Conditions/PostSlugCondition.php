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
 * Check against the current post's slug
 *
 * @codeCoverageIgnore
 */
class PostSlugCondition implements ConditionInterface {
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
	public function isSatisfied( Request $request ) {
		$post = get_post();
		return ( is_singular() && $post && $this->post_slug === $post->post_name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( Request $request ) {
		return [$this->post_slug];
	}
}
