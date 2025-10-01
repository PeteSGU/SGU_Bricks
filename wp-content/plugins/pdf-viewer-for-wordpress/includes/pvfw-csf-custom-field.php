<?php
/**
 * PVFWOF custom fields for pdfviewer post type
 *
 * @package  pdf-viewer-for-wordpress
 */

// Control core classes for avoid errors.
if ( class_exists( 'PVFWOF' ) ) {

	$prefix = 'tnc_pvfw_pdf_viewer_fields';

	// Create a metabox.
	PVFWOF::createMetabox(
		$prefix,
		array(
			'title'     => esc_html__( 'FlipBook Settings', 'pdf-viewer-for-wordpress' ),
			'post_type' => 'pdfviewer',
		)
	);

	PVFWOF::createSection(
		$prefix,
		array(
			'title'  => esc_html__( 'Basic Settings', 'pdf-viewer-for-wordpress' ),
			'fields' => array(
				array(
					'type'  => 'subheading',
					'title' => esc_html__( 'Basic Settings', 'pdf-viewer-for-wordpress' ),
				),

				array(
					'id'         => 'file',
					'type'       => 'upload',
					'title'      => 'PDF File',
					'desc'   => esc_html__( 'Select or Upload a PDF File', 'pdf-viewer-for-wordpress' ),
					'attributes' => array(
						'required' => 'required',
					),
				),

				array(
					'id'      => 'default_scroll',
					'type'    => 'select',
					'title'   => 'Default Scrolling Mode',
					'desc'   => esc_html__('Set Flip to display as FlipBook. Vertical or Horizontal Modes allows users to scroll to the selected direction to view the PDF.
					', 'pdf-viewer-for-wordpress'),
					'options' => array(
						'global' => 'Use Global',
						'0' => 'Vertical',
						'1' => 'Horizontal',
						'2' => 'Wrapped',
						'3' => 'Flip',
					),
					'default' => 'global',
				),

				array(
					'id'      => 'default_spread',
					'type'    => 'select',
					'title'   => 'Default Spread',
					'desc'    => esc_html__('Controls how many pages should be displayed on screen. Select EVEN or ODD if you want to use 2 page view.
					', 'pdf-viewer-for-wordpress'),
					'options' => array(
						'global' => 'Use Global',
						'0' => 'None',
						'1' => 'ODD',
						'2' => 'EVEN',
					),
					'default' => 'global',
				),

				array(
					'id'          => 'default-zoom',
					'type'        => 'select',
					'title'       => 'Default Zoom',
					'desc'        => esc_html__( 'Select your preferred Default zoom, page-fit works best in most cases. But you may need to use other setting depending on your PDF size.', 'pdf-viewer-for-wordpress'),
					'placeholder' => 'Select Default Zoom',
					'options'     => array(
						'global' => 'Use Global',
						'auto'        => 'Auto',
						'page-fit'    => 'Page Fit',
						'page-width'  => 'Page Width',
						'page-height' => 'Page Height',
						'75'          => '75%',
						'100'         => '100%',
						'150'         => '150%',
						'200'         => '200%',
					),
					'default'     => 'global',
				),

				array(
                    'id'                    => 'toolbar-default-page-mode',
                    'type'                  => 'select',
                    'title'                 => esc_html__( 'Page Mode', 'pdf-viewer-for-wordpress' ),
					'desc'                  => esc_html__( 'Selecting Bookmarks/Thumbs/Attachments here will open the left sidebar containing the selected elements.
					', 'pdf-viewer-for-wordpress'),
                    'options'               => array(
					  'global' => 'Use Global',
                      'none'            => 'None',
                      'bookmarks'       => 'Bookmarks',
                      'thumbs'          => 'Thumbnails',
                      'attachments'     => 'Attachments',
                    ),
                    'default' => 'global',
                ),

				array(
					'id'		=> 'default-page-number',
					'type'		=> 'text',
					'title'		=> esc_html__('Jump to Page', 'pdf-viewer-for-wordpress'),
					'desc'      => esc_html__( 'Enter the page number if you want any specific page to be displayed first.
					', 'pdf-viewer-for-wordpress'),
				),

				array(
					'id'          => 'icon-size',
					'type'        => 'select',
					'title'       => 'Choose icon Size',
					'options'     => array(
					  'small'  			=> 'Small',
					  'medium'  		=> 'Medium',
					  'large'  			=> 'Large',
					  'global' => 'Use Global Settings'
					),
					'default'     => 'global'
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
						'global' => 'Use Global Settings'
						),
					   'default'     => 'global'
				  ),

				 array(
					'id'          => 'language',
					'type'        => 'select',
					'title'       => 'Viewer Language',
					'placeholder' => 'Select Language',
					'options'     => array(
						'global' => 'Use Global',
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
					'default'     => 'global',
				),

				array(
					'id'       => 'return-link',
					'type'     => 'text',
					'title'    => esc_html__( 'Return to Site Link', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Enter the url where the Return to site button on bottom right should link to. Keeping blank will use the previous page link.', 'pdf-viewer-for-wordpress' ),
				),

				array(
					'id'       => 'default-return-text',
					'type'     => 'text',
					'title'    => esc_html__( 'Return to Site Link Text', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Return to site link that appears on bottom right corner of fullscreen viewer', 'pdf-viewer-for-wordpress' ),
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
					'content' => 'Want to use Global Settings?',
				),

				array(
					'id'      => 'toolbar-elements-use-global-settings',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Use Global Settings', 'pdf-viewer-for-wordpress' ),
					'default' => true,
				),

				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Toolbar Elements Visibility', 'pdf-viewer-for-wordpress' ),
				),

				array(
					'id'         => 'download',
					'type'       => 'switcher',
					'title'      => 'Download',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),

				array(
					'type'    => 'submessage',
					'content' => 'Need to protect Download strictly along with Preventing Download managers like IDM? Check out <a target="_blank" href="https://portal.themencode.com/downloads/secure-pdfs-tnc-flipbook-addon/">Secure PDFs Addon</a>.',
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),

				array(
					'id'         => 'print',
					'type'       => 'switcher',
					'title'      => 'Print',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'fullscreen',
					'type'       => 'switcher',
					'title'      => 'Fullscreen',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'zoom',
					'type'       => 'switcher',
					'title'      => 'Zoom',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'open',
					'type'       => 'switcher',
					'title'      => 'Open',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'pagenav',
					'type'       => 'switcher',
					'title'      => 'Pagenav',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'find',
					'type'       => 'switcher',
					'title'      => 'Find',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'current_view',
					'type'       => 'switcher',
					'title'      => 'Current View',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'share',
					'type'       => 'switcher',
					'title'      => 'Share',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'toggle_left',
					'type'       => 'switcher',
					'title'      => 'Toggle Left Menu',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'toggle_menu',
					'type'       => 'switcher',
					'title'      => 'Toggle Right Menu',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'rotate',
					'type'       => 'switcher',
					'title'      => 'Rotate',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'logo',
					'type'       => 'switcher',
					'title'      => 'Logo',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'handtool',
					'type'       => 'switcher',
					'title'      => 'Handtool',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'scroll',
					'type'       => 'switcher',
					'title'      => 'Scroll',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'doc_prop',
					'type'       => 'switcher',
					'title'      => 'Document Properties',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
				),
				array(
					'id'         => 'spread',
					'type'       => 'switcher',
					'title'      => 'Spread',
					'default'    => true,
					'dependency' => array( 'toolbar-elements-use-global-settings', '==', false ),
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
					'id'      => 'appearance-use-global-settings',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Use Global Settings', 'pdf-viewer-for-wordpress' ),
					'default' => true,
				),

				array(
					'type'    => 'subheading',
					'content' => 'Customize the look of your FlipBooks here',
					'dependency'  => array( 'appearance-use-global-settings', '==', false )
				),

				array(
					'id'    => 'appearance-disable-flip-sound',
					'type'  => 'switcher',
					'title' => esc_html__( 'Disable Flip Sound', 'pdf-viewer-for-wordpress' ),
					'text_on'    => 'Yes',
					'text_off'   => 'No',
					'dependency'  => array( 'appearance-use-global-settings', '==', false ),
				),

				array(
					'id'          => 'appearance-select-type',
					'type'        => 'select',
					'title'       => esc_html__( 'Do you want to use a Theme or use custom colors?', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'options'     => array(
						'select-theme' => 'Theme',
						'custom-color' => 'Custom Color (Defined Below)',
					),
					'default'     => 'select-theme',
					'dependency'  => array( 'appearance-use-global-settings', '==', false ),
				),

				array(
					'id'          => 'appearance-select-theme',
					'type'        => 'select',
					'title'       => esc_html__( 'Select Theme', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'options'     => array(
						'aqua-white'    => 'Aqua White',
						'material-blue' => 'Material Blue',
						'midnight-calm' => 'Midnight Calm',
            'smart-red' => 'Smart Red',
            'louis-purple' => 'Louis Purple',
            'sea-green' => 'Sea Green',
					),
					'default'     => 'midnight-calm',
					'dependency'  => array( 'appearance-select-type|appearance-use-global-settings', '==|==', 'select-theme|false' ),
				),

				array(
					'id'         => 'appearance-select-colors',
					'type'       => 'color_group',
					'title'      => 'Select Colors',
					'options'    => array(
						'primary-color'   => 'Primary Color',
						'secondary-color' => 'Secondary Color',
						'text-color'      => 'Text Color',
					),
					'dependency' => array( 'appearance-select-type|appearance-use-global-settings', '==|==', 'custom-color|false' ),
				),

				array(
					'id'          => 'appearance-select-icon',
					'type'        => 'select',
					'title'       => esc_html__( 'Icon Style', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'options'     => array(
						'dark-icons'  => 'Dark',
						'light-icons' => 'Light',
					),
					'dependency'  => array( 'appearance-select-type|appearance-use-global-settings', '==|==', 'custom-color|false' ),
				),

				array(
					'id'           => 'default-logo',
					'type'         => 'media',
					'title'        => esc_html__( 'Logo', 'pdf-viewer-for-wordpress' ),
					'desc'     => esc_html__( 'Logo that appears on top right corner of viewer page', 'pdf-viewer-for-wordpress' ),
					'dependency'  => array( 'appearance-use-global-settings', '==', false ),
					'library'      => 'image',
					'placeholder'  => 'https://',
					'button_title' => 'Upload Logo',
					'remove_title' => 'Remove Logo',
				),

				array(
					'id'           => 'default-favicon',
					'type'         => 'media',
					'title'        => esc_html__( 'Favicon', 'pdf-viewer-for-wordpress' ),
					'desc'     => esc_html__( 'Favicon for viewer pages.', 'pdf-viewer-for-wordpress' ),
					'dependency'  => array( 'appearance-use-global-settings', '==', false ),
					'library'      => 'image',
					'placeholder'  => 'https://',
					'button_title' => 'Upload Favicon',
					'remove_title' => 'Remove Favicon',
				),
				
				array(
					'id'      => 'default-viewer-bg-image-settings',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Use Background Image', 'pdf-viewer-for-wordpress' ),
					'dependency'  => array( 'appearance-use-global-settings', '==', false ),
					'default' => false,
				),

				array(
					'id'           => 'default-bg-img',
					'type'         => 'media',
					'title'        => esc_html__( 'Background Image', 'pdf-viewer-for-wordpress' ),
					'desc'     => esc_html__( 'Background Image for viewer pages.', 'pdf-viewer-for-wordpress' ),
					'dependency'  => array( 'appearance-use-global-settings|default-viewer-bg-image-settings', '==|==', 'false|true' ),
					'library'      => 'image',
					'placeholder'  => 'https://',
					'button_title' => 'Upload Image',
					'remove_title' => 'Remove Image',
				),

				array(
					'id'           => 'default-bg-img-size',
					'type'         => 'select',
					'title'        => esc_html__( 'Background Size', 'pdf-viewer-for-wordpress' ),
					'dependency'   => array( 'appearance-use-global-settings|default-viewer-bg-image-settings', '==|==', 'false|true' ),
					'placeholder'  => 'Select an option',
					'options'      => array(
						'auto'  => 'auto',
						'cover' => 'cover',
						'contain' => 'contain',
						'initial' => 'initial',
						'inherit' => 'inherit',
						'revert' => 'revert',
						'revert-layer' => 'revert-layer',
						'unset' => 'unset',
					),
					'default'     => 'cover',
				),

				array(
					'id'           => 'default-bg-img-repeat',
					'type'        => 'select',
					'title'        => esc_html__( 'Background Repeat', 'pdf-viewer-for-wordpress' ),
					'dependency'  => array( 'appearance-use-global-settings|default-viewer-bg-image-settings', '==|==', 'false|true' ),
					'placeholder' => 'Select an option',
					'options'     => array(
						'repeat'  => 'repeat',
						'no-repeat'  => 'no-repeat',
						'repeat-x' => 'repeat-x',
						'repeat-y' => 'repeat-y',
						'initial' => 'initial',
						'inherit' => 'inherit',
						'space' => 'space',
						'round' => 'round',
						'revert' => 'revert',
						'revert-layer' => 'revert-layer',
						'unset' => 'unset',
					),
					'default'     => 'no-repeat',
				),

				array(
					'id'           => 'default-bg-img-attachment',
					'type'        => 'select',
					'title'        => esc_html__( 'Background Attachment', 'pdf-viewer-for-wordpress' ),
					'dependency'  => array( 'appearance-use-global-settings|default-viewer-bg-image-settings', '==|==', 'false|true' ),
					'placeholder' => 'Select an option',
					'options'     => array(
						'scroll'  => 'scroll',
						'fixed' => 'fixed',
						'local' => 'local',
						'initial' => 'initial',
						'inherit' => 'inherit',
						'revert' => 'revert',
						'revert-layer' => 'revert-layer',
						'unset' => 'unset',
					),
					'default'     => 'scroll',
				),

				array(
					'id'           => 'default-bg-img-position',
					'type'        => 'text',
					'title'        => esc_html__( 'Background Position', 'pdf-viewer-for-wordpress' ),
					'desc'					=> esc_html__( 'Use the x and y keywords to specify the horizontal and vertical position separately, like this: center center or left 20px or 50% top or right 75% etc.' ),
					'dependency'  => array( 'appearance-use-global-settings|default-viewer-bg-image-settings', '==|==', 'false|true' ),
					'placeholder' => 'center center',
					'default'     => 'center center',
				),

				array(	
					'id'		  =>'flip_type',
					'type'        => 'select',
					'title'       => 'Flip Type',
					'default'	  => 'shadow',
					'options'     => array(
					 'global'		=> 'Global',
					 'smooth'       => 'Smooth',
					 'hard'  		=> 'Hard',
					),
					'dependency'  => array( 'appearance-use-global-settings', '==', false )
			    ),

				array(
					'id'    => 'page-turning-speed',
					'type'  => 'text',
					'title' => esc_html__( 'Page Turning Speed (ms)', 'pdf-viewer-for-wordpress' ),
					'dependency'  => array( 'appearance-use-global-settings', '==', 'false' )
				),

				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Want to setup an archive/bookshelf along with popup opening?','pdf-viewer-for-wordpress' ),
				), 

				array(
					'type'    => 'submessage',
					'content' => 'Check out <a  target="_blank" href="https://portal.themencode.com/downloads/display-pdf-viewer-for-wordpress/"> Display Addon </a> which lets you display FlipBooks as BookShelf or List/Grid Style archives. Additionally, you can choose to open FlipBooks in popups which makes it even compelling!',
				),

			),
		)
	);

	// Create a section.
	PVFWOF::createSection(
		$prefix,
		array(
			'title'  => 'Transcribe',
			'fields' => array(

				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Transcribe Settings', 'pdf-viewer-for-wordpress' ),
				),

				array(
					'type'    => 'content',
					'content' => esc_html__( 'Transcribe allows your readers to hear audio version of the PDF along wit highlighted reading. Pleae note that your PDF file must contain text for this feature.', 'pdf-viewer-for-wordpress' ),
				),

				array(
					'id'      => 'toolbar-elements-use-transcribe',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Transcribe', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Enable or Disable the Transcrbe feature for this FlipBook', 'pdf-viewer-for-wordpress' ),
					'default' => false,
				),

				array(
					'id'           => 'pdf-language-code',
					'type'        => 'select',
					'title'        => esc_html__( 'PDF Language', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Select the Language of your PDF content. Please make sure to select correct language code as per your PDF content. ', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'options'     => array(
						'en'    => 'en',  
						'it'    => 'it',  
						'sv'    => 'sv',  
						'fr'    => 'fr',
						'ms'	=> 'ms',
						'de'    => 'de',
						'he'	=> 'he',
						'id'	=> 'id',
						'bg'    => 'bg',
						'es'    => 'es',
						'fi'	=> 'fi',
						'pt'    => 'pt',
						'nl'	=> 'nl',
						'ja'    => 'ja',
						'ro'    => 'ro',
						'th'    => 'th',
						'hr'    => 'hr',
						'sk'	=> 'sk',
						'hi'	=> 'hi',
						'uk'	=> 'uk',
						'zh'	=> 'zh',
						'vi'	=> 'vi',
						'el'    => 'el',
						'ru'	=> 'ru',
						'ca'    => 'ca',
						'nb'    => 'nb',
						'da'    => 'da',
						'hu'	=> 'hu',
						'tr'    => 'tr',
						'ko'    => 'ko',
						'pl'    => 'pl',
						'cs'    => 'cs'
						
					),
					'default'     => 'en',
					'dependency'  => array( 'toolbar-elements-use-transcribe', '==', '1' )
					
				),

				// en models 
				array(
					'id'           => 'default-model-en',
					'type'         => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder'  => 'Select an option',
					'desc' 		   => esc_html__('Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress'),
					'options'      => array(
						'Samantha'   			  				 	 => 'Samantha (en-US)',  
						'Aaron'   		 							 => 'Aaron (en-US)',
						'Albert'	   		 					 	 => 'Albert (en-US)', 
						'Arthur'	   			    				 => 'Arthur (en-GB)',
						'Bad News'	     							 => 'Bad News (en-US)',
						'Bahh'  			        				 => 'Bahh (en-US)',
						'Bells'	 		 		    				 => 'Bells (en-US)',
						'Boing'          		   					 => 'Boing (en-US)',
						'Bubbles'         		   					 => 'Bubbles (en-US)',
						'Catherine'                					 => 'Catherine (en-AU)',
						'Cellos'           							 => 'Cellos (en-US)',
						'Daniel (English (United Kingdom))'          => 'Daniel (English (United Kingdom)) (en-GB)',
						'Eddy (English (United Kingdom))'            => 'Eddy (English (United Kingdom)) (en-GB)',
						'Eddy (English (United States))'           	 => 'Eddy (English (United States)) (en-US)',
						'Flo (English (United Kingdom))'          	 => 'Flo (English (United Kingdom)) (en-GB)',
						'Flo (English (United States))'           	 => 'Flo (English (United States)) (en-US)',
						'Fred'           				  			 => 'Fred (en-US)',
						'Good News'          				 	     => 'Good News (en-US)',
						'Gordon'          				 		     => 'Gordon (en-AU)',
						'Grandma (English (United Kingdom)) '        => 'Grandma (English (United Kingdom)) (en-GB)',
						'Grandma (English (United States))'          => 'Grandma (English (United States)) (en-US)',
						'Grandpa (English (United Kingdom))'         => 'Grandpa (English (United Kingdom)) (en-GB)',
						'Grandpa (English (United States)) '         => 'Grandpa (English (United States)) (en-US)',
						'Junior'           				 			 => 'Junior (en-US)',
						'Karen'           				  			 => 'Karen (en-AU)',
						'Kathy'           				  			 => 'Kathy (en-US)',
						'Martha'           				   			 => 'Martha (en-GB)',
						'Moira'           				   			 => 'Moira (en-IE)',
						'Nicky'          				  			 => 'Nicky (en-US)',
						'Organ'          				  			 => 'Organ (en-US)',
						'Ralph'          				  			 => 'Ralph (en-US)',
						'Reed (English (United Kingdom))'        	 => 'Reed (English (United Kingdom)) (en-GB)',
						'Reed (English (United States))'          	 => 'Reed (English (United States)) (en-US)',
						'Rishi'         				  			 => 'Rishi (en-IN)',
						'Rocko (English (United Kingdom))'           => 'Rocko (English (United Kingdom)) (en-GB)',
						'Sandy (English (United Kingdom))'         	 => 'Sandy (English (United Kingdom)) (en-GB)',
						'Sandy (English (United States))'            => 'Sandy (English (United States)) (en-US)',
						'Rocko (English (United States))'        	 => 'Rocko (English (United States)) (en-US)',
						'Shelley (English (United Kingdom))'         => 'Shelley (English (United Kingdom)) (en-GB)',
						'Shelley (English (United States))'          => 'Shelley (English (United States)) (en-US)',
						'Superstar'          				   		 => 'Superstar (en-US)',
						'Tessa'         				   			 => 'Tessa (en-ZA)',
						'Trinoids'        					  		 => 'Trinoids (en-US)',
						'Whisper'        					   		 => 'Whisper (en-US)',
						'Wobble'          				   		     => 'Wobble (en-US)',
						'Zarvox'          				  			 => 'Zarvox (en-US)',
						'Google US English'        					 => 'Google US English (en-US)',
						'Google UK English Female'        			 => 'Google UK English Female (en-GB)',
						'Google UK English Male'         		     => 'Google UK English Male (en-GB)',	
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'en|1' ),
				),


					// it models 
					array(
						'id'           => 'default-model-it',
						'type'        => 'select',
						'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
						'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
						'options'     => array(
							'Alice'   											=> 'Alice (it-IT)',  
							'Eddy (Italian (Italy))'   							=> 'Eddy (Italian (Italy)) (it-IT)',
							'Flo (Italian (Italy))'	   							=> 'Flo (Italian (Italy)) (it-IT)', 
							'Grandma (Italian (Italy))'							=> 'Grandma (Italian (Italy)) (it-IT)',
							'Grandpa (Italian (Italy))'	     					=> 'Grandpa (Italian (Italy)) (it-IT)',
							'Reed (Italian (Italy))'  			 			    => 'Reed (Italian (Italy)) (it-IT)',
							'Rocko (Italian (Italy))'	 		 			    => 'Rocko (Italian (Italy)) (it-IT)',
							'Sandy (Italian (Italy))'          				    => 'Sandy (Italian (Italy)) (it-IT)',
							'Shelley (Italian (Italy))'           			    => 'Shelley (Italian (Italy)) (it-IT)',
							'Google italiano'           						=> 'Google italiano (it-IT)',
							
						),
						'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'it|1' ),
					),

					
	
			  // sv models 
				array(
					'id'           => 'default-model-sv',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Alva'   											=> 'Alva (sv-SE)',  	
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'sv|1' ),
				),

				// fr models 

				array(
					'id'           => 'default-model-fr',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Amélie'   											=>'Amélie (fr-CA)', 
						'Daniel (French (France))'							=>'Daniel (French (France)) (fr-FR)',
						'Eddy (French (Canada))'							=>'Eddy (French (Canada)) (fr-CA)',	
						'Eddy (French (France)) '							=>'Eddy (French (France)) (fr-FR)',
						'Flo (French (Canada))'								=>'Flo (French (Canada)) (fr-CA)',
						'Flo (French (France))'								=>'Flo (French (France)) (fr-FR)',
						'Grandma (French (Canada))'							=>'Grandma (French (Canada)) (fr-CA)',
						'Grandma (French (France))'							=>'Grandma (French (France)) (fr-FR)',
						'Grandpa (French (Canada))'							=>'Grandpa (French (Canada)) (fr-CA)',
						'Grandpa (French (France))'							=>'Grandpa (French (France)) (fr-FR)',
						'acques (fr-FR)'									=>'Jacques (fr-FR)',
						'Marie (fr-FR)'										=>'Marie (fr-FR)',
						'Reed (French (Canada))'							=>'Reed (French (Canada)) (fr-CA)',
						'Rocko (French (Canada))'							=>'Rocko (French (Canada)) (fr-CA)',
						'Rocko (French (France))'							=>'Rocko (French (France)) (fr-FR)',
						'Sandy (French (Canada))'							=>'Sandy (French (Canada)) (fr-CA)',
						'Sandy (French (France))'							=>'Sandy (French (France)) (fr-FR)',
						'Shelley (French (Canada))'							=>'Shelley (French (Canada)) (fr-CA)',
						'Shelley (French (France))'							=>'Shelley (French (France)) (fr-FR)',
						'Thomas (fr-FR)'									=>'Thomas (fr-FR)',
						'Google français (fr-FR)'							=>'Google français (fr-FR)',
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'fr|1' ),
				),

				// ms models 

				array(
					'id'           => 'default-model-ms',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Amira'   											=> 'Amira (ms-MY)',  	
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'ms|1' ),
				),

				
				// de models 
				array(
					'id'           => 'default-model-de',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Anna'   											=> 'Anna (de-DE)',  	
						'Eddy (German (Germany))'   						=> 'Eddy (German (Germany)) (de-DE)',
						'Flo (German (Germany))'   							=> 'Flo (German (Germany)) (de-DE)',
						'Grandma (German (Germany))'   					    => 'Grandma (German (Germany)) (de-DE)',
						'Grandpa (German (Germany))'   					    => 'Grandpa (German (Germany)) (de-DE)',
						'Helena'   											=> 'Helena (de-DE)',
						'Martin'   											=> 'Martin (de-DE)',
						'Reed (German (Germany))'   						=> 'Reed (German (Germany)) (de-DE)',
						'Rocko (German (Germany)) '   						=> 'Rocko (German (Germany)) (de-DE)',
						'Sandy (German (Germany))'   						=> 'Sandy (German (Germany)) (de-DE)',
						'Shelley (German (Germany))'   						=> 'Shelley (German (Germany)) (de-DE)',
						'Google Deutsch'   									=> 'Google Deutsch (de-DE)',

					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'de|1' ),
				), 

				// he models 
				array(
					'id'           => 'default-model-he',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Carmit'   											=> 'Carmit (he-IL)',  	
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'he|1' ),
				),

				// id models 

				array(
					'id'           => 'default-model-id',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Damayanti'   											=> 'Damayanti (id-ID)',  	
						'Google Bahasa Indonesia '							=> 'Google Bahasa Indonesia (id-ID)'
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'id|1' ),
				),

				// id models 

				array(
					'id'          => 'default-model-bg',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Daria'   									    => 'Daria (bg-BG)',  	
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'bg|1' ),
				),

				// es models 
					array(
						'id'          => 'default-model-es',
						'type'        => 'select',
						'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
						'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
						'options'     => array(
							'Eddy (Spanish (Spain))'   						=> 'Eddy (Spanish (Spain)) (es-ES)',  
							'Eddy (Spanish (Mexico))'   				    => 'Eddy (Spanish (Mexico)) (es-MX)', 	
							'Flo (Spanish (Spain))'   					    => 'Flo (Spanish (Spain)) (es-ES)', 	
							'Flo (Spanish (Mexico))'   						=> 'Flo (Spanish (Mexico)) (es-MX)', 	
							'Grandma (Spanish (Spain))'   				    => 'Grandma (Spanish (Spain)) (es-ES)', 	
							'Grandma (Spanish (Mexico))'   				    => 'Grandma (Spanish (Mexico)) (es-MX)', 	
							'Grandpa (Spanish (Spain))'   					=> 'Grandpa (Spanish (Spain)) (es-ES)', 	
							'Grandpa (Spanish (Mexico))'   					=> 'Grandpa (Spanish (Mexico)) (es-MX)', 	
							'Mónica'   							   			=> 'Mónica (es-ES)', 	
							'Paulina'   									=> 'Paulina (es-MX)', 	
							'Reed (Spanish (Spain))'   						=> 'Reed (Spanish (Spain)) (es-ES)', 	
							'Reed (Spanish (Mexico))'   				    => 'Reed (Spanish (Mexico)) (es-MX)', 	
							'Rocko (Spanish (Spain))'   					=> 'Rocko (Spanish (Spain)) (es-ES)', 	
							'Rocko (Spanish (Mexico))'   				    => 'Rocko (Spanish (Mexico)) (es-MX)', 	
							'Sandy (Spanish (Spain))'   					=> 'Sandy (Spanish (Spain)) (es-ES)', 	
							'Sandy (Spanish (Mexico))'   				    => 'Sandy (Spanish (Mexico)) (es-MX)', 	
							'Shelley (Spanish (Spain))'   					=> 'Shelley (Spanish (Spain)) (es-ES)', 
							'Shelley (Spanish (Mexico))'   					=> 'Shelley (Spanish (Mexico)) (es-MX)',	
							'Google español'   								=> 'Google español (es-ES)',	
							'Google español de Estados Unidos'   			=> 'Google español de Estados Unidos (es-US)',	
						),
						'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'es|1' ),
					),
			    // fi models
				array(
					'id'          => 'default-model-fi',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Eddy (Finnish (Finland))'   									    => 'Eddy (Finnish (Finland)) (fi-FI)',  	
						'Flo (Finnish (Finland))'   							            => 'Flo (Finnish (Finland)) (fi-FI)', 
						'Grandma (Finnish (Finland))'   							        => 'Grandma (Finnish (Finland)) (fi-FI)', 
						'Grandpa (Finnish (Finland))'   							        => 'Grandpa (Finnish (Finland)) (fi-FI)', 
						'Reed (Finnish (Finland))'   							            => 'Reed (Finnish (Finland)) (fi-FI)', 
						'Rocko (Finnish (Finland))'   							            => 'Rocko (Finnish (Finland)) (fi-FI)', 
						'Sandy (Finnish (Finland))'   							            => 'Sandy (Finnish (Finland)) (fi-FI)', 
						'Satu'   							            				    => 'Satu (fi-FI)', 
						'Shelley (Finnish (Finland))'   							        => 'Shelley (Finnish (Finland)) (fi-FI)', 					
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'fi|1' ),
				),


				// pt models 
				array(
					'id'          => 'default-model-pt',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Eddy (Portuguese (Brazil))'   			    => 'Eddy (Portuguese (Brazil)) (pt-BR)',
						'Flo (Portuguese (Brazil))'   			    => 'Flo (Portuguese (Brazil)) (pt-BR)',    	
						'Grandma (Portuguese (Brazil))'   		    => 'Grandma (Portuguese (Brazil)) (pt-BR)',  
						'Grandpa (Portuguese (Brazil))'   		    => 'Grandpa (Portuguese (Brazil)) (pt-BR)',  
						'Joana'   						  			=> 'Joana (pt-PT)',  
						'Luciana'   								=> 'Luciana (pt-BR)',  
						'Reed (Portuguese (Brazil))'   				=> 'Reed (Portuguese (Brazil)) (pt-BR)',  
						'Rocko (Portuguese (Brazil))'   			=> 'Rocko (Portuguese (Brazil)) (pt-BR)',  
						'Sandy (Portuguese (Brazil))'   			=> 'Sandy (Portuguese (Brazil)) (pt-BR)',  
						'Shelley (Portuguese (Brazil))'   		    => 'Shelley (Portuguese (Brazil)) (pt-BR)',  
						'Google português do Brasil '   		    => 'Google português do Brasil (pt-BR)',  
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'pt|1' ),
				),

				// nl models 

				array(
					'id'          => 'default-model-nl',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Ellen'   			    => 'Ellen (nl-BE)',
						'Xander'   			    => 'Xander (nl-NL)',
						'Google Nederlands'     => 'Google Nederlands (nl-NL)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'nl|1' ),
				),

				// ja models 
				array(
					'id'          => 'default-model-ja',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Hattori'   	=> 'Hattori (ja-JP)',
						'Kyoko'   			    => 'Kyoko (ja-JP)',
						'O-Ren'     			=> 'O-Ren (ja-JP)',
						'Google 日本語'    		 => 'Google 日本語 (ja-JP)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'ja|1' ),
				),

				// ro models 

				array(
					'id'          => 'default-model-ro',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Ioana'   			=> 'Ioana (ro-RO)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'ro|1' ),
				),

				// th models 

				array(
					'id'          => 'default-model-th',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Kanya'   			=> 'Kanya (th-TH)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'th|1' ),
				),

				//hr models 

				array(
					'id'          => 'default-model-hr',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Lana'   			=> 'Lana (hr-HR)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'hr|1' ),
				),

				// sk models 

				array(
					'id'          => 'default-model-sk',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Laura'   			=> 'Laura (sk-SK)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'sk|1' ),
				),

				//uk models 
				array(
					'id'          => 'default-model-uk',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Lesya'   			=> 'Lesya (uk-UA)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'uk|1' ),
				),

				// hi models 

				array(
					'id'          => 'default-model-hi',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Lekha'   			=> 'Lekha (hi-IN)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'hi|1' ),
				),
				
				// vi models 

				array(
					'id'          => 'default-model-vi',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Linh'   			=> 'Linh (vi-VN)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'vi|1' ),
				),

				// el models

				array(
					'id'          => 'default-model-el',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Melina'   			=> 'Melina (el-GR)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'el|1' ),
				),

				// ru models 

				array(
					'id'          => 'default-model-ru',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Milena'   						=> 'Milena (ru-RU)',
						'Google русский'   			    => 'Google русский (ru-RU)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'ru|1' ),
				),

				// ca models

				array(
					'id'          => 'default-model-ca',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Montse'   			=> 'Montse (ca-ES)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'ca|1' ),
				),

				// nb models 


				array(
					'id'          => 'default-model-nb',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Nora'   			=> 'Nora (nb-NO)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'nb|1' ),
				),


				// da models 

				array(
					'id'          => 'default-model-da',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Sara'   			=> 'Sara (da-DK)',
					),	

					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'da|1' ),
				),

				// hu models 

				array(
					'id'          => 'default-model-hu',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Tünde'   			=> 'Tünde (hu-HU)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'hu|1' ),
				),


				// tr models 


				array(
					'id'          => 'default-model-tr',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Yelda'   			=> 'Yelda (tr-TR)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'tr|1' ),
				),


				// ko models 


				array(
					'id'          => 'default-model-ko',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Yuna'   				=> 'Yuna (ko-KR)',
						'Google 한국의'   			=> 'Google 한국의 (ko-KR)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'ko|1' ),
				),

				// pl models

				array(
					'id'          => 'default-model-pl',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
				     	'Zosia'   					=> 'Zosia (pl-PL)',
						'Google polski'				=> 'Google polski (pl-PL)',
					),	
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'pl|1' ),
				),

				// cs models 

				array(
					'id'          => 'default-model-cs',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Zuzana'   			=> 'Zuzana (cs-CZ)',
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'cs|1' ),
				),	
				// zh models 
				array(
					'id'           => 'default-model-zh',
					'type'        => 'select',
					'title'        => esc_html__( 'Default Model', 'pdf-viewer-for-wordpress' ),
					'placeholder' => 'Select an option',
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Select Default voice/Model. Readers may be able to change this based on your setting on visibility section below.', 'pdf-viewer-for-wordpress' ),
					'options'     => array(
						'Li-Mu'   					=> 'Li-Mu (zh-CN)',  
						'Meijia'   		 			=> 'Meijia (zh-TW)',
						'Sinji'	   		 			=> 'Sinji (zh-HK)', 
						'Tingting'	   			    => 'Tingting (zh-CN)',
						'Yu-shu'	     			=> 'Yu-shu (zh-CN)',
						'Google 普通话'  			 => 'Google 普通话（中国大陆）(zh-CN)',
						'Google 粤語（香港'	 		  => 'Google 粤語（香港） (zh-HK)',
						'Google 國語（臺灣'           => 'Google 國語（臺灣） (zh-TW)',
					),
					'dependency'   => array( 'pdf-language-code|toolbar-elements-use-transcribe', '==|==', 'zh|1' ),
				),
				
				
				// Default rate 

				array(
					'id'    	   => 'default-speed',
					'type' 		   => 'slider',
					'title'        => esc_html__( 'Default Speed', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Select the Default Speed of the voice, User may be able to choose it based on visibility settings below', 'pdf-viewer-for-wordpress' ),
					'min'     	   => 0.5,
					'max'    	   => 2,
					'step'         => 0.1,
					'value' 	   => 1,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),

				),

				array(
					'id'    	   => 'default-pitch',
					'type' 		   => 'slider',
					'title'        => esc_html__( 'Default Pitch', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Select the Default Pitch of the voice, User may be able to choose it based on visibility settings below', 'pdf-viewer-for-wordpress' ),
					'min'     	   => 0.5,
					'max'    	   => 2,
					'step'   	   => 0.1,
					'value' 	   => 1,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),
		
				// Heading 		
				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Visibility Settings', 'pdf-viewer-for-wordpress' ),
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),

				array(
					'type'    => 'content',
					'content' => esc_html__( 'Select which controls you want your readers to have for Transcribe feature.', 'pdf-viewer-for-wordpress' ),
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),

				// Enbale disable voice
				array(
					'id'      => 'visibility-voice',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Voice/Model Selection', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Display or Hide the ability to change Voice/Model for readers.', 'pdf-viewer-for-wordpress' ),
					'default' => true,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),
				// Enbale disable rate
				array(
					'id'      => 'visibility-speed',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Speed Control', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Display or Hide the ability to change Speed for readers.', 'pdf-viewer-for-wordpress' ),
					'default' => true,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),

				// Enable disable pitch 

				array(
					'id'      => 'visibility-pitch',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Pitch Control', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Display or Hide the ability to change Pitch for readers.', 'pdf-viewer-for-wordpress' ),
					'default' => true,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),

				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Visibility Settings for Mobile Screen', 'pdf-viewer-for-wordpress' ),
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),

				// Enbale disable voice
				array(
					'id'      => 'visibility-voice-responsive',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Voice/Model Selection', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Display or Hide the ability to change Voice/Model for readers.', 'pdf-viewer-for-wordpress' ),
					'default' => true,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),
				// Enbale disable rate
				array(
					'id'      => 'visibility-speed-responsive',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Speed Control', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Display or Hide the ability to change Speed for readers.', 'pdf-viewer-for-wordpress' ),
					'default' => true,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),

				// Enable disable pitch 

				array(
					'id'      => 'visibility-pitch-responsive',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Pitch Control', 'pdf-viewer-for-wordpress' ),
					'desc' => esc_html__( 'Display or Hide the ability to change Pitch for readers.', 'pdf-viewer-for-wordpress' ),
					'default' => true,
					'dependency'   => array( 'toolbar-elements-use-transcribe', '==', '1' ),
				),

			),
		)
	);


	PVFWOF::createSection(
		$prefix,
		array(
			'title'  => 'Misc. Options',
			'fields' => array(
				
				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Misc. Options', 'pdf-viewer-for-wordpress' ),
				),

				array(
					'id'         => 'same-mobile-spread',
					'type'       => 'switcher',
					'title'      => 'Use same spread setting on mobile',
					'default'    => false,
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
					'content' => '<a href="https://codecanyon.net/item/wp-file-access-manager/26430349" target="_blank">WP File Access Manager</a> can help you to protect each and every pdf files on your website. You can set permissions for each pdf files (as well as any other file type) by user, user role, user login status. Its also compatible with WooCommerce and Paid Memberships Pro plugins.',
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
					'title'      => esc_html__( 'Error Heading', 'pdf-viewer-for-wordpress' ),
					'attributes' => array(
						'placeholder' => esc_html__( 'SORRY', 'pdf-viewer-for-wordpress' ),
					),
				),

				array(
					'id'         => 'wfam-error-content',
					'type'       => 'textarea',
					'title'      => esc_html__( 'Error Content', 'pdf-viewer-for-wordpress' ),
					'attributes' => array(
						'placeholder' => esc_html__( 'You do not have permission to view this file, please contact us if you think this was by a mistake.', 'pdf-viewer-for-wordpress' ),
					),
				),

				array(
					'id'         => 'wfam-error-btn-text',
					'type'       => 'text',
					'title'      => esc_html__( 'Error Button Text', 'pdf-viewer-for-wordpress' ),
					'attributes' => array(
						'placeholder' => esc_html__( 'Go To Homepage', 'pdf-viewer-for-wordpress' ),
					),
				),

				array(
					'id'         => 'wfam-error-btn-url',
					'type'       => 'text',
					'title'      => esc_html__( 'Error Button URL', 'pdf-viewer-for-wordpress' ),
					'attributes' => array(
						'placeholder' => home_url(),
					),
				),
			),
		)
	);

	// Create a shortcoder
	PVFWOF::createShortcoder( $prefix, array(
		'button_title' => 'Add Shortcode',
	) );
}



