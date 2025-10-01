<?php
add_action( 'admin_menu', 'tnc_pdf_menu' );
function tnc_pdf_menu() {
    add_submenu_page( 'edit.php?post_type=pdfviewer', 'Import PDF File - TNC FlipBook - PDF viewer for WordPress', 'Import PDF File', 'upload_files', 'themencode-pdf-viewer-import-file', 'tnc_import_pdf_file', 4);
    add_submenu_page( 'edit.php?post_type=pdfviewer', 'Activation & Updates', 'Activation & Updates', 'manage_options', 'themencode-pdf-viewer-updates', 'tnc_pdf_viewer_updates', 5);
}

function tnc_import_pdf_file(){
    if ( !current_user_can( 'upload_files' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include dirname(__FILE__)."/import-pdf-file.php";
}
function tnc_pdf_viewer_updates(){
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include dirname(__FILE__)."/update-registration.php";
}

/*
*    Add  Addons & Integrations
*/

add_action('admin_menu', 'tnc_pvfw_addon_register_submenu_page');
 
function tnc_pvfw_addon_register_submenu_page() {
    add_submenu_page( 
		'edit.php?post_type=pdfviewer',
        'Addons & Integrations',
        'Addons & Integrations',
        'manage_options',
        'pdf-addon-integration-page',
        'tnc_pvfw_addon_integration_content',
    );
}
function tnc_pvfw_addon_integration_content() { 

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
    $pdf_secure_url = "https://portal.themencode.com/downloads/secure-pdfs-tnc-flipbook-addon/";
    
    
    ?>

    <div class="addon-title-wrapper">
        <div class="addon-title-container">
             <h2> <?php _e( get_admin_page_title(), 'pdf-viewer-for-wordpress');?></h2>
             <p> <?php _e( 'Here are the available addons and integrations that work with TNC FlipBook - PDF viewer for WordPress.', 'pdf-viewer-for-wordpress');?> </p>
        </div>
    </div>
    <div class="addon-integration-wrapper">   <?php _e( '', 'pdf-viewer-for-wordpress');?>
        <div class="addon-integration-container">
             <div class="addon-integration-grid">
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $divi_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e('Divi PDF viewer for WordPress', 'pdf-viewer-for-wordpress');?> </h3>
                              <p> <?php _e( 'Life-saver for Divi users. Get some amazing Divi module which will help you to embed FlipBooks easily right from you Divi builder. Check this out now.', 'pdf-viewer-for-wordpress');?></p>
                        </div>
                        <div class="item-btn">
                             <a target="_blank" href="<?php echo esc_url($divi_url);?>"><?php _e('Get Divi Addon', 'pdf-viewer-for-wordpress');?></a>
                         </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $avada_image; ?>" alt=""> 
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'Avada – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-for-wordpress');?> </h3>
                              <p>  <?php _e( 'Use this addon and you will get several elements to show the PDF viewer in many ways. Embed FlipBooks or create a link or image link and many more.', 'pdf-viewer-for-wordpress');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($avada_url);?>"><?php _e( 'Get Avada Addon', 'pdf-viewer-for-wordpress');?></a>
                        </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo  $elementor_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e('Elementor – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-for-wordpress');?> </h3>
                              <p> <?php _e('This addon has various elements which will ease the process of showing FlipBooks on your website in different manner. Save your time and work.', 'pdf-viewer-for-wordpress');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($elementor_url);?>"><?php _e( 'Get Elementor Addon', 'pdf-viewer-for-wordpress');?></a>
                        </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $display_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e( 'Display – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-for-wordpress');?></h3>
                              <p> <?php _e( 'Bookshelf is the most unique and stylish way of presenting your PDF files. There are also List/Grid view options. You can open FlipBooks as a PopUp or in a new tab', 'pdf-viewer-for-wordpress');?></p>
                        </div>
                        <div class="item-btn">
                                <a target="_blank" href="<?php echo esc_url( $display_url );?>"><?php _e( 'Get Display Addon', 'pdf-viewer-for-wordpress');?></a>
                         </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $wpbakery_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'WPBakery – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-for-wordpress');?></h3>
                              <p><?php _e( 'If you are using WPBakery page builder on your website, you can get this addon to embed FlipBooks using WPBakery Page Builder interface.', 'pdf-viewer-for-wordpress');?></p>
                        </div>
                        <div class="item-btn">
                          <a target="_blank" href="<?php echo esc_url($wpbakery_url);?>"><?php _e( 'Get WPBakery Addon', 'pdf-viewer-for-wordpress');?> </a>
                        </div>
                  </div>
                  <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo  $navigative_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e( 'Navigative – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-for-wordpress');?></h3>
                              <p> <?php _e( "This addon is useful if you want to have one viewer on a page but open multiple pdf's according to users click. You can have a list of PDF links on the sidebar using a widget.", "pdf-viewer-for-wordpress");?> </p>
                        </div>
                        <div class="item-btn">
                             <a target="_blank" href="<?php  echo esc_url($navigative_url);?>"> <?php _e( 'Get Navigative Addon', 'pdf-viewer-for-wordpress');?></a>
                        </div>
                    </div>
                    <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $preview_image; ?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3> <?php _e( 'Preview – TNC FlipBook – PDF viewer for WordPress Addon', 'pdf-viewer-for-wordpress');?></h3>
                              <p> <?php _e( "This addon, you can select specific pages of a PDF file and set restrictions for visitors. Restricted visitors will only see a partial view of those selected pages.", "pdf-viewer-for-wordpress");?> </p>
                        </div>
                        <div class="item-btn">
                             <a target="_blank" href="<?php  echo esc_url($preview_url);?>"> <?php _e('Get Preview Addon', 'pdf-viewer-for-wordpress');?></a>
                        </div>
                    </div>
                    <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $wpfile_image;?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'WP File Access Manager - Easy Way to Restrict WordPress Uploads', 'pdf-viewer-for-wordpress');?></h3>
                              <p><?php _e( 'If you want to restrict access to your media library files by user login/role/woocommerce purchase or paid memberships pro level, this plugin is for you!', 'pdf-viewer-for-wordpress');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($wpfile_url);?>"> <?php _e( 'Get it Now', 'pdf-viewer-for-wordpress');?> </a>
                         </div>
                    </div>
                    <div class="addon-integration-item">
                       <div class="image-wrap">
                          <img src="<?php echo $secure_image;?>" alt="">
                        </div>
                        <div class="item-content">
                              <h3><?php _e( 'Secure PDFs – TNC FlipBook Addon', 'pdf-viewer-for-wordpress');?></h3>
                              <p><?php _e( 'Protect your PDF files from being downloaded, ensuring they remain view-only and secure when displayed with TNC FlipBook.', 'pdf-viewer-for-wordpress');?></p>
                        </div>
                        <div class="item-btn">
                            <a target="_blank" href="<?php echo esc_url($pdf_secure_url);?>"> <?php _e( 'Get Secure PDFs Addon', 'pdf-viewer-for-wordpress');?> </a>
                         </div>
                    </div>
                  </div>
               </div>
         </div>
     </div>
