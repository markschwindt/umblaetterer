<?php
/**
 * The colophon grid as the theme sets it, when no widgets have been placed
 *
 * Built out of what the archive already knows about itself: who writes it, the
 * yearbooks it has kept since 2005, and where the imprint is.
 *
 * @package Umblätterer
 */

$umblaetterer_authors = get_users(
	array(
		'has_published_posts' => array( 'post' ),
		'orderby'             => 'post_count',
		'order'               => 'DESC',
		'number'              => 12,
	)
);

/*
 * The recurring yearbooks. Eighteen years of "Best of Feuilleton", "Das
 * Kinojahr" and "Best of US-Serien" sit in the pages table as forty-six
 * separate pages; listed flat they drown the footer, so each series is
 * collapsed to its most recent edition and a count.
 */
$umblaetterer_serien = array(
	'best-of-feuilleton' => __( 'Best of Feuilleton', 'umblaetterer' ),
	'das-kinojahr'       => __( 'Das Kinojahr', 'umblaetterer' ),
	'best-of-us-serien'  => __( 'Best of US-Serien', 'umblaetterer' ),
);

$umblaetterer_jahrbuecher = array();

// One query for every page, then grouped by slug prefix in PHP: three
// get_pages() calls to answer one question would be three too many.
$umblaetterer_pages = get_pages(
	array(
		'sort_column' => 'post_title',
		'sort_order'  => 'DESC',
	)
);

foreach ( $umblaetterer_serien as $umblaetterer_slug => $umblaetterer_label ) {
	$umblaetterer_found = array_values(
		array_filter(
			$umblaetterer_pages,
			static function ( $page ) use ( $umblaetterer_slug ) {
				return 0 === strpos( $page->post_name, $umblaetterer_slug );
			}
		)
	);

	if ( $umblaetterer_found ) {
		$umblaetterer_jahrbuecher[ $umblaetterer_label ] = $umblaetterer_found;
	}
}
?>

<?php if ( $umblaetterer_authors ) : ?>
	<section class="widget colophon__block">
		<h2 class="colophon__title"><?php esc_html_e( 'Das Consortium', 'umblaetterer' ); ?></h2>
		<ul class="rubrik-index">
			<?php foreach ( $umblaetterer_authors as $umblaetterer_author ) : ?>
				<li>
					<span class="rubrik-index__row">
						<a href="<?php echo esc_url( get_author_posts_url( $umblaetterer_author->ID ) ); ?>">
							<?php echo esc_html( $umblaetterer_author->display_name ); ?>
						</a>
						<span class="index-list__leader" aria-hidden="true"></span>
						<span class="rubrik-index__count">
							<?php echo esc_html( number_format_i18n( count_user_posts( $umblaetterer_author->ID, 'post' ) ) ); ?>
						</span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
<?php endif; ?>

<?php if ( $umblaetterer_jahrbuecher ) : ?>
	<section class="widget colophon__block">
		<h2 class="colophon__title"><?php esc_html_e( 'Die Jahrbücher', 'umblaetterer' ); ?></h2>

		<?php foreach ( $umblaetterer_jahrbuecher as $umblaetterer_label => $umblaetterer_pages ) : ?>
			<div class="colophon__serie">
				<p class="colophon__serie-title"><?php echo esc_html( $umblaetterer_label ); ?></p>
				<ul class="colophon__years">
					<?php foreach ( $umblaetterer_pages as $umblaetterer_page ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $umblaetterer_page ) ); ?>">
								<?php
								// The year is the only part of the title that varies.
								echo esc_html( trim( str_replace( $umblaetterer_label, '', $umblaetterer_page->post_title ) ) );
								?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</section>
<?php endif; ?>

<section class="widget colophon__block">
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
		$umblaetterer_ids = array_filter(
			array_map(
				static function ( $slug ) {
					$page = get_page_by_path( $slug );
					return $page ? $page->ID : null;
				},
				array( 'about', 'impressum', 'geloeschte-rubriken' )
			)
		);
		?>
		<ul>
			<?php
			if ( $umblaetterer_ids ) {
				wp_list_pages(
					array(
						'title_li' => '',
						'include'  => implode( ',', $umblaetterer_ids ),
					)
				);
			}
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
</section>
