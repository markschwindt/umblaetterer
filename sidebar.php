<?php
/**
 * The sidebar
 *
 * Kept for the template hierarchy's sake; the rail is composed inline by the
 * templates that want it, via template-parts/rail-rubriken.php.
 *
 * @package Umblätterer
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
