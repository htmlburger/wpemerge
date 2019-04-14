<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Facades\Application;
use WPEmerge\Facades\Route as RouteFacade;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide routing dependencies
 *
 * @codeCoverageIgnore
 */
class RoutingServiceProvider implements ServiceProviderInterface {

	/**
	 * Key=>Class dictionary of condition types
	 *
	 * @var array<string, string>
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
		'ajax' => \WPEmerge\Routing\Conditions\AjaxCondition::class,
		'admin' => \WPEmerge\Routing\Conditions\AdminCondition::class,
	];

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ] =
			static::$condition_types;

		$container[ WPEMERGE_ROUTING_ROUTER_KEY ] = function ( $c ) {
			return new Router( $c[ WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY ] );
		};

		$container[ WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY ] = function ( $c ) {
			return new ConditionFactory( $c[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ] );
		};

		$container[ WPEMERGE_ROUTING_ROUTE_REGISTRAR_KEY ] = $container->factory( function ( $c ) {
			return new RouteRegistrar( $c[ WPEMERGE_ROUTING_ROUTER_KEY ] );
		} );

		Application::facade( 'Route', RouteFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
