<?php
/**
 * PDF Viewer Global Settings Page
 *
 * All the settings are here, Powered by PVFWOF.
 *
 * @since 10.0
 *
 * @package pdf-viewer-by-themencode
 */

// Control core classes for avoid errors.
if ( class_exists( 'PVFWOF' ) ) {
  $prefix = 'pvfw_csf_options';

  // Create options
  PVFWOF::createOptions( $prefix, array(
  	'framework_title' => 'TNC PDF Viewer</a>',
    'framework_class' => '',

    'menu_title'  => esc_html__( 'Global Settings', 'pdf-viewer-by-themencode' ),
    'menu_slug'   => 'pdf-viewer-options',
    'menu_type'   => 'submenu',
    'menu_parent' => 'edit.php?post_type=pdfviewer',
    'show_bar_menu'=> false,
    'footer_text'   => '<a class="button button-primary" href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">Get Pro (TNC FlipBook)</a>',
    'footer_after'  => '',
    'footer_credit' => esc_html__( 'Thank you for using TNC PDF viewer' ),
  ) );


  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Welcome', 'pdf-viewer-by-themencode' ),
    'fields' => array(
      array (
        'type'     => 'callback',
        'function' => 'dashboard_design_markup',
      ),
    ),
  ));

  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Get Pro', 'pdf-viewer-by-themencode' ),
    'fields' => array(
      array (
        'type'     => 'callback',
        'function' => 'get_premium_design_markup',
      ),
    ),
  ));

  //
  // Create a section
  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Automatic Display ', 'pdf-viewer-by-themencode' ),
    'fields' => array(

      array(
        'type' => 'heading',
        'content' => esc_html__( 'Automatic Display (Pro Feature)', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type' => 'content',
        'content' => esc_html__( 'You can set options here to have all of your current .pdf links to either open or embed with a PDF Viewer.' , 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'          => 'select-automatic-display',
        'type'        => 'select',
        'title'       => esc_html__( 'Automatic Display', 'pdf-viewer-by-themencode' ),
        'desc'  => esc_html__( 'If you want to convert all current .pdf links to open with PDF Viewer, select any of the options. (Pro Feature)', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
        'options'     => array(
          'auto-iframe'  => esc_html__( 'Automatic iFrame/Embed', 'pdf-viewer-by-themencode' ),
          'auto-link'  => esc_html__( 'Automatic Link', 'pdf-viewer-by-themencode' ),
        ),
        'attributes'  => array(
          'disabled' => 'disabled',
        ),
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'type' => 'subheading',
        'content' => esc_html__('All links ending in .pdf will be replaced with Embedded PDF Viewer automatically', 'pdf-viewer-by-themencode' ),
        'dependency' => array(
          'select-automatic-display', '==', 'auto-iframe'
        ),
      ),

      array(
        'type' => 'subheading',
        'content' => esc_html__('All links ending in .pdf will open with PDF Viewer automatically', 'pdf-viewer-by-themencode' ),
        'dependency' => array( 'select-automatic-display', '==', 'auto-link' ),
      ),

      array(
        'id'          => 'select-automatic-link-target',
        'type'        => 'select',
        'title'       => esc_html__( 'Link Target', 'pdf-viewer-by-themencode' ),
        'subtitle'    => esc_html__( 'Pro Feature', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
        'options'     => array(
          '_parent'  => esc_html__( 'Same Tab', 'pdf-viewer-by-themencode' ),
          '_blank'  => esc_html__( 'New Tab', 'pdf-viewer-by-themencode' ),
        ),
        'dependency' => array( 'select-automatic-display', '==', 'auto-link' ),
        'attributes'  => array(
          'disabled' => 'disabled',
        ),
      ),

      array(
        'id'      => 'select-automatic-iframe-width',
        'type'    => 'text',
        'title'   => esc_html__( 'Automatic iFrame Width', 'pdf-viewer-by-themencode' ),
        'default' => '100%',
        'dependency' => array( 'select-automatic-display', '==', 'auto-iframe' ),
        'attributes'  => array(
          'disabled' => 'disabled',
        ),
      ),

      array(
        'id'      => 'select-automatic-iframe-height',
        'type'    => 'text',
        'title'   => esc_html__( 'Automatic iFrame Height', 'pdf-viewer-by-themencode' ),
        'default' => '800',
        'dependency' => array( 'select-automatic-display', '==', 'auto-iframe' ),
        'attributes'  => array(
          'disabled' => 'disabled',
        ),
      ),

    )
  ) );

  // Create a section
  PVFWOF::createSection( $prefix, array(
    'title'  => 'General Settings',
    'fields' => array(

      array(
        'type' => 'heading',
        'content' => esc_html__( 'General Settings', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type' => 'content',
        'content' => esc_html__( 'There are some common settings related to the viewer, set all the options accordingly.' , 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'           => 'general-logo',
        'type'         => 'media',
        'title'        => esc_html__( 'Logo', 'pdf-viewer-by-themencode' ),
        'desc'  => esc_html__( 'Logo that appears on top right corner of viewer page', 'pdf-viewer-by-themencode' ),
        'library'      => 'image',
        'placeholder'  => 'https://',
        'button_title' => 'Upload Logo',
        'remove_title' => 'Remove Logo',
      ),

      array(
        'id'           => 'general-favicon',
        'type'         => 'media',
        'title'        => esc_html__( 'Favicon', 'pdf-viewer-by-themencode' ),
        'desc'  => esc_html__( 'Favicon for viewer pages.', 'pdf-viewer-by-themencode' ),
        'library'      => 'image',
        'placeholder'  => 'https://',
        'button_title' => esc_html__( 'Upload Favicon', 'pdf-viewer-by-themencode' ),
        'remove_title' => esc_html__( 'Remove Favicon', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'      => 'general-fullscreen-text',
        'type'    => 'text',
        'title'   => esc_html__( 'FullScreen Link Text', 'pdf-viewer-by-themencode' ),
        'desc'  => esc_html__( 'Only applicable for iframe shortcode, the link that appears just above iframe', 'pdf-viewer-by-themencode' ),
        'default' => esc_html__( 'Fullscreen Mode', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'      => 'general-return-text',
        'type'    => 'text',
        'title'   => esc_html__( 'Return to Site Link Text', 'pdf-viewer-by-themencode' ),
        'desc'  => esc_html__( 'Return to site link that appears on bottom right corner of fullscreen viewer', 'pdf-viewer-by-themencode' ),
        'default' => esc_html__( 'Return to Site', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'      => 'general-analytics-id',
        'type'    => 'text',
        'title'   => esc_html__( 'Google Analytics ID', 'pdf-viewer-by-themencode' ),
        'desc'  => esc_html__( 'Pro Feature. Example: UA-XXXXXXXX-X', 'pdf-viewer-by-themencode' ),
        'attributes'  => array(
          'disabled' => 'disabled',
        ),
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'general-iframe-responsive-fix',
        'type'  => 'switcher',
        'title' => esc_html__( 'iFrame Responsive Fix', 'pdf-viewer-by-themencode' ),
      ),

    )
  ) );

  //
  // Create a section
  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Appearance', 'pdf-viewer-by-themencode' ),
    'fields' => array(

      array(
        'type'    => 'heading',
        'content' => esc_html__( 'Customize the look of your PDF Viewer Here', 'pdf-viewer-by-themencode' ),
      ),

     array(
      'id'          => 'appearance-select-type',
      'type'        => 'select',
      'title'       => esc_html__( 'Do you want to use a Theme or use custom colors?', 'pdf-viewer-by-themencode' ),
      'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
      'options'     => array(
        'select-theme'  => esc_html__( 'Theme', 'pdf-viewer-by-themencode' ),
        'custom-color'  => esc_html__( 'Custom Color (Pro Feature)', 'pdf-viewer-by-themencode' ),
      ),
      'default'     => 'select-theme'
      ),

      array(
        'id'          => 'appearance-select-theme',
        'type'        => 'select',
        'title'       => esc_html__( 'Select Theme', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
        'options'     => array(
          'aqua-white'  => esc_html__( 'Aqua White', 'pdf-viewer-by-themencode' ),
          'midnight-calm'  => esc_html__( 'Midnight Calm', 'pdf-viewer-by-themencode' ),
          'material-blue'  => esc_html__( 'Material Blue (Pro Feature)', 'pdf-viewer-by-themencode' ),
          'smart-red'  => esc_html__( 'Smart Red (Pro Feature)', 'pdf-viewer-by-themencode' ),
          'louis-purple'  => esc_html__( 'Louis Purple (Pro Feature)', 'pdf-viewer-by-themencode' ),
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

    )
  ) );

  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Toolbar', 'pdf-viewer-by-themencode' ),
    'fields' => array(

      array(
        'type' => 'heading',
        'content' => esc_html__( 'Toolbar Global Defaults', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type' => 'subheading',
        'content' => __( 'These settings only work on Pro Version <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815/" target="_blank">(TNC FlipBook)</a>', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'          => 'toolbar-default-scroll',
        'type'        => 'select',
        'title'       => esc_html__( 'Default Scroll', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
        'options'     => array(
          '0'  => esc_html__( 'Vertical Scrolling', 'pdf-viewer-by-themencode' ),
          '1'  => esc_html__( 'Horizontal Scrolling', 'pdf-viewer-by-themencode' ),
          '2'  => esc_html__('Wrapped Scrolling', 'pdf-viewer-by-themencode' ),
          '3'  => esc_html__( 'Flip (Pro Feature)', 'pdf-viewer-by-themencode' ),
        ),
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'          => 'toolbar-default-spread',
        'type'        => 'select',
        'title'       => esc_html__( 'Default Spread', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
        'options'     => array(
          '0'  => esc_html__( 'No Spreads', 'pdf-viewer-by-themencode' ),
          '1'  => esc_html__( 'Odd Spreads', 'pdf-viewer-by-themencode' ),
          '2'  => esc_html__( 'Even Spreads', 'pdf-viewer-by-themencode' ),
        ),
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'          => 'toolbar-viewer-language',
        'type'        => 'select',
        'title'       => esc_html__( 'Viewer Language', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select an option', 'pdf-viewer-by-themencode' ),
        'options'     => array(
          'en-US' => 'en-US',
          'ach' => 'ach',
          'af' => 'af',
          'ak' => 'ak',
          'an' => 'an',
          'ar' => 'ar',
          'as' => 'as',
          'ast' => 'ast',
          'az' => 'az',
          'be' => 'be',
          'bg' => 'bg',
          'bn-BD' => 'bn-BD',
          'bn-IN' => 'bn-IN',
          'br' => 'br',
          'bs' => 'bs',
          'ca' => 'ca',
          'cs' => 'cs',
          'csb' => 'csb',
          'cy' => 'cy',
          'da' => 'da',
          'de' => 'de',
          'el' => 'el',
          'en-GB' => 'en-GB',
          'en-ZA' => 'en-ZA',
          'eo' => 'eo',
          'es-AR' => 'es-AR',
          'es-CL' => 'es-CL',
          'es-ES' => 'es-ES',
          'es-MX' => 'es-MX',
          'et' => 'et',
          'eu' => 'eu',
          'fa' => 'fa',
          'ff' => 'ff',
          'fi' => 'fi',
          'fr' => 'fr',
          'fy-NL' => 'fy-NL',
          'ga-IE' => 'ga-IE',
          'gd' => 'gd',
          'gl' => 'gl',
          'gu-IN' => 'gu-IN',
          'he' => 'he',
          'hi-IN' => 'hi-IN',
          'hr' => 'hr',
          'hu' => 'hu',
          'hy-AM' => 'hy-AM',
          'id' => 'id',
          'is' => 'is',
          'it' => 'it',
          'ja' => 'ja',
          'ka' => 'ka',
          'kk' => 'kk',
          'km' => 'km',
          'kn' => 'kn',
          'ko' => 'ko',
          'ku' => 'ku',
          'lg' => 'lg',
          'lij' => 'lij',
          'lt' => 'lt',
          'lv' => 'lv',
          'mai' => 'mai',
          'mk' => 'mk',
          'ml' => 'ml',
          'mn' => 'mn',
          'mr' => 'mr',
          'ms' => 'ms',
          'my' => 'my',
          'nb-NO' => 'nb-NO',
          'nl' => 'nl',
          'nn-NO' => 'nn-NO',
          'nso' => 'nso',
          'oc' => 'oc',
          'or' => 'or',
          'pa-IN' => 'pa-IN',
          'pl' => 'pl',
          'pt-BR' => 'pt-BR',
          'pt-PT' => 'pt-PT',
          'rm' => 'rm',
          'ro' => 'ro',
          'ru' => 'ru',
          'rw' => 'rw',
          'sah' => 'sah',
          'si' => 'si',
          'sk' => 'sk',
          'sl' => 'sl',
          'son' => 'son',
          'sq' => 'sq',
          'sr' => 'sr',
          'sv-SE' => 'sv-SE',
          'sw' => 'sw',
          'ta' => 'ta',
          'ta-LK' => 'ta-LK',
          'te' => 'te',
          'th' => 'th',
          'tl' => 'tl',
          'tn' => 'tn',
          'tr' => 'tr',
          'uk' => 'uk',
          'ur' => 'ur',
          'vi' => 'vi',
          'wo' => 'wo',
          'xh' => 'xh',
          'zh-CN' => 'zh-CN',
          'zh-TW' => 'zh-TW',
          'zu' => 'zu',
        ),
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'type' => 'heading',
        'content' => esc_html__( 'Toolbar Elements Visibility', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type' => 'subheading',
        'content' => __( 'These settings only work on Pro Version <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">(TNC FlipBook)</a>', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'    => 'toolbar-share',
        'type'  => 'switcher',
        'title' => esc_html__( 'Share', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-print',
        'type'  => 'switcher',
        'title' => esc_html__( 'Print', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-download',
        'type'  => 'switcher',
        'title' => esc_html__( 'Download', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-open',
        'type'  => 'switcher',
        'title' => esc_html__( 'Open', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-zoom',
        'type'  => 'switcher',
        'title' => esc_html__( 'Zoom', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-fullscreen',
        'type'  => 'switcher',
        'title' => esc_html__( 'Fullscreen', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-logo',
        'type'  => 'switcher',
        'title' => esc_html__( 'Logo', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-find',
        'type'  => 'switcher',
        'title' => esc_html__( 'Find', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-pagenav',
        'type'  => 'switcher',
        'title' => esc_html__( 'Page Navigation', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-current-view',
        'type'  => 'switcher',
        'title' => esc_html__( 'Current View', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-rotate',
        'type'  => 'switcher',
        'title' => esc_html__( 'Rotate', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-handtool',
        'type'  => 'switcher',
        'title' => esc_html__( 'Handtool', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-doc-prop',
        'type'  => 'switcher',
        'title' => esc_html__( 'Document Properties', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-left-toggle',
        'type'  => 'switcher',
        'title' => esc_html__( 'Left Toggle Menu', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-right-toggle',
        'type'  => 'switcher',
        'title' => esc_html__( 'Right Toggle Menu', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-scroll',
        'type'  => 'switcher',
        'title' => esc_html__( 'Scroll Options', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'    => 'toolbar-spread',
        'type'  => 'switcher',
        'title' => esc_html__( 'Spread Options', 'pdf-viewer-by-themencode' ),
        'default' => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),
    )
));

  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Advanced', 'pdf-viewer-by-themencode' ),
    'fields' => array(

      array(
        'type' => 'heading',
        'content' => esc_html__( 'Advanced Settings', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type' => 'subheading',
        'content' => __( 'These settings below only work on Pro Version <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">(TNC FlipBook)</a>', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'         => 'advanced-context-menu',
        'type'       => 'switcher',
        'title'      => esc_html__( 'Context Menu/Right Click on Viewer Pages (Pro Feature)', 'pdf-viewer-by-themencode' ),
        'subtitle'   => esc_html__( 'Pro Feature', 'pdf-viewer-by-themencode' ),
        'text_on'    => esc_html__( 'Enabled', 'pdf-viewer-by-themencode' ),
        'text_off'   => esc_html__( 'Disabled', 'pdf-viewer-by-themencode' ),
        'text_width' => 100,
        'default'    => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),

      array(
        'id'         => 'advanced-text-copying',
        'type'       => 'switcher',
        'title'      => esc_html__( 'Text Copying (ctrl+c) keyboard shortcut', 'pdf-viewer-by-themencode' ),
        'subtitle'   => esc_html__( 'Pro Feature', 'pdf-viewer-by-themencode' ),
        'text_on'    => esc_html__( 'Enabled', 'pdf-viewer-by-themencode' ),
        'text_off'   => esc_html__( 'Disabled', 'pdf-viewer-by-themencode' ),
        'text_width' => 100,
        'default'    => true,
        'class'      => 'tnc-pdf-viewer-pro',
      ),


    )

  ) );

  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Custom CSS / JS', 'pdf-viewer-by-themencode' ),
    'fields' => array(

      array(
        'type' => 'heading',
        'content' => esc_html__( 'Custom CSS and JS', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type' => 'subheading',
        'content' => esc_html__( 'The custom css and javascript code you put below will be executed on viewer pages only.', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'       => 'custom-css',
        'type'     => 'code_editor',
        'title'    => esc_html__( 'Custom CSS (Only inside Viewer)', 'pdf-viewer-by-themencode' ),
        'subtitle' => __( 'Works on <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">Pro version</a> only', 'pdf-viewer-by-themencode' ),
        'settings' => array(
          'theme'  => 'mbo',
          'mode'   => 'css',
        ),
        'attributes'  => array(
          'disabled' => 'disabled',
        ),
        'class'      => 'tnc-pdf-viewer-pro',
      ),


      array(
        'id'       => 'custom-js',
        'type'     => 'code_editor',
        'title'    => esc_html__( 'Custom JS (Only inside Viewer)', 'pdf-viewer-by-themencode' ),
        'subtitle' => __( 'Works on <a href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815" target="_blank">Pro version</a> only', 'pdf-viewer-by-themencode' ),
        'settings' => array(
          'theme'  => 'monokai',
          'mode'   => 'javascript',
        ),
        'sanitize'  => false,
        'attributes'  => array(
          'disabled' => 'disabled',
        ),
        'class'      => 'tnc-pdf-viewer-pro',
      ),

    )
  ) );

  PVFWOF::createSection( $prefix, array(
    'title'  => esc_html__( 'Export/Import', 'pdf-viewer-by-themencode' ),
    'fields' => array(

      array(
        'type'    => 'heading',
        'content' => esc_html__( 'Export/Import Settings', 'pdf-viewer-by-themencode' ),
      ),
      array(
        'type'    => 'subheading',
        'content' => esc_html__( 'Take backup of all the global settings and import if needed.', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type' => 'backup',
      ),
    )
  ) );
}

/**
 *    welcome dashboard
 */

function dashboard_design_markup() {

  $welcome_titile =  __( 'Welcome to TNC PDF viewer', 'pdf-viewer-by-themencode');
  $welcome_desc   =  __( "Thank you for using TNC PDF viewer. On this page we'll give you some quick details that you may need.", "pdf-viewer-by-themencode");
  $premium_title  =   __( "Pro Features (TNC FlipBook)", "pdf-viewer-by-themencode");
  $upgrade_btn    =  __( 'Upgrade Now', 'pdf-viewer-by-themencode');
  $crown  = plugin_dir_url(__FILE__).'../images/crown.svg';
  $flipbook_mode = __( 'FlipBook Mode', 'pdf-viewer-by-themencode');
  $global_settings = __('Global Settings', 'pdf-viewer-by-themencode');
  $jump_to_page  =   __('Jump to page', 'pdf-viewer-by-themencode');
  $choose_icon_size =  __('Choose Icon Size', 'pdf-viewer-by-themencode');
  $select_toolbar_style =  __('Different Toolbar Styles', 'pdf-viewer-by-themencode');
  $available_themes =   __('More Unique Themes', 'pdf-viewer-by-themencode');
  $automatic_display =   __('Automatic Display', 'pdf-viewer-by-themencode'); 
  $custom_css         = __('Custom CSS', 'pdf-viewer-by-themencode');  
  $custom_js          =  __('Custom JS', 'pdf-viewer-by-themencode');  
  $disable_right_click  =  __('Disable Right Click', 'pdf-viewer-by-themencode');  
  $lots_of_toolbar      = __('Lots of Toolbar options', 'pdf-viewer-by-themencode'); 
  $add_unlimitedP_pdf =  __('Add Unlimited PDF files ', 'pdf-viewer-by-themencode');  
  $installation_title  =  __('Quick overview of TNC FlipBook (Pro):', 'pdf-viewer-by-themencode');  
  $free_title =   __('Available features in TNC PDF viewer ', 'pdf-viewer-by-themencode');  
  $vertical_srooling_mode =  __('Vertical Scrolling Mode', 'pdf-viewer-by-themencode');  
  $horizental_scrooling_mode =  __('Horizontal Scrolling Mode', 'pdf-viewer-by-themencode');
  $wrapped_scrolling_mode   = __('Wrapped Scrolling Mode', 'pdf-viewer-by-themencode');
  $various_zoom_option     =  __('Various Zoom Options', 'pdf-viewer-by-themencode');
  $even_odd_page_spread =  __('Even and Odd Page Spread ', 'pdf-viewer-by-themencode');
  $languages_100        = __('100+ Languages ', 'pdf-viewer-by-themencode');
  $two_free_themes      =  __('2 Color schemes', 'pdf-viewer-by-themencode');
  $logo_on_viewer_page  =  __('Logo on Viewer Page', 'pdf-viewer-by-themencode');
  $favicon_viewer_page  =  __('Favicon on Viewer Page', 'pdf-viewer-by-themencode');
  $import_pdf_file_form_url =  __('Import PDF File from URL', 'pdf-viewer-by-themencode');
  $export_import  =  __('Export/Import', 'pdf-viewer-by-themencode');
  $privacy_security =   __('Privacy/Security', 'pdf-viewer-by-themencode');

  $installation_desc   = __(' You can start by installing the plugin on your WordPress website. Simply go to Plugins->Add new and then search TNC PDF viewer or download the plugin from WordPress.org. Create PDF Viewers using <strong> PDF Viewer > Add New</strong> Menu, then share the link anywhere for users to access.
  
  Here are some videos from our documentation that will help you to figure out specific parts more easily. ', 'pdf-viewer-by-themencode'); 

  $usefull_title =   __('Useful Links ', 'pdf-viewer-by-themencode'); 
  $plugin_live_demo =   __('Plugin Live Demo ', 'pdf-viewer-by-themencode'); 
  $plugin_documentation =  __('  Plugin Documentation ', 'pdf-viewer-by-themencode');
  $video_documentation = __('Video Documentations', 'pdf-viewer-by-themencode');
  $support_portal   =  __('Support Portal', 'pdf-viewer-by-themencode'); 

  
  $welcome_str = <<<EOD
    <div class="pdf-viewer-greetings-wrapper">
      <div class="pdf-viewer-welcome-section">
           <div class="pdf-viewer-welcome-container">
                <div class="pdf-viewer-content">
                    <h2 class="welcome-title">$welcome_titile</h2>
                    <p class="welcome-desc">$welcome_desc</p>
                </div>
           </div>
      </div>
       <div class="pdf-viewer-feature-section">
           <div class="pdf-viewer-feature-container">
              <div class="pdf-viewer-fetaure-content">
                   <div class="premium-title"><h3> $free_title </h3></div>
               </div>
               <div class="tnc-features-wrapper"> 
                  <div class="tnc-fetaures-row">
                        <div class="tnc-features-col">
                            <div class="features-content free">
                                <h4> <span class="dashicons dashicons-yes-alt"></span> $vertical_srooling_mode </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content free">
                                <h4> <span class="dashicons dashicons-yes-alt"></span> $horizental_scrooling_mode </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content free">
                                <h4> <span class="dashicons dashicons-yes-alt"></span> $wrapped_scrolling_mode </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content free">
                                <h4> <span class="dashicons dashicons-yes-alt"></span> $various_zoom_option  </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content free">
                                <h4> <span class="dashicons dashicons-yes-alt"></span> $even_odd_page_spread </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content free">
                                <h4> <span class="dashicons dashicons-yes-alt"></span> $languages_100  </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                        <div class="features-content free">
                            <h4> <span class="dashicons dashicons-yes-alt"></span> $two_free_themes </h4> 
                        </div>  
                      </div>
                      <div class="tnc-features-col">
                        <div class="features-content free">
                            <h4> <span class="dashicons dashicons-yes-alt"></span> $logo_on_viewer_page  </h4> 
                        </div>  
                      </div>
                      <div class="tnc-features-col">
                        <div class="features-content free">
                            <h4> <span class="dashicons dashicons-yes-alt"></span> $favicon_viewer_page </h4> 
                        </div>  
                      </div>
                      <div class="tnc-features-col">
                        <div class="features-content free">
                            <h4> <span class="dashicons dashicons-yes-alt"></span> $import_pdf_file_form_url </h4> 
                        </div>  
                      </div>
                        <div class="tnc-features-col">
                          <div class="features-content free">
                              <h4> <span class="dashicons dashicons-yes-alt"></span> $export_import </h4> 
                          </div>  
                        </div>
                        <div class="tnc-features-col">
                          <div class="features-content free">
                              <h4> <span class="dashicons dashicons-yes-alt"></span> $privacy_security </h4> 
                          </div>  
                        </div>
                    </div>
               </div>
          </div>
      </div>
      <div class="pdf-viewer-feature-section">
           <div class="pdf-viewer-feature-container">
              <div class="pdf-viewer-fetaure-content">
                   <div class="premium-title"><h3> $premium_title </h3> <a target="_blank" href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815">$upgrade_btn</a></div>
               </div>
               <div class="tnc-features-wrapper"> 
                  <div class="tnc-fetaures-row">
                        <div class="tnc-features-col">
                            <div class="features-content">
                               <img src="$crown" alt="">
                               <h4><span>Pro</span> $flipbook_mode </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                               <img src="$crown" alt="">
                                <h4><span> pro </span> $jump_to_page </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $choose_icon_size </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4> <span> pro </span>  $select_toolbar_style  </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                               <img src="$crown" alt="">
                                <h4><span> pro </span>  $available_themes </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $automatic_display  </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                 <img src="$crown" alt="">
                                <h4><span> pro </span>  $custom_css </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                        <div class="features-content">
                            <img src="$crown" alt="">
                            <h4><span>Pro</span>   $global_settings </h4> 
                        </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                 <img src="$crown" alt="">
                                <h4><span> pro </span> $custom_js </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $disable_right_click  </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $lots_of_toolbar </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $add_unlimitedP_pdf </h4> 
                            </div>  
                       </div>
                    </div>
               </div>
               
          </div>
      </div>
      <div class="tnc-installtion-secion">
            <div class="tnc-installation-container">
                <div class="tnc-installation-content">
                     <h3>$installation_title</h3>
                </div>
                <div class="tnc-installtion-video">
                <iframe width="100%" height="500" src="https://www.youtube.com/embed/quDhGfTWur8?si=fkKv-qSs1HZ48AGo"" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            </div>
      </div>
      <div class="tnc-usefull-section">
            <div class="tnc-usefull-section">
              <div class="tnc-usefull-container">
                    <div class="tnc-usefull-content">
                          <h3> $usefull_title </h3>
                    </div>
                    <div  class="tnc-usefull-grid">
                          <a target="_blank" href="https://themencode.com/tncflipbook-preview/">
                            <div class="tnc-usefull-col">
                                <div class="usefull-icon">
                                    <span class="dashicons dashicons-desktop"></span>
                                </div>
                                <div class="usefull-titile">
                                    <h4>$plugin_live_demo</h4>
                                </div>
                            </div>
                         </a>
                       <a target="_blank" href="https://docs.themencode.com/docs/pdf-viewer-by-themencode/">  
                          <div class="tnc-usefull-col">
                              <div class="usefull-icon">
                                    <span class="dashicons dashicons-welcome-write-blog"></span>
                              </div>
                              <div class="usefull-titile">
                                  <h4>$plugin_documentation</h4>
                              </div>
                           </div>
                      </a>
                      <a target="_blank" href="https://youtu.be/zLmHpjYO9z4">
                        <div class="tnc-usefull-col">
                            <div class="usefull-icon">
                                <span class="dashicons dashicons-format-video"></span>
                            </div>
                            <div class="usefull-titile">
                                <h4> $video_documentation</h4>
                            </div>
                        </div>
                     </a>
                     <a target="_blank" href="https://themencode.support-hub.io/">
                        <div class="tnc-usefull-col">
                              <div class="usefull-icon">
                                  <span class="dashicons dashicons-groups"></span>
                              </div>
                              <div class="usefull-titile">
                                  <h4>$support_portal</h4>
                              </div>
                          </div>
                        </a>
                      </div>  
                </div>
            </div>
      </div>
    </div>
  EOD;

   echo  $welcome_str;
}


function get_premium_design_markup() { 

  $welcome_titile =  __( 'Get the Pro Version (TNC FlipBook)', 'pdf-viewer-by-themencode');
  $welcome_desc   =  __( "TNC FlipBook - PDF viewer for WordPress is the pro version of TNC PDF viewer. Unlock some amazing features including FlipBook, Customizability, Unlimited PDF's and many more...", "pdf-viewer-by-themencode");
  $premium_title  =   __( "Pro Features", "pdf-viewer-by-themencode");
  $upgrade_btn    =  __( 'Upgrade Now', 'pdf-viewer-by-themencode');
  $crown  = plugin_dir_url(__FILE__).'../images/crown.svg';
  $flipbook_mode = __( 'Flipbook Mode', 'pdf-viewer-by-themencode');
  $global_settings = __('Global Settings', 'pdf-viewer-by-themencode');
  $jump_to_page  =   __('Jump to page', 'pdf-viewer-by-themencode');
  $choose_icon_size =  __('Choose icon Size', 'pdf-viewer-by-themencode');
  $select_toolbar_style =  __('Select Toolbar Style', 'pdf-viewer-by-themencode');
  $available_themes =   __('Available Themes', 'pdf-viewer-by-themencode');
  $automatic_display =   __('Automatic Display', 'pdf-viewer-by-themencode'); 
  $custom_css         = __('Custom CSS', 'pdf-viewer-by-themencode');  
  $custom_js          =  __('Custom JS', 'pdf-viewer-by-themencode');  
  $disable_right_click  =  __('Disable Right Click', 'pdf-viewer-by-themencode');  
  $lots_of_toolbar      = __('Lots of Toolbar Options', 'pdf-viewer-by-themencode'); 
  $add_unlimitedP_pdf =  __('Add Unlimited PDF ', 'pdf-viewer-by-themencode'); 
  $installation_title   =   __('Quick overview of TNC FlipBook (Pro): ', 'pdf-viewer-by-themencode'); 


  $welcome_str = <<<EOD
    <div class="pdf-viewer-greetings-wrapper">
      <div class="pdf-viewer-welcome-section">
           <div class="pdf-viewer-welcome-container">
                <div class="pdf-viewer-content">
                    <h2 class="welcome-title">$welcome_titile</h2>
                    <p class="welcome-desc">$welcome_desc</p>
                </div>
           </div>
      </div>
      <div class="pdf-viewer-feature-section">
           <div class="pdf-viewer-feature-container">
              <div class="pdf-viewer-fetaure-content">
                   <div class="premium-title"><h3> $premium_title </h3> <a target="_blank" href="https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815">$upgrade_btn </a></div>
               </div>
               <div class="tnc-features-wrapper"> 
                  <div class="tnc-fetaures-row">
                        <div class="tnc-features-col">
                            <div class="features-content">
                               <img src="$crown" alt="">
                               <h4><span>Pro</span> $flipbook_mode </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                               <img src="$crown" alt="">
                                <h4><span>Pro</span> $global_settings  </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                               <img src="$crown" alt="">
                                <h4><span> pro </span> $jump_to_page  </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $choose_icon_size </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4> <span> pro </span> $select_toolbar_style </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                               <img src="$crown" alt="">
                                <h4><span> pro </span>  $available_themes </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $automatic_display </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                 <img src="$crown" alt="">
                                <h4><span> pro </span> $custom_css </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                 <img src="$crown" alt="">
                                <h4><span> pro </span> $custom_js </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span> $disable_right_click </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span>  $lots_of_toolbar </h4> 
                            </div>  
                       </div>
                       <div class="tnc-features-col">
                            <div class="features-content">
                                <img src="$crown" alt="">
                                <h4><span> pro </span>  $add_unlimitedP_pdf </h4> 
                            </div>  
                       </div>
                    </div>
               </div>
          </div>
      </div>
    </div>
      <div class="tnc-installtion-secion">
            <div class="tnc-installation-container">
                <div class="tnc-installation-content">
                     <h3>$installation_title</h3>
                </div>
                <div class="tnc-installtion-video">
                <iframe width="100%" height="500" src="https://www.youtube.com/embed/quDhGfTWur8?si=fkKv-qSs1HZ48AGo"" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            </div>
      </div>
  EOD;

   echo  $welcome_str;

}

 
