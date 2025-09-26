<?php

namespace Framework\Timber\Post;

use Timber\Post;

class Alert extends Post
{
	public $_fw_has_not_expired;
	public $_fw_context;

	public function fw_has_not_expired()
	{
		if ($this->_fw_has_not_expired) {
			return $this->_fw_has_not_expired;
		}

		$end_date = $this->meta('end_date');

		$this->_fw_has_not_expired = !$end_date || $end_date > time();

		return $this->_fw_has_not_expired;
	}

	public function fw_context()
	{
		if ($this->_fw_context) {
			return $this->_fw_context;
		}

		$this->_fw_context = [
			'published' => strtotime($this->modified_date),
			'title' => \Framework\text_encode($this->title),
			'description' => strip_tags(
				$this->meta('description'),
				'<strong><em><a><p>'
			),
		];

		return $this->_fw_context;
	}
}
