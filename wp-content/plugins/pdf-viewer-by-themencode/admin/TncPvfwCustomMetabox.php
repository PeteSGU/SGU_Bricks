<?php
/**
 * Register a meta box using a class.
 */

class TncPvfwCustomMetabox {
  public function __construct() {    
    add_action('add_meta_boxes', [$this,'tnc_pvfw_display_meta_box']);
    add_action('add_meta_boxes', [$this,'tnc_pvfw_add_upgrade_pro_meta_box']);
    add_action('add_meta_boxes', [$this,'tnc_wp_file_meta_box']);
  }

  public function tnc_pvfw_display_meta_box(){
    add_meta_box('tnc_pvfw_metabox_display', esc_html__( 'Display', 'pdf-viewer-by-themencode' ), array( $this, 'tnc_pvfw_display_meta_box_markup' ), 'pdfviewer', 'side', 'default');
  }


  public function tnc_pvfw_add_upgrade_pro_meta_box(){
    add_meta_box('tnc_pvfw_metabox_upgrade_pro', esc_html__( 'TNC PDF Upgrade Pro', 'pdf-viewer-by-themencode' ), array( $this, 'tnc_pdf_upgrade_prop_meta_box_markup' ),'pdfviewer', 'side', 'default');
  }

  public function tnc_wp_file_meta_box(){
    add_meta_box('tnc_pvfw_metabox_wpfile', esc_html__( 'WP File Acess Manager', 'pdf-viewer-by-themencode' ), array( $this, 'tnc_pvfw_wpfile_meta_box_markup' ), 'pdfviewer', 'side', 'default');
  }

  public function tnc_pvfw_display_meta_box_markup() {
    $tnc_pvfw_display_url = 'https://portal.themencode.com/downloads/display-pdf-viewer-for-wordpress/';
    $tnc_pvfw_display_logo = plugin_dir_url(__FILE__).'../images/display-logo.webp';
    ?>
      <div class="tnc_pvfw_display_wrapper">
        <div class="tnc_pvfw_display_col">
          <div class="tnc_pvfw_display_title">
             <div class="tnc-display-logo"><img src="<?php echo esc_url($tnc_pvfw_display_logo)?>" alt=""></div>
          </div>
          <div class="tnc_pvfw_display_subtitle">
            <p><?php _e("Showcase your PDF files with a Bookshelf or List/Grid of items.", 'pdf-viewer-for-wordpress');?></p>
          </div>
          <div class="tnc_pvfw_display_btn">
            <a target="_blank" href="<?php echo esc_url($tnc_pvfw_display_url);?>"><?php _e("Get it now", 'pdf-viewer-for-wordpress');?></a>
          </div>
        </div>
      </div>
    <?php
  }

  public function tnc_pdf_upgrade_prop_meta_box_markup() {
    $tnc_pvfw_upgrade_url = 'https://codecanyon.net/item/pdf-viewer-for-wordpress/8182815/';
    $tnc_pvfw_flipbook_image = plugin_dir_url(__FILE__).'../images/flipbook.png';
    ?>
       <div class="tnc_pvfw_review_wrapper">
        <div class="tnc_pvfw_wpfile_col">
          <div class="tnc-pvfw-wpfile-heading">
              <h3><?php _e("TNC Flipbook", 'pdf-viewer-by-themencode');?></h3>
          </div>
          <div class="wpfile-sub-heading">
              <h4><?php _e("Unlimited PDF viewers with FlipBook", 'pdf-viewer-by-themencode');?></h4>
          </div>
          <div class="tnc_pvfw_wpfile_subtitle">
            <p><?php _e("Custom viewers, Express support and many more...", 'pdf-viewer-by-themencode');?></p>
          </div>
          <div class="wp-file-tnc-flipbook-image">
              <img src="<?php echo esc_url($tnc_pvfw_flipbook_image);?>" alt="">
          </div>
          <div class="tnc_pvfw_flipbook_btn">
            <a target="_blank" href="<?php echo esc_url($tnc_pvfw_upgrade_url);?>"><?php _e("Upgrade now", 'pdf-viewer-by-themencode');?></a>
          </div>
        </div>
      </div>
    <?php
  }

  public function tnc_pvfw_wpfile_meta_box_markup(){
    $tnc_pvfw_wp_file_url = 'https://codecanyon.net/item/wp-file-access-manager/26430349';
    ?>
      <div class="tnc_pvfw_wp_file_wrapper">
        <div class="tnc_pvfw_wp_file_col">
          <div class="tnc_pvfw_wp_file_title">
            <h4> <?php _e("WP File Access Manager", 'pdf-viewer-for-wordpress');?></h4>
          </div>
          <div class="tnc_pvfw_wp_file_subtitle">
            <p><?php _e("Restrict unauthorized access to your WordPress files easily.", 'pdf-viewer-for-wordpress');?></p>
          </div>
          <div class="tnc_pvfw_wp_file_btn">
            <a target="_blank" href="<?php echo esc_url($tnc_pvfw_wp_file_url);?>"><?php _e("Get it now", 'pdf-viewer-for-wordpress');?></a>
          </div>
        </div>
      </div>
    <?php
  }

} // end class TncPvfwCustomMetabox;
