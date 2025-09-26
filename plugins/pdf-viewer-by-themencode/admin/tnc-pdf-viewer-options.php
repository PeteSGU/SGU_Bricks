<?php
/**
 * Register admin menu items
 *
 * Only the import menu register.
 *
 * @since [1.0]
 *
 * @package pdf-viewer-by-themencode
 */

add_action( 'admin_menu', 'tnc_pdf_lite_pdf_menu' );

/**
 * [tnc_pdf_menu description]
 */
function tnc_pdf_lite_pdf_menu() {
    add_submenu_page( 'edit.php?post_type=pdfviewer', 'Import PDF File - PDF Viewer for WordPress', 'Import PDF File', 'upload_files', 'themencode-pdf-viewer-import-file', 'tnc_pdf_lite_import_pdf_file_menu', 4 );
}

/**
 * [tnc_import_pdf_file description]
 */
function tnc_pdf_lite_import_pdf_file_menu() {
    if ( ! current_user_can( 'upload_files' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include dirname( __FILE__ ) . '/import-pdf-file.php';
}


/*
*    Add  Addons & Integrations
*/

add_action('admin_menu', 'tnc_addon_register_my_custom_submenu_page');
 
function tnc_addon_register_my_custom_submenu_page() {
    add_submenu_page( 
		'edit.php?post_type=pdfviewer',
        'Addons & Integrations',
        'Addons & Integrations',
        'manage_options',
        'pdf-addon-integration-page',
        'pdf_viewer_addon_integration_menu',
    );
}
function pdf_viewer_addon_integration_menu() { 

    $divi_url = "https://portal.themencode.com/downloads/divi-pdf-viewer-for-wordpress/";
    $divi_image =  plugin_dir_url(__FILE__).'../images/divi-pdf.png';
    $avada_url = "https://codecanyon.net/item/avada-pdf-viewer-for-wordpress-addon/43992846";
    $avada_image = plugin_dir_url(__FILE__).'../images/avada-pdf.png';
    $elementor_url = "https://codecanyon.net/item/elementor-pdf-viewer-for-wordpress-addon/27575246";
    $elementor_image = plugin_dir_url(__FILE__).'../images/elementor-pdf.png';
    $display_url = "https://portal.themencode.com/downloads/display-pdf-viewer-for-wordpress/";
    $display_image = plugin_dir_url(__FILE__).'../images/displa-pdf.png';
    $wpbakery_url = "https://codecanyon.net/item/pdf-viewer-for-wordpress-visual-composer-addon/17334228";
    $wpbakery_image = plugin_dir_url(__FILE__).'../images/wpbakery-pdf.png';
    $wpfile_url = "https://codecanyon.net/item/wp-file-access-manager/26430349";
    $wpfile_image = plugin_dir_url(__FILE__).'../images/wpfile-pdf.png';
    $navigative_url  = "https://codecanyon.net/item/navigative-pdf-viewer-for-wordpress-adoon/19393796";
    $navigative_image =  plugin_dir_url(__FILE__).'../images/navigative.png';
    $preview_url  = "https://portal.themencode.com/downloads/preview-pdf-viewer-for-wordpress-addon/";
    $preview_image = plugin_dir_url(__FILE__).'../images/Preview-Icon.png';
    $secure_image = plugin_dir_url(__FILE__).'../images/secure-pdf.png';
    $secure_url   = 'https://portal.themencode.com/downloads/secure-pdfs-tnc-flipbook-addon/';
    
    
    ?>

    <div class="addon-title-wrapper">
        <div class="addon-title-container">
             <h2> <?php _e( get_admin_page_title(), 'pdf-viewer-by-themencode');?></h2>
             <p> <?php _e( 'Here are the available addons and integrations that work with TNC PDF viewer.', 'pdf-viewer-by-themencode');?> </p>
        </div>
    </div>
    <div class="addon-integration-wrapper">   <?php _e( '', 'pdf-viewer-by-themencode');?>
        <div class="addon-integration-container">
             <div class="addon-integration-grid">
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $divi_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e('Divi PDF viewer for WordPress', 'pdf-viewer-by-themencode');?> </h3>
                              <p> <?php _e( 'Life-saver for Divi users. Get some amazing Divi module which will help you to embed PDF viewers easily right from you Divi builder. Check this out now.', 'pdf-viewer-by-themencode');?></p>
                        </div>
                        <div class="item-btn">
                             <a target="_blank" href="<?php echo esc_url($divi_url);?>"><?php _e( 'Get Divi Addon', 'pdf-viewer-by-themencode');?></a>
                         </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $avada_image; ?>" alt=""> 
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'Avada – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-by-themencode');?> </h3>
                              <p>  <?php _e( 'Use this addon and you will get several elements to show the PDF viewer in many ways. Embed PDF Viewer or create a link or image link and many more.', 'pdf-viewer-by-themencode');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($avada_url);?>"><?php _e( 'Get Avada Addon', 'pdf-viewer-by-themencode');?></a>
                        </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo  $elementor_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e('Elementor – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-by-themencode');?> </h3>
                              <p> <?php _e('This addon has various Widgets which will ease the process of showing PDF on your website in different manner. Save your time and work.', 'pdf-viewer-by-themencode');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($elementor_url);?>"><?php _e( 'Get Elementor Addon', 'pdf-viewer-by-themencode');?></a>
                        </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $display_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e( 'Display – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-by-themencode');?></h3>
                              <p> <?php _e( 'Bookshelf is the most unique and stylish way of presenting your PDF files. There are also List/Grid view options. You can open PDF Viewers as a PopUp or in a new tab', 'pdf-viewer-by-themencode');?></p>
                        </div>
                        <div class="item-btn">
                                <a target="_blank" href="<?php echo esc_url( $display_url );?>"><?php _e( 'Get Display Addon', 'pdf-viewer-by-themencode');?></a>
                         </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $wpbakery_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'WPBakery – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-by-themencode');?></h3>
                              <p><?php _e( 'If you are using WPBakery page builder on your website, you can get this addon to generate PDF Viewer shortcodes using WPBakery Page Builder interface.', 'pdf-viewer-by-themencode');?></p>
                        </div>
                        <div class="item-btn">
                          <a target="_blank" href="<?php echo esc_url($wpbakery_url);?>"><?php _e( 'Get WPBakery Addon', 'pdf-viewer-by-themencode');?> </a>
                        </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo  $navigative_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e( 'Navigative – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-by-themencode');?></h3>
                              <p> <?php _e( "This addon is useful if you want to have one viewer on a page but open multiple pdf's according to users click. You can have a list of PDF links on the sidebar using a widget.", "pdf-viewer-by-themencode");?> </p>
                        </div>
                        <div class="item-btn">
                             <a target="_blank" href="<?php  echo esc_url($navigative_url);?>"> <?php _e( 'Get Navigative Addon', 'pdf-viewer-by-themencode');?></a>
                        </div>
                    </div>
                    <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $preview_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e( 'Preview – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-by-themencode');?></h3>
                              <p> <?php _e( "This addon, you can select specific pages of a PDF file and set restrictions for viewers. Restricted viewers will only see a partial view of those selected pages.", "pdf-viewer-by-themencode");?> </p>
                        </div>
                        <div class="item-btn">
                             <a target="_blank" href="<?php  echo esc_url($preview_url);?>"> <?php _e( 'Get Preview Addon', 'pdf-viewer-by-themencode');?></a>
                        </div>
                    </div>
                    <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $wpfile_image;?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'WP File Access Manager - Easy Way to Restrict WordPress Uploads', 'pdf-viewer-by-themencode');?></h3>
                              <p><?php _e( 'If you want to restrict access to your media library files by user login/role/woocommerce purchase or paid memberships pro level, this plugin is for you!', 'pdf-viewer-by-themencode');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($wpfile_url);?>"> <?php _e( 'Get it now', 'pdf-viewer-by-themencode');?> </a>
                         </div>
                    </div>
                    <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $secure_image;?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'Secure PDFs – TNC FlipBook Addon', 'pdf-viewer-by-themencode');?></h3>
                              <p><?php _e( 'Protect your PDF files from being downloaded, ensuring they remain view-only and secure when displayed with TNC FlipBook.', 'pdf-viewer-by-themencode');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($secure_url);?>"> <?php _e( 'Get Secure PDFs Addon', 'pdf-viewer-by-themencode');?> </a>
                         </div>
                    </div>
                  </div>
               </div>
         </div>
     </div>
