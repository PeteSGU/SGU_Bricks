<?php

namespace Framework\Filters;

use Timber\Post;
use Timber\Timber;

class ACFRelationshipPost extends Filter
{
	public $meta_key = '';
	public $post_type = '';

	private $_filterable_values;

	public function __construct(
		string $post_type,
		string $meta_key,
		string $get,
		string $label,
		string $all_label,
		bool $multiple = false
	) {
		parent::__construct($get, $label, $all_label, $multiple);

		$this->post_type = $post_type;
		$this->meta_key = $meta_key;
	}

	public function filterable_values(): array
	{
		if ($this->_filterable_values) {
			return $this->_filterable_values;
		}

		$items = Timber::get_posts([
			'post_type' => $this->post_type,
			'posts_per_page' => -1,
			'order' => 'ASC',
			'orderby' => 'title',
		])->to_array();

		$this->_filterable_values = array_map(
			fn (Post $item) => [
				'ID' => $item->ID,
				'label' => $item->name,
				'value' => $item->slug,
				'selected' => $this->is_selected($item->slug),
			],
			$items
		);

		return $this->_filterable_values;
	}

	public function query_args(): array
	{
		return array_reduce(
			$this->filtered_values(),
			function ($carry, $cur) {
				$carry["meta_query_{$this->meta_key}_{$cur['ID']}"] = [
					'key' => $this->meta_key,
					'value' => '"' . $cur['ID'] . '"',
					'compare' => 'LIKE',
				];

				return $carry;
			},
			[
				'relation' => 'OR',
			]
		);
	}
}
