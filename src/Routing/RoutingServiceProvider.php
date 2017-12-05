<?php

namespace Obsidian\Routing;

use Obsidian\Framework;
use Obsidian\Extend;
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
	 * Register framework extensions
	 *
	 * @param  array $condition_extensions
	 * @return void
	 */
	protected function registerExtensions( $condition_extensions ) {
		foreach ( $condition_extensions as $name => $class_name ) {
			Extend::routeCondition( $name, $class_name );
		}
	}

	/**
	 * Register route conditions
	 *
	 * @param  Container $container
	 * @param  array     $conditions
	 * @return void
	 */
	protected function registerRouteConditions( Container $container, $conditions ) {
		foreach ( $conditions as $name => $class_name ) {
			$container[ 'framework.routing.conditions.' . $name ] = $class_name;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container['framework.config'] = array_merge( [
			'global_middleware' => [],
		], $container['framework.config'] );

		$container['framework.routing.global_middleware'] = apply_filters(
			'obsidian.global_middleware',
			$container['framework.config']['global_middleware']
		);

		$container['framework.routing.router'] = function() {
			return new Router();
		};

		Framework::facade( 'Router', RouterFacade::class );

		$this->registerExtensions( static::$condition_extensions );
		$this->registerRouteConditions( $container, Extend::get( ConditionInterface::class ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		\Router::boot(); // facade
	}
}
