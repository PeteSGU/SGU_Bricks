<?php

namespace Framework;

use Timber\Timber;
use Timber\Image;
use Timber\ImageHelper;

function icon(string $name)
{
	Timber::render('partials/icon.twig', [
		'name' => $name,
	]);
}

function get_image_attributes(Image $image, $sources, $alt = ''): array
{
	$return_arr = [
		'srcset' => '',
		'default_size' => null,
		'alt' => $alt ?: $image->alt(),
	];

	if (empty($sources) || !count($sources)) {
		return array_merge($return_arr, [
			'default_size' => $image->src(),
			'width' => $image->width(),
			'height' => $image->height(),
		]);
	}

	$smallest_image_size_dimensions = FW_IMAGE_SIZES[end($sources)];
	$srcset = array_map(function ($key) use ($image, $sources) {
		$size = $sources[$key];
		$source_meta = FW_IMAGE_SIZES[$size];
		$src = $image->src($size);

		return [
			'src' => $src,
			'width' => $source_meta[0],
		];
	}, array_keys($sources));

	$default_size = $srcset[count($srcset) - 1]['src'];
	$srcset_as_str = join(
		', ',
		array_map(
			fn($source) => $source['src'] . ' ' . $source['width'] . 'w',
			$srcset
		)
	);

	return array_merge($return_arr, [
		'srcset' => $srcset_as_str,
		'default_size' => $default_size,
		'width' => $smallest_image_size_dimensions[0],
		'height' => $smallest_image_size_dimensions[1],
	]);
}

function get_picture_attributes(
	Image $image,
	array $sources,
	string $default,
	string $alt = ''
): array {
	$image_parts = ImageHelper::analyze_url($image->src);

	$return_arr = [
		'srcset' => '',
		'default_size' => null,
		'alt' => $alt ?: $image->alt(),
	];

	if (empty($sources) || !count($sources)) {
		return array_merge($return_arr, [
			'default_size' => $image->src(),
			'width' => $image->width(),
			'height' => $image->height(),
		]);
	}

	$smallest_image_size_dimensions = FW_IMAGE_SIZES[end($sources)];
	$srcset = array_map(function ($key) use ($image, $sources, $image_parts) {
		$size = $sources[$key];
		$source_meta = FW_IMAGE_SIZES[$size];
		$src = $image->src($size);

		return [
			'min_width' => $key,
			'type' => "image/{$image_parts['extension']}",
			'src' => $src,
			'width' => $source_meta[0],
			'height' => $source_meta[1],
		];
	}, array_keys($sources));

	$default_size = $image->src($default);

	return array_merge($return_arr, [
		'srcset' => $srcset,
		'default_size' => $default_size,
		'width' => $smallest_image_size_dimensions[0],
		'height' => $smallest_image_size_dimensions[1],
	]);
}

function get_embed_video_data(string $url): ?array
{
	if (
		strpos($url, 'youtu.be') !== false ||
		strpos($url, 'youtube.com') !== false
	) {
		// Try to grab the ID from the YouTube URL (courtesy of various Stack Overflow authors)
		$pattern = '%^# Match any youtube URL
			(?:https?://)?  # Optional scheme. Either http or https
			(?:www\.)?	    # Optional www subdomain
			(?:			    # Group host alternatives
				youtu\.be/	# Either youtu.be,
			| youtube\.com  # or youtube.com
				(?:		    # Group path alternatives
				/embed/	    # Either /embed/
				| /v/		    # or /v/
				| .*v=		# or /watch\?v=
				)			    # End path alternatives.
			)			    # End host alternatives.
			([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
			($|&).*		    # if additional parameters are also in query string after video id.
			$%x';
		$result = preg_match($pattern, $url, $matches);

		if ($result === false) {
			return null;
		} else {
			$video_id = $matches[1];

			return [
				'service' => 'youtube',
				'id' => $video_id,
				'public_url' => 'https://www.youtube.com/watch?v=' . $video_id,
				'embed_url' => 'https://www.youtube.com/embed/' . $video_id,
			];
		}
	} elseif (strpos($url, 'vimeo.com') !== false) {
		$url_pieces = explode('/', $url);
		$video_id = end($url_pieces);

		return [
			'service' => 'vimeo',
			'id' => $video_id,
			'public_url' => 'https://vimeo.com/' . $video_id,
			'embed_url' =>
				'//player.vimeo.com/video/' .
				$video_id .
				'?title=0&byline=0&portrait=0',
		];
	}

	return null;
}

function get_embed_for_video_link(string $url): ?string
{
	$data = get_embed_video_data($url);

	if (!$data) {
		return null;
	}

	$video_service = $data['service'];
	$embed_url = $data['embed_url'];

	$lookup = [
		'youtube' => function ($url) {
			return '<iframe width="720" height="405" src="' .
				$url .
				'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		},
		'vimeo' => function ($url) {
			return '<iframe src="' .
				$url .
				'" allow="autoplay; fullscreen" allowfullscreen></iframe>';
		},
	];

	return array_key_exists($video_service, $lookup)
		? $lookup[$video_service]($embed_url)
		: null;
}
