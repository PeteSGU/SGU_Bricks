<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'pvfwof_demo_shortcodes';

//
// Create a shortcoder
//
PVFWOF::createShortcoder( $prefix, array(
  // 'button_title'   => 'Add Shortcode',
  // 'select_title'   => 'Select a shortcode',
  // 'insert_title'   => 'Insert Shortcode',
  // 'show_in_editor' => true,
  // 'gutenberg'      => array(
  //   'title'        => 'PVFWOF Shortcodes',
  //   'description'  => 'PVFWOF Shortcode Block',
  //   'icon'         => 'screenoptions',
  //   'category'     => 'widgets',
  //   'keywords'     => array( 'shortcode', 'pvfwof', 'insert' ),
  //   'placeholder'  => 'Write shortcode here...',
  // )
) );

//
// A shortcode [foo title=""]
//
PVFWOF::createSection( $prefix, array(
  'title'     => '[foo] view: normal',
  'view'      => 'normal',
  'shortcode' => 'foo',
  'fields'    => array(

    array(
      'id'    => 'opt_title',
      'type'  => 'text',
      'title' => 'Title',
    ),

    array(
      'id'    => 'opt_switcher',
      'type'  => 'switcher',
      'title' => 'Switcher',
      'label' => 'The label text of the switcher.',
    ),

  )
) );

//
// A shortcode [foo title=""]content[/foo]
//
PVFWOF::createSection( $prefix, array(
  'title'     => '[foo] view: normal alternative',
  'view'      => 'normal',
  'shortcode' => 'foo',
  'fields'    => array(

    array(
      'id'    => 'opt_title',
      'type'  => 'text',
      'title' => 'Title',
    ),

    array(
      'id'      => 'opt_checkbox',
      'type'    => 'checkbox',
      'title'   => 'Options',
      'options' => array(
        'opt-1' => 'Option 1',
        'opt-2' => 'Option 2',
        'opt-3' => 'Option 3',
      )
    ),

    array(
      'id'      => 'opt_select',
      'type'    => 'select',
      'title'   => 'Select',
      'options' => array(
        'opt-1' => 'Option 1',
        'opt-2' => 'Option 2',
        'opt-3' => 'Option 3',
      ),
    ),

    array(
      'id'    => 'content',
      'type'  => 'textarea',
      'title' => 'Content',
    ),

  )
) );

//
// A shortcode [content]content[/content][content]content[/content]
//
PVFWOF::createSection( $prefix, array(
  'title'     => '[foo] view: contents',
  'view'      => 'contents',
  'shortcode' => 'content',
  'fields'    => array(

    array(
      'id'    => 'opt_content_1',
      'type'  => 'textarea',
      'title' => 'Content 1',
    ),

    array(
      'id'    => 'opt_content_2',
      'type'  => 'textarea',
      'title' => 'Content 2',
    ),

  )
) );

//
// A shortcode [opt_content_1]content[/opt_content_1][opt_content_2]content[/opt_content_2]
//
PVFWOF::createSection( $prefix, array(
  'title'  => '[foo] view: contents alternative',
  'view'   => 'contents',
  'fields' => array(

    array(
      'id'    => 'opt_content_1',
      'type'  => 'textarea',
      'title' => 'Content 1',
    ),

    array(
      'id'    => 'opt_content_2',
      'type'  => 'textarea',
      'title' => 'Content 2',
    ),

  )
) );

PVFWOF::createSection( $prefix, array(
  'title'           => '[foo] view: group',
  'view'            => 'group',
  'shortcode'       => 'foo',
  'group_shortcode' => 'nested_foo',
  'group_fields'    => array(

    array(
      'id'     => 'opt_title',
      'type'   => 'text',
      'title'  => 'Title',
    ),

    array(
      'id'     => 'content',
      'type'   => 'textarea',
      'title'  => 'Content',
    ),

  )
) );

PVFWOF::createSection( $prefix, array(
  'title'     => '[foo] view: group alternative',
  'view'      => 'group',
  'shortcode' => 'foo',
  'fields'    => array(

    array(
      'id'    => 'opt_switcher',
      'type'  => 'switcher',
      'title' => 'Switcher',
      'label' => 'The label text of the switcher.',
    ),

    array(
      'id'      => 'opt_select',
      'type'    => 'select',
      'title'   => 'Select',
      'options' => array(
        'opt-1' => 'Option 1',
        'opt-2' => 'Option 2',
        'opt-3' => 'Option 3',
      ),
    ),

  ),
  'group_shortcode' => 'nested_foo',
  'group_fields'    => array(

    array(
      'id'    => 'title',
      'type'  => 'text',
      'title' => 'Title',
    ),

    array(
      'id'    => 'content',
      'type'  => 'textarea',
      'title' => 'Content',
    ),

  )
) );

PVFWOF::createSection( $prefix, array(
  'title'     => '[foo] view: repeater',
  'view'      => 'repeater',
  'shortcode' => 'foo',
  'fields'    => array(

    array(
      'id'    => 'opt_title',
      'type'  => 'text',
      'title' => 'Title',
    ),

    array(
      'id'    => 'opt_switcher',
      'type'  => 'switcher',
      'title' => 'Switcher',
      'label' => 'The label text of the switcher.',
    ),

    array(
      'id'      => 'opt_select',
      'type'    => 'select',
      'title'   => 'Select',
      'options' => array(
        'opt-1' => 'Option 1',
        'opt-2' => 'Option 2',
        'opt-3' => 'Option 3',
      ),
    ),

  )
) );

PVFWOF::createSection( $prefix, array(
  'title'     => '[foo] view: repeater alternative',
  'view'      => 'repeater',
  'shortcode' => 'foo',
  'fields'    => array(

    array(
      'id'    => 'opt_title',
      'type'  => 'text',
      'title' => 'Title',
    ),

    array(
      'id'    => 'opt_switcher',
      'type'  => 'switcher',
      'title' => 'Switcher',
      'label' => 'The label text of the switcher.',
    ),

    array(
      'id'      => 'opt_select',
      'type'    => 'select',
      'title'   => 'Select',
      'options' => array(
        'opt-1' => 'Option 1',
        'opt-2' => 'Option 2',
        'opt-3' => 'Option 3',
      ),
    ),

    array(
      'id'    => 'content',
      'type'  => 'textarea',
      'title' => 'Content',
    ),

  )
) );
