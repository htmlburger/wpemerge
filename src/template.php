<?php
/**
 * Template used to override the loaded template file by WordPress when a route is handled
 */
use CarbonFramework\Framework;
Framework::respond( apply_filters( 'carbon_framework_response', null ) );
