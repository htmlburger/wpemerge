<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Helpers\Url as UrlUtility;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Support\Arr;

/**
 * Check against the current ajax action.
 */
class AjaxCondition implements ConditionInterface {
	/**
	 * Ajax action to check against.
	 *
	 * @var string
	 */
	protected $action = '';

	/**
	 * Flag whether to check against ajax actions which run for authenticated users.
	 *
	 * @var boolean
	 */
	protected $private = true;

	/**
	 * Flag whether to check against ajax actions which run for unauthenticated users.
	 *
	 * @var boolean
	 */
	protected $public = false;

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param string  $action
	 * @param boolean $private
	 * @param boolean $public
	 */
	public function __construct( $action, $private = true, $public = false ) {
		$this->action = $action;
		$this->private = $private;
		$this->public = $public;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return false;
		}

		$authenticated = is_user_logged_in();
		$private_match = $this->private && $authenticated;
		$public_match = $this->public && !$authenticated;
		$action_match = $this->action === $request->post( 'action', $request->get( 'action' ) );

		return ($private_match || $public_match) && $action_match;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return ['action' => $this->action];
	}
}
