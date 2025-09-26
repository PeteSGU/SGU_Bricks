<?php

namespace Framework\Timber\Post;

use Timber\Post;
use Timber\Image;
use Timber\Timber;
use WP_Post;

class People extends Post
{
	public $_fw_phone_numbers;
	public $_fw_email_address;
	public $_fw_office_hours;
	public $_fw_departments;
	public $_fw_social_links;
	public $_fw_position;
	public $_fw_location;
	public $_fw_data_departments;
	public $_fw_ld_json;
	public $_fw_full_name;

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

	public function fw_departments(): array
	{
		if ($this->_fw_departments) {
			return $this->_fw_departments;
		}

		$this->_fw_departments = array_map(
			fn (WP_Post|int $item) => Timber::get_post($item),
			$this->meta('departments') ?: []
		);

		return $this->_fw_departments;
	}

	public function fw_position(): string
	{
		if ($this->_fw_position) {
			return $this->_fw_position;
		}

		$this->_fw_position = join(
			',<br>',
			array_column(
				$this->meta('title') ?: [],
				'title'
			)
		);

		return $this->_fw_position;
	}

	public function fw_location(): string
	{
		if ($this->_fw_location) {
			return $this->_fw_location;
		}

		$this->_fw_location = $this->meta('location') ?: '';

		return $this->_fw_location;
	}

	public function fw_full_name(): string
	{
		if ($this->_fw_full_name) {
			return $this->_fw_full_name;
		}

		$this->_fw_full_name = join(' ', [
			$this->meta('prefix'),
			$this->meta('first_name'),
			$this->meta('middle_name'),
			$this->meta('last_name'),
			$this->meta('suffix'),
		]);

		return $this->_fw_full_name;
	}

	public function fw_ld_json(): string
	{
		if ($this->_fw_ld_json) {
			return $this->_fw_ld_json;
		}

		$telephone = count($this->fs_phone_numbers())
			? $this->fs_phone_numbers()[0]['number']
			: '';
		$this->_fw_ld_json = json_encode([
			'@context' => 'http://schema.org',
			'@type' => 'Person',
			'email' => "mailto:{$this->fs_fs_email_address()}",
			'image' => (new Image($this->meta('image')))->src,
			'jobTitle' => $this->fs_position(),
			'name' => $this->fs_full_name(),
			'telephone' => $telephone,
			'url' => $this->link,
		]);

		return $this->_fw_ld_json;
	}
}
