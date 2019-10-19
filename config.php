<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

/**
 * Absolute path to application's directory.
 */
if ( ! defined( 'WPEMERGE_DIR' ) ) {
	define( 'WPEMERGE_DIR', __DIR__ );
}

/**
 * Service container keys.
 */
if ( ! defined( 'WPEMERGE_APPLICATION_KEY' ) ) {
	define( 'WPEMERGE_APPLICATION_KEY', 'wpemerge.application.application' );
}

if ( ! defined( 'WPEMERGE_APPLICATION_INJECTION_FACTORY_KEY' ) ) {
	define( 'WPEMERGE_APPLICATION_INJECTION_FACTORY_KEY', 'wpemerge.application.injection_factory' );
}

if ( ! defined( 'WPEMERGE_HELPERS_HANDLER_FACTORY_KEY' ) ) {
	define( 'WPEMERGE_HELPERS_HANDLER_FACTORY_KEY', 'wpemerge.handlers.helper_factory' );
}

if ( ! defined( 'WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY' ) ) {
	define( 'WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY', 'wpemerge.kernels.wordpress_http_kernel' );
}

if ( ! defined( 'WPEMERGE_CONFIG_KEY' ) ) {
	define( 'WPEMERGE_CONFIG_KEY', 'wpemerge.config' );
}

if ( ! defined( 'WPEMERGE_SESSION_KEY' ) ) {
	define( 'WPEMERGE_SESSION_KEY', 'wpemerge.session' );
}

if ( ! defined( 'WPEMERGE_REQUEST_KEY' ) ) {
	define( 'WPEMERGE_REQUEST_KEY', 'wpemerge.request' );
}

if ( ! defined( 'WPEMERGE_RESPONSE_KEY' ) ) {
	define( 'WPEMERGE_RESPONSE_KEY', 'wpemerge.response' );
}

if ( ! defined( 'WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY' ) ) {
	define( 'WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY', 'wpemerge.exceptions.error_handler' );
}

if ( ! defined( 'WPEMERGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY' ) ) {
	define( 'WPEMERGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY', 'wpemerge.exceptions.configuration_error_handler' );
}

if ( ! defined( 'WPEMERGE_RESPONSE_SERVICE_KEY' ) ) {
	define( 'WPEMERGE_RESPONSE_SERVICE_KEY', 'wpemerge.responses.response_service' );
}

if ( ! defined( 'WPEMERGE_ROUTING_ROUTER_KEY' ) ) {
	define( 'WPEMERGE_ROUTING_ROUTER_KEY', 'wpemerge.routing.router' );
}

if ( ! defined( 'WPEMERGE_ROUTING_ROUTE_BLUEPRINT_KEY' ) ) {
	define( 'WPEMERGE_ROUTING_ROUTE_BLUEPRINT_KEY', 'wpemerge.routing.route_registrar' );
}

if ( ! defined( 'WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY' ) ) {
	define( 'WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY', 'wpemerge.routing.conditions.condition_factory' );
}

if ( ! defined( 'WPEMERGE_ROUTING_CONDITION_TYPES_KEY' ) ) {
	define( 'WPEMERGE_ROUTING_CONDITION_TYPES_KEY', 'wpemerge.routing.conditions.condition_types' );
}

if ( ! defined( 'WPEMERGE_VIEW_SERVICE_KEY' ) ) {
	define( 'WPEMERGE_VIEW_SERVICE_KEY', 'wpemerge.view.view_service' );
}

if ( ! defined( 'WPEMERGE_VIEW_ENGINE_KEY' ) ) {
	define( 'WPEMERGE_VIEW_ENGINE_KEY', 'wpemerge.view.view_engine' );
}

if ( ! defined( 'WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY' ) ) {
	define( 'WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY', 'wpemerge.view.php_view_engine' );
}

if ( ! defined( 'WPEMERGE_SERVICE_PROVIDERS_KEY' ) ) {
	define( 'WPEMERGE_SERVICE_PROVIDERS_KEY', 'wpemerge.service_providers' );
}

if ( ! defined( 'WPEMERGE_FLASH_KEY' ) ) {
	define( 'WPEMERGE_FLASH_KEY', 'wpemerge.flash.flash' );
}

if ( ! defined( 'WPEMERGE_OLD_INPUT_KEY' ) ) {
	define( 'WPEMERGE_OLD_INPUT_KEY', 'wpemerge.old_input.old_input' );
}

if ( ! defined( 'WPEMERGE_CSRF_KEY' ) ) {
	define( 'WPEMERGE_CSRF_KEY', 'wpemerge.csrf.csrf' );
}
