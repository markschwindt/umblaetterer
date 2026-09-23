<?php
/**
 * Die Auszeichnungen — the badges the theme ships with
 *
 * Used only while the "Auszeichnungen" widget area is empty. Add a badge
 * widget and this steps aside entirely; the four below are then a record of
 * what the band held when it was still hard-coded.
 *
 * @package Umblätterer
 */

/*
 * Die Auszeichnungen — the badges above the imprint.
 *
 * The awards, the pointer to the Bluesky account and the Google Earth tour
 * of the Kaffeehäuser: the matter a paper prints in its own cause, set
 * between the colophon and the imprint where a masthead block would sit.
 * The two destinations that are not obvious — the Grimme nomination and the
 * Best-of page the mole belongs to — are the ones the About page already
 * links to, so they stay in step with it.
 */
$umblaetterer_maulwurf = get_page_by_path('best-of-feuilleton-2025');
?>


		<a class="badges__item" href="https://bsky.app/profile/umblaetterer.bsky.social" rel="me noopener">
			<?php // Bluesky's own mark, from simple-icons (CC0). Inlined so it takes currentColor. ?>
			<svg class="badges__mark badges__mark--icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path
					d="M12 10.8c-1.087-2.114-4.046-6.053-6.798-7.995C2.566.944 1.561 1.266.902 1.565.139 1.908 0 3.08 0 3.768c0 .69.378 5.65.624 6.479.815 2.736 3.713 3.66 6.383 3.364.136-.02.275-.039.415-.056-.138.022-.276.04-.415.056-3.912.58-7.387 2.005-2.83 7.078 5.013 5.19 6.87-1.113 7.823-4.308.953 3.195 2.05 9.271 7.733 4.308 4.267-4.308 1.172-6.498-2.74-7.078a8.741 8.741 0 0 1-.415-.056c.14.017.279.036.415.056 2.67.297 5.568-.628 6.383-3.364.246-.828.624-5.79.624-6.478 0-.69-.139-1.861-.902-2.206-.659-.298-1.664-.62-4.3 1.24C16.046 4.748 13.087 8.687 12 10.8Z" />
			</svg>
			<span class="badges__label"><?php esc_html_e('Folgen', 'umblaetterer'); ?></span>
			<span class="badges__text">
				<?php esc_html_e('Der Umblätterer', 'umblaetterer'); ?><br>
				<?php esc_html_e('auf Bluesky', 'umblaetterer'); ?>
			</span>
		</a>

		<a class="badges__item"
			href="<?php echo esc_url($umblaetterer_maulwurf ? get_permalink($umblaetterer_maulwurf) : home_url('/best-of-feuilleton-2025/')); ?>">
			<?php
			/*
			 * Vector, and transparent: it needs none of the blend the raster
			 * badge beside it needs to lose its white box. The alt is empty
			 * because the caption below already names the award, and a screen
			 * reader would otherwise read it twice inside one link.
			 */
			?>
			<img class="badges__mark badges__mark--vector"
				src="<?php echo esc_url(get_template_directory_uri() . '/assets/the-golden-mole.svg'); ?>"
				width="283" height="333" loading="lazy" decoding="async"
				alt="">
			<span class="badges__text">
				<?php esc_html_e('Der Goldene Maulwurf', 'umblaetterer'); ?><br>
				<?php esc_html_e('Best of Feuilleton 2025', 'umblaetterer'); ?>
			</span>
		</a>

		<a class="badges__item"
			href="https://www.grimme-online-award.de/archiv/2010/nominierte/n/d/der-umblaetterer-in-der-halbwelt-des-feuilletons"
			rel="noopener">
			<?php
			/*
			 * The alt is empty on purpose. The caption below now says what the
			 * badge says, and a screen reader that announced both would read
			 * the same award twice inside one link.
			 */
			?>
			<img class="badges__mark badges__mark--vector"
				src="<?php echo esc_url(get_template_directory_uri() . '/assets/grimme-online-awards.svg'); ?>"
				width="97" height="109" loading="lazy" decoding="async" alt="">
			<span class="badges__text">
				<?php esc_html_e('Grimme Online Award', 'umblaetterer'); ?><br>
				<?php esc_html_e('Nominee 2010', 'umblaetterer'); ?>
			</span>
		</a>

		<a class="badges__item" href="https://www.umblaetterer.de/wp-content/uploads/Kaffeehaus_des_Monats.kmz">
			<?php // A pin, drawn here rather than borrowed, so it needs no attribution. ?>
			<svg class="badges__mark badges__mark--icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path
					d="M12 2.25a6.75 6.75 0 0 0-6.75 6.75c0 4.79 5.92 11.72 6.17 12.01a.76.76 0 0 0 1.16 0c.25-.29 6.17-7.22 6.17-12.01A6.75 6.75 0 0 0 12 2.25Zm0 9.4a2.65 2.65 0 1 1 0-5.3 2.65 2.65 0 0 1 0 5.3Z" />
			</svg>
			<span class="badges__label"><?php esc_html_e('Karte', 'umblaetterer'); ?></span>
			<span class="badges__text">
				<?php esc_html_e('Kaffeehäuser des Monats …', 'umblaetterer'); ?><br>
				<?php esc_html_e('… bereisen mit Google Earth.', 'umblaetterer'); ?>
			</span>
		</a>
