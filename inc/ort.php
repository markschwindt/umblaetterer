<?php
/**
 * Der Ort — the dateline's place
 *
 * Every piece in this archive carries a place in a custom field keyed "Ort":
 * Leipzig, Berlin, Konstanz, London, and — for the ones written nowhere in
 * particular — the bibliographers' "sine loco". 1,632 entries have one, which
 * makes it the only custom field on the site and the oldest editorial habit in
 * it.
 *
 * A newspaper prints that place at the head of the report, so the theme does
 * too. This file registers the field, gives it somewhere to be edited, and
 * hands it to umblaetterer_dateline().
 *
 * @package Umblätterer
 */

/**
 * The meta key, exactly as twenty years of entries have spelled it.
 */
if (!defined('UMBLAETTERER_ORT_KEY')) {
	define('UMBLAETTERER_ORT_KEY', 'Ort');
}

/**
 * Post types that carry a place.
 *
 * @return string[]
 */
function umblaetterer_ort_post_types()
{
	return apply_filters('umblaetterer_ort_post_types', array('post', 'page'));
}

/**
 * Register the field so it is a declared part of a post rather than a loose row.
 *
 * Declaring it brings a sanitiser, a capability check and a REST field, which
 * the block editor and the API both need before they will touch it.
 */
function umblaetterer_register_ort()
{
	foreach (umblaetterer_ort_post_types() as $type) {
		register_post_meta(
			$type,
			UMBLAETTERER_ORT_KEY,
			array(
				'type' => 'string',
				'single' => true,
				'show_in_rest' => true,
				'description' => __('Der Ort, an dem der Beitrag geschrieben wurde.', 'umblaetterer'),
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback' => static function () {
					return current_user_can('edit_posts');
				},
			)
		);
	}
}
add_action('init', 'umblaetterer_register_ort');

/**
 * Read the place off a post.
 *
 * @param int|WP_Post|null $post_id Post to read. Defaults to the current one.
 * @return string Trimmed place, or an empty string.
 */
function umblaetterer_ort($post_id = null)
{
	$post = get_post($post_id);

	if (!$post) {
		return '';
	}

	return trim((string) get_post_meta($post->ID, UMBLAETTERER_ORT_KEY, true));
}

/**
 * Every place already in use, most-used first.
 *
 * Offered to the editor as a datalist so the twentieth piece from Konstanz is
 * filed under the same spelling as the first. Cached for a day: it is a
 * convenience, not a live index, and it costs a GROUP BY over 1,600 rows.
 *
 * @return string[]
 */
function umblaetterer_ort_vocabulary()
{
	$places = get_transient('umblaetterer_ort_vocabulary');

	if (false !== $places) {
		return (array) $places;
	}

	global $wpdb;

	$places = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT meta_value FROM {$wpdb->postmeta}
			 WHERE meta_key = %s AND TRIM(meta_value) <> ''
			 GROUP BY meta_value
			 ORDER BY COUNT(*) DESC, meta_value ASC
			 LIMIT 300",
			UMBLAETTERER_ORT_KEY
		)
	);

	$places = array_values(array_unique(array_map('trim', (array) $places)));

	set_transient('umblaetterer_ort_vocabulary', $places, DAY_IN_SECONDS);

	return $places;
}

/**
 * Forget the cached vocabulary when a place is saved.
 */
function umblaetterer_flush_ort_vocabulary()
{
	delete_transient('umblaetterer_ort_vocabulary');
}

/**
 * Add the editing box.
 */
function umblaetterer_ort_meta_box()
{
	foreach (umblaetterer_ort_post_types() as $type) {
		add_meta_box(
			'umblaetterer-ort',
			__('Ort', 'umblaetterer'),
			'umblaetterer_ort_meta_box_render',
			$type,
			'side',
			'default'
		);
	}
}
add_action('add_meta_boxes', 'umblaetterer_ort_meta_box');

/**
 * The box itself: one field, and the places already in use to choose from.
 *
 * @param WP_Post $post The post being edited.
 */
function umblaetterer_ort_meta_box_render($post)
{
	wp_nonce_field('umblaetterer_ort_save', 'umblaetterer_ort_nonce');

	$value = umblaetterer_ort($post);
	$vocab = umblaetterer_ort_vocabulary();
	?>
	<p>
		<label class="screen-reader-text" for="umblaetterer-ort-field"><?php esc_html_e('Ort', 'umblaetterer'); ?></label>
		<input type="text" id="umblaetterer-ort-field" name="umblaetterer_ort" class="widefat" list="umblaetterer-ort-list"
			value="<?php echo esc_attr($value); ?>" placeholder="<?php esc_attr_e('Leipzig', 'umblaetterer'); ?>"
			autocomplete="off">
		<datalist id="umblaetterer-ort-list">
			<?php foreach ($vocab as $place): ?>
				<option value="<?php echo esc_attr($place); ?>"></option>
			<?php endforeach; ?>
		</datalist>
	</p>
	<p class="description">
		<?php esc_html_e('Steht in der Datumszeile nach dem Namen. Für Beiträge ohne Ort ist »sine loco« üblich.', 'umblaetterer'); ?>
	</p>
	<?php
}

/**
 * Save the field.
 *
 * @param int $post_id The post being saved.
 */
function umblaetterer_ort_save($post_id)
{
	if (
		!isset($_POST['umblaetterer_ort_nonce'])
		|| !wp_verify_nonce(sanitize_key(wp_unslash($_POST['umblaetterer_ort_nonce'])), 'umblaetterer_ort_save')
	) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	/*
	 * The field is absent from the request rather than empty when the box was
	 * never rendered — a quick edit, a bulk edit, a REST write. Leaving early
	 * keeps those from wiping a place nobody meant to touch.
	 */
	if (!isset($_POST['umblaetterer_ort'])) {
		return;
	}

	$value = sanitize_text_field(wp_unslash($_POST['umblaetterer_ort']));

	if ('' === $value) {
		delete_post_meta($post_id, UMBLAETTERER_ORT_KEY);
	} else {
		update_post_meta($post_id, UMBLAETTERER_ORT_KEY, $value);
	}

	umblaetterer_flush_ort_vocabulary();
}
add_action('save_post', 'umblaetterer_ort_save');

/**
 * Show the place as a column in the posts list, so the archive can be scanned.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function umblaetterer_ort_column($columns)
{
	$before = array_slice($columns, 0, 2, true);
	$after = array_slice($columns, 2, null, true);

	return $before + array('umblaetterer_ort' => __('Ort', 'umblaetterer')) + $after;
}

/**
 * Fill that column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post being listed.
 */
function umblaetterer_ort_column_value($column, $post_id)
{
	if ('umblaetterer_ort' !== $column) {
		return;
	}

	$place = umblaetterer_ort($post_id);

	echo $place ? esc_html($place) : '<span aria-hidden="true">—</span>';
}

foreach (array('post', 'page') as $umblaetterer_ort_type) {
	$umblaetterer_ort_suffix = 'post' === $umblaetterer_ort_type ? 'posts' : 'pages';
	add_filter("manage_{$umblaetterer_ort_suffix}_columns", 'umblaetterer_ort_column');
	add_action("manage_{$umblaetterer_ort_suffix}_custom_column", 'umblaetterer_ort_column_value', 10, 2);
}
