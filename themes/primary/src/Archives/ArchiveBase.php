<?php

namespace Framework\Archives;

use Framework\Filters\FilterCollection;
use Framework\Interfaces\ArchiveInterface;
use Timber\PostCollectionInterface;
use Timber\Timber;

class ArchiveBase implements ArchiveInterface
{

	public $search_query = '';
	public $query_args = [];

	protected static $instance = null;
	protected $filters = [];

	protected function __construct()
	{
		$this->filters = $this->get_filters();
		$this->query_args = $this->get_query_args();
		$this->search_query = esc_attr(get_query_var('search', ''));
	}

	public function get_filters(): FilterCollection
	{
		return new FilterCollection();
	}

	public function get_query_args(): array
	{
		return [];
	}

	public function get_posts_from_query(): ?PostCollectionInterface
	{
		$query_args = $this->query_args;

		if ($this->search_query) {
			$query_args['s'] = $this->search_query;
		}

		return Timber::get_posts($query_args);
	}

	public function get_context(): array
	{
		$posts = $this->get_posts_from_query();

		return [
			'filters' => $this->filters->filter_context(),
			'is_filtered' => $this->filters->is_any_filtered() || $this->search_query,
			'items' => $posts,
			'pagination' => $posts->pagination(),
			'result_count' => $posts->found_posts,
			'results_text' => $this->search_query ?: $this->filters->__toString()
		];
	}

	public function is_any_filtered(): bool
	{
		return strlen($this->search_query) || $this->filters->is_any_filtered();
	}

	public static function get_instance()
	{
		if (self::$instance) return self::$instance;

		self::$instance = new self;

		return self::$instance;
	}
}
