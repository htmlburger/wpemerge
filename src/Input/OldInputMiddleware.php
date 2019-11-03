<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Input;

use Closure;
use WPEmerge\Requests\RequestInterface;

/**
 * Store current request data and clear old request data
 */
class OldInputMiddleware {
	/**
	 * OldInput service.
	 *
	 * @var OldInput
	 */
	protected $old_input = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param OldInput $old_input
	 */
	public function __construct( OldInput $old_input ) {
		$this->old_input = $old_input;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next ) {
		if ( $this->old_input->enabled() && $request->isPost() ) {
			$this->old_input->set( $request->body() );
		}

		return $next( $request );
	}
}
