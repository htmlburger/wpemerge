<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use Pimple\Container;

/**
 * Holds an IoC container.
 */
trait HasContainerTrait {
	/**
	 * IoC container.
	 *
	 * @var Container
	 */
	protected $container = null;

	/**
	 * Get the IoC container instance.
	 *
	 * @codeCoverageIgnore
	 * @return Container
	 */
	public function container() {
		return $this->container;
	}

	/**
	 * Set the IoC container instance.
	 *
	 * @codeCoverageIgnore
	 * @param  Container $container
	 * @return void
	 */
	public function setContainer( $container ) {
		$this->container = $container;
	}

	/**
	 * Resolve a dependency from the IoC container.
	 *
	 * @param  string     $key
	 * @return mixed|null
	 */
	public function resolve( $key ) {
		if ( ! isset( $this->container()[ $key ] ) ) {
			return null;
		}

		return $this->container()[ $key ];
	}
}
