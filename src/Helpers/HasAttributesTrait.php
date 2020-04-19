<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

use WPEmerge\Support\Arr;

/**
 * Represent an object which has an array of attributes.
 */
trait HasAttributesTrait {
	/**
	 * Attributes.
	 *
	 * @var array<string, mixed>
	 */
	protected $attributes = [];

	/**
	 * Get attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getAttribute( $attribute, $default = '' ) {
		return Arr::get( $this->getAttributes(), $attribute, $default );
	}

	/**
	 * Get all attributes.
	 *
	 * @return array<string, mixed>
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * Set attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return void
	 */
	public function setAttribute( $attribute, $value ) {
		$this->setAttributes( array_merge(
			$this->getAttributes(),
			[$attribute => $value]
		) );
	}

	/**
	 * Fluent alias for setAttribute().
	 *
	 * @codeCoverageIgnore
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return static $this
	 */
	public function attribute( $attribute, $value ) {
		$this->setAttribute( $attribute, $value );

		return $this;
	}

	/**
	 * Set all attributes.
	 * No attempt to merge attributes is done - this is a direct overwrite operation.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return void
	 */
	public function setAttributes( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Fluent alias for setAttributes().
	 *
	 * @codeCoverageIgnore
	 * @param  array<string, mixed> $attributes
	 * @return static               $this
	 */
	public function attributes( $attributes ) {
		$this->setAttributes( $attributes );

		return $this;
	}
}
