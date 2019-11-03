<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Responses;

use Psr\Http\Message\ResponseInterface;

interface ResponsableInterface {
	/**
	 * Convert to Psr\Http\Message\ResponseInterface.
	 *
	 * @return ResponseInterface
	 */
	public function toResponse();
}
