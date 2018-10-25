<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use WPEmerge\Support\Arr;

trait HasContextTrait {
	/**
	 * Context.
	 *
	 * @var array
	 */
	protected $context = [];

	/**
	 * Get context values.
	 *
	 * @param  string|null $key
	 * @return mixed
	 */
	public function getContext( $key = null ) {
		if ( $key === null ) {
			return $this->context;
		}

		return Arr::get( $this->context, $key );
	}

	/**
	 * Add context values.
	 *
	 * @param  string|array $key
	 * @param  mixed        $value
	 * @return self         $this
	 */
	public function with( $key, $value = null ) {
		if ( is_array( $key ) ) {
			$this->context = array_merge( $this->getContext(), $key );
		} else {
			$this->context[ $key ] = $value;
		}
		return $this;
	}
}
