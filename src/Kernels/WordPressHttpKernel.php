<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Kernels;

/**
 * Describes how a request is handled.
 */
class WordPressHttpKernel extends HttpKernel {
	/**
	 * {@inheritDoc}
	 */
	protected $middleware = [];

	/**
	 * {@inheritDoc}
	 */
	protected $middleware_groups = [
		'global' => [
			\WPEmerge\Flash\FlashMiddleware::class,
			\WPEmerge\Input\OldInputMiddleware::class,
		],

		'web' => [
			'global',
		],

		'ajax' => [
			'global',
		],

		'admin' => [
			'global',
		],
	];

	/**
	 * {@inheritDoc}
	 */
	protected $middleware_priority = [];
}
