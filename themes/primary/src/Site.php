<?php

namespace Framework;

use Bricks\Elements;
use Timber\Timber;
use Timber\Site as TimberSite;
use Timber\URLHelper;

class Site extends TimberSite
{
	public function __construct()
	{
		/**
		 * wordpress
		 */
		add_action('init', [$this, 'init'], 0);
		add_action('init', [$this, 'register_nav_menus']);
		add_action('after_setup_theme', [$this, 'after_setup_theme']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
		add_action('admin_init', [$this, 'admin_init'], 0);
		add_action('admin_menu', [$this, 'admin_menu'], 0);

		add_filter('upload_mimes', [$this, 'upload_mimes']);

		/**
		 * bricks
		 */
		add_action('init', [$this, 'bricks_register_element_types']);

		add_filter('bricks/builder/i18n', [$this, 'bricks_register_custom_element_categories']);

		/**
		 * acf
		 */
		add_filter(
			'acf/field_group/disable_field_settings_tabs',
			'__return_true'
		);

		/**
		 * timber
		 */
		add_filter('timber/twig/environment/options', [$this, 'update_twig_environment_options']);
		add_filter('timber/context', array($this, 'add_to_timber'));
		add_filter('timber/twig', array($this, 'add_to_twig'));
		add_filter('timber/twig/functions', [$this, 'add_twig_functions']);
		add_filter('timber/post/classmap', [$this, 'add_timber_post_classmap']);
		add_filter('timber/term/classmap', [$this, 'add_timber_taxonomy_classmap']);

		/**
		 * terms
		 */
		add_filter('pre_term_link', function ($termlink, $term): string {
			$term = Timber::get_term($term);

			return $term->fw_termlink ?? $termlink;
		}, 10, 2);

		add_filter('term_link', function ($termlink, $term, $taxonomy): string {
			return untrailingslashit($termlink);
		}, 10, 3);
	}

	public function init(): void
	{
		// If we don't do this, WordPress will strip out SVGs when drawing the_content
		global $allowedtags;

		$allowedtags['svg'] = ['class' => []];
		$allowedtags['use'] = ['xlink:href' => [], 'aria-hidden' => []];
	}

	public function admin_init(): void
	{
		// Remove comments, authors, and tags from Events
		remove_post_type_support('tribe_events', 'comments');
		remove_post_type_support('tribe_events', 'author');
		remove_post_type_support('tribe_events', 'excerpt');
		remove_meta_box('tagsdiv-post_tag', 'tribe_events', 'side');
		remove_meta_box('tribe_events_event_options', 'tribe_events', 'side');
		remove_submenu_page(
			'edit.php?post_type=tribe_events',
			'edit-tags.php?taxonomy=post_tag&amp;post_type=tribe_events'
		);
	}

	public function admin_menu(): void
	{
		// Remove comments support.
		remove_menu_page('edit-comments.php');

		// Remove default post type
		remove_menu_page('edit.php');
	}

	public function register_nav_menus(): void
	{
		$menus = apply_filters('fw_included_menus', [
			'school_navigation' => 'School Navigation',
			'about_navigation' => 'About Navigation',
			'experience_navigation' => 'Experience Navigation',
			'main_navigation' => 'Main Navigation',
			'audience_navigation' => 'Audience Navigation',
			'secondary_navigation' => 'Secondary Navigation',
			'social_navigation' => 'Social Navigation',
			'footer_navigation' => 'Footer Navigation',
			'utility_navigation' => 'Utility Navigation',
		]);

		register_nav_menus($menus);
	}

	public function bricks_register_custom_element_categories(array $categories): array
	{
		$custom_categories = [
			'framework' => esc_html__('Framework', 'framework')
		];

		return array_merge(
			$categories,
			$custom_categories
		);
	}

	public function bricks_register_element_types(): void
	{
		$element_files = [];

		foreach ($element_files as $file) {
			Elements::register_element($file);
		}
	}

	public function upload_mimes(array $mimes): array
	{
		$custom_mimes = [
			'svg_xml' => 'image/svg+xml',
			'svg' => 'image/svg',
			'webp' => 'image/webp'
		];

		return array_merge(
			$mimes,
			$custom_mimes
		);
	}

	public function after_setup_theme(): void
	{
		add_theme_support('title-tag');
		add_theme_support('html5', [
			'comment-list',
			'comment-form',
			'search-form',
			'gallery',
			'caption',
			'style',
			'script',
		]);
		add_theme_support('responsive-embeds');
		add_theme_support('editor-styles');
		add_theme_support('post-thumbnails');
		add_theme_support('menus');
		add_theme_support('wp-block-styles');

		// Remove comments support.
		remove_post_type_support('page', 'comments');

		// add image sizes
		foreach (FW_IMAGE_SIZES as $name => $size) {
			add_image_size($name, $size[0], $size[1], true);
		}

		do_action('fw_after_setup_theme');
	}

	public function enqueue_scripts(): void
	{
		if (bricks_is_builder_main()) {
			return;
		}

		wp_enqueue_style(
			'css-site',
			THEME_ROOT_URI_STATIC . 'css/site.css',
			[],
			filemtime(THEME_ROOT_STATIC . 'css/site.css')
		);

		wp_enqueue_style(
			'css-bricks',
			THEME_ROOT_URI_STATIC . 'css/bricks.css',
			['bricks-frontend'],
			filemtime(THEME_ROOT_STATIC . 'css/bricks.css')
		);

		wp_enqueue_script('jquery');
		wp_enqueue_script(
			'js-site',
			THEME_ROOT_URI_STATIC . 'js/site.js',
			['jquery'],
			filemtime(THEME_ROOT_STATIC . 'js/site.js'),
			true
		);

		wp_localize_script('js-site', 'wp_ajax', [
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax-nonce'),
		]);

		wp_add_inline_script(
			'js-site',
			'if ((typeof window.$) === "undefined") { window.$ = window.jQuery; }',
			'before'
		);

		do_action('fw_enqueue_scripts');
	}

	public function update_twig_environment_options(array $options): array
	{
		$options['autoescape'] = false;

		return $options;
	}

	public function add_to_timber(array $context): array
	{
		global $wp;

		unset($context['theme']);

		$context['framework'] = [
			'config' => FW_CONFIG,
		];
		$context['cell'] =
			$context['framework']['config']['default_cell_width'];
		$context['current_url'] = trailingslashit(
			home_url(add_query_arg([], $wp->request))
		);
		$context['menu'] = array_reduce(
			array_keys(get_registered_nav_menus()),
			function ($carry, $menu) {
				$carry[$menu] = Timber::get_menu($menu);

				return $carry;
			},
			[]
		);
		$context['options'] = get_fields('options');
		$context['sub_nav'] = get_subnav();
		$context['url_helper'] = new URLHelper();
		$context['has_page_media'] = $this->get_has_page_media();

		$context['site'] = $this;

		return apply_filters('fw_timber_add_to_context', $context);
	}

	public function add_to_twig($twig)
	{
		$twig->addExtension(new \Twig\Extension\StringLoaderExtension());

		return $twig;
	}

	public function add_twig_functions(array $functions): array
	{
		$custom_functions = [
			'framework_fn' => [
				'callable' => function ($name, ...$args) {
					$namespaced_fn_name = "Framework\\{$name}";

					if (!function_exists($namespaced_fn_name)) {
						return false;
					}

					return call_user_func_array($namespaced_fn_name, $args);
				}
			],
			'instanceof' => [
				'callable' => fn (mixed $var, string $class_name) => $var instanceof $class_name
			],
			'uniqid' => [
				'callable' => 'wp_unique_id'
			],
			'tel' => [
				'callable' => fn (string $number) => preg_replace('/[^0-9]/', '', $number)
			],
			'icon' => [
				'callable' => function (string $name): void {
					Timber::render('partials/icon.twig', [
						'name' => $name,
					]);
				}
			]
		];

		return array_merge(
			$functions,
			$custom_functions
		);
	}

	public function add_timber_post_classmap(array $classmap): array
	{
		$custom_classmap = [
			'alert' => \Framework\Timber\Post\Alert::class,
			'department' => \Framework\Timber\Post\Department::class,
			'news' => \Framework\Timber\Post\News::class,
			'person' => \Framework\Timber\Post\People::class,
			'program' => \Framework\Timber\Post\Program::class,
			'tribe_events' => \Framework\Timber\Post\TribeEvents::class,
			'blog' => \Framework\Timber\Post\Blog::class
		];

		return array_merge(
			$classmap,
			$custom_classmap
		);
	}

	public function add_timber_taxonomy_classmap(array $classmap): array
	{
		$custom_classmap = [
			'blog-category' => \Framework\Timber\Term\BlogCategory::class,
			'blog-media-type' => \Framework\Timber\Term\BlogMediaType::class,
			'department-category' => \Framework\Timber\Term\DepartmentCategory::class,
			'news-category' => \Framework\Timber\Term\NewsCategory::class,
			'program-degree-level' => \Framework\Timber\Term\ProgramDegreeLevel::class,
			'program-degree-type' => \Framework\Timber\Term\ProgramDegreeType::class,
			'program-learning-mode' => \Framework\Timber\Term\ProgramLearningMode::class,
			'school' => \Framework\Timber\Term\School::class,
			\Tribe__Events__Main::TAXONOMY => \Framework\Timber\Term\EventCategory::class,
			'event-type' => \Framework\Timber\Term\EventType::class
		];

		return array_merge(
			$classmap,
			$custom_classmap
		);
	}
}
