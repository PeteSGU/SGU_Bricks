<?php

namespace Framework\Archives;

use Framework\Filters\FilterCollection;
use Framework\Filters\Taxonomy;
use Framework\Interfaces\ArchiveInterface;
use Timber\Timber;
use Timber\PostCollectionInterface;
use Tribe__Events__Main;

class ArchiveEvents implements ArchiveInterface
{
	private static $instance = null;
	private $post_type = Tribe__Events__Main::POSTTYPE;
	private $query_args = [];
	private $search_query = '';
	private $filters = [];

	private function __construct()
	{
		$this->filters = $this->get_filters();
		$this->query_args = $this->get_query_args();
		$this->search_query = esc_attr(get_query_var('search', ''));
	}

	public function get_filters(): FilterCollection
	{
		return new FilterCollection([
			new Taxonomy(
				'school',
				'school',
				'School',
				'All Schools',
				false
			),
			new Taxonomy(
				Tribe__Events__Main::TAXONOMY,
				'category',
				'categories',
				'All Categories',
				false
			),
		]);
	}

	public function get_query_args(): array
	{
		return [
			'post_type' => $this->post_type,
			'posts_per_page' => get_option('posts_per_page', 10),
			'paged' => get_query_var('paged', 1),
			'eventDisplay' => 'custom',
			'start_date' => 'now',
			'tax_query' => [],
			'meta_query' => []
		];
	}

	public function get_posts_from_query(): ?PostCollectionInterface
	{
		$query_args = $this->query_args;

		if ($this->search_query) {
			$query_args['s'] = $this->search_query;
		}

		if ($this->filters->get('school')->is_filtered()) {
			$query_args['tax_query']['school_query'] = $this->filters
				->get('school')
				->query_args();
		}

		if ($this->filters->get('category')->is_filtered()) {
			$query_args['tax_query']['category_query'] = $this->filters
				->get('category')
				->query_args();
		}

		return Timber::get_posts(
			tribe_get_events($query_args, true)
		);
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
