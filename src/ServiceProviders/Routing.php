<?php

namespace CarbonFramework\ServiceProviders;

use ArrayAccess;
use CarbonFramework\Framework;

class Routing implements ServiceProviderInterface {
	public function register( ArrayAccess $container ) {
		$container['framework.routing.conditions.custom'] = \CarbonFramework\Routing\Conditions\Custom::class;
		$container['framework.routing.conditions.url'] = \CarbonFramework\Routing\Conditions\Url::class;
		$container['framework.routing.conditions.post_id'] = \CarbonFramework\Routing\Conditions\PostId::class;

		$container['framework.routing.router'] = function( $c ) {
			return new \CarbonFramework\Routing\Router();
		};

		Framework::facade( 'Router', \CarbonFramework\Facades\Router::class );
	}

	public function boot() {
		\Router::boot(); // facade
	}
}