/**
 *     Wp file acess manager and preview addon 
 */

 function wpfam_preview_addon_function(){
    $wpfile_url = "https://codecanyon.net/item/wp-file-access-manager/26430349";
    $wpfile_image = plugin_dir_url(__FILE__).'../images/wpfile-pdf.png';
    $preview_url  = "https://portal.themencode.com/downloads/preview-pdf-viewer-for-wordpress-addon/";
    $preview_image = plugin_dir_url(__FILE__).'../images/Preview-Icon.png';


	?>
			<div class="addon-integration-wrapper privacy-sc-addon">
					<div class="addon-integration-container">
						<div class="addon-integration-grid">
						     <div class="addon-integration-item">
								 <div class="image-wrap">
									<img src="<?php echo $wpfile_image;?>" alt="">
									</div>
									<div class="item-content">
										<h3><?php _e( 'WP File Access Manager - Easy Way to Restrict WordPress Uploads', 'pdf-viewer-for-wordpress');?></h3>
										<p><?php _e( 'If you want to restrict access to your media library files by user login/role/woocommerce purchase or paid memberships pro level, this plugin is for you!', 'pdf-viewer-for-wordpress');?></p>
									</div>
									<div class="item-btn">
										<a target="_blank" href="<?php echo esc_url($wpfile_url);?>"> <?php _e( 'Get It Now', 'pdf-viewer-for-wordpress');?> </a>
								   </div>
								</div>
								<div class="addon-integration-item">
									<div class="image-wrap">
										<img src="<?php echo $preview_image; ?>" alt="">
										</div>
										<div class="item-content">
											<h3> <?php _e( 'Preview – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-for-wordpress');?></h3>
											<p> <?php _e( "This addon, you can select specific pages of a PDF file and set restrictions for viewers. Restricted viewers will only see a partial view of those selected pages.", "pdf-viewer-for-wordpress");?> </p>
										</div>
										<div class="item-btn">
											<a target="_blank" href="<?php  echo esc_url($preview_url);?>"> <?php _e( 'Get It Now', 'pdf-viewer-for-wordpress');?></a>
										</div>
								</div>
							</div>
						</div>
					</div>
				
   <?php  
 }



