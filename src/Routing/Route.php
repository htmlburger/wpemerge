<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Helpers\Handler;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\UrlCondition;

/**
 * Represent a route
 */
class Route implements RouteInterface, HasQueryFilterInterface {
	use HasMiddlewareTrait;
	use HasQueryFilterTrait;

	/**
	 * Allowed methods.
	 *
	 * @var string[]
	 */
	protected $methods = [];

	/**
	 * Route handler.
	 *
	 * @var Handler
	 */
	protected $handler = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param  string[]           $methods
	 * @param  ConditionInterface $condition
	 * @param  string|\Closure    $handler
	 */
	public function __construct( $methods, $condition, $handler ) {
		$this->methods = $methods;
		$this->setCondition( $condition );
		$this->handler = new Handler( $handler, '', '\\App\\Controllers\\' );
	}

	/**
	 * Get allowed methods.
	 *
	 * @codeCoverageIgnore
	 * @return string[]
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * Get handler.
	 *
	 * @codeCoverageIgnore
	 * @return Handler
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * Set custom partial regex matching for the specified parameter.
	 *
	 * @throws ConfigurationException
	 * @param  string $parameter
	 * @param  string $regex
	 * @return static $this
	 */
	public function where( $parameter, $regex ) {
		$condition = $this->getCondition();

		if ( ! $condition instanceof UrlCondition ) {
			throw new ConfigurationException( 'Only routes with URL conditions can specify parameter regex matching.' );
		}

		$condition->setUrlWhere( array_merge(
			$condition->getUrlWhere(),
			[$parameter => $regex]
		) );

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		if ( ! in_array( $request->getMethod(), $this->methods ) ) {
			return false;
		}

		return $this->condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return $this->getCondition()->getArguments( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, $view ) {
		$arguments = array_merge(
			[$request, $view],
			array_values( $this->condition->getArguments( $request ) )
		);

		return call_user_func_array( [$this->getHandler(), 'execute'], $arguments );
	}
}
