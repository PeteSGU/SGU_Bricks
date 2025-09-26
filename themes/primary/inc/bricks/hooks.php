<?php

use Timber\Timber;

/**
 * Actions
 */

add_action('bricks_before_site_wrapper', function () {
	Timber::render('bricks/partials/alert-wrapper.twig');
});

/**
 * Filters
 */

function fw_format_acf_date(string $acf_field, string $format)
{
	$value = get_field($acf_field);

	return date($format, strtotime($value));
}

function fw_tribe_get_start_date($date_format = '')
{
	return tribe_get_start_date(null, false, $date_format);
}

function fw_tribe_get_time_span()
{
	global $post;

	$post = Timber::get_post($post->ID);

	return $post->fw_time_span();
}

function fw_person_full_name()
{
	global $post;

	return Timber::get_post($post->ID)->fw_full_name();
}

function fw_get_acf_link_title(string $selector): string
{
	global $post;

	$default_value = 'View More';

	try {

		$field = get_field($selector, $post->ID);

		return $field['title'] ?: $default_value;
	} catch (\Throwable $t) {
		return $default_value;
	}
}

add_filter('bricks/code/echo_function_names', function () {
	return [
		'get_post_type_archive_link',
		'fw_format_acf_date',
		'fw_tribe_get_start_date',
		'fw_tribe_get_time_span',
		'tribe_get_start_date',
		'tribe_get_start_time',
		'tribe_get_end_date',
		'tribe_get_event_meta',
		'tribe_get_venue',
		'fw_person_full_name',
		'fw_get_acf_link_title'
	];
});

add_filter('bricks/dynamic_data/format_value', function ($value, $tag, $post_id, $filters, $context) {
	return $value;
}, 10, 5);
