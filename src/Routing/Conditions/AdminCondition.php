<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\RequestInterface;

/**
 * Check against the current ajax action.
 */
class AdminCondition implements ConditionInterface {
	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	protected $menu = '';

	/**
	 * Parent menu slug.
	 *
	 * @var string
	 */
	protected $parent_menu = '';

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param string $menu
	 * @param string $parent_menu
	 */
	public function __construct( $menu, $parent_menu = '' ) {
		$this->menu = $menu;
		$this->parent_menu = $parent_menu;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return false;
		}

		return get_current_screen()->id === get_plugin_page_hookname( $this->menu, $this->parent_menu );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return ['hook' => get_plugin_page_hookname( $this->menu, $this->parent_menu )];
	}
}
