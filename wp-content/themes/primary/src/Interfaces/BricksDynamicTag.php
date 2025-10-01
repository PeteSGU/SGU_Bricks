<?php

namespace Framework\Interfaces;

use WP_Post;

interface BricksDynamicTag
{
	public function add(array $tags): array;
	public function value(string $tag, WP_Post $post, $context = 'text'): string;
	public function logic(string $tag, WP_Post $post, $context = 'text'): string;
	public function render(string $content, WP_Post $post, string $context = 'text'): string;
}
