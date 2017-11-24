<?php

namespace Obsidian\Routing\Conditions;

use Obsidian\Framework;
use Obsidian\Routing\Conditions\Custom as CustomCondition;
use Obsidian\Routing\Conditions\Multiple as MultipleCondition;
use Obsidian\Routing\Conditions\Url as UrlCondition;
use Closure;
use Exception;
use ReflectionClass;

/**
 * Check against the current url
 */
class Factory {
	/**
	 * Create a new condition
	 *
	 * @param  string|array|Closure $options
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
	 * Check if the passed argument is a registered condition type
	 *
	 * @param  mixed   $condition_type
	 * @return boolean
	 */
	protected static function conditionTypeRegistered( $condition_type ) {
		if ( ! is_string( $condition_type ) ) {
			return false;
		}

		$condition_class = Framework::resolve( 'framework.routing.conditions.' . $condition_type );
		return ( $condition_class !== null );
	}

	/**
	 * Resolve the condition type and it's arguments from an options array
	 *
	 * @param  array $options
	 * @return array
	 */
	protected static function getConditionTypeAndArguments( $options ) {
		$type = $options[0];
		$arguments = array_slice( $options, 1 );

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
	 * Create a new condition from a url
	 *
	 * @param  string             $url
	 * @return ConditionInterface
	 */
	protected static function makeFromUrl( $url ) {
		return new UrlCondition( $url );
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

		if ( is_array( $options[0] ) ) {
			return static::makeFromArrayOfConditions( $options );
		}

		$condition_options = static::getConditionTypeAndArguments( $options );
		$condition_class = Framework::resolve( 'framework.routing.conditions.' . $condition_options['type'] );

		$reflection = new ReflectionClass( $condition_class );
		$condition = $reflection->newInstanceArgs( $condition_options['arguments'] );
		return $condition;
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
}
