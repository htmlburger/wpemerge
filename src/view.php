<?php
/**
 * View used to override the loaded view file by WordPress when a route is handled
 */
// @codeCoverageIgnoreStart
$response = \WPEmerge\Facades\Framework::resolve( WPEMERGE_RESPONSE_KEY );
if ( $response !== null ) {
	\WPEmerge\Facades\Framework::respond( $response );
}
// @codeCoverageIgnoreEnd
