<?php

namespace Obsidian\Routing;

use Obsidian;
use Obsidian\Routing\Conditions\ConditionInterface;
use Obsidian\ServiceProviders\ServiceProviderInterface;
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
		'url' => \Obsidian\Routing\Conditions\Url::class,
		'custom' => \Obsidian\Routing\Conditions\Custom::class,
		'multiple' => \Obsidian\Routing\Conditions\Multiple::class,
		'post_id' => \Obsidian\Routing\Conditions\PostId::class,
		'post_slug' => \Obsidian\Routing\Conditions\PostSlug::class,
		'post_template' => \Obsidian\Routing\Conditions\PostTemplate::class,
		'post_type' => \Obsidian\Routing\Conditions\PostType::class,
		'query_var' => \Obsidian\Routing\Conditions\QueryVar::class,
		'has_query_var' => \Obsidian\Routing\Conditions\HasQueryVar::class,
	];

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ OBSIDIAN_CONFIG_KEY ] = array_merge( [
			'global_middleware' => [],
		], $container[ OBSIDIAN_CONFIG_KEY ] );

		$container[ OBSIDIAN_ROUTING_GLOBAL_MIDDLEWARE_KEY ] = apply_filters(
			'obsidian.global_middleware',
			$container[ OBSIDIAN_CONFIG_KEY ]['global_middleware']
		);

		$container[ OBSIDIAN_ROUTING_ROUTER_KEY ] = function() {
			return new Router();
		};

		foreach ( static::$condition_extensions as $name => $class_name ) {
			$container[ OBSIDIAN_ROUTING_CONDITIONS_KEY . $name ] = $class_name;
		}

		Obsidian::facade( 'Router', RouterFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		\Router::boot(); // facade
	}
}
