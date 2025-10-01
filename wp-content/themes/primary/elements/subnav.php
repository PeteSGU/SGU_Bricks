<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use Bricks\Element_Nav_Menu;
use Timber\Post;
use Timber\Timber;

class Element_Framework_Subnav extends Element_Nav_Menu
{
	public $category = 'framework';
	public $name = 'fw-subnav';
	public $icon = 'fas fa-bars';
	public $css_selector = '.fw_subnav_wrapper';
	public $scripts = [];
	public $nestable = true;

	public function get_label(): string
	{
		return esc_html__('FW - Subnav', 'framework');
	}

	public function set_control_groups()
	{
		$this->control_groups['menu'] = [
			'title' => esc_html__('Top level', 'bricks'),
		];

		$this->control_groups['mobile-menu'] = [
			'title' => esc_html__('Mobile menu', 'bricks'),
		];
	}

	public function set_controls()
	{
		parent::set_controls();

		unset($this->controls['menu']);
	}

	public function render()
	{
		echo "<div {$this->render_attributes('_root')}>";

		echo "<nav {$this->render_attributes('nav', true)}>";

		foreach ($this->wp_get_nav_menu_items() as $item) {
			echo '<li>hello</li>';
		}

		echo '</nav>';

		echo '</div>';
	}

	public function wp_get_nav_menu_items(): array
	{
		$post = new Post(get_the_ID());
		$post_id = $post->ID;
		$post_parent_id = $post->parent->ID;

		$result = Timber::get_posts([
			'post_type' => ['page', 'np-redirect'],
			'post_parent__in' => [$post_id, $post_parent_id],
			'meta_query' => [
				'relation' => 'OR',
				'nested_pages_not_hidden' => [
					'key' => '_np_nav_status',
					'value' => 'show',
					'compare' => '='
				],
				'nested_pages_has_no_status' => [
					'key' => '_np_nav_status',
					'compare' => 'NOT EXISTS'
				]
			]
		])->to_array();

		$items_by_type = array_reduce($result, function ($carry, $item) use ($post_id) {
			if ($item->parent == $post_id) {
				$carry['children'][] = $item;

				return $carry;
			}

			$carry['siblings'][] = $item;

			return $carry;
		}, [
			'children' => [],
			'siblings' => []
		]);

		return count($items_by_type['children'])
			? $items_by_type['children']
			: $items_by_type['siblings'];
	}
}
