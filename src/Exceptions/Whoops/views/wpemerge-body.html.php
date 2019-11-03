<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

/**
 * @noinspection ALL
 */
?>
<div class="wpemerge-whoops">
	<style><?php echo $stylesheet ?></style>

	<div class="Whoops container">
		<div class="stack-container">

			<?php $tpl->render( $panel_left_outer ) ?>

			<?php $tpl->render( $panel_details_outer ) ?>

		</div>
	</div>

	<script><?php echo $prettify ?></script>
	<script><?php echo $zepto ?></script>
	<script><?php echo $clipboard ?></script>
	<script><?php echo $javascript ?></script>
</div>
