<?php
/**
 * Die Seitenspalte — the rail
 *
 * The register of standing columns, plus the run of years. With 105 rubrics
 * and eighteen volumes, these two lists are how a reader actually enters this
 * archive; a chronological feed is not.
 *
 * @package Umblätterer
 */

$umblaetterer_rubriken = umblaetterer_rubriken( 24 );
?>

<?php if ( $umblaetterer_rubriken ) : ?>
	<section class="well">
		<div class="section-head">
			<h2 class="section-head__title"><?php esc_html_e( 'Die Rubriken', 'umblaetterer' ); ?></h2>
			<span class="section-head__note">
				<?php
				$umblaetterer_total = wp_count_terms(
					array(
						'taxonomy'   => 'category',
						'hide_empty' => true,
					)
				);

				printf(
					/* translators: %s: total number of categories. */
					esc_html__( '%s im Blatt', 'umblaetterer' ),
					esc_html( number_format_i18n( is_wp_error( $umblaetterer_total ) ? 0 : $umblaetterer_total ) )
				);
				?>
			</span>
		</div>

		<ul class="rubrik-index">
			<?php foreach ( $umblaetterer_rubriken as $umblaetterer_term ) : ?>
				<li>
					<span class="rubrik-index__row">
						<a href="<?php echo esc_url( get_category_link( $umblaetterer_term ) ); ?>">
							<?php echo esc_html( $umblaetterer_term->name ); ?>
						</a>
						<span class="index-list__leader" aria-hidden="true"></span>
						<span class="rubrik-index__count"><?php echo esc_html( number_format_i18n( $umblaetterer_term->count ) ); ?></span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
<?php endif; ?>

<section>
	<div class="section-head">
		<h2 class="section-head__title"><?php esc_html_e( 'Die Jahrgänge', 'umblaetterer' ); ?></h2>
	</div>

	<ul class="rubrik-index">
		<?php umblaetterer_jahrgaenge(); ?>
	</ul>
</section>

<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
	<div class="widget-area">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div>
<?php endif; ?>
