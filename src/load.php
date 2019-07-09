<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

use WPEmerge\Application\Application;
use WPEmerge\Support\Facade;
use WPEmerge\Support\AliasLoader;
use Pimple\Container;

if ( php_sapi_name() !== 'cli' && ! defined( 'ABSPATH' ) ) {
	return;
}

// @codeCoverageIgnoreStart
$container = new Container();
$container[ WPEMERGE_APPLICATION_KEY ] = function ( $container ) {
	return new Application( $container );
};

Facade::setFacadeApplication( $container );
AliasLoader::getInstance()->register();
// @codeCoverageIgnoreEnd
