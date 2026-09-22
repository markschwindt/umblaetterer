<?php
/**
 * Umblätterer functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Umblätterer
 */

if ( ! defined( 'UMBLAETTERER_VERSION' ) ) {
	define( 'UMBLAETTERER_VERSION', '2.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function umblaetterer_setup() {
	load_theme_textdomain( 'umblaetterer', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );

	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Kopfleiste (primary)', 'umblaetterer' ),
			'menu-2' => esc_html__( 'Impressum (footer)', 'umblaetterer' ),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	/*
	 * The wordmark is an inline SVG rather than an <img>, so that it inherits
	 * currentColor and turns with the palette in the night edition. The custom
	 * logo support stays registered so the Customizer can still override it.
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 720,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'umblaetterer_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * @global int $content_width
 */
function umblaetterer_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'umblaetterer_content_width', 640 );
}
add_action( 'after_setup_theme', 'umblaetterer_content_width', 0 );

/**
 * Register widget areas.
 *
 * One area, and it is the foot of the page. The rail beside the listings is
 * not a widget area: it carries the one register the archive is actually
 * navigated by — the rubrics — and that is generated from the taxonomy rather
 * than arranged by hand. Everything else a publication wants to hang off the
 * bottom of its pages, the run of volumes included, goes into the grid below.
 *
 * The id stays 'sidebar-1' so that widgets already placed under the previous
 * theme keep their assignment and simply reappear in the new position.
 */
function umblaetterer_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Fußleiste', 'umblaetterer' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Each widget becomes one ruled cell in the grid at the foot of every page. Short lists sit best here; a widget with a very long list is given extra width automatically.', 'umblaetterer' ),
			'before_widget' => '<section id="%1$s" class="widget colophon__block %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="colophon__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'umblaetterer_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function umblaetterer_scripts() {
	wp_enqueue_style( 'umblaetterer-style', get_stylesheet_uri(), array(), UMBLAETTERER_VERSION );
	wp_style_add_data( 'umblaetterer-style', 'rtl', 'replace' );

	wp_enqueue_script(
		'umblaetterer-script',
		get_template_directory_uri() . '/js/umblaetterer.js',
		array(),
		UMBLAETTERER_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'umblaetterer_scripts' );

/**
 * Preload the two faces that are needed before the first paint.
 *
 * The reading face and the display face both appear above the fold on every
 * template, so they are worth the early hint. Jost is not: it only ever sets
 * kickers, which are small enough that a swap goes unnoticed.
 */
function umblaetterer_preload_fonts() {
	$fonts = array(
		'assets/fonts/spectral-400-normal-latin.woff2',
		'assets/fonts/oldstandard-400-normal-latin.woff2',
	);

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_template_directory_uri() . '/' . $font )
		);
	}
}
add_action( 'wp_head', 'umblaetterer_preload_fonts', 1 );

/**
 * Paint the browser chrome in the paper colour, so the page does not end at a
 * hard white edge on mobile.
 *
 * One value, not a pair keyed to prefers-color-scheme: the ivory is what every
 * reader gets regardless of their system, and the script swaps this out if
 * they ask for the night edition.
 */
function umblaetterer_theme_color() {
	echo '<meta name="theme-color" content="#f4efe1">' . "\n";
}
add_action( 'wp_head', 'umblaetterer_theme_color', 2 );

/**
 * Set the chosen edition before first paint, to avoid a flash of the wrong one.
 *
 * This has to be inline and it has to be in the head: any other arrangement
 * shows the reader an ivory page for one frame before turning it dark.
 */
function umblaetterer_edition_script() {
	?>
	<script>
	( function () {
		try {
			var e = localStorage.getItem( 'umbl-edition' );
			if ( e === 'nacht' || e === 'tag' ) {
				document.documentElement.setAttribute( 'data-edition', e );
			}
		} catch ( err ) {}
	}() );
	</script>
	<?php
}
add_action( 'wp_head', 'umblaetterer_edition_script', 3 );

