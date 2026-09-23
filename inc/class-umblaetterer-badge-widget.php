<?php
/**
 * Das Badge — one badge in the footer band, as a widget
 *
 * The badges were four hard-coded links in footer.php. They are the kind of
 * thing that changes without a developer: an award won, an account moved, a
 * nomination that stops being current. One widget is one badge; the widget
 * area they sit in is an ordinary sortable list, so they can be added,
 * reordered and retired from Appearance → Widgets or the Customizer.
 *
 * @package Umblätterer
 */

/**
 * A single badge: a mark, an optional kicker, two lines of caption, a link.
 */
class Umblaetterer_Badge_Widget extends WP_Widget {

	/**
	 * The marks the theme can draw itself, as an alternative to an upload.
	 *
	 * @return array<string, string>
	 */
	public static function marks() {
		return array(
			'image'   => __( 'Bild aus der Mediathek', 'umblaetterer' ),
			'bluesky' => __( 'Bluesky-Falter', 'umblaetterer' ),
			'pin'     => __( 'Kartennadel', 'umblaetterer' ),
			'none'    => __( 'ohne Zeichen', 'umblaetterer' ),
		);
	}

	/**
	 * Register the widget.
	 */
	public function __construct() {
		parent::__construct(
			'umblaetterer_badge',
			__( 'Umblätterer: Badge', 'umblaetterer' ),
			array(
				'classname'                   => 'umblaetterer-badge',
				'description'                 => __( 'Ein Zeichen mit Unterschrift für die Leiste über dem Impressum — ein Preis, ein Verweis, eine Karte.', 'umblaetterer' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Default values for a new, unconfigured badge.
	 *
	 * @return array<string, string>
	 */
	protected function defaults() {
		return array(
			'mark'     => 'image',
			'image_id' => 0,
			'label'    => '',
			'line1'    => '',
			'line2'    => '',
			'url'      => '',
		);
	}

	/**
	 * Front-end output: exactly one .badges__item.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved widget settings.
	 */
	public function widget( $args, $instance ) {
		$i = wp_parse_args( (array) $instance, $this->defaults() );

		$mark  = isset( $i['mark'] ) ? $i['mark'] : 'image';
		$url   = trim( (string) $i['url'] );
		$label = trim( (string) $i['label'] );
		$line1 = trim( (string) $i['line1'] );
		$line2 = trim( (string) $i['line2'] );

		$figure = '';

		if ( 'image' === $mark && $i['image_id'] ) {
			/*
			 * alt is deliberately empty: the caption below sits inside the same
			 * link and already names the thing, so a screen reader announcing
			 * both would say it twice.
			 */
			$figure = wp_get_attachment_image(
				(int) $i['image_id'],
				'full',
				false,
				array(
					'class'    => 'badges__mark badges__mark--vector',
					'alt'      => '',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		} elseif ( in_array( $mark, array( 'bluesky', 'pin' ), true ) ) {
			$figure = umblaetterer_badge_mark( $mark );
		}

		if ( ! $figure && ! $label && ! $line1 && ! $line2 ) {
			return;
		}

		$tag  = $url ? 'a' : 'div';
		$attr = '';

		if ( $url ) {
			$attr = ' href="' . esc_url( $url ) . '"';

			// Anything off this site opens with the usual precaution.
			if ( false === strpos( $url, home_url() ) ) {
				$attr .= ' rel="noopener"';
			}
		}

		printf( '<%1$s class="badges__item"%2$s>', $tag, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.

		echo $figure; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image / theme SVG.

		if ( $label ) {
			printf( '<span class="badges__label">%s</span>', esc_html( $label ) );
		}

		if ( $line1 || $line2 ) {
			echo '<span class="badges__text">';
			echo esc_html( $line1 );

			if ( $line1 && $line2 ) {
				echo '<br>';
			}

			echo esc_html( $line2 );
			echo '</span>';
		}

		printf( '</%s>', $tag ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal tag name.
	}

	/**
	 * The editing form.
	 *
	 * @param array $instance Saved widget settings.
	 */
	public function form( $instance ) {
		$i        = wp_parse_args( (array) $instance, $this->defaults() );
		$image_id = (int) $i['image_id'];
		$preview  = $image_id ? wp_get_attachment_image( $image_id, array( 80, 80 ), false, array( 'style' => 'max-width:80px;height:auto;' ) ) : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'mark' ) ); ?>"><?php esc_html_e( 'Zeichen', 'umblaetterer' ); ?></label>
			<select class="widefat umbl-badge-mark" id="<?php echo esc_attr( $this->get_field_id( 'mark' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mark' ) ); ?>">
				<?php foreach ( self::marks() as $value => $title ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $i['mark'], $value ); ?>><?php echo esc_html( $title ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<div class="umbl-badge-image" <?php echo 'image' === $i['mark'] ? '' : 'style="display:none"'; ?>>
			<p class="umbl-badge-preview"><?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<p>
				<input type="hidden" class="umbl-badge-id" id="<?php echo esc_attr( $this->get_field_id( 'image_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_id' ) ); ?>" value="<?php echo esc_attr( $image_id ); ?>">
				<button type="button" class="button umbl-badge-select"><?php esc_html_e( 'Bild wählen', 'umblaetterer' ); ?></button>
				<button type="button" class="button-link umbl-badge-remove" <?php echo $image_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'entfernen', 'umblaetterer' ); ?></button>
			</p>
		</div>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'label' ) ); ?>"><?php esc_html_e( 'Kennzeile (optional)', 'umblaetterer' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label' ) ); ?>" value="<?php echo esc_attr( $i['label'] ); ?>" placeholder="<?php esc_attr_e( 'Folgen', 'umblaetterer' ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'line1' ) ); ?>"><?php esc_html_e( 'Unterschrift, erste Zeile', 'umblaetterer' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'line1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'line1' ) ); ?>" value="<?php echo esc_attr( $i['line1'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'line2' ) ); ?>"><?php esc_html_e( 'Unterschrift, zweite Zeile', 'umblaetterer' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'line2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'line2' ) ); ?>" value="<?php echo esc_attr( $i['line2'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php esc_html_e( 'Verweis', 'umblaetterer' ); ?></label>
			<input class="widefat" type="url" id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>" value="<?php echo esc_attr( $i['url'] ); ?>" placeholder="https://">
		</p>
		<?php
	}

	/**
	 * Sanitise on save.
	 *
	 * @param array $new Submitted values.
	 * @param array $old Previously saved values.
	 * @return array
	 */
	public function update( $new, $old ) {
		$marks = array_keys( self::marks() );

		return array(
			'mark'     => in_array( $new['mark'], $marks, true ) ? $new['mark'] : 'image',
			'image_id' => isset( $new['image_id'] ) ? absint( $new['image_id'] ) : 0,
			'label'    => sanitize_text_field( $new['label'] ),
			'line1'    => sanitize_text_field( $new['line1'] ),
			'line2'    => sanitize_text_field( $new['line2'] ),
			'url'      => esc_url_raw( trim( $new['url'] ) ),
		);
	}
}
