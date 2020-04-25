<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use Closure;
use WPEmerge\Helpers\Handler;
use WPEmerge\Helpers\HandlerFactory;
use WPEmerge\Helpers\MixedType;

/**
 * Provide general view-related functionality.
 */
class ViewService {
	/**
	 * Configuration.
	 *
	 * @var array<string, mixed>
	 */
	protected $config = [];

	/**
	 * View engine.
	 *
	 * @var ViewEngineInterface
	 */
	protected $engine = null;

	/**
	 * Handler factory.
	 *
	 * @var HandlerFactory
	 */
	protected $handler_factory = null;

	/**
	 * Global variables.
	 *
	 * @var array
	 */
	protected $globals = [];

	/**
	 * View composers.
	 *
	 * @var array
	 */
	protected $composers = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param array<string, mixed> $config
	 * @param ViewEngineInterface  $engine
	 * @param HandlerFactory       $handler_factory
	 */
	public function __construct( $config, ViewEngineInterface $engine, HandlerFactory $handler_factory ) {
		$this->config = $config;
		$this->engine = $engine;
		$this->handler_factory = $handler_factory;
	}

	/**
	 * Get global variables.
	 *
	 * @return array
	 */
	public function getGlobals() {
		return $this->globals;
	}

	/**
	 * Set a global variable.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function addGlobal( $key, $value ) {
		$this->globals[ $key ] = $value;
	}

	/**
	 * Set an array of global variables.
	 *
	 * @param  array $globals
	 * @return void
	 */
	public function addGlobals( $globals ) {
		foreach ( $globals as $key => $value ) {
			$this->addGlobal( $key, $value );
		}
	}

	/**
	 * Get view composer.
	 *
	 * @param  string    $view
	 * @return Handler[]
	 */
	public function getComposersForView( $view ) {
		$view = $this->engine->canonical( $view );

		$composers = [];

		foreach ( $this->composers as $composer ) {
			if ( in_array( $view, $composer['views'], true ) ) {
				$composers[] = $composer['composer'];
			}
		}

		return $composers;
	}

	/**
	 * Add view composer.
	 *
	 * @param  string|string[] $views
	 * @param  string|Closure  $composer
	 * @return void
	 */
	public function addComposer( $views, $composer ) {
		$views = array_map( function ( $view ) {
			return $this->engine->canonical( $view );
		}, MixedType::toArray( $views ) );

		$handler = $this->handler_factory->make( $composer, 'compose', $this->config['namespace'] );

		$this->composers[] = [
			'views' => $views,
			'composer' => $handler,
		];
	}

	/**
	 * Composes a view instance with contexts in the following order: Global, Composers, Local.
	 *
	 * @param  ViewInterface $view
	 * @return void
	 */
	public function compose( ViewInterface $view ) {
		$global = ['global' => $this->getGlobals()];
		$local = $view->getContext();

		$view->with( $global );

		$composers = $this->getComposersForView( $view->getName() );
		foreach ( $composers as $composer ) {
			$composer->execute( $view );
		}

		$view->with( $local );
	}

	/**
	 * Create a view instance.
	 *
	 * @param  string|string[] $views
	 * @return ViewInterface
	 */
	public function make( $views ) {
		return $this->engine->make( MixedType::toArray( $views ) );
	}

	/**
	 * Trigger core hooks for a partial, if any.
	 *
	 * @codeCoverageIgnore
	 * @param  string $name
	 * @return void
	 */
	public function triggerPartialHooks( $name ) {
		if ( ! function_exists( 'apply_filters' ) ) {
			// We are not in a WordPress environment - skip triggering hooks.
			return;
		}

		$core_partial = '/^(header|sidebar|footer)(?:-(.*?))?(\.|$)/i';
		$matches = [];
		$is_partial = preg_match( $core_partial, $name, $matches );

		if ( $is_partial && apply_filters( "wpemerge.partials.{$matches[1]}.hook", true ) ) {
			do_action( "get_{$matches[1]}", $matches[2] );
		}
	}

	/**
	 * Render a view.
	 *
	 * @codeCoverageIgnore
	 * @param  string|string[]      $views
	 * @param  array<string, mixed> $context
	 * @return void
	 */
	public function render( $views, $context = [] ) {
		$view = $this->make( $views )->with( $context );
		$this->triggerPartialHooks( $view->getName() );
		echo $view->toString();
	}
}
