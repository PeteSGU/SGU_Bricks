<?php

namespace Framework;

use Timber\Timber;

/**
 * page specific context
 */
add_filter('fw_timber_add_to_context', function ($context) {
	$fields = get_fields();

	$context['page_theme'] = $fields['page_theme'] ?: 'blue';

	$context['page_logo'] =
		$context['page_theme'] != 'blue' ? 'logo_dark' : 'logo';

	return $context;
});

/**
 * global context
 */
add_filter('fw_timber_add_to_context', function ($context) {
	switch_to_blog(1);

	$context['global'] = [];

	$context['global']['options'] = get_fields('options');

	/**
	 * here we set up the global menus that will be consistent throughout
	 * the main site and sub sites, edited only in the main site
	 */
	$nav_menu_locations = get_nav_menu_locations();

	$context['global']['menu'] = array_reduce(
		array_keys($nav_menu_locations),
		function ($carry, $menu) {
			$carry[$menu] = Timber::get_menu($menu);

			return $carry;
		},
		[]
	);

	$context['global']['menu']['global_menu_main_navigation'] = fw_get_menu_main_navigation();

	/**
	 * the school navigation only has one icon for the "SGU" gateway,
	 * but the other links need an icon when in the mega menu.
	 *
	 * so here I remove the additional icons for the school navigation only.
	 */
	$context['global']['menu']['school_navigation']->items = array_map(
		function ($item) {
			if ($item->menu_order === 1) {
				return $item;
			}

			$item->icon = null;

			return $item;
		},
		$context['global']['menu']['school_navigation']?->items ?? []
	);

	restore_current_blog();

	return $context;
});

/**
 * footer context
 */
add_filter('fw_timber_add_to_context', function ($context) {
	$ld_json = [
		'@context' => 'http://schema.org',
		'@type' => FW_CONFIG['schema_type'],
		'name' => $context['site']->title,
		'logo' => PARENT_ROOT_URI_STATIC . 'images/logo-schema.png',
		'url' => HOME_ROOT,
		'address' => [
			'@type' => 'PostalAddress',
			'streetAddress' => FW_CONFIG['street'],
			'addressLocality' => FW_CONFIG['locality'],
			'addressRegion' => FW_CONFIG['region'],
		],
	];

	if (FW_CONFIG['email']) {
		$ld_json['email'] = FW_CONFIG['email'];
	}

	if (FW_CONFIG['phone']) {
		$ld_json['telephone'] = FW_CONFIG['phone'];
	}

	$social_nav_items = $context['menu']['social_navigation']->count
		? $context['menu']['social_navigation']
		: $context['global']['menu']['social_navigation'];

	if ($social_nav_items) {
		$social_urls = array_map(
			fn ($item) => $item->link,
			$social_nav_items->items ?: []
		);

		$ld_json['sameAs'] = $social_urls;
	}

	$context['footer_ld_json'] = json_encode($ld_json);

	return $context;
});
