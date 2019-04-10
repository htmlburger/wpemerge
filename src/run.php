<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

use WPEmerge\Facades\Application;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$kernel = Application::resolve( WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY );
$kernel->bootstrap();
