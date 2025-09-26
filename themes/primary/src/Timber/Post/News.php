<?php

namespace Framework\Timber\Post;

use Timber\Timber;
use Timber\Post;
use Timber\Image;
use Timber\Term;

class News extends Post
{
	const AVERAGE_WORDS_PER_MINUTE = 240;

	public $_fw_ld_json;
	public $_fw_date;
	public $_fw_author;
	public $_fw_categories;
	public $_fw_introduction;
	public $_fw_minutes_to_read;
	public $_fw_image;
	public $_fw_schools;

	public function fw_introduction(): ?string
	{
		if ($this->_fw_introduction) {
			return $this->_fw_introduction;
		}

		$this->_fw_introduction = $this->meta('introduction');

		return $this->_fw_introduction;
	}

	public function fw_minutes_to_read(): int
	{
		if ($this->_fw_minutes_to_read) {
			return $this->_fw_minutes_to_read;
		}

		$content = wp_strip_all_tags($this->content, true);
		$total_words = str_word_count($content);
		$minutes_to_read = (int) floor(
			$total_words / self::AVERAGE_WORDS_PER_MINUTE
		);

		$this->_fw_minutes_to_read = $minutes_to_read === 0 ? 1 : $minutes_to_read;

		return $this->_fw_minutes_to_read;
	}

	public function fw_date(): string
	{
		if ($this->_fw_date) {
			return $this->_fw_date;
		}

		$this->_fw_date = $this->meta('date');

		return $this->_fw_date;
	}

	public function fw_author(): ?string
	{
		if ($this->_fw_author) {
			return $this->_fw_author;
		}

		$this->_fw_author = $this->meta('author');

		return $this->_fw_author;
	}

	public function fw_ld_json(): string
	{
		if ($this->_fw_ld_json) {
			return $this->_fw_ld_json;
		}

		$context = Timber::context();

		$this->_fw_ld_json = json_encode([
			'@context' => 'https://schema.org',
			'@type' => 'NewsArticle',
			'url' => $this->link,
			'publisher' => [
				'@type' => $context['framework']['config']['schema_type'],
				'name' => $context['site']->name,
			],
			'headline' => $this->title,
			'mainEntityOfPage' => $this->link,
			'image' => (new Image($this->meta('image')))->src,
			'datePublished' => $this->date('c'),
			'author' => $this->fw_author() ?: '',
			'description' => $this->introduction,
		]);

		return $this->_fw_ld_json;
	}

	public function fw_categories(): array
	{
		if ($this->_fw_categories) {
			return $this->_fw_categories;
		}

		$this->_fw_categories = $this->terms('news-category') ?: [];

		return $this->_fw_categories;
	}

	public function fw_schools(): array
	{
		if ($this->_fw_schools) {
			return $this->_fw_schools;
		}

		$this->_fw_schools = $this->terms('school') ?: [];

		return $this->_fw_schools;
	}

	public function fw_schoolPrimary(): ?Term
	{
		$terms = $this->fw_schools();

		if (!count($terms)) {
			return null;
		}

		return $terms[0];
	}
}
