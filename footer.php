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
		if ( ! umblaetterer_jahrgaenge_placed() ) {
			get_template_part( 'template-parts/colophon', 'jahrgaenge' );
		}
		?>
	</div>

	<?php
	/*
	 * Die Auszeichnungen — the badges above the imprint.
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
	<div class="badges">

		<a class="badges__item" href="https://bsky.app/profile/umblaetterer.bsky.social" rel="me noopener">
			<?php // Bluesky's own mark, from simple-icons (CC0). Inlined so it takes currentColor. ?>
			<svg class="badges__mark badges__mark--icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path d="M12 10.8c-1.087-2.114-4.046-6.053-6.798-7.995C2.566.944 1.561 1.266.902 1.565.139 1.908 0 3.08 0 3.768c0 .69.378 5.65.624 6.479.815 2.736 3.713 3.66 6.383 3.364.136-.02.275-.039.415-.056-.138.022-.276.04-.415.056-3.912.58-7.387 2.005-2.83 7.078 5.013 5.19 6.87-1.113 7.823-4.308.953 3.195 2.05 9.271 7.733 4.308 4.267-4.308 1.172-6.498-2.74-7.078a8.741 8.741 0 0 1-.415-.056c.14.017.279.036.415.056 2.67.297 5.568-.628 6.383-3.364.246-.828.624-5.79.624-6.478 0-.69-.139-1.861-.902-2.206-.659-.298-1.664-.62-4.3 1.24C16.046 4.748 13.087 8.687 12 10.8Z"/>
			</svg>
			<span class="badges__label"><?php esc_html_e( 'Folgen', 'umblaetterer' ); ?></span>
			<span class="badges__text">
				<?php esc_html_e( 'Der Umblätterer', 'umblaetterer' ); ?><br>
				<?php esc_html_e( 'auf Bluesky', 'umblaetterer' ); ?>
			</span>
		</a>

		<a class="badges__item" href="<?php echo esc_url( $umblaetterer_maulwurf ? get_permalink( $umblaetterer_maulwurf ) : home_url( '/best-of-feuilleton-2025/' ) ); ?>">
			<img
				class="badges__mark badges__mark--image"
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/goldener-maulwurf-2025.jpg' ); ?>"
				width="80" height="70" loading="lazy" decoding="async"
				alt="<?php esc_attr_e( 'Der Goldene Maulwurf', 'umblaetterer' ); ?>"
			>
			<span class="badges__text">
				<?php esc_html_e( 'Der Goldene Maulwurf', 'umblaetterer' ); ?><br>
				<?php esc_html_e( 'Best of Feuilleton 2025', 'umblaetterer' ); ?>
			</span>
		</a>

		<a class="badges__item" href="https://www.grimme-online-award.de/archiv/2010/nominierte/n/d/der-umblaetterer-in-der-halbwelt-des-feuilletons" rel="noopener">
			<?php
			/*
			 * The alt is empty on purpose. The caption below now says what the
			 * badge says, and a screen reader that announced both would read
			 * the same award twice inside one link.
			 */
			?>
			<img
				class="badges__mark badges__mark--image"
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/grimme-nominiert-2010.gif' ); ?>"
				width="120" height="85" loading="lazy" decoding="async"
				alt=""
			>
			<span class="badges__text">
				<?php esc_html_e( 'Grimme Online Award', 'umblaetterer' ); ?><br>
				<?php esc_html_e( 'Nominee 2010', 'umblaetterer' ); ?>
			</span>
		</a>

		<a class="badges__item" href="https://www.umblaetterer.de/wp-content/uploads/Kaffeehaus_des_Monats.kmz">
			<?php // A pin, drawn here rather than borrowed, so it needs no attribution. ?>
			<svg class="badges__mark badges__mark--icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path d="M12 2.25a6.75 6.75 0 0 0-6.75 6.75c0 4.79 5.92 11.72 6.17 12.01a.76.76 0 0 0 1.16 0c.25-.29 6.17-7.22 6.17-12.01A6.75 6.75 0 0 0 12 2.25Zm0 9.4a2.65 2.65 0 1 1 0-5.3 2.65 2.65 0 0 1 0 5.3Z"/>
			</svg>
			<span class="badges__label"><?php esc_html_e( 'Karte', 'umblaetterer' ); ?></span>
			<span class="badges__text">
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