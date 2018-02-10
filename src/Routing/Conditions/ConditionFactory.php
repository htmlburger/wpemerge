<?php

namespace WPEmerge\Routing\Conditions;

use Closure;
use Exception;
use ReflectionClass;
use WPEmerge\Routing\Conditions\CustomCondition;
use WPEmerge\Routing\Conditions\MultipleCondition;
use WPEmerge\Routing\Conditions\UrlCondition;

/**
 * Check against the current url
 */
class ConditionFactory {
	const NEGATE_CONDITION_PREFIX = '!';

	/**
	 * Registered condition types.
	 *
	 * @var array<string, string>
	 */
	protected $condition_types = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param array<string, string> $condition_types
	 */
	public function __construct( $condition_types ) {
		$this->condition_types = $condition_types;
	}

	/**
	 * Create a new condition.
	 *
	 * @throws InvalidRouteConditionException
	 * @param  string|array|Closure           $options
	 * @return ConditionInterface
	 */
	public function make( $options ) {
		if ( is_string( $options ) ) {
			return $this->makeFromUrl( $options );
		}

		if ( is_array( $options ) ) {
			return $this->makeFromArray( $options );
		}

		if ( $options instanceof Closure ) {
			return $this->makeFromClosure( $options );
		}

		throw new InvalidRouteConditionException( 'Invalid condition options supplied.' );
	}

	/**
	 * Get condition class for condition type.
	 *
	 * @param  string      $condition_type
	 * @return string|null
	 */
	protected function getConditionTypeClass( $condition_type ) {
		if ( ! isset( $this->condition_types[ $condition_type ] ) ) {
			return null;
		}

		return $this->condition_types[ $condition_type ];
	}

	/**
	 * Check if the passed argument is a registered condition type.
	 *
	 * @param  mixed   $condition_type
	 * @return boolean
	 */
	protected function conditionTypeRegistered( $condition_type ) {
		if ( ! is_string( $condition_type ) ) {
			return false;
		}

		return $this->getConditionTypeClass( $condition_type ) !== null;
	}

	/**
	 * Check if a condition is negated.
	 *
	 * @param  mixed   $condition
	 * @return boolean
	 */
	protected function isNegatedCondition( $condition ) {
		return (
			is_string( $condition )
			&&
			substr( $condition, 0, strlen( static::NEGATE_CONDITION_PREFIX ) ) === static::NEGATE_CONDITION_PREFIX
		);
	}

	/**
	 * Parse a negated condition and its arguments.
	 *
	 * @param  string $type
	 * @param  array  $arguments
	 * @return array
	 */
	protected function parseNegatedCondition( $type, $arguments ) {
		$negated_type = substr( $type, strlen( static::NEGATE_CONDITION_PREFIX ) );
		$arguments = array_merge( [ $negated_type ], $arguments );
		$type = 'negate';

		return ['type' => $type, 'arguments' => $arguments];
	}

	/**
	 * Parse the condition type and its arguments from an options array.
	 *
	 * @throws Exception
	 * @param  array $options
	 * @return array
	 */
	protected function parseConditionOptions( $options ) {
		$type = $options[0];
		$arguments = array_values( array_slice( $options, 1 ) );

		if ( $this->isNegatedCondition( $type ) ) {
			return $this->parseNegatedCondition( $type, $arguments );
		}

		if ( ! $this->conditionTypeRegistered( $type ) ) {
			if ( is_callable( $type ) ) {
				return ['type' => 'custom', 'arguments' => $options];
			}

			throw new Exception( 'Unknown condition type specified: ' . $type );
		}

		return ['type' => $type, 'arguments' => $arguments ];
	}

	/**
	 * Create a new condition from a url.
	 *
	 * @param  string             $url
	 * @return ConditionInterface
	 */
	protected function makeFromUrl( $url ) {
		return new UrlCondition( $url );
	}

	/**
	 * Create a new condition from an array.
	 *
	 * @throws Exception
	 * @param  array               $options
	 * @return ConditionInterface
	 */
	protected function makeFromArray( $options ) {
		if ( count( $options ) === 0 ) {
			throw new Exception( 'No condition type specified.' );
		}

		if ( is_array( $options[0] ) ) {
			return $this->makeFromArrayOfConditions( $options );
		}

		$condition_options = $this->parseConditionOptions( $options );
		$condition_class = $this->getConditionTypeClass( $condition_options['type'] );

		$reflection = new ReflectionClass( $condition_class );
		$condition = $reflection->newInstanceArgs( $condition_options['arguments'] );
		return $condition;
	}

	/**
	 * Create a new condition from an array of conditions.
	 *
	 * @param  array               $options
	 * @return ConditionInterface
	 */
	protected function makeFromArrayOfConditions( $options ) {
		return new MultipleCondition( $options );
	}

	/**
	 * Create a new condition from a closure.
	 *
	 * @param  Closure            $closure
	 * @return ConditionInterface
	 */
	protected function makeFromClosure( Closure $closure ) {
		return new CustomCondition( $closure );
	}
}
