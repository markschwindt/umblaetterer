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
 * Read the real dimensions of an SVG attachment.
 *
 * WordPress sizes images with getimagesize(), which cannot read SVG, so an
 * uploaded vector is recorded as 1×1. The width and height come from the file
 * itself: its own attributes if it has them, otherwise the viewBox.
 *
 * @param int $attachment_id Attachment to measure.
 * @return array{width: int, height: int}|false
 */
function umblaetterer_svg_dimensions( $attachment_id ) {
	$cached = get_post_meta( $attachment_id, '_umblaetterer_svg_size', true );

	if ( is_array( $cached ) && ! empty( $cached['width'] ) ) {
		return $cached;
	}

	$file = get_attached_file( $attachment_id );

	if ( ! $file || ! file_exists( $file ) ) {
		return false;
	}

	// The opening tag is all that matters; SVGs here run to several kilobytes
	// of path data that nothing below needs to read.
	$head = file_get_contents( $file, false, null, 0, 2048 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	if ( ! $head || ! preg_match( '/<svg\b[^>]*>/i', $head, $tag ) ) {
		return false;
	}

	$width  = 0;
	$height = 0;

	if ( preg_match( '/\bwidth="([\d.]+)/i', $tag[0], $m ) ) {
		$width = (float) $m[1];
	}

	if ( preg_match( '/\bheight="([\d.]+)/i', $tag[0], $m ) ) {
		$height = (float) $m[1];
	}

	if ( ( ! $width || ! $height ) && preg_match( '/viewBox="\s*[\d.-]+[ ,]+[\d.-]+[ ,]+([\d.]+)[ ,]+([\d.]+)/i', $tag[0], $m ) ) {
		$width  = (float) $m[1];
		$height = (float) $m[2];
	}

	if ( ! $width || ! $height ) {
		return false;
	}

	$size = array(
		'width'  => (int) round( $width ),
		'height' => (int) round( $height ),
	);

	update_post_meta( $attachment_id, '_umblaetterer_svg_size', $size );

	return $size;
}

/**
 * Give wp_get_attachment_image() the true size of an SVG.
 *
 * Without this every uploaded vector renders as width="1" height="1", and a
 * rule of `width: auto; height: auto` then collapses it to nothing at all.
 * Only steps in when WordPress has clearly failed to measure, so a plugin that
 * gets it right is left alone.
 *
 * @param array|false $image         Array of image data, or false.
 * @param int         $attachment_id Attachment ID.
 * @return array|false
 */
function umblaetterer_svg_image_src( $image, $attachment_id ) {
	if ( ! is_array( $image ) || 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
		return $image;
	}

	if ( ! empty( $image[1] ) && $image[1] > 1 && ! empty( $image[2] ) && $image[2] > 1 ) {
		return $image;
	}

	$size = umblaetterer_svg_dimensions( $attachment_id );

	if ( $size ) {
		$image[1] = $size['width'];
		$image[2] = $size['height'];
	}

	return $image;
}
add_filter( 'wp_get_attachment_image_src', 'umblaetterer_svg_image_src', 10, 2 );

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
 * Whether the volumes register has already been placed in the colophon.
 *
 * @param bool $set Pass true to record that it has.
 * @return bool
 */
function umblaetterer_jahrgaenge_placed( $set = false ) {
	static $placed = false;

	if ( $set ) {
		$placed = true;
	}

	return $placed;
}

/**
 * Put the volumes register directly before the recent-posts widget.
 *
 * "Die Jahrgänge" is the theme's own block, not a widget, so it would
 * otherwise have to sit wherever the template prints it — at the end of the
 * colophon, a long way from the recent pieces it belongs beside. Rather than
 * splice it into the widgets' markup by hand, which means parsing HTML and
 * hoping no widget contains a stray closing tag, it is pushed in front of that
 * one widget's own `before_widget` through WordPress's own filter.
 *
 * If the recent-posts widget is not in the sidebar there is nothing to hang it
 * from, and footer.php falls back to printing it at the end.
 *
 * @param array $params Parameters for the widget about to be rendered.
 * @return array
 */
function umblaetterer_colophon_order( $params ) {
	if ( empty( $params[0]['id'] ) || 'sidebar-1' !== $params[0]['id'] ) {
		return $params;
	}

	if ( empty( $params[0]['widget_id'] ) || 0 !== strpos( $params[0]['widget_id'], 'recent-posts' ) ) {
		return $params;
	}

	ob_start();
	get_template_part( 'template-parts/colophon', 'jahrgaenge' );
	$params[0]['before_widget'] = ob_get_clean() . $params[0]['before_widget'];

	umblaetterer_jahrgaenge_placed( true );

	return $params;
}
add_filter( 'dynamic_sidebar_params', 'umblaetterer_colophon_order' );

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
