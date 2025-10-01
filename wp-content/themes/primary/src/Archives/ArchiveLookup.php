<?php

namespace Framework\Archives;

use Framework\Interfaces\ArchiveInterface;

abstract class ArchiveLookup
{
	public static function get_archive_by_post_type(string $post_type): ?ArchiveInterface
	{
		$lookup = [
			'news' => fn () => ArchiveNews::get_instance(),
			'blog' => fn () => ArchiveBlog::get_instance(),
			'events' => fn () => ArchiveEvents::get_instance(),
			'person' => fn () => ArchivePeople::get_instance(),
			'department' => fn () => ArchiveDepartments::get_instance(),
		];

		$archive = array_key_exists($post_type, $lookup) ? $lookup[$post_type]() : null;

		return $archive;
	}
}
