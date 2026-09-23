<?php
/**
 * Custom template tags for this theme
 *
 * The small typographic parts a newspaper page is assembled from: the rubric
 * above a headline, the dateline below it, the rules between sections.
 *
 * @package Umblätterer
 */

if ( ! function_exists( 'umblaetterer_kicker' ) ) :
	/**
	 * The rubric above a headline.
	 *
	 * @param array $args {
	 *     @type bool   $flag  Show the "Neu" flag in the second ink.
	 *     @type string $class Extra classes for the wrapper.
	 * }
	 */
	function umblaetterer_kicker( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'flag'  => false,
				'class' => '',
			)
		);

		$rubrik = umblaetterer_rubrik();

		if ( ! $rubrik && ! $args['flag'] ) {
			return;
		}

		printf( '<div class="kicker %s">', esc_attr( $args['class'] ) );

		if ( $args['flag'] ) {
			printf( '<span class="flag-new">%s</span>', esc_html__( 'Neu', 'umblaetterer' ) );
		}

		if ( $rubrik ) {
			printf(
				'<a href="%s" rel="category">%s</a>',
				esc_url( get_category_link( $rubrik ) ),
				esc_html( $rubrik->name )
			);
		}

		echo '</div>';
	}
endif;

if ( ! function_exists( 'umblaetterer_dateline' ) ) :
	/**
	 * The line under a headline: who wrote it, when, and how long it is.
	 *
	 * @param array $args {
	 *     @type bool $author  Show the byline.
	 *     @type bool $date    Show the date.
	 *     @type bool $reading Show the reading time.
	 *     @type bool $comments Show the number of letters received.
	 * }
	 */
	function umblaetterer_dateline( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'author'   => true,
				'date'     => true,
				'reading'  => false,
				'comments' => false,
			)
		);

		$parts = array();

		if ( $args['author'] ) {
			$parts[] = sprintf(
				'<span class="byline"><a class="url fn n" href="%s" rel="author">%s</a></span>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_html( get_the_author() )
			);
		}

		if ( $args['date'] ) {
			$parts[] = sprintf(
				'<time class="entry-date published" datetime="%s">%s</time>',
				esc_attr( get_the_date( DATE_W3C ) ),
				esc_html( umblaetterer_datum( 'short', get_the_date( 'U' ) ) )
			);
		}

		if ( $args['reading'] ) {
			$parts[] = sprintf(
				/* translators: %d: number of minutes. */
				esc_html__( '%d Min. Lektüre', 'umblaetterer' ),
				umblaetterer_reading_minutes()
			);
		}

		if ( $args['comments'] && ( comments_open() || get_comments_number() ) ) {
			$count = (int) get_comments_number();

			$parts[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( get_permalink() . '#comments' ),
				$count
					? esc_html(
						sprintf(
							/* translators: %d: number of comments. */
							_n( '%d Zuschrift', '%d Zuschriften', $count, 'umblaetterer' ),
							$count
						)
					)
					: esc_html__( 'Zuschrift senden', 'umblaetterer' )
			);
		}

		if ( empty( $parts ) ) {
			return;
		}

		echo '<div class="dateline-line">';
		echo wp_kses_post( implode( '<span class="sep" aria-hidden="true">·</span>', $parts ) );
		echo '</div>';
	}
endif;

