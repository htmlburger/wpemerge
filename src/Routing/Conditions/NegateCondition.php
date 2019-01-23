<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Facades\RouteCondition;
use WPEmerge\Requests\RequestInterface;

/**
 * Negate another condition's result.
 *
 * @codeCoverageIgnore
 */
class NegateCondition implements ConditionInterface {
	/**
	 * Condition to negate.
	 *
	 * @var ConditionInterface
	 */
	protected $condition = [];

	/**
	 * Constructor.
	 *
	 * @param mixed $condition
	 */
	public function __construct( $condition ) {
		if ( $condition instanceof ConditionInterface ) {
			$this->condition = $condition;
		} else {
			$this->condition = call_user_func( [RouteCondition::class, 'make'], func_get_args() );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		return ! $this->condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return $this->condition->getArguments( $request );
	}
}
