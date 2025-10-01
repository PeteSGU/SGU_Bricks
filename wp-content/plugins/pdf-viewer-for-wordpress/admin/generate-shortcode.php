<?php 

/**
 * Adds a submenu page under a custom post type pdfviewer.
 */
function tnc_pvfw_shortcode_generate_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=pdfviewer',
        __( 'Generate Shortcode', 'pdf-viewer-for-wordpress'),
        __( 'Generate Shortcode', 'pdf-viewer-for-wordpress'),
        'manage_options',
        'generate-shortcode-page',
        'generate_shortcode_page_callback'
    );
}

add_action('admin_menu', 'tnc_pvfw_shortcode_generate_submenu_page');

/**
 * Display callback for the submenu page.
 */
function generate_shortcode_page_callback() {
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
          
            // Add them to the shortcode
            $pvfw_shortcode = "[pvfw-embed viewer_id='$post_id' width='$width' height='$height' iframe_title='$iframe_title']"; 
        }
        if ($selected_option === 'pvfw_link') {
            $post_id = sanitize_text_field($_POST['pvfw_link_post_id']); 
            $link_text = sanitize_text_field($_POST['pvfw_link_text']);
            $link_target = sanitize_text_field($_POST['pvfw_link_target']);
            $link_class = sanitize_text_field($_POST['pvfw_link_class']);
            
            // Add them to the shortcode
            $pvfw_shortcode = "[pvfw-link viewer_id='$post_id' text='$link_text' class='$link_class' target='$link_target']"; 
        }

        if ($selected_option === 'pvfw_image_link') {
            $post_id = sanitize_text_field($_POST['pvfw_image_post_id']); 
            $image_url = sanitize_text_field($_POST['pvfw_image_image_url']);
            $link_target = sanitize_text_field($_POST['pvfw_image_link_target']);
            $image_width = sanitize_text_field($_POST['pvfw_image_width']);
            $image_height = sanitize_text_field($_POST['pvfw_image_height']);
            $image_alignment = sanitize_text_field($_POST['pvfw_image_link_alignment']);
            $image_class = sanitize_text_field($_POST['pvfw_image_class']);
    
            // Add them to the shortcode
            $pvfw_shortcode = "[pvfw-image-link viewer_id='$post_id' img_url='$image_url' target='$link_target' width='$image_width' height='$image_height' alignment='$image_alignment' class='$image_class']"; 
        }

    ?>
    
    
      <div class="pvfw-right-form-wrap">
        <div class="pvfw-shortcode-title">
            <h2><?php _e( 'Shortcode','pdf-viewer-for-wordpress' ); ?></h2>
            <p><?php _e( 'Insert this shortcode into your posts or pages','pdf-viewer-for-wordpress' );?></p>
        </div>
         <div class="pvfw-shortcode-contain">
            <code><?php echo $pvfw_shortcode; ?></code>
         </div>
      </div>

    <?php
    }else {
      ?>
        <div class="error">
            <p><?php _e( 'Please select a shortcode before submitting the form.','pdf-viewer-for-wordpress' ); ?></p>
        </div>
      <?php
    }
    ?>
    <div class="pvfw-left-form-wrap">
         <div class="pvfw-shortcode-title">
            <h2><?php _e( 'Generate Shortcode','pdf-viewer-for-wordpress' ); ?></h2>
            <p><?php _e( 'Create shortcode and insert it on your pages or posts','pdf-viewer-for-wordpress' ); ?></p>
        </div>
        <form method="post" action="">
           <div class="pvfw-form-item">
                <label for="pvfw_shortcode_option"><?php _e( 'Select Shortcode','pdf-viewer-for-wordpress' ); ?></label>
                <select name="pvfw_shortcode_option" id="pvfw_shortcode_option">
                    <option value=""><?php _e( 'Select Shortcode','pdf-viewer-for-wordpress' ); ?></option>
                    <option value="pvfw_embed"><?php _e( 'Embed a FlipBook','pdf-viewer-for-wordpress' ); ?></option>
                    <option value="pvfw_link"><?php _e( 'Link to a FlipBook','pdf-viewer-for-wordpress' ); ?></option>
                    <option value="pvfw_image_link"><?php _e( 'Image Link to a FlipBook','pdf-viewer-for-wordpress' ); ?></option>
                </select>
            </div>

            <!-- Additional fields for Embed option -->
            <div id="embedFields" style="display: none;">
            <div class="pvfw-form-item">
                <label for="pvfw_embed_post_id"><?php _e( 'Select FlipBook to Embed','pdf-viewer-for-wordpress' ); ?></label>
            
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
                    <label for="pvfw_embed_width"><?php _e( 'Width' , 'pdf-viewer-for-wordpress' ); ?></label>
                    <input type="text" name="pvfw_embed_width" id="pvfw_embed_width" value="<?php echo esc_attr( '100%' ); ?>">
                </div>
                <div class="pvfw-form-item">
                    <label for="pvfw_embed_height"><?php _e( 'Height' , 'pdf-viewer-for-wordpress' ); ?></label>
                     <input type="text" name="pvfw_embed_height" id="pvfw_embed_height" value=" <?php echo esc_attr( '800' ); ?>">
                </div>
                <div class="pvfw-form-item">
                    <label for="pvfw_embed_iframe_title"><?php _e( 'iFrame Title','pdf-viewer-for-wordpress' ); ?></label>
                    <input type="text" name="pvfw_embed_iframe_title" id="pvfw_embed_iframe_title"> 
                </div>
            </div>
             <!-- Additional fields for link option -->
             <div id="linkFields" style="display: none;">
                    <div class="pvfw-form-item">
                        <label for="pvfw_link_post_id"><?php _e( 'Select FlipBook to Link to','pdf-viewer-for-wordpress' ); ?></label>
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
                        <label for="pvfw_link_text"><?php _e( 'Link Text','pdf-viewer-for-wordpress' ); ?></label>
                        <input type="text" name="pvfw_link_text" id="pvfw_link_text" value=" <?php echo esc_attr( 'Open PDF' ); ?>">
                    </div>
                    <div class="pvfw-form-item">
                        <label for="pvfw_link_target"><?php _e( 'Link Target','pdf-viewer-for-wordpress' ); ?></label>
                        <select name="pvfw_link_target" id="pvfw_link_target">
                            <option value="_parent"><?php _e( 'Same Window','pdf-viewer-for-wordpress' ); ?></option>
                            <option value="_blank"><?php _e( 'New Window','pdf-viewer-for-wordpress' ); ?></option>
                         </select>
                   </div>
                   <div class="pvfw-form-item">
                        <label for="pvfw_link_class"><?php _e( 'Link CSS Class','pdf-viewer-for-wordpress' ); ?></label>
                        <input type="text" name="pvfw_link_class" id="pvfw_link_class" value="<?php echo esc_attr( 'pdf-viewer-link-single' ); ?>">
                    </div>

            </div> 
            <!-- Additional fields for image option -->
            <div id="imageFields" style="display: none;">
                    <div class="pvfw-form-item">
                         <label for="pvfw_image_post_id"><?php _e( 'Select FlipBook to Link Image','pdf-viewer-for-wordpress' );?></label>
                         <select name="pvfw_image_post_id" id="pvfw_image_post_id">
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
                        <label for="pvfw_image_image_url"><?php _e( 'Upload Image','pdf-viewer-for-wordpress' ) ;?></label>
                        <input type="text" name="pvfw_image_image_url" id="pvfw_image_image_url" value="" readonly>
                        <input type="button" id="pvfw_image_upload_image_button" class="button-secondary" value="<?php echo esc_attr( 'Select Image' ); ?>">
                    </div>
                     <div class="pvfw-form-item">
                        <label for="pvfw_image_link_target"><?php _e( 'Image Link Target','pdf-viewer-for-wordpress' ); ?></label>
                        <select name="pvfw_image_link_target" id="pvfw_image_link_target">
                            <option value="_parent"><?php _e( 'Same Window','pdf-viewer-for-wordpress' ); ?></option>
                            <option value="_blank"><?php _e( 'New Window','pdf-viewer-for-wordpress' ); ?></option>
                        </select>
                     </div>
                     <div class="pvfw-form-item">
                        <label for="pvfw_image_width"><?php _e( 'Image Width','pdf-viewer-for-wordpress' ); ?></label>
                        <input type="text" name="pvfw_image_width" id="pvfw_image_width" value="<?php echo esc_attr( '100%' ); ?>">
                    </div>
                    <div class="pvfw-form-item">
                        <label for="pvfw_image_height"><?php _e( 'Image Height','pdf-viewer-for-wordpress' ); ?></label>
                        <input type="text" name="pvfw_image_height" id="pvfw_image_height" value="<?php echo esc_attr( 'auto' ); ?>">
                    </div>
                    <div class="pvfw-form-item">
                         <label for="pvfw_image_link_alignment"><?php _e( 'Image Alignment','pdf-viewer-for-wordpress' ); ?></label>
                        <select name="pvfw_image_link_alignment" id="pvfw_image_link_alignment">
                            <option value="inherit"><?php _e( 'Inherit','pdf-viewer-for-wordpress' );?></option>
                            <option value="left"><?php _e( 'Left','pdf-viewer-for-wordpress' );?></option>
                            <option value="center"><?php _e( 'Center','pdf-viewer-for-wordpress' ); ?></option>
                            <option value="right"><?php _e( 'Right','pdf-viewer-for-wordpress' ); ?></option>
                    </select>
                   </div>
                   <div class="pvfw-form-item">
                        <label for="pvfw_image_class"><?php _e( 'Image CSS Class','pdf-viewer-for-wordpress' ); ?></label>
                        <input type="text" name="pvfw_image_class" id="pvfw_image_class" value="<?php echo esc_attr( 'pdf-viewer-image-link-single' ); ?>">
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




