<?php

namespace Framework\Interfaces;

use Framework\Filters\FilterCollection;
use Timber\PostCollectionInterface;

interface ArchiveInterface
{
	public function get_filters(): FilterCollection;
	public function get_query_args(): array;
	public function get_posts_from_query(): ?PostCollectionInterface;
	public function get_context(): array;
	public function is_any_filtered(): bool;
}
