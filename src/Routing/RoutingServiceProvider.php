<?php

namespace CarbonFramework\Routing;

use CarbonFramework\Framework;
use CarbonFramework\ServiceProviders\ServiceProviderInterface;

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
	protected static $condition_class_dictionary = [
		'url' => \CarbonFramework\Routing\Conditions\Url::class,
		'custom' => \CarbonFramework\Routing\Conditions\Custom::class,
		'multiple' => \CarbonFramework\Routing\Conditions\Multiple::class,
		'post_id' => \CarbonFramework\Routing\Conditions\PostId::class,
		'post_slug' => \CarbonFramework\Routing\Conditions\PostSlug::class,
		'post_template' => \CarbonFramework\Routing\Conditions\PostTemplate::class,
		'post_type' => \CarbonFramework\Routing\Conditions\PostType::class,
		'query_var' => \CarbonFramework\Routing\Conditions\QueryVar::class,
		'has_query_var' => \CarbonFramework\Routing\Conditions\HasQueryVar::class,
	];

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container['framework.config'] = array_merge( [
			'global_middleware' => [],
		], $container['framework.config'] );

		$container['framework.routing.global_middleware'] = $container['framework.config']['global_middleware'];
		$container['framework.routing.global_middleware'] = apply_filters(
			'carbon_framework_global_middleware',
			$container['framework.routing.global_middleware']
		);

		foreach ( static::$condition_class_dictionary as $key => $class ) {
			$container[ 'framework.routing.conditions.' . $key ] = $class;
		}

		$container['framework.routing.router'] = function() {
			return new \CarbonFramework\Routing\Router();
		};

		Framework::facade( 'Router', \CarbonFramework\Routing\RouterFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		\Router::boot(); // facade
	}
}
