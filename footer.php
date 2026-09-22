<?php
/**
 * Das Impressum — the colophon
 *
 * @package Umblätterer
 */

?>

	<footer id="colophon" class="site-footer">

		<?php // Thick over thin, closing the sheet as the masthead opened it. ?>
		<div class="site-footer__closing" role="presentation"></div>

		<div class="colophon">
			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<?php dynamic_sidebar( 'footer-1' ); ?>
			<?php else : ?>

				<div class="colophon__block">
					<h2 class="colophon__title"><?php esc_html_e( 'Die Rubriken', 'umblaetterer' ); ?></h2>
					<ul>
						<?php foreach ( umblaetterer_rubriken( 8 ) as $umblaetterer_term ) : ?>
							<li>
								<a href="<?php echo esc_url( get_category_link( $umblaetterer_term ) ); ?>">
									<?php echo esc_html( $umblaetterer_term->name ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="colophon__block">
					<h2 class="colophon__title"><?php esc_html_e( 'Die Jahrgänge', 'umblaetterer' ); ?></h2>
					<ul>
						<?php wp_get_archives( array( 'type' => 'yearly', 'limit' => 9 ) ); ?>
					</ul>
				</div>

				<div class="colophon__block">
					<h2 class="colophon__title"><?php esc_html_e( 'In eigener Sache', 'umblaetterer' ); ?></h2>
					<?php
					if ( has_nav_menu( 'menu-2' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'menu-2',
								'depth'          => 1,
								'container'      => false,
							)
						);
					} else {
						?>
						<ul>
							<?php
							wp_list_pages(
								array(
									'title_li' => '',
									'include'  => implode(
										',',
										array_filter(
											array_map(
												static function ( $slug ) {
													$page = get_page_by_path( $slug );
													return $page ? $page->ID : null;
												},
												array( 'about', 'impressum', 'geloeschte-rubriken' )
											)
										)
									),
								)
							);
							?>
							<li>
								<a href="<?php echo esc_url( get_feed_link() ); ?>">
									<?php esc_html_e( 'RSS-Feed', 'umblaetterer' ); ?>
								</a>
							</li>
						</ul>
						<?php
					}
					?>
				</div>

			<?php endif; ?>
		</div>

		<div class="site-info">
			<span class="site-info__mark" aria-hidden="true">❧</span>
			<span>
				<?php
				printf(
					/* translators: %s: site name. */
					esc_html__( '© %1$s %2$s', 'umblaetterer' ),
					esc_html( wp_date( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</span>
			<span class="sep" aria-hidden="true">·</span>
			<span>
				<?php
				printf(
					/* translators: %s: total number of published posts. */
					esc_html__( '%s Beiträge im Archiv', 'umblaetterer' ),
					esc_html( number_format_i18n( umblaetterer_issue_number() ) )
				);
				?>
			</span>
			<span class="sep" aria-hidden="true">·</span>
			<span>
				<?php
				printf(
					/* translators: 1: theme name, 2: theme author link. */
					esc_html__( 'Satz und Druck: %1$s von %2$s', 'umblaetterer' ),
					'Umblätterer',
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
