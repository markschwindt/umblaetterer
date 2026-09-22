<?php
/**
 * The search form
 *
 * @package Umblätterer
 */

$umblaetterer_id = 'search-' . wp_unique_id();
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $umblaetterer_id ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Im Archiv suchen', 'umblaetterer' ); ?></span>
		<input
			type="search"
			id="<?php echo esc_attr( $umblaetterer_id ); ?>"
			class="search-field"
			placeholder="<?php esc_attr_e( 'Im Archiv blättern …', 'umblaetterer' ); ?>"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			name="s"
		>
	</label>
	<button type="submit" class="search-submit"><?php esc_html_e( 'Suchen', 'umblaetterer' ); ?></button>
</form>
