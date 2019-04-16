<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * View used to override the loaded view file by WordPress when a route is handled
 */
$response = \WPEmerge\Facades\Application::resolve( WPEMERGE_RESPONSE_KEY );
if ( $response !== null ) {
	\WPEmerge\Facades\Response::respond( $response );
}
