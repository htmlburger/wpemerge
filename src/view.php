<?php
/**
 * View used to override the loaded view file by WordPress when a route is handled.
 *
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wpemerge.respond' );
