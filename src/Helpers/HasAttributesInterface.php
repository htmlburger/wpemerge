<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

/**
 * Represent an object which has an array of attributes.
 */
interface HasAttributesInterface {
	/**
	 * Get attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getAttribute( $attribute, $default = '' );

	/**
	 * Get all attributes.
	 *
	 * @return array<string, mixed>
	 */
	public function getAttributes();

	/**
	 * Set attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return void
	 */
	public function setAttribute( $attribute, $value );

	/**
	 * Fluent alias for setAttribute().
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return static $this
	 */
	public function attribute( $attribute, $value );

	/**
	 * Set attributes.
	 * No attempt to merge attributes is done - this is a direct overwrite operation.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return void
	 */
	public function setAttributes( $attributes );

	/**
	 * Fluent alias for setAttributes().
	 *
	 * @param  array<string, mixed> $attributes
	 * @return static               $this
	 */
	public function attributes( $attributes );
}
