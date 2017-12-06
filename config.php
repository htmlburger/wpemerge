<?php
/**
 * Absolute path to framework's directory
 */
if ( ! defined( 'WP_EMERGE_DIR' ) ) {
	define( 'WP_EMERGE_DIR', __DIR__ );
}

/**
 * Service container keys and key prefixes
 */
if ( ! defined( 'WP_EMERGE_FRAMEWORK_KEY' ) ) {
	define( 'WP_EMERGE_FRAMEWORK_KEY', 'framework.framework.framework' );
}

if ( ! defined( 'WP_EMERGE_CONFIG_KEY' ) ) {
	define( 'WP_EMERGE_CONFIG_KEY', 'framework.config' );
}

if ( ! defined( 'WP_EMERGE_SESSION_KEY' ) ) {
	define( 'WP_EMERGE_SESSION_KEY', 'framework.session' );
}

if ( ! defined( 'WP_EMERGE_ROUTING_ROUTER_KEY' ) ) {
	define( 'WP_EMERGE_ROUTING_ROUTER_KEY', 'framework.routing.router' );
}

if ( ! defined( 'WP_EMERGE_ROUTING_CONDITIONS_KEY' ) ) {
	define( 'WP_EMERGE_ROUTING_CONDITIONS_KEY', 'framework.routing.conditions.' );
}

if ( ! defined( 'WP_EMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY' ) ) {
	define( 'WP_EMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY', 'framework.routing.global_middleware' );
}

if ( ! defined( 'WP_EMERGE_TEMPLATING_ENGINE_KEY' ) ) {
	define( 'WP_EMERGE_TEMPLATING_ENGINE_KEY', 'framework.templating.engine' );
}

if ( ! defined( 'WP_EMERGE_TEMPLATING_ENGINE_PHP_KEY' ) ) {
	define( 'WP_EMERGE_TEMPLATING_ENGINE_PHP_KEY', 'framework.templating.engine.php' );
}

if ( ! defined( 'WP_EMERGE_FLASH_KEY' ) ) {
	define( 'WP_EMERGE_FLASH_KEY', 'framework.flash.flash' );
}

if ( ! defined( 'WP_EMERGE_OLD_INPUT_KEY' ) ) {
	define( 'WP_EMERGE_OLD_INPUT_KEY', 'framework.old_input.old_input' );
}

if ( ! defined( 'WP_EMERGE_SERVICE_PROVIDERS_KEY' ) ) {
	define( 'WP_EMERGE_SERVICE_PROVIDERS_KEY', 'framework.service_providers' );
}
