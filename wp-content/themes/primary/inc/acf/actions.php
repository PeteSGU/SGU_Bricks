<?php

namespace Framework;

use WP_Post;
use Timber\Post;

/**
 * When a page with a "Program" template is saved
 * and has a "Program" ACF field filled out,
 * this will update the CPT Program `url` field
 * to match the page url.
 */
add_action('acf/save_post', function ($post_id) {
	$template = get_page_template_slug();

	if ($template !== 'template-program.php') {
		return;
	}

	$program = get_field('program', $post_id);

	if (!($program instanceof WP_Post)) {
		return;
	}

	$program = new Post($program);

	update_field('url', $program->link, $program->ID);
});
