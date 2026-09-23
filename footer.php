<?php
/**
 * Das Impressum — the colophon
 *
 * A large ruled grid at the foot of every page: each widget becomes one cell,
 * and the hairlines between them are drawn the way the columns of the page
 * above are drawn. When no widgets are placed, the theme sets the grid itself
 * out of the material the archive already has — the masthead of contributors,
 * the run of yearbooks, and the imprint.
 *
 * @package Umblätterer
 */

?>

<footer id="colophon" class="site-footer">

	<?php // Thick over thin, closing the sheet as the masthead opened it. ?>
	<div class="site-footer__closing" role="presentation"></div>

	<div class="colophon">
		<?php
		if (is_active_sidebar('sidebar-1')):
			dynamic_sidebar('sidebar-1');
		else:
			get_template_part('template-parts/colophon', 'default');
		endif;

		/*
		 * The rubric register belongs beside the content, so it only appears
		 * down here on the templates that have no rail — a single piece, or a
		 * page — where a reader would otherwise be left with no way into the
		 * other sixteen hundred.
		 */
		if (!umblaetterer_has_rail()):
			?>
			<section class="widget colophon__block">
				<h2 class="colophon__title"><?php esc_html_e('Die Rubriken', 'umblaetterer'); ?></h2>
				<ul class="rubrik-index">
					<?php foreach (umblaetterer_rubriken(18) as $umblaetterer_term): ?>
						<li>
							<span class="rubrik-index__row">
								<a href="<?php echo esc_url(get_category_link($umblaetterer_term)); ?>">
									<?php echo esc_html($umblaetterer_term->name); ?>
								</a>
								<span class="index-list__leader" aria-hidden="true"></span>
								<span
									class="rubrik-index__count"><?php echo esc_html(number_format_i18n($umblaetterer_term->count)); ?></span>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
			<?php
		endif;
		?>

		<?php
		/*
		 * Normally the volumes have already been placed, immediately after the
		 * recent-posts widget, by umblaetterer_colophon_order(). This catches
		 * the case where that widget is not in the sidebar at all.
		 */
		if (!umblaetterer_jahrgaenge_placed()) {
			get_template_part('template-parts/colophon', 'jahrgaenge');
		}
		?>
	</div>

	<?php
	/*
	 * Die Auszeichnungen — the badge band above the imprint.
	 *
	 * One widget is one badge. While the area is empty the theme sets the band
	 * itself, so a site that has never opened Appearance → Widgets still shows
	 * the awards rather than a gap.
	 */
	?>
	<div class="badges">
		<?php
		if ( is_active_sidebar( 'badges' ) ) {
			dynamic_sidebar( 'badges' );
		} else {
			get_template_part( 'template-parts/badges', 'default' );
		}
		?>
	</div>

	<div class="site-info">
		<span>
			<?php
			printf(
				/* translators: 1: current year, 2: site name. */
				esc_html__('© %1$s %2$s', 'umblaetterer'),
				esc_html(wp_date('Y')),
				esc_html(get_bloginfo('name'))
			);
			?>
		</span>
		<span class="sep" aria-hidden="true">·</span>
		<span>
			<?php
			printf(
				/* translators: %s: total number of published posts. */
				esc_html__('%s Beiträge im Archiv', 'umblaetterer'),
				esc_html(number_format_i18n(umblaetterer_issue_number()))
			);
			?>
		</span>
		<span class="sep" aria-hidden="true">·</span>
		<span>
			<?php
			printf(
				/* translators: 1: theme name, 2: theme author link. */
				esc_html__('Satz und Gestaltung: %1$s %2$s', 'umblaetterer'),
				'',
				'<a href="https://markschwindt.com">Mark Schwindt</a>'
			);
			?>
		</span>
	</div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>