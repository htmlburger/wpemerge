<?php

namespace CarbonFramework\Routing\Conditions;

use Closure;
use ReflectionClass;
use Exception;
use CarbonFramework\Framework;
use CarbonFramework\Routing\Conditions\Url as UrlCondition;
use CarbonFramework\Routing\Conditions\Custom as CustomCondition;
use CarbonFramework\Routing\Conditions\Multiple as MultipleCondition;

/**
 * Check against the current url
 */
class Factory {
	/**
	 * Create a new condition
	 *
	 * @param  string|Condition|array $options
	 * @return ConditionInterface
	 */
	public static function make( $options ) {
		if ( is_string( $options ) ) {
			return static::makeFromUrl( $options );
		}

		if ( is_a( $options, Closure::class ) ) {
			return static::makeFromClosure( $options );
		}

		if ( is_array( $options ) ) {
			if ( count( $options ) > 0 && is_array( $options[0] ) ) {
				return static::makeFromArrayOfConditions( $options );
			}

			return static::makeFromArray( $options );
		}

		throw new InvalidRouteConditionException( 'Invalid condition options supplied.' );
	}

	/**
	 * Create a new condition from a url
	 *
	 * @param  string             $url
	 * @return ConditionInterface
	 */
	protected static function makeFromUrl( $url ) {
		return new UrlCondition( $url );
	}

	/**
	 * Create a new condition from a closure
	 *
	 * @param  Closure            $closure
	 * @return ConditionInterface
	 */
	protected static function makeFromClosure( Closure $closure ) {
		return new CustomCondition( $closure );
	}

	/**
	 * Create a new condition from an array
	 *
	 * @param  array               $options
	 * @return ConditionInterface
	 */
	protected static function makeFromArray( $options ) {
		if ( count( $options ) === 0 ) {
			throw new Exception( 'No condition type specified.' );
		}

		$condition_type = $options[0];
		$arguments = array_slice( $options, 1 );

		$condition_class = Framework::resolve( 'framework.routing.conditions.' . $condition_type );
		if ( $condition_class === null ) {
			throw new Exception( 'Unknown condition type specified: ' . $condition_type );
		}

		$reflection = new ReflectionClass( $condition_class );
		$condition = $reflection->newInstanceArgs( $arguments );
		return $condition;
	}

	/**
	 * Create a new condition from an array of conditions
	 *
	 * @param  array               $options
	 * @return ConditionInterface
	 */
	protected static function makeFromArrayOfConditions( $options ) {
		return new MultipleCondition( $options );
	}
}
