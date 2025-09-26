<?php

namespace Framework\Timber\Post;

use Timber\Post;

class Department extends Post
{
	public $_fw_link;
	public $_fw_location;
	public $_fw_office_hours;
	public $_fw_social_links;
	public $_fw_phone_numbers;
	public $_fw_ld_json;
	public $_fw_categories;

	public function link()
	{
		if ($this->_fw_link) {
			return $this->_fw_link;
		}

		$this->_fw_link = $this->meta('detail_link')['url'] ?? null;

		return $this->_fw_link;
	}

	public function fw_ld_json(): string
	{
		if ($this->_fw_ld_json) {
			return $this->_fw_ld_json;
		}

		$this->_fw_ld_json = json_encode([
			'@context' => 'http://schema.org',
			'@type' => 'Place',
			'name' => $this->title,
			'telephone' => $this->phone_number,
			'url' => $this->link,
			'address' => $this->location,
		]);

		return $this->_fw_ld_json;
	}

	public function fw_location(): string
	{
		if ($this->_fw_location) {
			return $this->_fw_location;
		}

		$this->_fw_location = $this->meta('location');

		return $this->_fw_location;
	}

	public function fw_office_hours(): array
	{
		if ($this->_fw_office_hours) {
			return $this->_fw_office_hours;
		}

		$this->_fw_office_hours = $this->meta('office_hours') ?: [];

		return $this->_fw_office_hours;
	}

	public function fw_social_links(): array
	{
		if ($this->_fw_social_links) {
			return $this->_fw_social_links;
		}

		$this->_fw_social_links = $this->meta('social_links') ?: [];

		return $this->_fw_social_links;
	}

	public function fw_categories(): array
	{
		if ($this->_fw_categories) {
			return $this->_fw_categories;
		}

		$this->_fw_categories = $this->terms('department_categories') ?: [];

		return $this->_fw_categories;
	}
}
