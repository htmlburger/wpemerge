<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wpemerge.respond' );
remove_all_filters( 'wpemerge.respond' );
