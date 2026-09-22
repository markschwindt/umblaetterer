<?php
/**
 * Im Archiv gefunden — search results
 *
 * @package Umblätterer
 */

get_header();
?>

	<main id="primary" class="site-main">

		<header class="page-header">
			<p class="page-header__kicker"><?php esc_html_e( 'Im Archiv gesucht', 'umblaetterer' ); ?></p>

			<h1 class="page-title">
				<span><?php echo esc_html( get_search_query() ); ?></span>
			</h1>

			<?php global $wp_query; ?>

			<p class="page-header__count">
				<?php
				printf(
					/* translators: %s: number of results. */
					esc_html( _n( '%s Fundstelle', '%s Fundstellen', (int) $wp_query->found_posts, 'umblaetterer' ) ),
					esc_html( number_format_i18n( $wp_query->found_posts ) )
				);
				?>
			</p>
		</header><!-- .page-header -->

		<?php if ( have_posts() ) : ?>

			<div class="issue__lower">
				<section class="archive-index">
					<ul class="index-list">
						<?php
						while ( have_posts() ) :
							the_post();
							umblaetterer_index_entry(
								array(
									'author' => true,
									'teaser' => true,
								)
							);
						endwhile;
						?>
					</ul>

					<?php
					the_posts_pagination(
						array(
							'mid_size'           => 2,
							'prev_text'          => esc_html__( '← Zurück', 'umblaetterer' ),
							'next_text'          => esc_html__( 'Weiter →', 'umblaetterer' ),
							'screen_reader_text' => esc_html__( 'Seiten', 'umblaetterer' ),
						)
					);
					?>
				</section>

				<aside class="issue__rail">
					<?php get_template_part( 'template-parts/rail', 'rubriken' ); ?>
				</aside>
			</div>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

	</main><!-- #primary -->

<?php
get_footer();