/**
 * Teasers, cut to length and stripped of two decades of inline markup.
 *
 * @param int         $words   Number of words to keep.
 * @param int|WP_Post $post_id Post to read from. Defaults to the current post.
 * @return string Plain-text teaser, ending in an ellipsis.
 */
function umblaetterer_teaser( $words = 34, $post_id = null ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return '';
	}

	if ( post_password_required( $post ) ) {
		return esc_html__( 'Dieser Beitrag ist geschützt.', 'umblaetterer' );
	}

	$raw = $post->post_excerpt;

	if ( '' === trim( $raw ) ) {
		$raw = $post->post_content;

		/*
		 * Many posts open with a pulled quotation — the house style of this
		 * feuilleton. A teaser made of somebody else's words tells the reader
		 * nothing about the piece, so lead quotations are dropped and the
		 * teaser starts at the author's own first sentence.
		 */
		$raw = preg_replace( '#^\s*<blockquote[^>]*>.*?</blockquote>#is', '', $raw );
		$raw = strip_shortcodes( $raw );
	}

	/*
	 * strip_tags() closes up whatever sat either side of a tag, so a heading
	 * followed by a paragraph comes out as "WienDas Café…". Block boundaries
	 * become spaces first; inline tags are left to close up, which is what
	 * they are for.
	 */
	$raw = preg_replace(
		'#</?(?:p|div|br|h[1-6]|li|ul|ol|dl|dt|dd|blockquote|table|tr|td|th|section|article|figure|figcaption|hr|pre|address)\b[^>]*>#i',
		' ',
		$raw
	);

	$raw = wp_strip_all_tags( $raw, true );
	$raw = html_entity_decode( $raw, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

	// The house style opens pieces with typed rules made of underscores and
	// macrons; they are ornament, and they are not a teaser.
	$raw = preg_replace( '/[_¯]{2,}/u', ' ', $raw );
	$raw = str_replace( "\xc2\xa0", ' ', $raw );
	$raw = trim( preg_replace( '/\s+/u', ' ', $raw ) );

	return wp_trim_words( $raw, $words, '&nbsp;…' );
}

/**
 * The post's primary rubric.
 *
 * Posts here carry several categories; the standing column is almost always
 * the one with the fewest posts in it, because "Buchbuch" (514 posts) is a
 * genre while "Raddatz-Festwochen" (29) is the actual occasion. The rarer
 * term is the more informative kicker.
 *
 * @param int|WP_Post $post_id Post to read from.
 * @return WP_Term|null
 */
function umblaetterer_rubrik( $post_id = null ) {
	$terms = get_the_category( $post_id ? get_post( $post_id )->ID : null );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return null;
	}

	usort(
		$terms,
		static function ( $a, $b ) {
			return $a->count <=> $b->count;
		}
	);

	return $terms[0];
}

/**
 * Estimated reading time, in minutes.
 *
 * 200 words a minute is the usual figure for English. German prose of this
 * kind runs slower, so 180 is closer to honest.
 *
 * @param int|WP_Post $post_id Post to measure.
 * @return int Minutes, never less than one.
 */
function umblaetterer_reading_minutes( $post_id = null ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return 1;
	}

	$text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );

	// str_word_count() splits German words at every umlaut, which inflates
	// every figure on this site by a third. Count letter-runs instead.
	$words = preg_match_all( '/\p{L}[\p{L}\p{M}\x{2019}\'-]*/u', $text );

	return max( 1, (int) ceil( $words / 180 ) );
}

/**
 * The companion column that runs beside the lead story.
 *
 * First choice is the lead's own standing column, which is what a reader of a
 * feuilleton actually wants next. But the rubric is chosen for being rare, and
 * a rare rubric sometimes holds exactly one piece — so the column falls back
 * to the paper's oldest trick instead: what stood here ten years ago.
 *
 * @param int $lead_id The post occupying the lead slot.
 * @return array{title: string, note: string, posts: WP_Post[]}
 */
