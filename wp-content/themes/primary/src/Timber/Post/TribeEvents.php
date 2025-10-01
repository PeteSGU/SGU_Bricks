<?php

namespace Framework\Timber\Post;

use Timber\Post;
use Timber\Image;
use Tribe__Events__Main;

class TribeEvents extends Post
{
	public $_fw_all_day;
	public $_fw_start;
	public $_fw_end;
	public $_address_parts;
	public $_fw_location;
	public $_fw_venue;
	public $_fw_blurb;
	public $_fw_categories;
	public $_fw_types;
	public $_fw_registration_url;
	public $_fw_ld_json;
	public $_fw_actions;
	public $_fw_same_day;
	public $_fw_date_span;
	public $_fw_time_span;

	public function fw_all_day(): bool
	{
		if ($this->_fw_all_day) {
			return $this->_fw_all_day;
		}

		$this->_fw_all_day = tribe_event_is_all_day($this->ID);

		return $this->_fw_all_day;
	}

	public function fw_same_day(): bool
	{
		if ($this->_fw_same_day) {
			return $this->_fw_same_day;
		}

		$this->_fw_same_day =
			gmdate('Y-m-d', $this->fs_start()) ===
			gmdate('Y-m-d', $this->fs_end());

		return $this->_fw_same_day;
	}

	public function fw_start(): string
	{
		if ($this->_fw_start) {
			return $this->_fw_start;
		}

		$this->_fw_start = strtotime(
			tribe_get_start_date($this->ID, true, 'Y-m-d H:i:s')
		);

		return $this->_fw_start;
	}

	public function fw_end(): string
	{
		if ($this->_fw_end) {
			return $this->_fw_end;
		}

		$this->_fw_end = strtotime(
			tribe_get_end_date($this->ID, true, 'Y-m-d H:i:s')
		);

		return $this->_fw_end;
	}

	public function fw_date_span(): string
	{
		if ($this->_fw_date_span) {
			return $this->_fw_date_span;
		}

		if ($this->fw_same_day()) {
			$this->_fw_date_span = date('F d, Y', $this->fw_start());

			return $this->_fw_date_span;
		}

		$date_start = $this->fw_start();
		$date_end = $this->fw_end();

		if ($date_start && $date_end) {
			$this->_fw_date_span = join('-', [
				date('F d', $this->fw_start()),
				date('d, Y', $this->fw_end()),
			]);

			return $this->_fw_date_span;
		}

		$this->_fw_date_span = date('F d, Y', $this->fw_start());

		return $this->_fw_date_span;
	}

	public function fw_time_span(): ?string
	{
		if ($this->_fw_time_span) {
			return $this->_fw_time_span;
		}

		if ($this->fw_all_day()) {
			$this->_fw_time_span = null;

			return $this->_fw_time_span;
		}

		$time_start = (function () {
			$meta = $this->fw_start();

			return $meta ?? null;
		})();
		$time_end = (function () {
			$meta = $this->fw_end();

			return $meta ?? null;
		})();

		if ($time_start && $time_end) {
			$this->_fw_time_span = join(' - ', [
				date('g:ia', $time_start),
				date('g:ia', $time_end),
			]);

			return $this->_fw_time_span;
		}

		if ($time_start) {
			$this->_fw_time_span = date('g:ia', $time_start);

			return $this->_fw_time_span;
		}

		$this->_fw_time_span = null;

		return $this->_fw_time_span;
	}

	public function fw_venue(): string
	{
		if ($this->_fw_venue) {
			return $this->_fw_venue;
		}

		$this->_fw_venue = tribe_get_venue($this->ID) ?: '';

		return $this->_fw_venue;
	}

	public function fw_blurb(): string
	{
		if ($this->_fw_blurb) {
			return $this->_fw_blurb;
		}

		$this->_fw_blurb = $this->meta('introduction') ?: '';

		return $this->_fw_blurb;
	}

