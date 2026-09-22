<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Umblätterer
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function umblaetterer_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'umblaetterer_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function umblaetterer_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'umblaetterer_pingback_header' );

/**
 * A running head for sites that have not yet assigned a menu.
 *
 * Rather than the usual "here is every page you own" fallback, this prints the
 * standing columns the paper actually runs, which is both more useful and
 * closer to what the masthead of a feuilleton would carry.
 */
function umblaetterer_menu_fallback() {
	$items = array();

	$front = array(
		home_url( '/' ) => esc_html__( 'Titelseite', 'umblaetterer' ),
	);

	foreach ( $front as $url => $label ) {
		$items[] = sprintf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	foreach ( umblaetterer_rubriken( 6 ) as $term ) {
		$items[] = sprintf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( get_category_link( $term ) ),
			esc_html( $term->name )
		);
	}

	$about = get_page_by_path( 'about' );

	if ( $about ) {
		$items[] = sprintf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( get_permalink( $about ) ),
			esc_html__( 'Über uns', 'umblaetterer' )
		);
	}

	printf( '<ul id="primary-menu" class="menu">%s</ul>', implode( '', $items ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Use the theme's own vocabulary in the archive title.
 *
 * WordPress prefixes archive titles with "Category:" and friends. On a page
 * that already labels its own furniture, the prefix is noise.
 *
 * @param string $title The archive title.
 * @return string
 */
function umblaetterer_archive_title( $title ) {
	if ( is_category() || is_tag() || is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
	} elseif ( is_year() ) {
		$title = get_the_date( 'Y' );
	} elseif ( is_month() ) {
		$title = umblaetterer_datum( 'long', get_the_date( 'U' ) );
		$title = preg_replace( '/^.*?, \d+\. /', '', $title );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'umblaetterer_archive_title' );

/**
 * The label that sits above an archive title, naming what kind of archive it is.
 *
 * @return string
 */
function umblaetterer_archive_kicker() {
	if ( is_category() ) {
		return esc_html__( 'Rubrik', 'umblaetterer' );
	}

	if ( is_tag() ) {
		return esc_html__( 'Schlagwort', 'umblaetterer' );
	}

	if ( is_author() ) {
		return esc_html__( 'Aus der Feder von', 'umblaetterer' );
	}

	if ( is_year() || is_month() || is_day() ) {
		return esc_html__( 'Jahrgang', 'umblaetterer' );
	}

	if ( is_search() ) {
		return esc_html__( 'Im Archiv gefunden', 'umblaetterer' );
	}

	return esc_html__( 'Archiv', 'umblaetterer' );
}

/**
 * Hyphenation needs a language, and a lot of this theme's justified type is
 * German. Make sure the document says so even if the install does not.
 *
 * @param string $output The language attributes.
 * @return string
 */
function umblaetterer_language_attributes( $output ) {
	if ( false === strpos( $output, 'lang=' ) ) {
		$output .= ' lang="de"';
	}

	return $output;
}
add_filter( 'language_attributes', 'umblaetterer_language_attributes' );

/**
 * Trim the more-link to the theme's wording.
 *
 * @return string
 */
function umblaetterer_more_link() {
	return sprintf(
		' <a class="more-link" href="%s">%s</a>',
		esc_url( get_permalink() ),
		esc_html__( 'Weiterblättern →', 'umblaetterer' )
	);
}
add_filter( 'the_content_more_link', 'umblaetterer_more_link' );