function umblaetterer_lead_companion( $lead_id ) {
	$rubrik = umblaetterer_rubrik( $lead_id );

	if ( $rubrik ) {
		$kin = get_posts(
			array(
				'category'            => $rubrik->term_id,
				'numberposts'         => 4,
				'exclude'             => array( $lead_id ),
				'ignore_sticky_posts' => true,
			)
		);

		if ( $kin ) {
			return array(
				/* translators: %s: name of the rubric. */
				'title' => sprintf( esc_html__( 'Mehr aus %s', 'umblaetterer' ), $rubrik->name ),
				'note'  => sprintf(
					/* translators: %s: number of posts in the rubric. */
					esc_html( _n( '%s Beitrag', '%s Beiträge', $rubrik->count, 'umblaetterer' ) ),
					number_format_i18n( $rubrik->count )
				),
				'posts' => $kin,
			);
		}
	}

	// Ten years back, counted from the lead's own date rather than from today,
	// so the column stays true even when the paper has not appeared in a while.
	$then = (int) get_the_date( 'Y', $lead_id ) - 10;

	$archive = get_posts(
		array(
			'numberposts'         => 4,
			'ignore_sticky_posts' => true,
			'date_query'          => array( array( 'year' => $then ) ),
		)
	);

	if ( $archive ) {
		return array(
			'title' => esc_html__( 'Vor zehn Jahren', 'umblaetterer' ),
			'note'  => (string) $then,
			'posts' => $archive,
		);
	}

	return array(
		'title' => esc_html__( 'Aus dem Archiv', 'umblaetterer' ),
		'note'  => '',
		'posts' => get_posts(
			array(
				'numberposts'         => 4,
				'offset'              => 4,
				'ignore_sticky_posts' => true,
			)
		),
	);
}

/**
 * The issue number: how many pieces the paper has published to date.
 *
 * Cached for a day — it is a masthead ornament, not a live counter, and it
 * should not cost a COUNT(*) over 1,600 rows on every page view.
 *
 * @return int
 */
function umblaetterer_issue_number() {
	$number = get_transient( 'umblaetterer_issue_number' );

	if ( false === $number ) {
		$counts = wp_count_posts( 'post' );
		$number = isset( $counts->publish ) ? (int) $counts->publish : 0;
		set_transient( 'umblaetterer_issue_number', $number, DAY_IN_SECONDS );
	}

	return (int) $number;
}

/**
 * Drop the cached issue number whenever the count could have changed.
 */
function umblaetterer_flush_issue_number() {
	delete_transient( 'umblaetterer_issue_number' );
}
add_action( 'transition_post_status', 'umblaetterer_flush_issue_number' );
add_action( 'deleted_post', 'umblaetterer_flush_issue_number' );

/**
 * The German date, written out the way a dateline writes it.
 *
 * WordPress's own date_i18n() depends on the site locale being German, which
 * cannot be assumed of a local install, so the names are carried here.
 *
 * @param string $format 'long' for "Montag, 22. September 2026", 'short' for "22. Sept. 2026".
 * @param mixed  $when   Anything strtotime() understands. Defaults to now.
 * @return string
 */
