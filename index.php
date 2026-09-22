<?php
/**
 * The generic fallback template
 *
 * The front page is set by home.php and archives by archive.php; this file
 * catches anything the hierarchy has not already claimed, and presents it as
 * a register.
 *
 * @package Umblätterer
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<?php if ( ! is_front_page() ) : ?>
				<header class="page-header">
					<h1 class="page-title"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>

			<ul class="index-list archive-index">
				<?php
				while ( have_posts() ) :
					the_post();
					umblaetterer_index_entry( array( 'author' => true ) );
				endwhile;
				?>
			</ul>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => esc_html__( '← Zurück', 'umblaetterer' ),
					'next_text' => esc_html__( 'Weiter →', 'umblaetterer' ),
				)
			);
			?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

	</main><!-- #primary -->

<?php
get_footer();
