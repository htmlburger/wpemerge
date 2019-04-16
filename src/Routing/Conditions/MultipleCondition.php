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
 * Check against an array of conditions in an AND logical relationship.
 */
class MultipleCondition implements ConditionInterface, HasUrlWhereInterface {
	/**
	 * Array of conditions to check.
	 *
	 * @var array<ConditionInterface>
	 */
	protected $conditions = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param array<ConditionInterface> $conditions
	 */
	public function __construct( $conditions ) {
		$this->conditions = $conditions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		foreach ( $this->conditions as $condition ) {
			if ( ! $condition->isSatisfied( $request ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		$arguments = [];

		foreach ( $this->conditions as $condition ) {
			$arguments = array_merge( $arguments, $condition->getArguments( $request ) );
		}

		return $arguments;
	}

	/**
	 * Get all assigned conditions
	 *
	 * @codeCoverageIgnore
	 * @return array<\WPEmerge\Routing\Conditions\ConditionInterface>
	 */
	public function getConditions() {
		return $this->conditions;
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function getUrlWhere() {
		return [];
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function setUrlWhere( $where ) {
		foreach ( $this->conditions as $condition ) {
			if ( $condition instanceof HasUrlWhereInterface ) {
				$condition->setUrlWhere( $where );
			}
		}
	}
}
