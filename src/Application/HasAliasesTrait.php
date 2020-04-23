<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use Closure;
use BadMethodCallException;
use WPEmerge\Support\Arr;

/**
 * Add methods to classes at runtime.
 * Loosely based on spatie/macroable.
 *
 * @codeCoverageIgnore
 */
trait HasAliasesTrait {
	/**
	 * Registered aliases.
	 *
	 * @var array<string, array>
	 */
	protected $aliases = [];

	/**
	 * Get whether an alias is registered.
	 *
	 * @param  string  $alias
	 * @return boolean
	 */
	public function hasAlias( $alias ) {
		return isset( $this->aliases[ $alias ] );
	}

	/**
	 * Get a registered alias.
	 *
	 * @param  string     $alias
	 * @return array|null
	 */
	public function getAlias( $alias ) {
		if ( ! $this->hasAlias( $alias ) ) {
			return null;
		}

		return $this->aliases[ $alias ];
	}

	/**
	 * Register an alias.
	 * Useful when passed the return value of getAlias() to restore
	 * a previously registered alias, for example.
	 *
	 * @param  array<string, mixed> $alias
	 * @return void
	 */
	public function setAlias( $alias ) {
		$name = Arr::get( $alias, 'name' );

		$this->aliases[ $name ] = [
			'name' => $name,
			'target' => Arr::get( $alias, 'target' ),
			'method' => Arr::get( $alias, 'method', '' ),
		];
	}

	/**
	 * Register an alias.
	 * Identical to setAlias but with a more user-friendly interface.
	 *
	 * @codeCoverageIgnore
	 * @param  string         $alias
	 * @param  string|Closure $target
	 * @param  string         $method
	 * @return void
	 */
	public function alias( $alias, $target, $method = '' ) {
		$this->setAlias( [
			'name' => $alias,
			'target' => $target,
			'method' => $method,
		] );
	}

	/**
	 * Call alias if registered.
	 *
	 * @param string $method
	 * @param array  $parameters
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		if ( ! $this->hasAlias( $method ) ) {
			throw new BadMethodCallException( "Method {$method} does not exist." );
		}

		$alias = $this->aliases[ $method ];

		if ( $alias['target'] instanceof Closure ) {
			return call_user_func_array( $alias['target']->bindTo( $this, static::class ), $parameters );
		}

		$target = $this->resolve( $alias['target'] );

		if ( ! empty( $alias['method'] ) ) {
			return call_user_func_array( [$target, $alias['method']], $parameters );
		}

		return $target;
	}

	/**
	 * Resolve a dependency from the IoC container.
	 *
	 * @param  string     $key
	 * @return mixed|null
	 */
	abstract public function resolve( $key );
}
