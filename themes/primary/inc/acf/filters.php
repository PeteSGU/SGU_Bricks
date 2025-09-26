<?php

namespace Framework;

use Timber\Site;
use Timber\Timber;
use Tribe__Events__Main;
use Framework\Timber\News_Post;
use Framework\Timber\People_Post;
use Framework\Timber\Department_Post;
use Framework\Timber\Tribe_Events_Post;

/**
 * Format
 */

function get_normalized_video_data($value, $post_id, $field)
{
	if (!$value) {
		return $value;
	}

	fw_debug_message(
		"Field `{$field['name']}` has been modified in filter: `framework_acf_layout_data_transform` with function `get_normalized_video_data`"
	);

	return get_embed_video_data($value);
}

add_filter(
	'acf/format_value/name=video',
	'Framework\get_normalized_video_data',
	10,
	3
);
add_filter(
	'acf/format_value/name=header_video',
	'Framework\get_normalized_video_data',
	10,
	3
);
add_filter(
	'acf/format_value/name=background_video',
	'Framework\get_normalized_video_data',
	10,
	3
);

/**
 * Components
 */

add_filter(
	'acf/format_value/name=full_width',
	function ($value, $post_id, $field) {
		if (!is_array($value)) {
			return $value;
		}

		return array_map(function ($item) {
			$layout = $item['acf_fc_layout'];

			return apply_filters(
				"framework_acf_layout_data_transform/name={$layout}",
				$item,
				$layout
			);
		}, $value);
	},
	10,
	3
);

add_filter('acf/load_field/name=school_site', function ($field) {
	$field['choices'] = array_combine(
		array_column(FW_SUB_SITES, 'ID'),
		array_column(FW_SUB_SITES, 'title')
	);

	return $field;
});

/**
 * ACF Group - Partial - News By School
 * Layout - School of Medicine
 * Field - Category
 */
add_filter('acf/load_field/key=field_641cb7a5af1ca', function ($field) {
	$site = fw_get_site_by_handle('medical');
	$terms = fw_get_site_terms_by_taxonomy($site->ID, 'news_categories');

	$field['choices'] = array_combine(
		array_column($terms, 'ID'),
		array_column($terms, 'name')
	);

	return $field;
});

/**
 * ACF Group - Partial - News By School
 * Layout - School of Veterinary Medicine
 * Field - Category
 */
add_filter('acf/load_field/key=field_641cb7e7af1cc', function ($field) {
	$site = fw_get_site_by_handle('veterinary');
	$terms = fw_get_site_terms_by_taxonomy($site->ID, 'news_categories');

	$field['choices'] = array_combine(
		array_column($terms, 'ID'),
		array_column($terms, 'name')
	);

	return $field;
});

/**
 * ACF Group - Partial - News By School
 * Layout - Graduate
 * Field - Category
 */
add_filter('acf/load_field/key=field_641db5b643ed2', function ($field) {
	$site = fw_get_site_by_handle('medical');
	$terms = fw_get_site_terms_by_taxonomy($site->ID, 'news_categories');

	$field['choices'] = array_combine(
		array_column($terms, 'ID'),
		array_column($terms, 'name')
	);

	return $field;
});

/**
 * ACF Group - Partial - News By School
 * Layout - Undergraduate
 * Field - Category
 */
add_filter('acf/load_field/key=field_641db60243ed4', function ($field) {
	$site = fw_get_site_by_handle('medical');
	$terms = fw_get_site_terms_by_taxonomy($site->ID, 'news_categories');

	$field['choices'] = array_combine(
		array_column($terms, 'ID'),
		array_column($terms, 'name')
	);

	return $field;
});

(function () {
	$field_names = ['wp_site', 'wp_sites'];

	foreach ($field_names as $field_name) {
		add_filter(
			"acf/load_field/name={$field_name}",
			function ($field) {
				$sites = array_map(function (Site $site) {
					$name = (int) $site->ID === 1 ? 'Gateway' : $site->name;

					return [
						'ID' => $site->ID,
						'name' => $name,
					];
				}, FW_SUB_SITES);

				$field['choices'] = array_combine(
					array_column($sites, 'ID'),
					array_column($sites, 'name')
				);

				return $field;
			},
			10,
			1
		);
	}
})();


add_filter(
	'framework_acf_layout_data_transform/name=contact_info',
	function (array $context, string $layout): array {
		$context['item'] = [];

		if ($context['type'] === 'from_directory') {
			$person = $context['person']['selected_posts'][0];

			$context['item'] = new People_Post($person->ID);
		} else {
			$context['item'] = $context['manual'];

			$context['item']['position'] = $context['item']['title'] ?? '';
			$context['item']['departments'] = array_map(
				fn ($item) => new Department_Post($item),
				$context['item']['departments'] ?: []
			);
		}

		fw_debug_message(
			"Component `{$layout}`: context has been modified in filter: `framework_acf_layout_data_transform`"
		);

		return $context;
	},
	10,
	2
);