<?php   
}


/*
*    Add  Faq  
*/

add_action('admin_menu', 'tnc_pvfw_faq_register_submenu_page',30);


function tnc_pvfw_faq_register_submenu_page() {
    add_submenu_page('edit.php?post_type=pdfviewer', 'FAQ', 'FAQ', 'manage_options', 'tnc_pvfw_faq', 'tnc_pvfw_faq_render');
}

function tnc_pvfw_faq_render() {

        ?>
            <div class="tnc-pvfw-faq-wrapper">
                    <div class="tnc-pvfw-faq-container">
                         <div class="tnc-pvfw-faq-items">
                                <div class="tnc-pvfw-faq-item">
                                    <h4 class="tnc-pvfw-faq-title"><?php echo esc_html__('1.Failed to Fetch Error Message – An error occurred while loading the PDF ?' , 'pdf-viewer-for-wordpress' ); ?></h4>  
                                    <p class="tnc-pvfw-faq-descprition"> <?php echo esc_html__('Although this is the most common error that you might face, this is very easy to solve. Just read the following article and the problem will be solved.','pdf-viewer-for-wordpress');?>
<a target="_blank" href="<?php echo esc_url('https://themencode.com/fix-failed-to-fetch-error-tnc-flipbook-pdf-viewer-for-wordpress/');?>"><?php echo esc_html('https://themencode.com/fix-failed-to-fetch-error-tnc-flipbook-pdf-viewer-for-wordpress/');?></a>                                    </p>
                                </div>
                                <div class="tnc-pvfw-faq-item">
                                    <h4 class="tnc-pvfw-faq-title"><?php echo esc_html__('2.FlipBook Not Working in Divi Theme ?','pdf-viewer-for-wordpress'); ?></h4>
                                    <p class="tnc-pvfw-faq-descprition">
                                        <?php echo esc_html__('This problem occurs due to a Divi Settings. Please read the Following article and it will solve the issue.','pdf-viewer-for-wordpress'); ?>
                                        <a target="_blank" href="<?php echo esc_url('https://themencode.com/pdf-viewer-for-wordpress-flipbook-conflict-with-divi-theme-solve/'); ?>">
                                            <?php echo esc_html('https://themencode.com/pdf-viewer-for-wordpress-flipbook-conflict-with-divi-theme-solve/'); ?>
                                        </a>
                                    </p>
                                </div>  
                                <div class="tnc-pvfw-faq-item">
                                    <h4 class="tnc-pvfw-faq-title"> <?php echo esc_html__('3. PDF viewer or FlipBook not opening properly / getting a 404 page ?','pdf-viewer-for-wordpress');?></h4>  
                                    <p class="tnc-pvfw-faq-descprition"><?php echo esc_html__('This issue might be caused by the wrong permalink structure. Follow this article and hopefully your problem will be solved','pdf-viewer-for-wordpress');?>  <a target="_blank" href="<?php echo esc_url('https://themencode.com/how-to-reset-permalinks-on-wordpress-website/'); ?>"><?php echo esc_html('https://themencode.com/how-to-reset-permalinks-on-wordpress-website/');?></a></p>
                                </div>
                         </div>
                         <div class="tnc-pvfw-faq-items">
                               <div class="tnc-pvfw-faq-item">
                                    <h4 class="tnc-pvfw-faq-title"><?php echo esc_html__('4. Error Message : Purchase key is invalid, please check and try again ?','pdf-viewer-for-wordpress'); ?> </h4>  
                                    <p class="tnc-pvfw-faq-descprition"> <?php echo esc_html__('It may happen if you just purchased the plugin within a few minutes or if there is any additional space or any other character within the code. You can try waiting for a while or maing sure there is nothing extra within the code. If it still does not work, you can ','pdf-viewer-for-wordpress');?><a target="_blank" href="<?php echo esc_url('https://themencode.support-hub.io/');?>"><?php echo esc_html('Contact our Support Team');?></a> </p>
                                </div>
                                <div class="tnc-pvfw-faq-item">
                                    <h4 class="tnc-pvfw-faq-title"><?php echo esc_html__('5. I can’t show FlipBook or PDF viewers as a BookShelf or List/Grid ?','pdf-viewer-for-wordpress');?> </h4>  
                                    <p class="tnc-pvfw-faq-descprition"> <?php echo esc_html__('In order to showcase your FlipBook or PDF viewers as a BookShelf or List/Grid you need to have the Display PDF viewer for WordPress Addon. Get this plugin from ThemeNcode portal and you can show as a BookShelf or List/Grid. ','pdf-viewer-for-wordpress');?> <a target="_blank" href="<?php echo esc_url('https://portal.themencode.com/downloads/display-pdf-viewer-for-wordpress/');?>"><?php echo esc_html('Click here to get the Display Addon');?></a></p>
                                </div>
                                <div class="tnc-pvfw-faq-item">
                                    <h4 class="tnc-pvfw-faq-title"><?php echo esc_html__('6. Error message are shown over the Embedded FlipBook ?','pdf-viewer-for-wordpress');?></h4>  
                                    <p class="tnc-pvfw-faq-descprition"><?php echo esc_html__('This problem occurs due to a technical glitch. Just go to TNC FlipBook and Global Settings and click the save button once. Then, go to that specific FlipBook Settings and click the update button once. Hopefully your issue will be solved.','pdf-viewer-for-wordpress');?></p>
                                </div>
                         </div>
                    </div>
            </div>
        <?php
}
