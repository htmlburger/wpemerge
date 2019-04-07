<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Middleware\HasMiddlewareInterface;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Requests\RequestInterface;

/**
 * Represent a route
 */
class Pipeline implements HasMiddlewareInterface {
	use HasMiddlewareTrait;

	/**
	 * Pipeline handler.
	 *
	 * @var PipelineHandler
	 */
	protected $handler = null;

	/**
	 * Constructor.
	 *
	 * @param  string|\Closure $handler
	 */
	public function __construct( $handler ) {
		$this->handler = new PipelineHandler( $handler );
	}

	/**
	 * Get handler.
	 *
	 * @return PipelineHandler
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * Get a response for the given request.
	 *
	 * @param  RequestInterface                    $request
	 * @param  array                               $arguments
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function run( RequestInterface $request, $arguments ) {
		return $this->executeMiddleware( $this->getMiddleware(), $request, function () use ( $arguments ) {
			return call_user_func_array( [$this->getHandler(), 'execute'], $arguments );
		} );
	}
}