add_filter(
	'framework_acf_layout_data_transform/name=related_events',
	function (array $context, string $layout): array {
		$context['items'] = [];

		if ($context['type'] === 'by_category') {
			$context['items'] = Timber::get_posts(
				tribe_get_events([
					'posts_per_page' => 3,
					'eventDisplay' => 'custom',
					'start_date' => 'now',
					'tax_query' => [
						[
							'taxonomy' => Tribe__Events__Main::TAXONOMY,
							'field' => 'term_id',
							'terms' => $context['category'],
						],
					],
				]),
				'\Framework\Timber\Tribe_Events_Post'
			);
			$context['archive_link'] = get_post_type_archive_link('tribe_events');
		} else {
			$content = $context['news'];
			$site_id = $content['site_id'];

			switch_to_blog($site_id);

			$context['items'] = array_map(
				fn ($item) => new Tribe_Events_Post($item),
				$context['events']['selected_posts']
			);
			$context['archive_link'] = get_post_type_archive_link('tribe_events');

			restore_current_blog();
		}

		fw_debug_message(
			"Component `{$layout}`: context has been modified in filter: `framework_acf_layout_data_transform`"
		);

		return $context;
	},
	10,
	2
);

add_filter(
	'framework_acf_layout_data_transform/name=related_news_cards',
	function (array $context, string $layout): array {
		$context['items'] = [];

		if ($context['type'] === 'by_category') {
			$context['items'] = Timber::get_posts(
				[
					'post_type' => 'news',
					'post_status' => 'publish',
					'posts_per_page' => 3,
					'meta_query' => [
						'date_clause' => [
							'key' => 'date',
							'compare' => 'EXISTS',
						],
					],
					'orderby' => [
						'date_clause' => 'DESC',
					],
					'tax_query' => [
						[
							'taxonomy' => 'news_categories',
							'field' => 'term_id',
							'terms' => $context['category'],
						],
					],
				],
				'\Framework\Timber\News_Post'
			);
			$context['archive_link'] = get_post_type_archive_link('news');
		} else {
			$content = $context['news'];
			$site_id = $content['site_id'];

			switch_to_blog($site_id);

			$context['items'] = array_map(
				fn ($item) => new News_Post($item),
				$context['news']['selected_posts']
			);
			$context['archive_link'] = get_post_type_archive_link('news');

			restore_current_blog();
		}

		fw_debug_message(
			"Component `{$layout}`: context has been modified in filter: `framework_acf_layout_data_transform`"
		);

		return $context;
	},
	10,
	2
);

add_filter(
	'framework_acf_layout_data_transform/name=related_news_stacked',
	function (array $context, string $layout): array {
		$context['items'] = [];

		if ($context['type'] === 'by_category') {
			$context['items'] = Timber::get_posts(
				[
					'post_type' => 'news',
					'post_status' => 'publish',
					'posts_per_page' => 3,
					'meta_query' => [
						'date_clause' => [
							'key' => 'date',
							'compare' => 'EXISTS',
						],
					],
					'orderby' => [
						'date_clause' => 'DESC',
					],
					'tax_query' => [
						[
							'taxonomy' => 'news_categories',
							'field' => 'term_id',
							'terms' => $context['category'],
						],
					],
				],
				'\Framework\Timber\News_Post'
			);
		} else {
			$content = $context['news'];
			$site_id = $content['site_id'];

			switch_to_blog($site_id);

			$context['items'] = array_map(
				fn ($item) => new News_Post($item),
				$context['news']['selected_posts']
			);
			$context['archive_link'] = get_post_type_archive_link('news');

			restore_current_blog();
		}

		restore_current_blog();

		fw_debug_message(
			"Component `{$layout}`: context has been modified in filter: `framework_acf_layout_data_transform`"
		);

		return $context;
	},
	10,
	2
);

add_filter(
	'framework_acf_layout_data_transform/name=news_blog_feature',
	function (array $context, string $layout): array {
		$content = reset($context['items']);
		$featured_post_id = reset($content['featured']['selected_posts']);
		$news_posts_ids = $content['news']['selected_posts'];
		$site_id = $content['featured']['site_id'];

		switch_to_blog(($site_id));

		$context['featured_post'] = new News_Post($featured_post_id);
		$context['news_posts'] = array_map(fn ($id) => new News_Post($id), $news_posts_ids);
		$context['current_site'] = new Site($site_id);
		$content['links'] = [
			[
				"label" =>  "News",
				"url" =>  get_post_type_archive_link("news")
			],
			[
				"label" =>  "Blog",
				"url" =>  get_post_type_archive_link("blog")
			]
		];

		restore_current_blog();

		fw_debug_message(
			"Component `{$layout}`: context has been modified in filter: `framework_acf_layout_data_transform`"
		);

		return $context;
	},
	10,
	2
);

add_filter(
	'framework_acf_layout_data_transform/name=related_news_categories',
	function (array $context, string $layout): array {
		$content = reset($context['news']['news_by_school']);

		$site = fw_get_site_from_flexible_layout_handle($content['acf_fc_layout']);
		$type = $content['type'];

		switch_to_blog($site->ID);

		$context['current_site'] = $site;
		$context['terms'] = fw_get_site_terms_by_taxonomy($site->ID, 'news_categories');
		$context['archive_link'] = get_post_type_archive_link('news');
		$content['items'] = [];

		if ($type === 'by_category') {
			$context['items'] = Timber::get_posts(
				[
					'post_type' => 'news',
					'post_status' => 'publish',
					'posts_per_page' => 3,
					'meta_query' => [
						'date_clause' => [
							'key' => 'date',
							'compare' => 'EXISTS',
						],
					],
					'orderby' => [
						'date_clause' => 'DESC',
					],
					'tax_query' => [
						[
							'taxonomy' => 'news_categories',
							'field' => 'term_id',
							'terms' => $content['category'],
						],
					],
				],
				'\Framework\Timber\News_Post'
			);
		} else {
			$context['items'] = array_map(
				fn ($item) => new News_Post($item),
				$content['news']['selected_posts']
			);
		}

		restore_current_blog();

		return $context;
	},
	10,
	2
);
