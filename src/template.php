<?php
/**
 * Template used to override the loaded template file by WordPress when a route is handled
 *
 * @codeCoverageIgnore
 */
use CarbonFramework\Framework;
$response = apply_filters( 'carbon_framework_response', null );
if ( $response !== null ) {
	Framework::respond( $response );
}
