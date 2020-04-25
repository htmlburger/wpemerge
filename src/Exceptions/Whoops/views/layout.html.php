<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

/**
 * Layout template file for Whoops's pretty error output.
 *
 * @noinspection ALL
 */

$is_admin = function_exists( 'is_admin' ) && is_admin() && apply_filters( 'wpemerge.pretty_errors.apply_admin_styles', true );
$is_ajax = function_exists( 'wp_doing_ajax' ) && wp_doing_ajax();

if ( $is_admin && ! $is_ajax ) {
	?>
	<!--suppress CssUnusedSymbol -->
	<style>
		.wpemerge-whoops {
			position: relative;
			z-index: 1;
			margin: 20px 20px 0 0;
		}

		.wpemerge-whoops .stack-container {
			display: flex;
		}

		.wpemerge-whoops .left-panel {
			position: static;
			height: auto;
			overflow: visible;
		}

		.wpemerge-whoops .details-container {
			position: static;
			height: auto;
			overflow: visible;
		}

		@media (max-width: 600px) {
			.wpemerge-whoops {
				margin: 10px 10px 0 0;
			}

			.wpemerge-whoops .stack-container {
				display: block;
			}
		}
	</style>
	<!--suppress JSValidateTypes, JSValidateTypes -->
	<script>
		jQuery(window).load(function () {
			jQuery(window).scrollTop(0);

			jQuery('.frames-container').on('click', '.frame', function() {
				jQuery(window).scrollTop(0);
			});
		});
	</script>
	<?php
	require 'wpemerge-body.html.php';
	return;
}
?>
<!DOCTYPE html><?php echo $preface; ?>
<html lang="en_US">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<title><?php echo $tpl->escape( $page_title ) ?></title>
</head>
<body>
	<?php require 'wpemerge-body.html.php'; ?>
</body>
</html>
