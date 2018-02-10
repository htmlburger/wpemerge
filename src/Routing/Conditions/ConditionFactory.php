<?php

namespace WPEmerge\Routing\Conditions;

use Closure;
use Exception;
use WPEmerge\Facades\Framework;
use WPEmerge\Routing\Conditions\CustomCondition;
use WPEmerge\Routing\Conditions\MultipleCondition;
use WPEmerge\Routing\Conditions\UrlCondition;
use ReflectionClass;

/**
 * Check against the current url
 */
class ConditionFactory {
	/**
	 * Create a new condition.
	 *
	 * @throws InvalidRouteConditionException
	 * @param  string|array|Closure           $options
	 * @return ConditionInterface
	 */
	public static function make( $options ) {
		if ( is_string( $options ) ) {
			return static::makeFromUrl( $options );
		}

		if ( is_array( $options ) ) {
			return static::makeFromArray( $options );
		}

		if ( is_a( $options, Closure::class ) ) {
			return static::makeFromClosure( $options );
		}

		throw new InvalidRouteConditionException( 'Invalid condition options supplied.' );
	}

	/**
	 * Check if the passed argument is a registered condition type.
	 *
	 * @param  mixed   $condition_type
	 * @return boolean
	 */
	protected static function conditionTypeRegistered( $condition_type ) {
		if ( ! is_string( $condition_type ) ) {
			return false;
		}

		$condition_class = Framework::resolve( WPEMERGE_ROUTING_CONDITIONS_KEY . $condition_type );
		return ( $condition_class !== null );
	}

	/**
	 * Resolve the condition type and its arguments from an options array.
	 *
	 * @throws Exception
	 * @param  array $options
	 * @return array
	 */
	protected static function getConditionTypeAndArguments( $options ) {
		$type = $options[0];
		$arguments = array_values( array_slice( $options, 1 ) );

		if ( is_string( $type ) && substr( $type, 0, 1 ) === '!' ) {
			$arguments = array_merge( [ substr( $type, 1 ) ], $arguments );
			$type = 'negate';
		}

		if ( ! static::conditionTypeRegistered( $type ) ) {
			if ( is_callable( $type ) ) {
				$type = 'custom';
				$arguments = $options;
			} else {
				throw new Exception( 'Unknown condition type specified: ' . $type );
			}
		}

		return array(
			'type' => $type,
			'arguments' => $arguments,
		);
	}

	/**
	 * Create a new condition from a url.
	 *
	 * @param  string             $url
	 * @return ConditionInterface
	 */
	protected static function makeFromUrl( $url ) {
		return new UrlCondition( $url );
	}

	/**
	 * Create a new condition from an array.
	 *
	 * @throws Exception
	 * @param  array               $options
	 * @return ConditionInterface
	 */
	protected static function makeFromArray( $options ) {
		if ( count( $options ) === 0 ) {
			throw new Exception( 'No condition type specified.' );
		}

		if ( is_array( $options[0] ) ) {
			return static::makeFromArrayOfConditions( $options );
		}

		$condition_options = static::getConditionTypeAndArguments( $options );
		$condition_class = Framework::resolve( WPEMERGE_ROUTING_CONDITIONS_KEY . $condition_options['type'] );

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
	protected static function makeFromArrayOfConditions( $options ) {
		return new MultipleCondition( $options );
	}

	/**
	 * Create a new condition from a closure.
	 *
	 * @param  Closure            $closure
	 * @return ConditionInterface
	 */
	protected static function makeFromClosure( Closure $closure ) {
		return new CustomCondition( $closure );
	}
}
