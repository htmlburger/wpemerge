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
 *
 * @codeCoverageIgnore
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
	 * Check if the private authentication requirement matches.
	 *
	 * @return boolean
	 */
	protected function matchesPrivateRequirement() {
		return $this->private && is_user_logged_in();
	}

	/**
	 * Check if the public authentication requirement matches.
	 *
	 * @return boolean
	 */
	protected function matchesPublicRequirement() {
		return $this->public && ! is_user_logged_in();
	}

	/**
	 * Check if the ajax action matches the requirement.
	 *
	 * @param  RequestInterface $request
	 * @return boolean
	 */
	protected function matchesActionRequirement( RequestInterface $request ) {
		return $this->action === $request->body( 'action', $request->query( 'action' ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		if ( ! wp_doing_ajax() ) {
			return false;
		}

		if ( ! $this->matchesActionRequirement( $request ) ) {
			return false;
		}

		return $this->matchesPrivateRequirement() || $this->matchesPublicRequirement();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return ['action' => $this->action];
	}
}
