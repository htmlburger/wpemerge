<?php

namespace WPEmerge\Routing;

use WPEmerge\Facades\Framework;
use WPEmerge\Facades\Router as RouterFacade;
use WPEmerge\Facades\RouteCondition as RouteConditionFacade;
use WPEmerge\Routing\Conditions\ConditionFactory;
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
	protected static $condition_types = [
		'url' => \WPEmerge\Routing\Conditions\UrlCondition::class,
		'custom' => \WPEmerge\Routing\Conditions\CustomCondition::class,
		'multiple' => \WPEmerge\Routing\Conditions\MultipleCondition::class,
		'negate' => \WPEmerge\Routing\Conditions\NegateCondition::class,
		'post_id' => \WPEmerge\Routing\Conditions\PostIdCondition::class,
		'post_slug' => \WPEmerge\Routing\Conditions\PostSlugCondition::class,
		'post_status' => \WPEmerge\Routing\Conditions\PostStatusCondition::class,
		'post_template' => \WPEmerge\Routing\Conditions\PostTemplateCondition::class,
		'post_type' => \WPEmerge\Routing\Conditions\PostTypeCondition::class,
		'query_var' => \WPEmerge\Routing\Conditions\QueryVarCondition::class,
	];

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_CONFIG_KEY ] = array_merge( [
			'global_middleware' => [],
		], $container[ WPEMERGE_CONFIG_KEY ] );

		$container[ WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY ] = $container[ WPEMERGE_CONFIG_KEY ]['global_middleware'];

		$container[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ] = static::$condition_types;

		$container[ WPEMERGE_ROUTING_ROUTER_KEY ] = function( $c ) {
			return new Router( $c[ WPEMERGE_REQUEST_KEY ], $c[ WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY ] );
		};

		$container[ WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY ] = function( $c ) {
			return new ConditionFactory( $c[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ] );
		};

		Framework::facade( 'Router', RouterFacade::class );
		Framework::facade( 'RouteCondition', RouteConditionFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		RouterFacade::boot();
	}
}
