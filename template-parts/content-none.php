<?php
/**
 * "Nothing found" — a blank galley
 *
 * @package Umblätterer
 */

?>

<section class="no-results not-found">
	<p class="page-header__kicker"><?php esc_html_e( 'Fehlanzeige', 'umblaetterer' ); ?></p>

	<h1 class="page-title"><?php esc_html_e( 'Eine leere Spalte', 'umblaetterer' ); ?></h1>

	<div class="error-404__note">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: %s: link to the WordPress new-post screen. */
					__( 'Noch ist nichts gesetzt. <a href="%s">Hier beginnt der erste Beitrag</a>.', 'umblaetterer' ),
					array( 'a' => array( 'href' => array() ) )
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>
			<p><?php esc_html_e( 'Zu dieser Suche findet sich im Archiv nichts. Vielleicht mit weniger Worten noch einmal?', 'umblaetterer' ); ?></p>
			<?php
			get_search_form();

		else :
			?>
			<p><?php esc_html_e( 'An dieser Stelle steht nichts. Der Weg zurück führt über die Suche.', 'umblaetterer' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div>
</section><!-- .no-results -->
