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
	 * @codeCoverageIgnore
	 * @param ConditionInterface $condition
	 */
	public function __construct( $condition ) {
		$this->condition = $condition;
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

		return [];
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
