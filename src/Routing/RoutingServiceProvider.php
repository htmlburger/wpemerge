<?php

namespace WPEmerge\Routing;

use WPEmerge;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\ServiceProviders\ServiceProviderInterface;
use Pimple\Container;

/**
 * Provide routing dependencies
 *
 * @codeCoverageIgnore
 */
class RoutingServiceProvider implements ServiceProviderInterface {
	/**
	 * Key=>Class dictionary of condition types
	 *
	 * @var string[string]
	 */
	protected static $condition_extensions = [
		'url' => \WPEmerge\Routing\Conditions\Url::class,
		'custom' => \WPEmerge\Routing\Conditions\Custom::class,
		'multiple' => \WPEmerge\Routing\Conditions\Multiple::class,
		'post_id' => \WPEmerge\Routing\Conditions\PostId::class,
		'post_slug' => \WPEmerge\Routing\Conditions\PostSlug::class,
		'post_template' => \WPEmerge\Routing\Conditions\PostTemplate::class,
		'post_type' => \WPEmerge\Routing\Conditions\PostType::class,
		'query_var' => \WPEmerge\Routing\Conditions\QueryVar::class,
		'has_query_var' => \WPEmerge\Routing\Conditions\HasQueryVar::class,
	];

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WP_EMERGE_CONFIG_KEY ] = array_merge( [
			'global_middleware' => [],
		], $container[ WP_EMERGE_CONFIG_KEY ] );

		$container[ WP_EMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY ] = apply_filters(
			'WP_EMERGE.global_middleware',
			$container[ WP_EMERGE_CONFIG_KEY ]['global_middleware']
		);

		$container[ WP_EMERGE_ROUTING_ROUTER_KEY ] = function() {
			return new Router();
		};

		foreach ( static::$condition_extensions as $name => $class_name ) {
			$container[ WP_EMERGE_ROUTING_CONDITIONS_KEY . $name ] = $class_name;
		}

		WPEmerge::facade( 'Router', RouterFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		\Router::boot(); // facade
	}
}
