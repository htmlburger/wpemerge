<?php

namespace WPEmerge\Exceptions\Whoops;

use Pimple\Container;

/**
 * Provide debug data for usage with \Whoops\Handler\PrettyPageHandler.
 *
 * @codeCoverageIgnore
 */
class DebugDataProvider {
	/**
	 * Container.
	 *
	 * @var Container
	 */
	protected $container = null;

	/**
	 * Constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( $container ) {
		$this->container = $container;
	}

	/**
	 * Convert a value to a scalar representation.
	 *
	 * @param  mixed $value
	 * @return mixed
	 */
	public function toScalar( $value ) {
		$type = gettype( $value );

		if ( ! is_scalar( $value ) ) {
			$value = '(' . $type . ')' . ( $type === 'object' ? ' ' . get_class( $value ) : '' );
		}

		return $value;
	}

	/**
	 * Return pritable data about the current route.
	 *
	 * @param \Whoops\Exception\Inspector $inspector
	 * @return array<string, mixed>
	 */
	public function route( $inspector ) {
		/** @var \WPEmerge\Routing\RouteInterface|null $route */
		$route = $this->container[ WPEMERGE_ROUTING_ROUTER_KEY ]->getCurrentRoute();

		if ( ! $route ) {
			return [];
		}

		$attributes = [];

		foreach ( $route->getAttributes() as $attribute => $value ) {
			// Only convert the first level of an array to scalar for simplicity.
			if ( is_array( $value ) ) {
				$value = '[' . implode( ', ', array_map( [$this, 'toScalar'], $value ) ) . ']';
			} else {
				$value = $this->toScalar( $value );
			}

			$attributes[ $attribute ] = $value;
		}

		return $attributes;
	}
}
