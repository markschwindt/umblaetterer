<?php
/**
 * Das Register — an archive
 *
 * Rubrics, years and authors are all presented the same way: as a table of
 * contents. "Buchbuch" holds 514 posts; a feed of 514 cards would be unusable,
 * a register of 514 titles is a reference work.
 *
 * @package Umblätterer
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<p class="page-header__kicker"><?php echo esc_html( umblaetterer_archive_kicker() ); ?></p>

				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );

				global $wp_query;
				?>

				<p class="page-header__count">
					<?php
					printf(
						/* translators: %s: number of posts found. */
						esc_html( _n( '%s Beitrag', '%s Beiträge', (int) $wp_query->found_posts, 'umblaetterer' ) ),
						esc_html( number_format_i18n( $wp_query->found_posts ) )
					);
					?>
				</p>
			</header><!-- .page-header -->

			<div class="issue__lower">
				<section class="archive-index">
					<ul class="index-list">
						<?php
						while ( have_posts() ) :
							the_post();
							umblaetterer_index_entry(
								array(
									'author' => true,
									'rubrik' => ! is_category(),
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
