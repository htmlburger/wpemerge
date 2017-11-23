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

		$container['framework.routing.conditions.custom'] = \CarbonFramework\Routing\Conditions\Custom::class;
		$container['framework.routing.conditions.url'] = \CarbonFramework\Routing\Conditions\Url::class;
		$container['framework.routing.conditions.multiple'] = \CarbonFramework\Routing\Conditions\Multiple::class;
		$container['framework.routing.conditions.post_id'] = \CarbonFramework\Routing\Conditions\PostId::class;
		$container['framework.routing.conditions.post_slug'] = \CarbonFramework\Routing\Conditions\PostSlug::class;
		$container['framework.routing.conditions.post_template'] = \CarbonFramework\Routing\Conditions\PostTemplate::class;
		$container['framework.routing.conditions.post_type'] = \CarbonFramework\Routing\Conditions\PostType::class;
		$container['framework.routing.conditions.query_var'] = \CarbonFramework\Routing\Conditions\QueryVar::class;
		$container['framework.routing.conditions.has_query_var'] = \CarbonFramework\Routing\Conditions\HasQueryVar::class;

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
