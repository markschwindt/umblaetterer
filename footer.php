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

		<section class="widget colophon__block">
			<h2 class="colophon__title"><?php esc_html_e('Die Jahrgänge', 'umblaetterer'); ?></h2>
			<ul class="rubrik-index">
				<?php umblaetterer_jahrgaenge(); ?>
			</ul>
		</section>
	</div>

	<?php
	/*
	 * Die Hausmarken — the strip of house marks above the imprint.
	 *
	 * The awards, the pointer to the Bluesky account and the Google Earth tour
	 * of the Kaffeehäuser: the matter a paper prints in its own cause, set
	 * between the colophon and the imprint where a masthead block would sit.
	 * The two destinations that are not obvious — the Grimme nomination and the
	 * Best-of page the mole belongs to — are the ones the About page already
	 * links to, so they stay in step with it.
	 */
	$umblaetterer_maulwurf = get_page_by_path( 'best-of-feuilleton-2025' );
	?>
	<div class="hausmarken">

		<a class="hausmarken__item" href="https://bsky.app/profile/umblaetterer.bsky.social" rel="me noopener">
			<span class="hausmarken__label"><?php esc_html_e( 'Folgen', 'umblaetterer' ); ?></span>
			<span class="hausmarken__text">
				<?php esc_html_e( 'Der Umblätterer', 'umblaetterer' ); ?><br>
				<?php esc_html_e( 'auf Bluesky', 'umblaetterer' ); ?>
			</span>
		</a>

		<a class="hausmarken__item" href="<?php echo esc_url( $umblaetterer_maulwurf ? get_permalink( $umblaetterer_maulwurf ) : home_url( '/best-of-feuilleton-2025/' ) ); ?>">
			<img
				class="hausmarken__badge"
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/goldener-maulwurf-2025.jpg' ); ?>"
				width="80" height="70" loading="lazy" decoding="async"
				alt="<?php esc_attr_e( 'Der Goldene Maulwurf', 'umblaetterer' ); ?>"
			>
			<span class="hausmarken__text">
				<?php esc_html_e( 'Der Goldene Maulwurf', 'umblaetterer' ); ?><br>
				<?php esc_html_e( 'Best of Feuilleton 2025', 'umblaetterer' ); ?>
			</span>
		</a>

		<a class="hausmarken__item" href="https://www.grimme-online-award.de/archiv/2010/nominierte/n/d/der-umblaetterer-in-der-halbwelt-des-feuilletons" rel="noopener">
			<img
				class="hausmarken__badge"
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/grimme-nominiert-2010.gif' ); ?>"
				width="120" height="85" loading="lazy" decoding="async"
				alt="<?php esc_attr_e( 'Für den Grimme Online Award 2010 nominiert', 'umblaetterer' ); ?>"
			>
		</a>

		<a class="hausmarken__item" href="https://www.umblaetterer.de/wp-content/uploads/Kaffeehaus_des_Monats.kmz">
			<span class="hausmarken__label"><?php esc_html_e( 'Karte', 'umblaetterer' ); ?></span>
			<span class="hausmarken__text">
				<?php esc_html_e( 'Kaffeehäuser des Monats …', 'umblaetterer' ); ?><br>
				<?php esc_html_e( '… bereisen mit Google Earth.', 'umblaetterer' ); ?>
			</span>
		</a>

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
				esc_html__('Satz und Design: %1$s %2$s', 'umblaetterer'),
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