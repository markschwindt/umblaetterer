<?php
/**
 * The body of a single post
 *
 * @package Umblätterer
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>

	<header class="entry-header">
		<?php
		if ( is_singular() ) {
			umblaetterer_kicker( array( 'class' => 'kicker--large' ) );
			the_title( '<h1 class="entry-title">', '</h1>' );
			umblaetterer_dateline(
				array(
					'reading'  => true,
					'comments' => true,
				)
			);
		} else {
			umblaetterer_kicker();
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			umblaetterer_dateline();
		}
		?>
	</header><!-- .entry-header -->

	<?php umblaetterer_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Weiterblättern <span class="screen-reader-text">zu „%s"</span> →', 'umblaetterer' ),
					array( 'span' => array( 'class' => array() ) )
				),
				wp_kses_post( get_the_title() )
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Fortsetzung:', 'umblaetterer' ) . ' ',
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( is_singular() ) : ?>
		<p class="entry-end" aria-hidden="true">❧</p>
	<?php endif; ?>

	<footer class="entry-footer">
		<?php umblaetterer_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
