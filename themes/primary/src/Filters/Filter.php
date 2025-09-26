<?php

namespace Framework\Filters;

use Framework\Interfaces\FilterInterface;

abstract class Filter implements FilterInterface
{
	public $get = '';
	public $label = '';
	public $all_label = '';
	public $multiple = false;

	private $_filtered_values;
	private $_is_filtered;
	private $_query_string_value;

	public function __construct(
		string $get,
		string $label,
		string $all_label,
		bool $multiple = false
	) {
		$this->get = $get;
		$this->label = $label;
		$this->all_label = $all_label;
		$this->multiple = $multiple;
	}

	public function is_selected(string $value): bool
	{
		$query_value = $this->query_string_value();

		if (!$query_value) {
			return false;
		}

		if (!count($query_value)) {
			return true;
		}

		return (bool) in_array($value, $query_value);
	}

	public function is_filtered(): bool
	{
		if ($this->_is_filtered) {
			return $this->_is_filtered;
		}

		$this->_is_filtered = count(
			array_filter(
				$this->query_string_value(),
				fn ($value) => $value && strlen($value)
			)
		);

		return $this->_is_filtered;
	}

	public function filtered_values(): array
	{
		if ($this->_filtered_values) {
			return $this->_filtered_values;
		}

		$query = $this->query_string_value();

		$this->_filtered_values = array_filter(
			$this->filterable_values(),
			fn ($item) => in_array($item['value'], $query)
		);

		return $this->_filtered_values;
	}

	public function query_string_value(): array
	{
		if ($this->_query_string_value) {
			return $this->_query_string_value;
		}

		$value = $_GET[$this->get] ?? null;

		if (!$value) {
			$this->_query_string_value = [];

			return $this->_query_string_value;
		}

		if (!is_array($value)) {
			$value = [$value];
		}

		$value = array_filter($value, fn ($v) => strlen(trim($v)));

		$this->_query_string_value = array_map('esc_attr', $value);

		return $this->_query_string_value;
	}

	public function __toString(): string
	{
		return join(', ', array_column($this->filtered_values(), 'label'));
	}
}
