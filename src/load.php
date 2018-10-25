<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

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
$container[ WPEMERGE_FRAMEWORK_KEY ] = function ( $container ) {
	return new Framework( $container );
};

Facade::setFacadeApplication( $container );
AliasLoader::getInstance()->register();
AliasLoader::getInstance()->alias( 'WPEmerge', FrameworkFacade::class );
// @codeCoverageIgnoreEnd
