<?php
/**
 * Der Kopf — the masthead
 *
 * Everything from <!doctype> down to the opening of #content: the opening
 * rule, the wordmark, the dateline bar, and the running head of rubrics.
 *
 * @package Umblätterer
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="reading-rule" id="reading-rule" aria-hidden="true"></div>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Zum Inhalt springen', 'umblaetterer' ); ?></a>

	<header id="masthead" class="site-header">

		<?php // Thick over thin: the rule that opens a newspaper page. ?>
		<div class="site-header__opening" role="presentation"></div>

		<div class="site-branding">
			<?php if ( is_front_page() && is_home() ) : ?>
				<h1 class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php umblaetterer_wordmark(); ?></a>
				</h1>
			<?php else : ?>
				<p class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php umblaetterer_wordmark(); ?></a>
				</p>
			<?php endif; ?>

			<?php
			$umblaetterer_description = get_bloginfo( 'description', 'display' );
			if ( $umblaetterer_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $umblaetterer_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div><!-- .site-branding -->

		<?php
		/*
		 * The dateline bar. Date on the left, issue number in the middle,
		 * motto on the right — the three slots a paper of the period used,
		 * in that order. The issue number is the count of everything
		 * published here since May 2007.
		 */
		?>
		<div class="dateline">
			<span class="dateline__date"><?php echo esc_html( umblaetterer_datum( 'long' ) ); ?></span>
			<span class="dateline__issue">
				<?php
				printf(
					/* translators: %s: running number of published posts. */
					esc_html__( 'Nr. %s', 'umblaetterer' ),
					esc_html( (string) umblaetterer_issue_number() )
				);
				?>
			</span>
			<span class="dateline__motto"><?php esc_html_e( 'Seit Mai 2007', 'umblaetterer' ); ?></span>
		</div>

		<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Rubriken', 'umblaetterer' ); ?>">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				<?php esc_html_e( 'Rubriken', 'umblaetterer' ); ?>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
					'depth'          => 2,
					'container'      => 'div',
					'fallback_cb'    => 'umblaetterer_menu_fallback',
				)
			);
			?>
		</nav><!-- #site-navigation -->

		<div class="masthead-utility">
			<?php get_search_form(); ?>
			<button
				class="edition-toggle"
				type="button"
				id="edition-toggle"
				aria-pressed="false"
			><?php esc_html_e( 'Nachtausgabe', 'umblaetterer' ); ?></button>
		</div>
	</header><!-- #masthead -->
