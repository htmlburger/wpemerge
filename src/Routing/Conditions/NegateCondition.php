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
 */
class NegateCondition implements ConditionInterface, HasUrlWhereInterface {
	/**
	 * Condition to negate.
	 *
	 * @var ConditionInterface
	 */
	protected $condition = null;

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

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function getUrlWhere() {
		if ( $this->condition instanceof HasUrlWhereInterface ) {
			$this->condition->getUrlWhere();
		}
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function setUrlWhere( $where ) {
		if ( $this->condition instanceof HasUrlWhereInterface ) {
			$this->condition->setUrlWhere( $where );
		}
	}
}
