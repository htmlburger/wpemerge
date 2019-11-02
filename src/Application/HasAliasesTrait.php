<?php

namespace WPEmerge\Application;

use Closure;
use BadMethodCallException;

/**
 * Add methods to classes at runtime.
 * Loosely based on spatie/macroable.
 *
 * @codeCoverageIgnore
 */
trait HasAliasesTrait {
	/**
	 * Aliases.
	 *
	 * @var array<string, string|Closure>
	 */
	protected $aliases = [];

	/**
	 * Register an alias.
	 *
	 * @param  string         $alias
	 * @param  string|Closure $target
	 * @return void
	 */
	public function alias( $alias, $target )
	{
		$this->aliases[ $alias ] = $target;
	}

	/**
	 * Get whether an alias is registered.
	 *
	 * @param  string  $alias
	 * @return boolean
	 */
	public function hasAlias( $alias ) {
		return isset( $this->aliases[ $alias ] );
	}

	public function __call( $method, $parameters ) {
		if ( ! $this->hasAlias( $method ) ) {
			throw new BadMethodCallException( "Method {$method} does not exist." );
		}

		$target = $this->aliases[ $method ];

		if ( is_string( $target ) ) {
			return $this->resolve( $target );
		}

		return call_user_func_array( $target->bindTo( $this, static::class ), $parameters );
	}

	/**
	 * Resolve a dependency from the IoC container.
	 *
	 * @param  string     $key
	 * @return mixed|null
	 */
	abstract public function resolve( $key );
}
