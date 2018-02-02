<?php
/**
 * View used to override the loaded view file by WordPress when a route is handled
 */
// @codeCoverageIgnoreStart
$response = apply_filters( 'wpemerge.response', null );
if ( $response !== null ) {
	\WPEmerge\Facades\Framework::respond( $response );
}
// @codeCoverageIgnoreEnd
