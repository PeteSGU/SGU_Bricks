<?php

namespace Framework\Filters;

use Timber\Post;
use Timber\Timber;
use Tribe__Events__Main;

class TribeEventsVenue extends Filter
{
	public $meta_key = '_EventVenueID';
	public $post_type = Tribe__Events__Main::VENUE_POST_TYPE;

	private $_filterable_values;

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
		return [
			'key' => $this->meta_key,
			'value' => array_column($this->filtered_values(), 'ID'),
			'compare' => 'IN',
			'type' => 'NUMERIC',
		];
	}
}
