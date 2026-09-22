<?php
/**
 * Die Jahrgänge — the run of volumes, as one colophon block
 *
 * Its own file because it is placed twice over: normally it is injected into
 * the widget stream directly after the recent-posts widget (see
 * umblaetterer_colophon_order()), and it falls back to the end of the colophon
 * when that widget is not there to hang it from.
 *
 * @package Umblätterer
 */

?>
<section class="widget colophon__block">
	<h2 class="colophon__title"><?php esc_html_e( 'Die Jahrgänge', 'umblaetterer' ); ?></h2>
	<ul class="rubrik-index">
		<?php umblaetterer_jahrgaenge(); ?>
	</ul>
</section>
