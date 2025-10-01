<?php

namespace Framework;

use Framework\Archives\ArchiveLookup;
use Timber\Timber;

add_shortcode('fw_google_cse_search_input', function (): ?string {
	$markup = Timber::compile(
		'partials/google-cse.twig',
		Timber::context()
	);

	return $markup ?: null;
});

add_shortcode('fw_archive_filter', function (array $attrs, string $content = ''): ?string {
	$attrs = shortcode_atts([
		'post_type' => null
	], $attrs);

	$post_type = $attrs['post_type'] ?? null;

	if (!$post_type) return null;

	$archive = ArchiveLookup::get_archive_by_post_type($post_type);

	if (!$archive) return null;

	$context = $archive->get_context();
	$post_type_title = get_post_type_object($post_type)->label;

	$markup = Timber::compile(
		'partials/filter.twig',
		[
			'label' => $post_type_title,
			'tools' => $context['filters'],
			'search_placeholder' => "Search {$post_type_title}"
		]
	);

	return $markup ?: null;
});

add_shortcode('fw_archive_list', function (array $attrs, string $content = ''): ?string {
	$attrs = shortcode_atts([
		'post_type' => null
	], $attrs);

	$post_type = $attrs['post_type'] ?? null;

	if (!$post_type) return null;

	$archive = ArchiveLookup::get_archive_by_post_type($post_type);

	if (!$archive) return null;

	$context = $archive->get_context();

	$markup = Timber::compile(
		"partials/{$post_type}/{$post_type}-list.twig",
		$context
	);

	return $markup ?: null;
});

add_shortcode('fw_subnav', function (array $attrs, string $content = ''): ?string {
	$attrs = shortcode_atts([
		'only_children' => true
	], $attrs);

	$subnav = get_subnav($attrs['only_children']);

	if (!count($subnav)) return '';

	$markup = Timber::compile(
		'navigation/subnav.twig',
		[
			'items' => $subnav
		]
	);

	return $markup;
});

/**
 * News Specific
 */

add_shortcode('fw_news_listing_feature_carousel', function (array $attrs, string $content = ''): ?string {
	$ids = get_field('news_listing_featured_items', 'option') ?? false;

	if (!$ids) return null;

	$archive = ArchiveLookup::get_archive_by_post_type('news');

	if ($archive->is_any_filtered()) return null;

	$items = Timber::get_posts($ids);

	$markup = Timber::compile(
		'partials/news/news-feature-carousel.twig',
		[
			'items' => $items
		]
	);

	return $markup;
});
