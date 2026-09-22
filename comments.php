<?php
/**
 * Zuschriften — the correspondence column
 *
 * @package Umblätterer
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$umblaetterer_count = (int) get_comments_number();

			printf(
				/* translators: %s: number of letters received. */
				esc_html( _n( '%s Zuschrift', '%s Zuschriften', $umblaetterer_count, 'umblaetterer' ) ),
				esc_html( number_format_i18n( $umblaetterer_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 64,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => esc_html__( '← Ältere Zuschriften', 'umblaetterer' ),
				'next_text' => esc_html__( 'Neuere Zuschriften →', 'umblaetterer' ),
			)
		);
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Die Zuschriften zu diesem Beitrag sind geschlossen.', 'umblaetterer' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => esc_html__( 'Zuschrift senden', 'umblaetterer' ),
			'title_reply_to'       => esc_html__( 'Antwort an %s', 'umblaetterer' ),
			'cancel_reply_link'    => esc_html__( 'Abbrechen', 'umblaetterer' ),
			'label_submit'         => esc_html__( 'Absenden', 'umblaetterer' ),
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Die E-Mail-Adresse wird nicht veröffentlicht.', 'umblaetterer' ) . '</p>',
			'comment_field'        => sprintf(
				'<p class="comment-form-comment"><label for="comment">%s</label><textarea id="comment" name="comment" rows="6" required></textarea></p>',
				esc_html__( 'Ihre Zuschrift', 'umblaetterer' )
			),
		)
	);
	?>

</div><!-- #comments -->
