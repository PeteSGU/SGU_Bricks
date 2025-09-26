<?php

namespace Framework\Interfaces;

interface FilterInterface
{
	public function filterable_values(): array;
	public function filtered_values(): array;
	public function query_string_value(): array;
	public function query_args(): array;
	public function __toString(): string;
}
