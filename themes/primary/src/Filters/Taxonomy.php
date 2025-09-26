<?php

namespace Framework\Filters;

use Timber\Term;
use Timber\Timber;

class Taxonomy extends Filter
{
	public $taxonomy = '';

	private $_filterable_values;

	public function __construct(
		string $taxonomy,
		string $get,
		string $label,
		string $all_label,
		bool $multiple = false
	) {
		parent::__construct($get, $label, $all_label, $multiple);

		$this->taxonomy = $taxonomy;
	}

	public function filterable_values(): array
	{
		if ($this->_filterable_values) {
			return $this->_filterable_values;
		}

		$items = Timber::get_terms([
			'taxonomy' => $this->taxonomy,
			'hide_empty' => true,
			'order' => 'ASC',
			'orderby' => 'title',
		]);

		$this->_filterable_values = array_map(
			fn (Term $item) => [
				'ID' => $item->ID,
				'label' => $item->name,
				'value' => $item->slug,
				'selected' => $this->is_selected($item->slug),
			],
			(array) $items
		);

		return $this->_filterable_values;
	}

	public function query_args(): array
	{
		return [
			'taxonomy' => $this->taxonomy,
			'field' => 'term_id',
			'terms' => array_column($this->filtered_values(), 'ID'),
			'operator' => 'IN',
		];
	}
}
