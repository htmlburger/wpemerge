<?php

use WPEmerge\Framework\Framework;
use WPEmerge\Facades\Framework as FrameworkFacade;
use WPEmerge\Support\Facade;
use WPEmerge\Support\AliasLoader;
use Pimple\Container;

if ( php_sapi_name() !== 'cli' && ! defined( 'ABSPATH' ) ) {
	exit;
}

// @codeCoverageIgnoreStart
$container = new Container();
$container[ WPEMERGE_FRAMEWORK_KEY ] = function( $container ) {
	return new Framework( $container );
};

Facade::setFacadeApplication( $container );
AliasLoader::getInstance()->register();
AliasLoader::getInstance()->alias( 'WPEmerge', FrameworkFacade::class );
// @codeCoverageIgnoreEnd
