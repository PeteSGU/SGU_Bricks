<?php

namespace Framework;

use Routes;

Routes::map('news-category/:slug', function ($params) {
	$location = join('?', [
		trailingslashit(get_post_type_archive_link('news')),
		http_build_query([
			'category[]' => $params['slug']
		])
	]);

	wp_redirect($location, 301);
	exit();
});
