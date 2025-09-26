<?php

namespace Framework;

use Timber\Timber;
use Timber\Post;
use WP_Post;

function get_breadcrumb(): array
{
	$post = get_post();

	if (empty($post)) {
		return [];
	}

	if (is_front_page()) {
		if (CURRENT_SITE === 1) {
			return [];
		} else {
			$site_name = get_bloginfo();

			return [['title' => $site_name, 'url' => WWW_ROOT]];
		}
	}

	$breadcrumb = [];

	if (CURRENT_SITE !== 1) {
		$site_name = get_bloginfo();
		$breadcrumb[] = ['title' => $site_name, 'url' => WWW_ROOT];
	}

	if (is_page()) {
		if ($post->post_parent) {
			$home = get_post(get_option('page_on_front'));

			for ($i = count($post->ancestors) - 1; $i >= 0; $i--) {
				if ($home->ID != $post->ancestors[$i]) {
					$breadcrumb[] = [
						'title' => get_the_title($post->ancestors[$i]),
						'url' => get_permalink($post->ancestors[$i]),
					];
				}
			}
		}

		// $breadcrumb[] = [
		// 	"title" => get_the_title($post->ID),
		// 	"url" => get_permalink($post->ID),
		// ];
	}

	return $breadcrumb;
}

function get_subnav($only_children = true, string $icon = ''): array
{
	$post = get_post();

	if (!$post) {
		return [];
	}

	$parent = $post->post_parent;

	// First level of sub-sites behaves a little weird
	if (CURRENT_SITE !== 1 && is_front_page()) {
		return get_visible_page_children(0, 1, $icon);
	} elseif (CURRENT_SITE !== 1 && !$parent) {
		$children = get_visible_page_children($post->ID, 1, $icon);

		if ($only_children) {
			return $children;
		}

		$siblings = get_visible_page_children(0, 1, $icon);

		if (!count($children)) {
			return $siblings;
		}

		$found = false;

		foreach ($siblings as $index => $sibling) {
			if ($sibling['id'] == $post->ID) {
				$siblings[$index]['children'] = $children;
				$found = true;
			}
		}

		if ($found) {
			return $siblings;
		} else {
			return $children;
		}
	}

	if (!$parent) {
		return get_visible_page_children($post->ID, 1, $icon);
	} else {
		$children = get_visible_page_children($post->ID, 1, $icon);

		if ($only_children) {
			return $children;
		}

		$siblings = get_visible_page_children($parent, 1, $icon);

		if (count($children)) {
			$found = false;

			foreach ($siblings as $index => $sibling) {
				if ($sibling['id'] == $post->ID) {
					$siblings[$index]['children'] = $children;
					$found = true;
				}
			}

			if ($found) {
				return $siblings;
			} else {
				return $children;
			}
		} else {
			$parent_post = get_post($parent);

			if (!$parent_post->post_parent && CURRENT_SITE == 1) {
				return $siblings;
			} elseif (!$parent_post->post_parent) {
				$ancestors = get_visible_page_children(0, 1, $icon);
			} else {
				$ancestors = get_visible_page_children(
					$parent_post->post_parent,
					1,
					$icon
				);
			}

			$found = false;

			foreach ($ancestors as $index => $ancestor) {
				if ($ancestor['id'] == $parent) {
					$ancestors[$index]['children'] = $siblings;
					$found = true;
				}
			}

			if ($found) {
				return $ancestors;
			} else {
				return $siblings;
			}
		}
	}
}

function get_visible_page_children(
	int $parent,
	int $depth = 1,
	string $icon = ''
): array {
	$navigation = get_children([
		'post_parent' => $parent,
		'post_type' => ['page', 'np-redirect'],
		'post_status' => 'publish',
		'orderby' => 'menu_order',
		'order' => 'ASC',
	]);
	$rendered_navigation = [];

	foreach ($navigation as $item) {
		if ($item->post_type != 'page' && $item->post_type != 'np-redirect') {
			continue;
		}

		$nav_status = get_post_meta($item->ID, '_np_nav_status', true);

		if ($nav_status == 'hide') {
			continue;
		}

		$item->target = get_post_meta($item->ID, '_np_link_target', true);

		if ($item->post_type == 'np-redirect') {
			$type = get_post_meta($item->ID, '_np_nav_menu_item_type', true);

			if ($type == 'custom') {
				$item->url = $item->post_content;
			} elseif ($type == 'post_type_archive') {
				$post_type = get_post_meta(
					$item->ID,
					'_np_nav_menu_item_object',
					true
				);
				$item->url = get_post_type_archive_link($post_type);
			} else {
				$target_id = get_post_meta(
					$item->ID,
					'_np_nav_menu_item_object_id',
					true
				);
				$item->url = get_permalink($target_id);
			}
		} else {
			$item->url = get_permalink($item->ID);
		}

		if ($depth > 1) {
			$item->children = get_visible_page_children($item->ID, $depth - 1);
		}

		$rendered_navigation[] = [
			'id' => $item->ID,
			'title' => $item->post_title,
			'url' => $item->url,
			'target' => $item->target,
			'icon' => $icon,
		];
	}

	return $rendered_navigation;
}
