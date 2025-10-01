<?php
if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.
// Control core classes for avoid errors
if( class_exists( 'PVFWOF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'pvfw_csf_shortcodes';

  //
  // Create a shortcoder
PVFWOF::createShortcoder( $prefix, array(
  'button_title'   => esc_html__( 'Add PDF viewer', 'pdf-viewer-by-themencode' ),
  'select_title'   => esc_html__( 'Select Type', 'pdf-viewer-by-themencode' ),
  'insert_title'   => esc_html__( 'Insert PDF viewer', 'pdf-viewer-by-themencode' ),
  'show_in_editor' => true,
  'gutenberg'      => array(
    'title'        => esc_html__( 'PDF viewer', 'pdf-viewer-by-themencode' ),
    'description'  => esc_html__( 'Use this to Generate PDF viewer for WordPress Shortcodes', 'pdf-viewer-by-themencode' ),
    'icon'         => 'pdf',
    'category'     => 'media',
    'keywords'     => array( 'shortcode', 'pdf', 'viewer','flipbook','tnc pdf viewer','tnc','embed'),
    'placeholder'  => esc_html__( 'Use the Add PDF viewer button above to generate shortcode...', 'pdf-viewer-by-themencode' ),
  )
) );

// tnc-pdf-viewer-iframe
PVFWOF::createSection( $prefix, array(
  'title'     => esc_html__( 'Embed a PDF viewer', 'pdf-viewer-by-themencode' ),
  'view'      => 'normal', 
  'shortcode' => 'pvfw-embed', 
  'fields'    => array(

    array(
      'type'    => 'subheading',
      'content' => esc_html__( 'Basic Options', 'pdf-viewer-by-themencode' ),
    ),

    array(
      'type'    => 'callback',
      'function' => 'tnc_pvfw_create_viewer_url_callback',
      'class'    => 'tnc-viewer_url',
    ),

    array(
      'id'          => 'viewer_id',
      'type'        => 'select',
      'title'       => esc_html__( 'Select viewer to Embed', 'pdf-viewer-by-themencode' ),
      'subtitle'    => esc_html__( 'Search using the viewer title', 'pdf-viewer-by-themencode' ),
      'placeholder' => esc_html__( 'Select a viewer', 'pdf-viewer-by-themencode' ),
      'chosen'      => true,
      'ajax'        => true,
      'options'     => 'posts',
      'query_args'  => array(
        'post_type' => 'pdfviewer'
      )
    ),


    array(
      'id'    => 'width',
      'type'  => 'text',
      'title' => esc_html__( 'Width', 'pdf-viewer-by-themencode' ),
      'default'=> '100%',
    ),

    array(
      'id'    => 'height',
      'type'  => 'text',
      'title' => esc_html__( 'Height', 'pdf-viewer-by-themencode' ),
      'default' => '800'
    ),

    array(
      'id'    => 'iframe_title',
      'type'  => 'text',
      'title' => esc_html__( 'iFrame Title', 'pdf-viewer-by-themencode' ),
    ),

    array(
      'id'    => 'page',
      'type'  => 'text',
      'title' => esc_html__( 'Jump to page', 'pdf-viewer-by-themencode' ),
    ),
  )

) );

  // pvfw-link
  PVFWOF::createSection( $prefix, array(
    'title'     => esc_html__( 'Link to a PDF viewer', 'pdf-viewer-by-themencode' ),
    'view'      => 'normal', 
    'shortcode' => 'pvfw-link', 
    'fields'    => array(

      array(
        'type'    => 'subheading',
        'content' => esc_html__( 'Basic Options', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'type'    => 'callback',
        'function' => 'tnc_pvfw_create_viewer_url_callback',
        'class'      => 'tnc-viewer_url',
      ),

      array(
        'id'          => 'viewer_id',
        'type'        => 'select',
        'title'       => esc_html__( 'Select viewer to Link to', 'pdf-viewer-by-themencode' ),
        'subtitle'    => esc_html__( 'Search using the viewer title', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select a viewer', 'pdf-viewer-by-themencode' ),
        'chosen'      => true,
        'ajax'        => true,
        'options'     => 'posts',
        'query_args'  => array(
          'post_type' => 'pdfviewer'
        )
      ),

      array(
        'id'    => 'text',
        'type'  => 'text',
        'title' => esc_html__( 'Link Text', 'pdf-viewer-by-themencode' ),
        'default'=> esc_html__( 'Open PDF', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'    => 'class',
        'type'  => 'text',
        'title' => esc_html__( 'Link CSS Class', 'pdf-viewer-by-themencode' ),
        'default' => esc_html__( 'pdf-viewer-link-single', 'pdf-viewer-by-themencode' ),
      ),

      array(
        'id'          => 'open_type',
        'type'        => 'select',
        'title'       => esc_html__( 'Open Type', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select a Type','pdf-viewer-by-themencode' ),
        'options'     => array(
          'dspdfvfw-iframe'  => esc_html__( 'Popup', 'pdf-viewer-by-themencode' ),
          'pvfw-plain-link'  => esc_html__( 'Link', 'pdf-viewer-by-themencode' ),
        ),
        'default'     => 'pvfw-plain-link',
      ),
      array(
        'type'     => 'callback',
        'function' => 'tnc_requires_display_addon_callback',
      ),
      array(
        'id'          => 'target',
        'type'        => 'select',
        'title'       => esc_html__( 'Link Target', 'pdf-viewer-by-themencode' ),
        'placeholder' => esc_html__( 'Select Link Target','pdf-viewer-by-themencode' ),
        'options'     => array(
          '_blank'  => esc_html__( 'New Tab', 'pdf-viewer-by-themencode' ),
          '_parent'  => esc_html__( 'Same Tab', 'pdf-viewer-by-themencode' ),
        ),
        'default'     => '_parent',
        'dependency' => array('open_type', '==', 'pvfw-plain-link'),
      ),

      array(
        'id'    => 'page',
        'type'  => 'text',
        'title' => esc_html__( 'Jump to page', 'pdf-viewer-by-themencode' ),
      ),
    )

  ) );

}

function tnc_requires_display_addon_callback() {
  if (!class_exists('Display_Pdf_Viewer_For_WordPress')) {
					$tnc_display_url = 'https://portal.themencode.com/downloads/display-pdf-viewer-for-wordpress/';	
					printf('<p  style="padding-left:17px;padding-top:13px;" > %1$s <a target="_blank" href="%2$s">%3$s</a>%4$s</p>',esc_html__('Popup Type requires', 'pdf-viewer-by-themencode' ),esc_url($tnc_display_url),esc_html__('Display – PDF viewer for WordPress Addon ', 'pdf-viewer-by-themencode'),esc_html__('to function properly.','pdf-viewer-by-themencode' )); 
  }
 
}