if ( ! function_exists( 'umblaetterer_index_entry' ) ) :
	/**
	 * One line of a register: title, dot leader, date.
	 *
	 * This is the workhorse of the theme. With 1,618 posts across 105 rubrics,
	 * most listing views are not a feed but a table of contents, and a table
	 * of contents is the form that carries the most titles legibly.
	 *
	 * @param array $args {
	 *     @type bool $rubrik  Show the rubric beneath the title.
	 *     @type bool $author  Show the author beside the date.
	 *     @type bool $teaser  Show a short teaser beneath the title.
	 * }
	 */
	function umblaetterer_index_entry( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'rubrik' => true,
				'author' => false,
				'teaser' => false,
			)
		);

		$rubrik = $args['rubrik'] ? umblaetterer_rubrik() : null;
		?>
		<li>
			<?php
			/*
			 * The dateline sits outside the row, not inside it. Inside, it was
			 * a flex item whose position depended on how much room the title
			 * left — ranged right on a short title, dropped below and still
			 * ranged right when the leader wrapped with it, left only when the
			 * leader stayed behind. Out here it is a block, and a block begins
			 * at the left margin whatever the title does.
			 */
			?>
			<div class="index-list__row">
				<span class="index-list__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</span>
				<span class="index-list__leader" aria-hidden="true"></span>
			</div>

			<div class="index-list__meta">
				<?php if ( $args['author'] ) : ?>
					<?php echo esc_html( get_the_author() ); ?> ·
				<?php endif; ?>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
					<?php echo esc_html( umblaetterer_datum( 'short', get_the_date( 'U' ) ) ); ?>
				</time>
			</div>

			<?php if ( $rubrik ) : ?>
				<div class="index-list__sub">
					<a href="<?php echo esc_url( get_category_link( $rubrik ) ); ?>">
						<?php echo esc_html( $rubrik->name ); ?>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( $args['teaser'] ) : ?>
				<p class="brief__teaser"><?php echo esc_html( umblaetterer_teaser( 22 ) ); ?></p>
			<?php endif; ?>
		</li>
		<?php
	}
endif;

if ( ! function_exists( 'umblaetterer_entry_footer' ) ) :
	/**
	 * The rubrics a piece was filed under, plus the edit link.
	 */
	function umblaetterer_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories = get_the_category_list( '<span class="sep" aria-hidden="true">·</span>' );
			$tags       = get_the_tag_list( '', '<span class="sep" aria-hidden="true">·</span>' );

			if ( $categories || $tags ) {
				echo '<div class="entry-taxonomy">';

				if ( $categories ) {
					printf(
						'<span class="entry-taxonomy__label">%s</span>%s',
						esc_html__( 'Rubriken', 'umblaetterer' ),
						wp_kses_post( $categories )
					);
				}

				if ( $tags ) {
					printf(
						'<span class="entry-taxonomy__label">%s</span>%s',
						esc_html__( 'Schlagworte', 'umblaetterer' ),
						wp_kses_post( $tags )
					);
				}

				echo '</div>';
			}
		}

		edit_post_link(
			esc_html__( 'Korrigieren', 'umblaetterer' ),
			'<div class="entry-taxonomy"><span class="edit-link">',
			'</span></div>'
		);
	}
endif;

if ( ! function_exists( 'umblaetterer_badge_mark' ) ) :
	/**
	 * One of the marks the theme draws itself, for a badge widget.
	 *
	 * Kept here rather than uploaded so they inherit currentColor and turn with
	 * the palette. The butterfly is Bluesky's own, from simple-icons (CC0); the
	 * pin is drawn here, so it needs no attribution.
	 *
	 * @param string $mark 'bluesky' or 'pin'.
	 * @return string Inline SVG, or an empty string for anything unrecognised.
	 */
	function umblaetterer_badge_mark( $mark ) {
		$paths = array(
			'bluesky' => 'M12 10.8c-1.087-2.114-4.046-6.053-6.798-7.995C2.566.944 1.561 1.266.902 1.565.139 1.908 0 3.08 0 3.768c0 .69.378 5.65.624 6.479.815 2.736 3.713 3.66 6.383 3.364.136-.02.275-.039.415-.056-.138.022-.276.04-.415.056-3.912.58-7.387 2.005-2.83 7.078 5.013 5.19 6.87-1.113 7.823-4.308.953 3.195 2.05 9.271 7.733 4.308 4.267-4.308 1.172-6.498-2.74-7.078a8.741 8.741 0 0 1-.415-.056c.14.017.279.036.415.056 2.67.297 5.568-.628 6.383-3.364.246-.828.624-5.79.624-6.478 0-.69-.139-1.861-.902-2.206-.659-.298-1.664-.62-4.3 1.24C16.046 4.748 13.087 8.687 12 10.8Z',
			'pin'     => 'M12 2.25a6.75 6.75 0 0 0-6.75 6.75c0 4.79 5.92 11.72 6.17 12.01a.76.76 0 0 0 1.16 0c.25-.29 6.17-7.22 6.17-12.01A6.75 6.75 0 0 0 12 2.25Zm0 9.4a2.65 2.65 0 1 1 0-5.3 2.65 2.65 0 0 1 0 5.3Z',
		);

		if ( empty( $paths[ $mark ] ) ) {
			return '';
		}

		return sprintf(
			'<svg class="badges__mark badges__mark--icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="%s"/></svg>',
			$paths[ $mark ]
		);
	}
