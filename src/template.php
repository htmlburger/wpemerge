<?php
/**
 * Template used to override the loaded template file by WordPress when a route is handled
 */
// @codeCoverageIgnoreStart
$response = apply_filters( 'obsidian.response', null );
if ( $response !== null ) {
	Obsidian::respond( $response );
}
// @codeCoverageIgnoreEnd
