<?php

namespace Framework\Timber\Post;

use Timber\Post;

class Program extends Post
{
	public $_fw_blurb;
	public $_fw_tax_learning_modes;
	public $_fw_tax_degree_levels;
	public $_fw_tax_degree_types;
	public $_fw_post_department;
	public $_fw_data_category;

	public function fw_blurb(): string
	{
		if ($this->_fw_blurb) {
			return $this->_fw_blurb;
		}

		$this->_fw_blurb = $this->meta('blurb') ?: '';

		return $this->_fw_blurb;
	}

	public function fw_learning_modes(): array
	{
		if ($this->_fw_tax_learning_modes) {
			return $this->_fw_tax_learning_modes;
		}

		$this->_fw_tax_learning_modes =
			$this->terms('program_learning_mode') ?: [];

		return $this->_fw_tax_learning_modes;
	}

	public function fw_degree_levels(): array
	{
		if ($this->_fw_tax_degree_levels) {
			return $this->_fw_tax_degree_levels;
		}

		$this->_fw_tax_degree_levels =
			$this->terms('program_degree_level') ?: [];

		return $this->_fw_tax_degree_levels;
	}

	public function fw_degree_types(): array
	{
		if ($this->_fw_tax_degree_types) {
			return $this->_fw_tax_degree_types;
		}

		$this->_fw_tax_degree_types = $this->terms('program_degree_type') ?: [];

		return $this->_fw_tax_degree_types;
	}

	public function fw_department(): Post
	{
		if ($this->_fw_post_department) {
			return $this->_fw_post_department;
		}

		$this->_fw_post_department = new Post($this->meta('department'));

		return $this->_fw_post_department;
	}

	public function fw_data_category(): string
	{
		if ($this->_fw_data_category) {
			return $this->_fw_data_category;
		}

		$modes = array_column($this->fs_learning_modes() ?: [], 'name');
		$department = $this->fs_department()
			? [$this->fs_department()->name]
			: [];
		$levels = array_column($this->fs_degree_levels() ?: [], 'name');

		$this->_fw_data_category = join(
			', ',
			array_merge($modes, $department, $levels)
		);

		return $this->_fw_data_category;
	}
}
