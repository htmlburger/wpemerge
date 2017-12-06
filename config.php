<?php
/**
 * Absolute path to framework's directory
 */
if ( ! defined( 'OBSIDIAN_DIR' ) ) {
	define( 'OBSIDIAN_DIR', __DIR__ );
}

/**
 * Service container keys and key prefixes
 */
if ( ! defined( 'OBSIDIAN_FRAMEWORK_KEY' ) ) {
	define( 'OBSIDIAN_FRAMEWORK_KEY', 'framework.framework.framework' );
}

if ( ! defined( 'OBSIDIAN_CONFIG_KEY' ) ) {
	define( 'OBSIDIAN_CONFIG_KEY', 'framework.config' );
}

if ( ! defined( 'OBSIDIAN_SESSION_KEY' ) ) {
	define( 'OBSIDIAN_SESSION_KEY', 'framework.session' );
}

if ( ! defined( 'OBSIDIAN_ROUTING_ROUTER_KEY' ) ) {
	define( 'OBSIDIAN_ROUTING_ROUTER_KEY', 'framework.routing.router' );
}

if ( ! defined( 'OBSIDIAN_ROUTING_CONDITIONS_KEY' ) ) {
	define( 'OBSIDIAN_ROUTING_CONDITIONS_KEY', 'framework.routing.conditions.' );
}

if ( ! defined( 'OBSIDIAN_ROUTING_GLOBAL_MIDDLEWARE_KEY' ) ) {
	define( 'OBSIDIAN_ROUTING_GLOBAL_MIDDLEWARE_KEY', 'framework.routing.global_middleware' );
}

if ( ! defined( 'OBSIDIAN_TEMPLATING_ENGINE_KEY' ) ) {
	define( 'OBSIDIAN_TEMPLATING_ENGINE_KEY', 'framework.templating.engine' );
}

if ( ! defined( 'OBSIDIAN_TEMPLATING_ENGINE_PHP_KEY' ) ) {
	define( 'OBSIDIAN_TEMPLATING_ENGINE_PHP_KEY', 'framework.templating.engine.php' );
}

if ( ! defined( 'OBSIDIAN_FLASH_KEY' ) ) {
	define( 'OBSIDIAN_FLASH_KEY', 'framework.flash.flash' );
}

if ( ! defined( 'OBSIDIAN_OLD_INPUT_KEY' ) ) {
	define( 'OBSIDIAN_OLD_INPUT_KEY', 'framework.old_input.old_input' );
}

if ( ! defined( 'OBSIDIAN_SERVICE_PROVIDERS_KEY' ) ) {
	define( 'OBSIDIAN_SERVICE_PROVIDERS_KEY', 'framework.service_providers' );
}
