<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Helpers\HasAttributesTrait;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionInterface;

/**
 * Represent a route
 */
class Route implements RouteInterface, HasQueryFilterInterface {
	use HasAttributesTrait;
	use HasQueryFilterTrait;

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		$methods = $this->getAttribute( 'methods', [] );
		$condition = $this->getAttribute( 'condition' );

		if ( ! in_array( $request->getMethod(), $methods ) ) {
			return false;
		}

		if ( ! $condition instanceof ConditionInterface ) {
			throw new ConfigurationException( 'Route does not have a condition.' );
		}

		return $condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		$condition = $this->getAttribute( 'condition' );

		if ( ! $condition instanceof ConditionInterface ) {
			throw new ConfigurationException( 'Route does not have a condition.' );
		}

		return $condition->getArguments( $request );
	}
}
