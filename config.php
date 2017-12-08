<?php
/**
 * Absolute path to framework's directory
 */
if ( ! defined( 'WPEMERGE_DIR' ) ) {
	define( 'WPEMERGE_DIR', __DIR__ );
}

/**
 * Service container keys and key prefixes
 */
if ( ! defined( 'WPEMERGE_FRAMEWORK_KEY' ) ) {
	define( 'WPEMERGE_FRAMEWORK_KEY', 'framework.framework.framework' );
}

if ( ! defined( 'WPEMERGE_CONFIG_KEY' ) ) {
	define( 'WPEMERGE_CONFIG_KEY', 'framework.config' );
}

if ( ! defined( 'WPEMERGE_SESSION_KEY' ) ) {
	define( 'WPEMERGE_SESSION_KEY', 'framework.session' );
}

if ( ! defined( 'WPEMERGE_ROUTING_ROUTER_KEY' ) ) {
	define( 'WPEMERGE_ROUTING_ROUTER_KEY', 'framework.routing.router' );
}

if ( ! defined( 'WPEMERGE_ROUTING_CONDITIONS_KEY' ) ) {
	define( 'WPEMERGE_ROUTING_CONDITIONS_KEY', 'framework.routing.conditions.' );
}

if ( ! defined( 'WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY' ) ) {
	define( 'WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY', 'framework.routing.global_middleware' );
}

if ( ! defined( 'WPEMERGE_VIEW_ENGINE_KEY' ) ) {
	define( 'WPEMERGE_VIEW_ENGINE_KEY', 'framework.view.engine' );
}

if ( ! defined( 'WPEMERGE_VIEW_ENGINE_PHP_KEY' ) ) {
	define( 'WPEMERGE_VIEW_ENGINE_PHP_KEY', 'framework.view.engine.php' );
}

if ( ! defined( 'WPEMERGE_FLASH_KEY' ) ) {
	define( 'WPEMERGE_FLASH_KEY', 'framework.flash.flash' );
}

if ( ! defined( 'WPEMERGE_OLD_INPUT_KEY' ) ) {
	define( 'WPEMERGE_OLD_INPUT_KEY', 'framework.old_input.old_input' );
}

if ( ! defined( 'WPEMERGE_SERVICE_PROVIDERS_KEY' ) ) {
	define( 'WPEMERGE_SERVICE_PROVIDERS_KEY', 'framework.service_providers' );
}
