<?php 

/**
 * Adds a submenu page under a custom post type pdfviewer.
 */
function tnc_pvfw_lite_shortcode_generate_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=pdfviewer',
        __( 'Generate Shortcode', 'pdf-viewer-by-themencode'),
        __( 'Generate Shortcode', 'pdf-viewer-by-themencode'),
        'manage_options',
        'generate-shortcode-page',
        'tnc_pvfw_lite_generate_shortcode_page_callback'
    );
}

add_action('admin_menu', 'tnc_pvfw_lite_shortcode_generate_submenu_page');

/**
 * Display callback for the submenu page.
 */
function tnc_pvfw_lite_generate_shortcode_page_callback() {
     ?>
       <div class="pvfw-shortcode-form-wrapper"> 
    <?php
    // Form handling and shortcode generation logic
    if (isset($_POST['pvfw_shortcode_submit']) && !empty($_POST['pvfw_shortcode_option'])) {
        $selected_option = sanitize_text_field($_POST['pvfw_shortcode_option']); // Get the selected option
         // Additional fields based on selected option
        if ($selected_option === 'pvfw_embed') {
            $post_id = sanitize_text_field($_POST['pvfw_embed_post_id']); 
            $width = sanitize_text_field($_POST['pvfw_embed_width']);
            $height = sanitize_text_field($_POST['pvfw_embed_height']);
            $iframe_title = sanitize_text_field($_POST['pvfw_embed_iframe_title']);
            $jump_page = sanitize_text_field($_POST['pvfw_embed_jump_page']);
            
            // Add them to the shortcode
            $pvfw_shortcode = "[pvfw-embed viewer_id='$post_id' width='$width' height='$height' iframe_title='$iframe_title' page='$jump_page']"; 
        }
        if ($selected_option === 'pvfw_link') {
            $post_id = sanitize_text_field($_POST['pvfw_link_post_id']); 
            $link_text = sanitize_text_field($_POST['pvfw_link_text']);
            $link_target = sanitize_text_field($_POST['pvfw_link_target']);
            $link_class = sanitize_text_field($_POST['pvfw_link_class']);
            $jump_page = sanitize_text_field($_POST['pvfw_link_jump_page']);
            $open_type = sanitize_text_field($_POST['pvfw_open_type']);

            // Add them to the shortcode
            $pvfw_shortcode = "[pvfw-link viewer_id='$post_id' text='$link_text' class='$link_class' open_type='$open_type' target='$link_target' page='$jump_page']"; 
        }

    ?>
    
    
      <div class="pvfw-right-form-wrap">
        <div class="pvfw-shortcode-title">
            <h2><?php _e( 'Shortcode','pdf-viewer-by-themencode' ); ?></h2>
            <p><?php _e( 'Insert this shortcode into your posts or pages','pdf-viewer-by-themencode' );?></p>
        </div>
         <div class="pvfw-shortcode-contain">
            <code><?php echo $pvfw_shortcode; ?></code>
         </div>
      </div>

    <?php
    }else {
      ?>
        <div class="error">
            <p><?php _e( 'Please select a shortcode before submitting the form.','pdf-viewer-by-themencode' ); ?></p>
        </div>
      <?php
    }
    ?>
    <div class="pvfw-left-form-wrap">
         <div class="pvfw-shortcode-title">
            <h2><?php _e( 'Generate Shortcode','pdf-viewer-by-themencode' ); ?></h2>
            <p><?php _e( 'Create shortcode and insert it on your pages or posts','pdf-viewer-by-themencode' ); ?></p>
        </div>
        <form method="post" action="">
           <div class="pvfw-form-item">
                <label for="pvfw_shortcode_option"><?php _e( 'Select Shortcode','pdf-viewer-by-themencode' ); ?></label>
                <select name="pvfw_shortcode_option" id="pvfw_shortcode_option">
                    <option value=""><?php _e( 'Select Shortcode','pdf-viewer-by-themencode' ); ?></option>
                    <option value="pvfw_embed"><?php _e( 'Embed a viewer','pdf-viewer-by-themencode' ); ?></option>
                    <option value="pvfw_link"><?php _e( 'Link to a viewer','pdf-viewer-by-themencode' ); ?></option>
                </select>
            </div>

            <!-- Additional fields for Embed option -->
            <div id="embedFields" style="display: none;">
                <div class="pvfw-form-item">
                    <label for="pvfw_embed_post_id"><?php _e( 'Select viewer to Embed','pdf-viewer-by-themencode' ); ?></label>
                
                        <select name="pvfw_embed_post_id" id="pvfw_embed_post_id">
                            <?php
                                // Query posts based on the selected post type pdfviewer
                                $posts = get_posts(array('post_type' => 'pdfviewer', 'numberposts' => -1));
                                foreach ($posts as $post) {
                                    echo "<option value='{$post->ID}'>{$post->post_title}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="pvfw-form-item">
                        <label for="pvfw_embed_width"><?php _e( 'Width' , 'pdf-viewer-by-themencode' ); ?></label>
                        <input type="text" name="pvfw_embed_width" id="pvfw_embed_width" value="<?php echo esc_attr( '100%' ); ?>">
                    </div>
                    <div class="pvfw-form-item">
                        <label for="pvfw_embed_height"><?php _e( 'Height' , 'pdf-viewer-by-themencode' ); ?></label>
                        <input type="text" name="pvfw_embed_height" id="pvfw_embed_height" value=" <?php echo esc_attr( '800' ); ?>">
                    </div>
                    <div class="pvfw-form-item">
                        <label for="pvfw_embed_iframe_title"><?php _e( 'iFrame Titlte','pdf-viewer-by-themencode' ); ?></label>
                        <input type="text" name="pvfw_embed_iframe_title" id="pvfw_embed_iframe_title"> 
                    </div> 
                    <div class="pvfw-form-item">
                        <label for="pvfw_embed_jump_page"><?php _e( 'Jump to page','pdf-viewer-by-themencode' ); ?></label>
                        <input type="text" name="pvfw_embed_jump_page" id="pvfw_embed_jump_page"> 
                    </div>
            </div>
             <!-- Additional fields for link option -->
             <div id="linkFields" style="display: none;">
                    <div class="pvfw-form-item">
                        <label for="pvfw_link_post_id"><?php _e( 'Select viewer to link to','pdf-viewer-by-themencode' ); ?></label>
                        <select name="pvfw_link_post_id" id="pvfw_link_post_id">
                            <?php
                                // Query posts based on the selected post type pdfviewer
                                $posts = get_posts(array('post_type' => 'pdfviewer', 'numberposts' => -1));
                                foreach ($posts as $post) {
                                    echo "<option value='{$post->ID}'>{$post->post_title}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="pvfw-form-item">
                        <label for="pvfw_link_text"><?php _e( 'Link Text','pdf-viewer-by-themencode' ); ?></label>
                        <input type="text" name="pvfw_link_text" id="pvfw_link_text" value=" <?php echo esc_attr( 'Open PDF' ); ?>">
                    </div>

                    <div class="pvfw-form-item">
                        <label for="pvfw_open_type"><?php _e( 'Link Target','pdf-viewer-by-themencode' ); ?></label>
                        <select name="pvfw_open_type" id="pvfw_open_type">
                            <option value="pvfw-plain-link"><?php _e( 'Link','pdf-viewer-by-themencode' ); ?></option>
                            <option value="dspdfvfw-iframe"><?php _e( 'Popup','pdf-viewer-by-themencode' ); ?></option>
                         </select>
                    </div>

                    <div class="pvfw-form-item">
                        <label for="pvfw_link_target"><?php _e( 'Link Target','pdf-viewer-by-themencode' ); ?></label>
                        <select name="pvfw_link_target" id="pvfw_link_target">
                            <option value="_parent"><?php _e( 'Same Window','pdf-viewer-by-themencode' ); ?></option>
                            <option value="_blank"><?php _e( 'New Window','pdf-viewer-by-themencode' ); ?></option>
                         </select>
                    </div>

                   <div class="pvfw-form-item">
                        <label for="pvfw_link_jump_page"><?php _e( 'Jump to Page','pdf-viewer-by-themencode' ); ?></label>
                        <input type="text" name="pvfw_link_jump_page" id="pvfw_link_jump_page">
                    </div>

                   <div class="pvfw-form-item">
                        <label for="pvfw_link_class"><?php _e( 'Link CSS Class','pdf-viewer-by-themencode' ); ?></label>
                        <input type="text" name="pvfw_link_class" id="pvfw_link_class" value="<?php echo esc_attr( 'pdf-viewer-link-single' ); ?>">
                    </div>

            </div> 
           
        
            <div class="pvfw-submit-btn-wrapper">
               <input type="submit" name="pvfw_shortcode_submit" class="button-primary pvfw-shortcode-submit-btn" value=" <?php echo esc_attr( 'Generate Shortcode' ); ?> ">
            </div>
        </form>
    </div>
</div>
<?php
}