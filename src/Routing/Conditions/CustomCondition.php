<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing\Conditions;

use WPEmerge\Requests\RequestInterface;

/**
 * Check against a custom callable.
 */
class CustomCondition implements ConditionInterface {
	/**
	 * Callable to use
	 *
	 * @var callable
	 */
	protected $callable = null;

	/**
	 * Arguments to pass to the callable and controller
	 *
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param callable $callable
	 * @param mixed    ,...$arguments
	 */
	public function __construct( $callable ) {
		$this->callable = $callable;
		$this->arguments = array_values( array_slice( func_get_args(), 1 ) );
	}

	/**
	 * Get the assigned callable
	 *
	 * @codeCoverageIgnore
	 * @return callable
	 */
	public function getCallable() {
		return $this->callable;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		return call_user_func_array( $this->callable, $this->arguments );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return $this->arguments;
	}
}
