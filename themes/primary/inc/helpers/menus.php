<?php

namespace Framework;

use Framework\Multisite;
use Timber\Timber;
use Timber\URLHelper;

function fw_get_menu_main_navigation()
{
	switch_to_blog(1);

	$menu = Timber::get_menu('school_navigation', [
		'depth' => 1,
	]);
	$sites = Multisite::get_subsites();

	$menu_item_paths = array_map(
		fn ($item) => URLHelper::get_rel_url($item->link),
		$menu->items
	);
	$menu_items = array_reduce(
		$sites,
		function ($carry, $site) use ($menu, $menu_item_paths) {
			$found_key = array_search($site->path, $menu_item_paths);

			if ($found_key === false) {
				return $carry;
			}

			switch_to_blog($site->blog_id);

			$site_menu_main_nav = Timber::get_menu('menu_main_navigation');
			$menu_item = $menu->items[$found_key];

			foreach ($site_menu_main_nav->items as $item) {
				$menu_item->add_child($item);
			}

			$carry[] = $menu_item;

			restore_current_blog();

			return $carry;
		},
		[]
	);

	restore_current_blog();

	return [
		'items' => $menu_items,
	];
}
