<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

use Closure;
use WPEmerge\Exceptions\Exception;
use WPEmerge\Facades\Framework;
use WPEmerge\Framework\ClassNotFoundException;

/**
 * Represent a generic handler - a Closure or a class method to be resolved from the service container
 */
class Handler {
	/**
	 * Parsed handler
	 *
	 * @var array|Closure|null
	 */
	protected $handler = null;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 * @param string|Closure $raw_handler
	 * @param string         $default_method
	 * @param string         $class_prefix
	 */
	public function __construct( $raw_handler, $default_method = '', $class_prefix = '' ) {
		$handler = $this->parse( $raw_handler, $default_method, $class_prefix );

		if ( $handler === null ) {
			throw new Exception( 'No or invalid handler provided.' );
		}

		$this->handler = $handler;
	}

	/**
	 * Parse a raw handler to a Closure or a [class, method] array
	 *
	 * @param  string|Closure     $raw_handler
	 * @param  string             $default_method
	 * @param  string             $class_prefix
	 * @return array|Closure|null
	 */
	protected function parse( $raw_handler, $default_method, $class_prefix ) {
		if ( $raw_handler instanceof Closure ) {
			return $raw_handler;
		}

		return $this->parseFromString( $raw_handler, $default_method, $class_prefix );
	}

	/**
	 * Parse a raw string handler to a [class, method] array
	 *
	 * @param  string     $raw_handler
	 * @param  string     $default_method
	 * @param  string     $class_prefix
	 * @return array|null
	 */
	protected function parseFromString( $raw_handler, $default_method, $class_prefix ) {
		list( $class, $method ) = array_pad( preg_split( '/@|::/', $raw_handler, 2 ), 2, '' );

		if ( empty( $method ) ) {
			$method = $default_method;
		}

		if ( ! empty( $class ) && ! empty( $method ) ) {
			return [
				'class' => $class,
				'method' => $method,
				'class_prefix' => $class_prefix,
			];
		}

		return null;
	}

	/**
	 * Get the parsed handler
	 *
	 * @return array|Closure|null
	 */
	public function get() {
		return $this->handler;
	}

	/**
	 * Execute the parsed handler with any provided arguments and return the result
	 *
	 * @param  mixed ...$arguments
	 * @return mixed
	 */
	public function execute() {
		$arguments = func_get_args();

		if ( $this->handler instanceof Closure ) {
			return call_user_func_array( $this->handler, $arguments );
		}

		$class_prefix = $this->handler['class_prefix'];
		$class = $this->handler['class'];
		$method = $this->handler['method'];

		try {
			$instance = Framework::instantiate( $class );
		} catch ( ClassNotFoundException $e ) {
			try {
				$instance = Framework::instantiate( $class_prefix . $class );
			} catch ( ClassNotFoundException $e ) {
				throw new ClassNotFoundException( 'Class not found - tried: ' . $class . ', ' . $class_prefix . $class );
			}
		}

		return call_user_func_array( [$instance, $method], $arguments );
	}
}
