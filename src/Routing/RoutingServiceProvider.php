<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Facades\Framework;
use WPEmerge\Facades\RouteCondition as RouteConditionFacade;
use WPEmerge\Facades\Router as RouterFacade;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\ServiceProviders\ExtendsConfigTrait;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide routing dependencies
 *
 * @codeCoverageIgnore
 */
class RoutingServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

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
	];

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$this->registerConfiguration( $container );
		$this->registerDependencies( $container );
		$this->registerFacades();
	}

	/**
	 * Register configuration options.
	 *
	 * @param  \Pimple\Container $container
	 * @return void
	 */
	protected function registerConfiguration( $container ) {
		$this->extendConfig( $container, 'middleware_default_priority', 100 );
		$this->extendConfig( $container, 'middleware_priority', [] );
		$this->extendConfig( $container, 'global_middleware', [] );

		$container[ WPEMERGE_ROUTING_MIDDLEWARE_DEFAULT_PRIORITY_KEY ] =
			$container[ WPEMERGE_CONFIG_KEY ]['middleware_default_priority'];

		$container[ WPEMERGE_ROUTING_MIDDLEWARE_PRIORITY_KEY ] =
			$container[ WPEMERGE_CONFIG_KEY ]['middleware_priority'];

		$container[ WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY ] =
			$container[ WPEMERGE_CONFIG_KEY ]['global_middleware'];

		$container[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ] =
			static::$condition_types;
	}

	/**
	 * Register dependencies.
	 *
	 * @param  \Pimple\Container $container
	 * @return void
	 */
	protected function registerDependencies( $container ) {
		$container[ WPEMERGE_ROUTING_ROUTER_KEY ] = function ( $c ) {
			return new Router(
				$c[ WPEMERGE_REQUEST_KEY ],
				$c[ WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY ],
				$c[ WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY ],
				$c[ WPEMERGE_ROUTING_MIDDLEWARE_PRIORITY_KEY ],
				$c[ WPEMERGE_ROUTING_MIDDLEWARE_DEFAULT_PRIORITY_KEY ],
				$c[ WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY ]
			);
		};

		$container[ WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY ] = function ( $c ) {
			return new ConditionFactory( $c[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ] );
		};
	}

	/**
	 * Register facades.
	 *
	 * @return void
	 */
	protected function registerFacades() {
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
