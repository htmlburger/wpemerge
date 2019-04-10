<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Exceptions;

use Exception as PhpException;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Requests\RequestInterface;

interface ErrorHandlerInterface {
	/**
	 * Register any necessary error, exception and shutdown handlers.
	 *
	 * @return void
	 */
	public function register();

	/**
	 * Unregister any registered error, exception and shutdown handlers.
	 *
	 * @return void
	 */
	public function unregister();

	/**
	 * Get a response representing the specified exception.
	 *
	 * @param  PhpException $exception
	 * @return ResponseInterface
	 */
	public function getResponse( RequestInterface $request, PhpException $exception );
}
