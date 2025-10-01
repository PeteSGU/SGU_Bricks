<?php

namespace Framework;

use Illuminate\Support\Collection;
use Timber\Site;
use Timber\Timber;

abstract class Multisite
{
	private static $sites = [];

	public static function get_all(): array
	{
		return self::_get_collection()
			->all();
	}

	public static function get_subsites(): array
	{
		return self::_get_collection()
			->filter(fn (Site $site) => (int) $site->id !== 1)
			->all();
	}

	public static function getSiteById(int $site_id): ?Site
	{
		return self::_get_collection()
			->first(fn (Site $site) => (int) $site->blog_id === $site_id);
	}

	public static function get_terms_by_taxonomy(int $site_id, string $taxonomy, bool $hide_empty = true)
	{
		switch_to_blog($site_id);

		$terms = Timber::get_terms([
			'taxonomy' => $taxonomy,
			'hide_empty' => $hide_empty
		]);

		restore_current_blog();

		return $terms;
	}

	public static function get_site_by_handle(string $handle): ?Site
	{
		return self::_get_collection()
			->first(fn (Site $site) => str_contains($site->path, strtolower($handle)));
	}

	private static function _get_collection(): Collection
	{
		if (self::$sites) return self::$sites;

		self::$sites = collect(
			array_column(get_sites(), 'blog_id')
		)->map(fn ($id) =>  new Site($id));

		return self::$sites;
	}
}