	public function fw_address_parts(): array
	{
		if ($this->_fw_address_parts) {
			return $this->_fw_address_parts;
		}

		$this->_fw_address_parts = [
			'address' => tribe_get_address($this->ID),
			'city' => tribe_get_city($this->ID),
			'state' => tribe_get_state($this->ID),
			'zip' => tribe_get_zip($this->ID),
			'country' => tribe_get_country($this->ID),
		];

		return $this->_fw_address_parts;
	}

	public function fw_location(): string
	{
		if ($this->_fw_location !== null) {
			return $this->_fw_location;
		}

		$address_parts = $this->fw_address_parts();
		$address = $address_parts['address'];
		$city = $address_parts['city'];
		$state = $address_parts['state'];
		$zip = $address_parts['zip'];
		$country = $address_parts['country'];

		if (!$address) {
			$this->_fw_location = '';

			return $this->_fw_location;
		}

		$this->_fw_location = join(
			'<br>',
			array_filter([
				$address,
				"{$city}, {$state} {$zip}",
				$country !== 'United States' ? $country : '',
			])
		);

		return $this->_fw_location;
	}

	public function fw_categories(): array
	{
		if ($this->_fw_categories) {
			return $this->_fw_categories;
		}

		$this->_fw_categories =
			$this->terms(Tribe__Events__Main::TAXONOMY) ?: [];

		return $this->_fw_categories;
	}

	public function fw_event_types(): array
	{
		if ($this->_fw_event_types) {
			return $this->_fw_event_types;
		}

		$this->_fw_event_types =
			$this->terms('event-type') ?: [];

		return $this->_fw_event_types;
	}

	public function fw_registration_url(): ?string
	{
		if ($this->_fw_registration_url) {
			return $this->_fw_registration_url;
		}

		$this->_fw_registration_url = $this->meta('registration_url');

		return $this->_fw_registration_url;
	}

	public function fw_ld_json(): string
	{
		if ($this->_fw_ld_json) {
			return $this->_fw_ld_json;
		}

		$this->_fw_ld_json = [
			'@context' => 'http://schema.org',
			'@type' => 'Event',
			'name' => $this->title,
			'image' => (new Image($this->meta('image')))->src,
			'description' => $this->fs_blurb(),
			'startDate' => $this->fs_all_day()
				? date('Y-m-d', $this->fs_start())
				: date('c', $this->fs_start()),
			'endDate' => $this->fs_all_day()
				? date('Y-m-d', $this->fs_end())
				: date('c', $this->fs_end()),
		];

		if ($registration_url = $this->fs_registration_url()) {
			$this->_fw_ld_json['offers'] = [
				'@type' => 'Offer',
				'url' => $registration_url,
			];
		}

		if ($venue = $this->fs_venue()) {
			$address_parts = $this->fs_address_parts();
			$this->_fw_ld_json['location'] = [
				'@type' => 'Place',
				'address' => [
					'@type' => 'PostalAddress',
					'addressLocality' => $address_parts['city'],
					'addressRegion' => $address_parts['state'],
					'postalCode' => $address_parts['zip'],
					'streetAddress' => $address_parts['address'],
				],
				'name' => $venue,
			];
		}

		$this->_fw_ld_json = json_encode($this->_fw_ld_json);

		return $this->_fw_ld_json;
	}

	public function fw_actions(): array
	{
		if ($this->_fw_actions) {
			return $this->_fw_actions;
		}

		global $post;

		$post = $this->ID;

		setup_postdata($post);

		$this->_fw_actions = [
			[
				'title' => 'Add to iCal',
				'url' => tribe_get_single_ical_link(),
			],
			[
				'title' => 'Add to Google',
				'url' => tribe_get_gcal_link(),
				'target' => '_blank',
			],
		];

		if ($registration_url = $this->fs_registration_url()) {
			array_unshift($this->_fw_actions, [
				'title' => 'Register',
				'url' => $registration_url,
			]);
		}

		wp_reset_postdata();

		return $this->_fw_actions;
	}
}
