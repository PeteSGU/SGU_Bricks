<?php
/**
 * PVFWOF custom fields for pdfviewer post type
 *
 * @package  pdf-viewer-by-themencode
 */

// Control core classes for avoid errors.
if ( class_exists( 'PVFWOF' ) ) {

	$prefix = 'tnc_pvfw_pdf_viewer_fields';

	// Create a metabox.
	PVFWOF::createMetabox(
		$prefix,
		array(
			'title'     => esc_html__( 'TNC PDF viewer Settings', 'pdf-viewer-by-themencode' ),
			'post_type' => 'pdfviewer',
		)
	);

	PVFWOF::createSection(
		$prefix,
		array(
			'title'  => esc_html__( 'Basic Settings', 'pdf-viewer-by-themencode' ),
			'fields' => array(
				array(
					'type'  => 'subheading',
					'title' => esc_html__( 'Basic Settings', 'pdf-viewer-by-themencode' ),
				),

				array(
					'id'         => 'file',
					'type'       => 'upload',
					'title'      => 'PDF File',
					'desc'   => esc_html__( 'Select or Upload a PDF File', 'pdf-viewer-by-themencode' ),
					'attributes' => array(
						'required' => 'required',
					),
				),

				array(
					'id'          => 'pagemode',
					'type'        => 'select',
					'title'       => 'Page Mode',
					'desc'        => esc_html__('Selecting Bookmarks/Thumbs/Attachments here will open the left sidebar containing the selected elements.', 'pdf-viewer-by-themencode' ),
					//'subtitle' => 'Select on Shortcode inserter for Free version',
					'options'     => array(
						'none'        => 'Default',
						'thumbs'      => 'Thumbnails',
						'bookmarks'   => 'Bookmarks',
						'attachments' => 'Attachments',
					),
					// 'attributes' => array(
					// 	'disabled' => 'disabled',
					// ),
					'default'     => 'none',
				),

				array(
					'id'          => 'default_zoom',
					'type'        => 'select',
					'title'       => 'Default Zoom',
					'desc'        => esc_html__('Select your preferred Default zoom, page-fit works best in most cases. But you may need to use other setting depending on your PDF size.', 'pdf-viewer-by-themencode' ),
					//'subtitle' => 'Select on Shortcode inserter for Free version',
					'options'     => array(
						'auto'        => 'Auto',
						'page-fit'    => 'Page Fit',
						'page-width'  => 'Page Width',
						'page-height' => 'Page Height',
						'75'          => '75%',
						'100'         => '100%',
						'150'         => '150%',
						'200'         => '200%',
					),
					// 'attributes' => array(
					// 	'disabled' => 'disabled',
					// ),
					'default'     => 'page-fit',
				),

				array(
					'id'      => 'default_scroll',
					'type'    => 'select',
					'title'   => 'Default Scrolling Mode',
					'desc'    => esc_html__('Set Flip to display as FlipBook. Vertical or Horizontal Modes allows users to scroll to the selected direction to view the PDF.', 'pdf-viewer-by-themencode' ),
					'options' => array(
						'0' => 'Vertical',
						'1' => 'Horizontal',
						'2' => 'Wrapped',
						'3' => 'Flip - Pro Feature (TNC FlipBook)',
					),
					'default' => '0',
				),

				array(
					'id'      => 'default_spread',
					'type'    => 'select',
					'title'   => 'Default Spread',
					'desc'    => esc_html__('Select EVEN or ODD if you want to use 2 page view.', 'pdf-viewer-by-themencode' ),
					'options' => array(
						'0' => 'None',
						'1' => 'ODD',
						'2' => 'EVEN',
					),
					'default' => '0',
				),

				array(
					'id'		=> 'default-page-number',
					'type'		=> 'text',
					'title'		=> esc_html__('Jump to Page', 'pdf-viewer-by-themencode'),
					'desc'      => esc_html__('Enter the page number if you want any specific page to be displayed first.', 'pdf-viewer-by-themencode' ),
					'class'     => 'tnc-pdf-viewer-pro',
				),

				array(
					'id'          => 'icon-size',
					'type'        => 'select',
					'title'       => 'Choose icon Size',
					'options'     => array(
					  'small'  			=> 'Small',
					  'medium'  		=> 'Medium',
					  'large'  			=> 'Large',
					),
					'default'     => 'medium',
					'class'      => 'tnc-pdf-viewer-pro',
				  ),

				  array(	
					   'id'			 =>'select-toolbar-style',
					   'type'        => 'select',
					   'title'       => 'Select Toolbar Style',
					   'options'     => array(
						'top-full-width'  		=> 'Top Full Width',
						'bottom-full-width'     => 'Bottom Full Width',
						'top-center'  			=> 'Top Center',
						'bottom-center'  		=> 'Bottom center',
						),
					   'default'     => 'top-full-width',
					   'class'      => 'tnc-pdf-viewer-pro',
				  ),

				array(
					'id'          => 'language',
					'type'        => 'select',
					'title'       => 'Viewer Language',
					'placeholder' => 'Select Language',
					'options'     => array(
						'en-US' => 'en-US',
						'ach'   => 'ach',
						'af'    => 'af',
						'ak'    => 'ak',
						'an'    => 'an',
						'ar'    => 'ar',
						'as'    => 'as',
						'ast'   => 'ast',
						'az'    => 'az',
						'be'    => 'be',
						'bg'    => 'bg',
						'bn-BD' => 'bn-BD',
						'bn-IN' => 'bn-IN',
						'br'    => 'br',
						'bs'    => 'bs',
						'ca'    => 'ca',
						'cs'    => 'cs',
						'csb'   => 'csb',
						'cy'    => 'cy',
						'da'    => 'da',
						'de'    => 'de',
						'el'    => 'el',
						'en-GB' => 'en-GB',
						'en-ZA' => 'en-ZA',
						'eo'    => 'eo',
						'es-AR' => 'es-AR',
						'es-CL' => 'es-CL',
						'es-ES' => 'es-ES',
						'es-MX' => 'es-MX',
						'et'    => 'et',
						'eu'    => 'eu',
						'fa'    => 'fa',
						'ff'    => 'ff',
						'fi'    => 'fi',
						'fr'    => 'fr',
						'fy-NL' => 'fy-NL',
						'ga-IE' => 'ga-IE',
						'gd'    => 'gd',
						'gl'    => 'gl',
						'gu-IN' => 'gu-IN',
						'he'    => 'he',
						'hi-IN' => 'hi-IN',
						'hr'    => 'hr',
						'hu'    => 'hu',
						'hy-AM' => 'hy-AM',
						'id'    => 'id',
						'is'    => 'is',
						'it'    => 'it',
						'ja'    => 'ja',
						'ka'    => 'ka',
						'kk'    => 'kk',
						'km'    => 'km',
						'kn'    => 'kn',
						'ko'    => 'ko',
						'ku'    => 'ku',
						'lg'    => 'lg',
						'lij'   => 'lij',
						'lt'    => 'lt',
						'lv'    => 'lv',
						'mai'   => 'mai',
						'mk'    => 'mk',
						'ml'    => 'ml',
						'mn'    => 'mn',
						'mr'    => 'mr',
						'ms'    => 'ms',
						'my'    => 'my',
						'nb-NO' => 'nb-NO',
						'nl'    => 'nl',
						'nn-NO' => 'nn-NO',
						'nso'   => 'nso',
						'oc'    => 'oc',
						'or'    => 'or',
						'pa-IN' => 'pa-IN',
						'pl'    => 'pl',
						'pt-BR' => 'pt-BR',
						'pt-PT' => 'pt-PT',
						'rm'    => 'rm',
						'ro'    => 'ro',
						'ru'    => 'ru',
						'rw'    => 'rw',
						'sah'   => 'sah',
						'si'    => 'si',
						'sk'    => 'sk',
						'sl'    => 'sl',
						'son'   => 'son',
						'sq'    => 'sq',
						'sr'    => 'sr',
						'sv-SE' => 'sv-SE',
						'sw'    => 'sw',
						'ta'    => 'ta',
						'ta-LK' => 'ta-LK',
						'te'    => 'te',
						'th'    => 'th',
						'tl'    => 'tl',
						'tn'    => 'tn',
						'tr'    => 'tr',
						'uk'    => 'uk',
						'ur'    => 'ur',
						'vi'    => 'vi',
						'wo'    => 'wo',
						'xh'    => 'xh',
						'zh-CN' => 'zh-CN',
						'zh-TW' => 'zh-TW',
						'zu'    => 'zu',
					),
					'default'     => 'en-US',
				),

				array(
					'id'       => 'return-link',
					'type'     => 'text',
					'title'    => esc_html__( 'Return to Site Link', 'pdf-viewer-by-themencode' ),
					'desc' => esc_html__( 'Enter the url where the Return to site button on bottom right should link to. Keeping blank will use the previous page link.', 'pdf-viewer-by-themencode' ),
				),
			),
		)
	);

	PVFWOF::createSection(
		$prefix,
		array(
			'title'  => 'Toolbar Elements',
			'fields' => array(
				array(
					'type'    => 'subheading',
					'content' => 'Control Visibility of Toolbar Elements',
				),

				array(
					'type'    => 'content',
					'content' => 'Controlling visibility of Toolbar Elements is only available in Pro Version <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">(TNC FlipBook)</a>',
				),

				array(
					'id'      => 'toolbar-elements-use-global-settings',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Use Global Settings', 'pdf-viewer-by-themencode' ),
					'default' => false,
					'class'      => 'tnc-pdf-viewer-pro',
				),

				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Toolbar Elements Visibility', 'pdf-viewer-by-themencode' ),
				),

				array(
					'id'         => 'download',
					'type'       => 'switcher',
					'title'      => 'Download',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'print',
					'type'       => 'switcher',
					'title'      => 'Print',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'fullscreen',
					'type'       => 'switcher',
					'title'      => 'Fullscreen',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'zoom',
					'type'       => 'switcher',
					'title'      => 'Zoom',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'open',
					'type'       => 'switcher',
					'title'      => 'Open',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'pagenav',
					'type'       => 'switcher',
					'title'      => 'Pagenav',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'find',
					'type'       => 'switcher',
					'title'      => 'Find',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ), 
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'current_view',
					'type'       => 'switcher',
					'title'      => 'Current View',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'share',
					'type'       => 'switcher',
					'title'      => 'Share',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'toggle_left',
					'type'       => 'switcher',
					'title'      => 'Toggle Left Menu',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'toggle_menu',
					'type'       => 'switcher',
					'title'      => 'Toggle Right Menu',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'rotate',
					'type'       => 'switcher',
					'title'      => 'Rotate',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'logo',
					'type'       => 'switcher',
					'title'      => 'Logo',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'handtool',
					'type'       => 'switcher',
					'title'      => 'Handtool',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'scroll',
					'type'       => 'switcher',
					'title'      => 'Scroll',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'doc_prop',
					'type'       => 'switcher',
					'title'      => 'Document Properties',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),
				array(
					'id'         => 'spread',
					'type'       => 'switcher',
					'title'      => 'Spread',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
					'class'      => 'tnc-pdf-viewer-pro',
				),

			),
		)
	);

	PVFWOF::createSection(
		$prefix,
		array(
			'title'  => 'Appearance',
			'fields' => array(

				array(
					'type'    => 'subheading',
					'content' => 'Want to use Global Settings?',
				),

				array(
					'type'    => 'content',
					'content' => 'Using global settings is only available in Pro Version <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">(TNC FlipBook)</a>',
				),


				array(
					'type'    => 'subheading',
					'content' => 'Customize the look of your PDF Viewer here',
				),

				array(
					'id'          => 'appearance-select-type',
					'type'        => 'select',
					'title'       => esc_html__( 'Do you want to use a Theme or use custom colors?', 'pdf-viewer-by-themencode' ),
					'placeholder' => 'Select an option',
					'options'     => array(
						'select-theme' => 'Theme',
						'custom-color' => 'Custom Color (Pro Feature)',
					),
					'default'     => 'select-theme',
				),

				array(
					'id'          => 'appearance-select-theme',
					'type'        => 'select',
					'title'       => esc_html__( 'Select Theme', 'pdf-viewer-by-themencode' ),
					'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
					'options'     => array(
						'aqua-white'    => esc_html__( 'Aqua White', 'pdf-viewer-by-themencode' ),
						'midnight-calm' => esc_html__( 'Midnight Calm', 'pdf-viewer-by-themencode' ),
						'material-blue' => esc_html__( 'Material Blue (Pro Feature)', 'pdf-viewer-by-themencode' ),
						'smart-red' => esc_html__( 'Smart Red (Pro Feature)', 'pdf-viewer-by-themencode' ),
						'louis-purple' => esc_html__( 'Louis Purple (Pro Feature)', 'pdf-viewer-by-themencode' ),
						'sea-green' => esc_html__( 'Sea Green (Pro Feature)', 'pdf-viewer-by-themencode' ),
					),
					'default'     => 'midnight-calm',
					'dependency'  => array( 'appearance-select-type', '==', 'select-theme' ),
				),

				array(
					'id'          => 'appearance-select-icon',
					'type'        => 'select',
					'title'       => esc_html__( 'Icon Style', 'pdf-viewer-by-themencode' ),
					'placeholder' => 'Select an option (Pro Feature)',
					'options'     => array(
						'dark-icons'  => 'Dark',
						'light-icons' => 'Light',
					),
					'dependency'  => array( 'appearance-select-type', '==', 'custom-color' ),
				),
				array(
					'id'           => 'default-logo',
					'type'         => 'media',
					'title'        => esc_html__( 'Logo', 'pdf-viewer-by-themencode' ),
					'desc'     => esc_html__( 'Logo that appears on top right corner of viewer page', 'pdf-viewer-by-themencode' ),
					'library'      => 'image',
					'placeholder'  => 'https://',
					'button_title' => 'Upload Logo',
					'remove_title' => 'Remove Logo',
					'class'      => 'tnc-pdf-viewer-pro',
				),

				array(
					'id'           => 'default-favicon',
					'type'         => 'media',
					'title'        => esc_html__( 'Favicon', 'pdf-viewer-by-themencode' ),
					'desc'     => esc_html__( 'Favicon for viewer pages.', 'pdf-viewer-by-themencode' ),
					'library'      => 'image',
					'placeholder'  => 'https://',
					'button_title' => 'Upload Favicon',
					'remove_title' => 'Remove Favicon',
					'class'      => 'tnc-pdf-viewer-pro',
				),
			),
		)
	);

	PVFWOF::createSection(
		$prefix,
		array(
			'title'  => 'Privacy/Security',
			'fields' => array(
				array(
					'type'    => 'subheading',
					'content' => 'Need to protect PDF file access to specific pdf files?',
				),

				array(
					'type'    => 'content',
					'content' => '<a href="https://codecanyon.net/item/wp-file-access-manager/26430349" target="_blank">WP File Access Manager</a> can help you to protect each and every pdf files on your website. You can set permissions for each pdf files (as well as any other file type) by user, user role, user login status. It is also compatible with WooCommerce and Paid Memberships Pro plugins.',
				),

				array(
					'type'    => 'content',
					'content' => 'Note: If you\'re using nginx web server, you need to be able to add a rule to your nginx config, otherwise WP File Access Manager won\'t be able to work.',
				),

				array(
					'type'    => 'content',
					'content' => '<a class="button button-primary" href="https://codecanyon.net/item/wp-file-access-manager/26430349" target="_blank">Get WP File Access Manager now!</a>',
				),

				array(
					'type'    => 'subheading',
					'content' => 'Customize Messages Displayed',
				),

				array(
					'type'    => 'content',
					'content' => 'Following settings are only valid when you have WP File Access Manager installed and activated.',
				),

				array(
					'id'         => 'wfam-error-heading',
					'type'       => 'text',
					'title'      => esc_html__( 'Error Heading', 'pdf-viewer-by-themencode' ),
					'attributes' => array(
						'placeholder' => esc_html__( 'SORRY', 'pdf-viewer-by-themencode' ),
					),
				),

				array(
					'id'         => 'wfam-error-content',
					'type'       => 'textarea',
					'title'      => esc_html__( 'Error Content', 'pdf-viewer-by-themencode' ),
					'attributes' => array(
						'placeholder' => esc_html__( 'You do not have permission to view this file, please contact us if you think this was by a mistake.', 'pdf-viewer-by-themencode' ),
					),
				),

				array(
					'id'         => 'wfam-error-btn-text',
					'type'       => 'text',
					'title'      => esc_html__( 'Error Button Text', 'pdf-viewer-by-themencode' ),
					'attributes' => array(
						'placeholder' => esc_html__( 'Go To Homepage', 'pdf-viewer-by-themencode' ),
					),
				),

				array(
					'id'         => 'wfam-error-btn-url',
					'type'       => 'text',
					'title'      => esc_html__( 'Error Button URL', 'pdf-viewer-by-themencode' ),
					'attributes' => array(
						'placeholder' => home_url(),
					),
				),
			),
		)
	);
}
