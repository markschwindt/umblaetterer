<?php
/**
 * Die Titelseite — the front page
 *
 * A front page in the manner of the period: one Aufmacher across the top,
 * three ruled columns beneath it, and a register of everything else. There is
 * not a photograph in twenty years of this archive, so the page is built the
 * way a 1920s page was built — out of nothing but type and rules.
 *
 * @package Umblätterer
 */

get_header();

$umblaetterer_paged = max( 1, (int) get_query_var( 'paged' ) );
$umblaetterer_n     = 0;
?>

	<main id="primary" class="site-main issue">

		<?php if ( have_posts() ) : ?>

			<?php if ( $umblaetterer_paged > 1 ) : ?>
				<header class="page-header">
					<p class="page-header__kicker"><?php esc_html_e( 'Rückblättern', 'umblaetterer' ); ?></p>
					<h1 class="page-title">
						<?php
						printf(
							/* translators: %s: page number. */
							esc_html__( 'Seite %s', 'umblaetterer' ),
							esc_html( number_format_i18n( $umblaetterer_paged ) )
						);
						?>
					</h1>
				</header>

				<ul class="index-list archive-index">
					<?php
					while ( have_posts() ) :
						the_post();
						umblaetterer_index_entry( array( 'author' => true ) );
					endwhile;
					?>
				</ul>

			<?php else : ?>

				<?php
				/*
				 * Page one. The loop is walked once and split by position:
				 * the first post is the lead, the next three are the columns,
				 * the rest fall into the register at the foot of the page.
				 */
				$umblaetterer_briefs   = array();
				$umblaetterer_register = array();

				while ( have_posts() ) :
					the_post();
					++$umblaetterer_n;

					if ( 1 === $umblaetterer_n ) :
						?>

						<article <?php post_class( 'lead' ); ?>>
							<?php umblaetterer_kicker( array( 'flag' => true, 'class' => 'kicker--large' ) ); ?>

							<h2 class="lead__title">
								<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
							</h2>

							<?php umblaetterer_dateline( array( 'reading' => true, 'comments' => true ) ); ?>

							<div class="lead__body">
								<div class="lead__standfirst">
									<p><?php echo esc_html( umblaetterer_teaser( 58 ) ); ?></p>
									<p>
										<a href="<?php the_permalink(); ?>" class="more-link">
											<?php esc_html_e( 'Weiterblättern →', 'umblaetterer' ); ?>
										</a>
									</p>
								</div>

								<?php
								/*
								 * Beside the lead: either the standing column it belongs to,
								 * or — when that column holds nothing else — what stood in
								 * this paper ten years ago. A feuilleton is read by rubric
								 * and by memory, not by date.
								 */
								$umblaetterer_companion = umblaetterer_lead_companion( get_the_ID() );

								if ( $umblaetterer_companion['posts'] ) :
									?>
									<aside class="lead__aside">
										<div class="section-head">
											<h3 class="section-head__title"><?php echo esc_html( $umblaetterer_companion['title'] ); ?></h3>
											<?php if ( $umblaetterer_companion['note'] ) : ?>
												<span class="section-head__note"><?php echo esc_html( $umblaetterer_companion['note'] ); ?></span>
											<?php endif; ?>
										</div>

										<ul class="index-list">
											<?php
											foreach ( $umblaetterer_companion['posts'] as $umblaetterer_kin_post ) :
												setup_postdata( $GLOBALS['post'] = $umblaetterer_kin_post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
												umblaetterer_index_entry( array( 'rubrik' => true ) );
											endforeach;
											wp_reset_postdata();
											?>
										</ul>
									</aside>
									<?php
								endif;
								?>
							</div>
						</article>

						<div class="rule--double rule--double-thin" role="presentation"></div>
						<?php

					elseif ( $umblaetterer_n <= 4 ) :
						$umblaetterer_briefs[] = get_the_ID();
					else :
						$umblaetterer_register[] = get_the_ID();
					endif;

				endwhile;
				?>

				<?php if ( $umblaetterer_briefs ) : ?>
					<div class="column-set column-set--3">
						<?php
						foreach ( $umblaetterer_briefs as $umblaetterer_id ) :
							setup_postdata( $GLOBALS['post'] = get_post( $umblaetterer_id ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
							?>
							<article <?php post_class( 'brief' ); ?>>
								<?php umblaetterer_kicker(); ?>
								<h2 class="brief__title">
									<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
								</h2>
								<?php umblaetterer_dateline(); ?>
								<p class="brief__teaser"><?php echo esc_html( umblaetterer_teaser( 30 ) ); ?></p>
							</article>
							<?php
						endforeach;
						wp_reset_postdata();
						?>
					</div>
				<?php endif; ?>

				<div class="issue__lower">

					<section class="issue__further">
						<div class="section-head">
							<h2 class="section-head__title"><?php esc_html_e( 'Weiter im Blatt', 'umblaetterer' ); ?></h2>
							<span class="section-head__note"><?php esc_html_e( 'Zuletzt erschienen', 'umblaetterer' ); ?></span>
						</div>

						<ul class="index-list">
							<?php
							foreach ( $umblaetterer_register as $umblaetterer_id ) :
								setup_postdata( $GLOBALS['post'] = get_post( $umblaetterer_id ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
								umblaetterer_index_entry( array( 'author' => true ) );
							endforeach;
							wp_reset_postdata();
							?>
						</ul>
					</section>

					<aside class="issue__rail">
						<?php get_template_part( 'template-parts/rail', 'rubriken' ); ?>
					</aside>
				</div>

			<?php endif; ?>

			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => esc_html__( '← Zurückblättern', 'umblaetterer' ),
					'next_text'          => esc_html__( 'Weiterblättern →', 'umblaetterer' ),
					'screen_reader_text' => esc_html__( 'Seiten', 'umblaetterer' ),
				)
			);
			?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

	</main><!-- #primary -->

<?php
get_footer();
