<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

use WPEmerge\Application\InjectionFactory;

/**
 * Handler factory.
 */
class HandlerFactory {
	/**
	 * Injection Factory.
	 *
	 * @var InjectionFactory
	 */
	protected $injection_factory = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param InjectionFactory $injection_factory
	 */
	public function __construct( InjectionFactory $injection_factory ) {
		$this->injection_factory = $injection_factory;
	}

	/**
	 * Make a Handler.
	 *
	 * @codeCoverageIgnore
	 * @param string|\Closure $raw_handler
	 * @param string         $default_method
	 * @param string         $namespace
	 *
	 * @return Handler
	 */
	public function make( $raw_handler, $default_method = '', $namespace = '' ) {
		return new Handler( $this->injection_factory, $raw_handler, $default_method, $namespace );
	}
}