endif;

if ( ! function_exists( 'umblaetterer_fleuron' ) ) :
	/**
	 * A rule with a printer's flower set into it.
	 *
	 * @param string $mark The ornament. Defaults to an aldus leaf.
	 */
	function umblaetterer_fleuron( $mark = '❧' ) {
		printf(
			'<div class="rule--fleuron" role="separator"><span aria-hidden="true">%s</span></div>',
			esc_html( $mark )
		);
	}
endif;

if ( ! function_exists( 'umblaetterer_wordmark' ) ) :
	/**
	 * The blackletter wordmark, inlined.
	 *
	 * Inlined rather than linked so that it inherits currentColor: the logo
	 * file is a fixed navy, which would strand it on the page in the night
	 * edition. A Customizer logo, if one is set, wins.
	 */
	function umblaetterer_wordmark() {
		if ( has_custom_logo() ) {
			the_custom_logo();
			return;
		}

		$path = get_template_directory() . '/assets/' . UMBLAETTERER_WORDMARK;

		if ( ! file_exists( $path ) ) {
			printf(
				'<span class="site-logo site-logo--fallback">%s</span>',
				esc_html( get_bloginfo( 'name' ) )
			);
			return;
		}

		$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		// Drop the XML prolog, and the embedded fill so currentColor can take over.
		$svg = preg_replace( '/<\?xml.*?\?>/', '', $svg );
		$svg = preg_replace( '#<defs>.*?</defs>#s', '', $svg );
		$svg = str_replace(
			'<svg ',
			'<svg class="site-logo" role="img" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '" ',
			$svg
		);

		/*
		 * Number the letters in document order.
		 *
		 * The fourteen paths are the fourteen letters, drawn left to right, but
		 * Illustrator has them in two groups — "Der" and "Umblätterer" — so a
		 * CSS :nth-child stagger would restart counting at the U. Each path
		 * carries its own index instead, and the stylesheet multiplies it into
		 * an animation delay.
		 */
		$umblaetterer_letter = 0;

		$svg = preg_replace_callback(
			'/<path\b/',
			static function () use ( &$umblaetterer_letter ) {
				return sprintf( '<path style="--i:%d"', $umblaetterer_letter++ );
			},
			$svg
		);

		echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'umblaetterer_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 */
	function umblaetterer_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) {
			echo '<figure class="post-thumbnail">';
			the_post_thumbnail( 'large' );
			echo '</figure>';
			return;
		}

		printf(
			'<a class="post-thumbnail" href="%s" aria-hidden="true" tabindex="-1">%s</a>',
			esc_url( get_permalink() ),
			get_the_post_thumbnail( null, 'medium' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
endif;

if ( ! function_exists( 'umblaetterer_posted_on' ) ) :
	/**
	 * Kept for compatibility with anything still calling the Underscores tag.
	 */
	function umblaetterer_posted_on() {
		umblaetterer_dateline( array( 'author' => false ) );
	}
endif;

if ( ! function_exists( 'umblaetterer_posted_by' ) ) :
	/**
	 * Kept for compatibility with anything still calling the Underscores tag.
	 */
	function umblaetterer_posted_by() {
		umblaetterer_dateline( array( 'date' => false ) );
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;
