<?php

namespace Framework\Timber\Post;

class Blog extends News
{
	public function fw_categories(): array
	{
		if ($this->_fw_categories) {
			return $this->_fw_categories;
		}

		$this->_fw_categories = $this->terms('blog-category') ?: [];

		return $this->_fw_categories;
	}
}
