<?php

namespace Framework;

use Timber\Timber;

function get_alert()
{
	if (is_multisite()) {
		switch_to_blog(1);
	}

	$alert_posts = Timber::get_posts(
		[
			'post_type' => 'alert',
			'post_status' => 'publish',
			'meta_query' => [
				'relation' => 'OR',
				['key' => 'start_date', 'compare' => '=', 'value' => ''],
				[
					'key' => 'start_date',
					'compare' => '<=',
					'value' => date('Ymd'),
				],
			],
		],
		'\Framework\Timber\Alert_Post'
	)->to_array();

	// Only bring in alerts that haven't ended or have no end date
	$alert = end(
		array_filter($alert_posts, fn ($alert) => $alert->fw_has_not_expired())
	);

	if ($alert) {
		$context = $alert->fw_context();

		Timber::render('partials/alert.twig', $context);
	}

	if (is_multisite()) {
		restore_current_blog();
	}

	wp_die();
}

// Register alert AJAX
add_action('wp_ajax_get_alert', 'Framework\get_alert');
add_action('wp_ajax_nopriv_get_alert', 'Framework\get_alert');
