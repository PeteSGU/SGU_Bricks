<?php

namespace Framework;

use Timber\Timber;

function get_theme_include_path(string $relative_path): ?string
{
	if (!is_array(Timber::$locations)) {
		return false;
	}

	foreach (Timber::$locations as $location) {
		$file = trailingslashit($location) . $relative_path;

		if (file_exists($file)) {
			return $file;
		}
	}

	return false;
}

function include_theme_path_file(string $relative_path)
{
	$file = get_theme_include_path($relative_path);

	if (!$file) {
		return false;
	}

	return include $file;
}

function catch_404(): void
{
	status_header(404);
	nocache_headers();

	include get_query_template('404');

	wp_die();
}

$framework_uniqid_namespaces = [];

function uniqid($namespace): string
{
	global $framework_uniqid_namespaces;

	if (!array_key_exists($namespace, $framework_uniqid_namespaces)) {
		$framework_uniqid_namespaces[$namespace] = 0;
	}

	$framework_uniqid_namespaces[$namespace] += 1;

	return "{$namespace}-{$framework_uniqid_namespaces[$namespace]}";
}