<?php
}




/**
 *    Add Shortocde in a menu under tnc pdf viewer meneu 
 * 
 */
function tnc_shortcode_register_my_custom_submenu_page() {
    add_submenu_page( 
		'edit.php?post_type=pdfviewer',
        'Shortcodes',
        'Shortcodes',
        'manage_options',
        'tnc-pdf-shortcode-page',
        'tnc_pdf_viewer_shortcode_menu',
    );
}
add_action('admin_menu', 'tnc_shortcode_register_my_custom_submenu_page');


/**
 *    Tnc pdf viewer shortcode menu 
 */
function  tnc_pdf_viewer_shortcode_menu() {


    ?>
    
      <div class="tnc-shortcode-title-wrapper">
          <div class="tnc-shortcode-title-container">
                <div class="tnc-shortcode-content">
                  <h2 class="tnc-shortcode-title"><?php _e( 'Shortcodes included with TNC PDF viewer', 'pdf-viewer-by-themencode');?></h2>
                  <p class="tnc-shortcode-desc">
                  <?php _e( 'TNC PDF viewer comes with a few shortcodes which you can inset on any post or page to display a link or embed PDF viewer on that location. Here we\'re going to demonstrate every shortcodes.', 'pdf-viewer-by-themencode');?>
                  </p>
                </div>
          </div>  
      </div>
      <div class="tnc-shortcode-wrapper">
            <div class="tnc-shortcode-container">
                 <h3 class="tnc-shorcode-emebed-title"><?php _e( '1. [pvfw-embed]', 'pdf-viewer-by-themencode');?></h3>
                 <p class="tnc-shortcode-desc"><?php _e( 'This shortcode allows you to embed already created PDF viewers (Using TNC PDF viewer  > Add New menu) on any post or page. It comes with a few parameters.', 'pdf-viewer-by-themencode');?></p>
            </div>
            <div class="tnc-shortcode-list-desc">
              <ul>
                <li><?php _e( '<strong>viewer_id:</strong> This is the id of the viewer you want to embed.', 'pdf-viewer-by-themencode');?> </li>
                <li><?php _e( '<strong>width:</strong> This is the width of the iframe that will be embedded on the page.', 'pdf-viewer-by-themencode');?></li>
                <li><?php _e( '<strong>height:</strong> This is the height of the iframe that will be embedded on the page.', 'pdf-viewer-by-themencode');?></li>
                <li><?php _e( '<strong>iframe_title:</strong> The title of the iframe.', 'pdf-viewer-by-themencode');?></li>
                <li><?php _e( '<strong>page:</strong> The page number which you want to open on load', 'pdf-viewer-by-themencode');?></li>
              </ul>
            </div>
            <div class="tnc-shorcode-embed-container">
                <h4 class="shorcode-example-title"> <?php _e( '<strong>An example of the [pvfw-embed] shortcode:</strong>', 'pdf-viewer-by-themencode');?></h4>
                <div class="tnc-shorcode-code"><code><strong>[pvfw-embed viewer_id="6" width="100%" height="800" iframe_title="PDF Viewer" page="5" ]</strong></code></div>
            </div>
      </div>

      <div class="tnc-shortcode-wrapper">
            <div class="tnc-shortcode-container">
                 <h3 class="tnc-shorcode-emebed-title"><?php _e( '2. [pvfw-link]', 'pdf-viewer-by-themencode');?></h3>
                 <p class="tnc-shortcode-desc"><?php _e( '[pvfw-link] shortcode allows you to embed already created PDF viewers (Using TNC PDF viewer  > Add New menu) on any post or page. It comes with a few parameters.', 'pdf-viewer-by-themencode');?></p>
            </div>
            <div class="tnc-shortcode-list-desc">
              <ul>
                <li><?php _e( '<strong>viewer_id:</strong> This is the id of the viewer you want to link to.', 'pdf-viewer-by-themencode');?> </li>
                <li><?php _e( '<strong>text:</strong> The anchor text that will be used as a link.', 'pdf-viewer-by-themencode');?></li>
                <li><?php _e( '<strong>class:</strong> You can add a CSS class here to the link.', 'pdf-viewer-by-themencode');?></li>
                <li><?php _e( '<strong>target:</strong> Defines the target of the link. Put _blank if you want to open the link in a new tab or use _parent if you want to open the link on the same tab.', 'pdf-viewer-by-themencode');?></li>
                <li><?php _e( '<strong>page:</strong> The page number which you want to open on load', 'pdf-viewer-by-themencode');?></li>
              </ul>
            </div>
            <div class="tnc-shorcode-embed-container">
                <h4 class="shorcode-example-title"> <?php _e( '<strong>An example of the  [pvfw-link] shortcode:</strong>', 'pdf-viewer-by-themencode');?></h4>
                <div class="tnc-shorcode-code"><code><strong>[pvfw-link viewer_id="6" text="Open PDF" target="_blank" class="pdf-viewer-link" page="5"]</strong></code></div>
            </div>
      </div>


   <?php 

}
