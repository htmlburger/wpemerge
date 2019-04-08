<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Facades\Application;
use WPEmerge\Facades\Router;
use WPEmerge\Routing\Conditions\UrlCondition;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$container = Application::getContainer();
$kernel = $container[ WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY ];

$kernel->bootstrap();

// Enable Route query filtering.
add_action( 'request', function ( $query_vars ) use ( $container ) {
	$request = $container[ WPEMERGE_REQUEST_KEY ];
	$routes = Router::getRoutes();

	foreach ( $routes as $route ) {
		$filtered = $route->applyQueryFilter( $request, $query_vars );

		if ( $filtered !== false ) {
			$query_vars = $filtered;
			break;
		}
	}

	return $query_vars;
}, 1000 );

// Enable kernel.
add_action( 'template_include', function ( $view ) use ( $container, $kernel ) {
	$request = $container[ WPEMERGE_REQUEST_KEY ];

	$response = $kernel->handle( $request, $view );

	if ( $response instanceof \Psr\Http\Message\ResponseInterface ) {
		$container[ WPEMERGE_RESPONSE_KEY ] = $response;

		return WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php';
	}

	return $view;
}, 1000 );
