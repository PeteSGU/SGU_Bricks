<?php

namespace Framework\Bricks\DynamicTags;

use Framework\Interfaces\BricksDynamicTag;
use WP_Post;

abstract class BaseDynamicTag implements BricksDynamicTag
{
	public $tag_name;
	public $label;
	public $group;

	public function __construct(string $tag_name, string $label, string $group = 'Framework')
	{
		$this->tag_name = $tag_name;
		$this->label = $label;
		$this->group = $group;

		add_filter('bricks/dynamic_tags_list', [$this, 'add']);

		add_filter('bricks/dynamic_data/render_tag', [$this, 'value'], 10, 3);
		add_filter('bricks/dynamic_data/render_tag', [$this, 'value'], 10, 3);

		add_filter('bricks/dynamic_data/render_content', [$this, 'render'], 10, 3);
		add_filter('bricks/frontend/render_data', [$this, 'render'], 10, 2);
	}

	public function add(array $tags): array
	{
		$tags[] = [
			'name' => $this->tag_name_in_brackets(),
			'label' => $this->label,
			'group' => $this->group
		];

		return $tags;
	}

	public function value(string $tag, WP_Post $post, $context = 'text'): string
	{
		if ($tag !== $this->tag_name) {
			return $tag;
		}

		$value = call_user_func_array([$this, 'logic'], func_get_args());

		return $value;
	}

	public function logic(string $tag, WP_Post $post, $context = 'text'): string
	{
		return 'Not implemented.';
	}

	public function render(string $content, WP_Post $post, string $context = 'text'): string
	{
		if (strpos($content, $this->tag_name_in_brackets()) === false) {
			return $content;
		}

		$value = call_user_func_array([$this, 'logic'], func_get_args());

		$content = str_replace($this->tag_name_in_brackets(), $value, $content);

		return $content;
	}

	private function tag_name_in_brackets(): string
	{
		return '{' . $this->tag_name . '}';
	}
}