function umblaetterer_datum( $format = 'long', $when = null ) {
	$time = null === $when ? current_time( 'timestamp' ) : ( is_numeric( $when ) ? (int) $when : strtotime( $when ) );

	$days = array(
		esc_html__( 'Sonntag', 'umblaetterer' ),
		esc_html__( 'Montag', 'umblaetterer' ),
		esc_html__( 'Dienstag', 'umblaetterer' ),
		esc_html__( 'Mittwoch', 'umblaetterer' ),
		esc_html__( 'Donnerstag', 'umblaetterer' ),
		esc_html__( 'Freitag', 'umblaetterer' ),
		esc_html__( 'Samstag', 'umblaetterer' ),
	);

	$months = array(
		1  => 'Januar',
		2  => 'Februar',
		3  => 'März',
		4  => 'April',
		5  => 'Mai',
		6  => 'Juni',
		7  => 'Juli',
		8  => 'August',
		9  => 'September',
		10 => 'Oktober',
		11 => 'November',
		12 => 'Dezember',
	);

	$day   = (int) wp_date( 'j', $time );
	$month = $months[ (int) wp_date( 'n', $time ) ];
	$year  = wp_date( 'Y', $time );

	if ( 'short' === $format ) {
		$abbr = in_array( $month, array( 'März', 'Mai', 'Juni', 'Juli' ), true ) ? $month : mb_substr( $month, 0, 4 ) . '.';

		return sprintf( '%d. %s %s', $day, $abbr, $year );
	}

	return sprintf( '%s, %d. %s %s', $days[ (int) wp_date( 'w', $time ) ], $day, $month, $year );
}

/**
 * Let the theme's own listings ask for the rubric register without repeating
 * the query arguments in four templates.
 *
 * @param int $number How many rubrics to return.
 * @return WP_Term[]
 */
function umblaetterer_rubriken( $number = 24 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => $number,
			'hide_empty' => true,
		)
	);

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Whether the current template puts the rail beside its content.
 *
 * The rubric register lives in the rail on the front page and on listings. On
 * a single piece there is no rail, so the foot of the page has to carry it
 * instead — otherwise a reader who arrives on an article from a search engine
 * is given no way into the other sixteen hundred.
 *
 * @return bool
 */
function umblaetterer_has_rail() {
	return is_home() && 1 === max( 1, (int) get_query_var( 'paged' ) )
		|| is_archive()
		|| is_search();
}

/**
 * The run of volumes, set as a register.
 *
 * wp_get_archives() emits "<a>2016</a>&nbsp;(21)", which is a footnote, not an
 * index entry. The same rows are re-cut here with a dot leader so the years
 * read as the same kind of object as the rubrics beside them.
 *
 * @param int $limit How many years to show. 0 for all.
 */
function umblaetterer_jahrgaenge( $limit = 0 ) {
	$markup = wp_get_archives(
		array(
			'type'            => 'yearly',
			'show_post_count' => true,
			'limit'           => $limit ? $limit : '',
			'echo'            => 0,
		)
	);

	echo preg_replace( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'#<li>\s*(<a[^>]*>.*?</a>)\s*&nbsp;\((\d+)\)\s*</li>#s',
		'<li><span class="rubrik-index__row">$1'
			. '<span class="index-list__leader" aria-hidden="true"></span>'
			. '<span class="rubrik-index__count">$2</span></span></li>',
		$markup
	);
}

/**
 * Give the reader more than ten posts per archive page.
 *
 * With 1,600 posts and 105 rubrics, ten-at-a-time turns "Buchbuch" into
 * fifty-two pages of paging. The listing is a register of titles, so it can
 * carry far more per page than a card grid could.
 *
 * @param WP_Query $query The query about to run.
 */
function umblaetterer_archive_length( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// Feeds keep whatever the site has set under Settings → Reading. Long-time
	// subscribers should not have their reader re-fetch six extra items
	// because the front page wanted a longer register.
	if ( $query->is_feed() ) {
		return;
	}

	if ( $query->is_archive() || $query->is_search() ) {
		$query->set( 'posts_per_page', 30 );
	} elseif ( $query->is_home() ) {
		// The front page is one lead, three columns, and a register beneath.
		$query->set( 'posts_per_page', 16 );
	}
}
add_action( 'pre_get_posts', 'umblaetterer_archive_length' );

/**
 * Wrap oEmbeds so they can be constrained to the measure.
 *
 * @param string $html The embed markup.
 * @return string
 */
function umblaetterer_embed_wrapper( $html ) {
	return '<div class="entry-embed">' . $html . '</div>';
}
add_filter( 'embed_oembed_html', 'umblaetterer_embed_wrapper' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}
