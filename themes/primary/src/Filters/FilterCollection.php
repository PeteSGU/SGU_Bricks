<?php

namespace Framework\Filters;

use Exception;

class FilterCollection
{
	public $filters = [];

	public function __construct(array $filters = [])
	{
		array_map([$this, 'add'], $filters);
	}

	public function get(string $get): Filter
	{
		if (!array_key_exists($get, $this->filters)) {
			throw new Exception(
				"Framework: Filter with get `{$get}` does not exist."
			);
		}

		return $this->filters[$get];
	}

	public function is_any_filtered(): bool
	{
		return (bool) count(
			array_filter(
				array_column($this->filters, 'get'),
				fn ($get) => $this->get($get)->is_filtered()
			)
		);
	}

	public function filter_context(): array
	{
		return array_map(
			fn (Filter $filter) => [
				'options' => $filter->filterable_values(),
				'filtered' => $filter->filtered_values(),
				'filtered_as_string' => $filter->__toString(),
				'label' => $filter->label,
				'all_label' => $filter->all_label,
				'multiple' => $filter->multiple,
				'name' => $filter->get,
			],
			$this->filters
		);
	}

	public function __toString(): string
	{
		$values = array_filter(
			array_column($this->filter_context(), 'filtered_as_string')
		);

		return join(', ', $values);
	}

	private function add(Filter $filter): self
	{
		$this->filters[$filter->get] = $filter;

		return $this;
	}
}
