<?php
/**
 * Eine fehlende Seite — 404
 *
 * @package Umblätterer
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found">
			<p class="page-header__kicker"><?php esc_html_e( 'Herausgerissen', 'umblaetterer' ); ?></p>

			<p class="error-404__number" aria-hidden="true">404</p>

			<h1 class="page-title"><?php esc_html_e( 'Diese Seite fehlt im Blatt', 'umblaetterer' ); ?></h1>

			<div class="error-404__note">
				<p><?php esc_html_e( 'Was hier stehen sollte, ist nicht mehr da — umgeblättert, umbenannt oder nie gesetzt. Das Archiv reicht bis Mai 2007 zurück; die Suche findet darin fast alles.', 'umblaetterer' ); ?></p>
			</div>

			<?php get_search_form(); ?>

			<div class="error-404__suggestions">
				<div class="section-head">
					<h2 class="section-head__title"><?php esc_html_e( 'Zuletzt erschienen', 'umblaetterer' ); ?></h2>
				</div>

				<ul class="index-list">
					<?php
					$umblaetterer_recent = new WP_Query(
						array(
							'posts_per_page'      => 6,
							'ignore_sticky_posts' => true,
						)
					);

					while ( $umblaetterer_recent->have_posts() ) :
						$umblaetterer_recent->the_post();
						umblaetterer_index_entry();
					endwhile;

					wp_reset_postdata();
					?>
				</ul>
			</div>
		</section><!-- .error-404 -->

	</main><!-- #primary -->

<?php
get_footer();
