
// Check if the id exists before adding the event listener
var PvfwUploadBtn = document.getElementById('pvfw_image_upload_image_button');
if (PvfwUploadBtn) {
    PvfwUploadBtn.addEventListener('click', function() {
        var mediaUploader = wp.media({
            frame: 'post',
            state: 'insert',
            multiple: false
        });

        // Function to run when image is selected
        mediaUploader.on('insert', function() {
            var json = mediaUploader.state().get('selection').first().toJSON();
            document.getElementById('pvfw_image_image_url').value = json.url;
        });

        // Open media uploader
        mediaUploader.open();
    });
}

// Check if the id exists before adding the event listener
// JavaScript to show/hide additional fields based on selected option

const PvfwShortcodeOption = document.getElementById('pvfw_shortcode_option');

if (PvfwShortcodeOption) {
    PvfwShortcodeOption.addEventListener('change', function() {
        const embedFields = document.getElementById('embedFields');
        const linkFields = document.getElementById('linkFields');
        const imageFields = document.getElementById('imageFields');

        if (this.value === 'pvfw_embed') {
            embedFields.style.display = 'block';
        } else {
            embedFields.style.display = 'none';
        }

        if (this.value === 'pvfw_link') {
            linkFields.style.display = 'block';
        } else {
            linkFields.style.display = 'none';
        }

        if (this.value === 'pvfw_image_link') {
            imageFields.style.display = 'block';
        } else {
            imageFields.style.display = 'none';
        }
    });
}

// Init Clipboard js 
document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector('.pvfw-copy-btn')) {
      new ClipboardJS('.pvfw-copy-btn');
    }
 });


// Clipboard Copy alert message 
document.addEventListener('DOMContentLoaded', function() {
if (document.querySelector('.pvfw-copy-btn')) {
    const copyButtons = document.querySelectorAll('.pvfw-copy-btn');

    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const inputElement = document.querySelector(this.getAttribute('data-clipboard-target'));
            const messageElement = document.getElementById(this.getAttribute('data-message-id'));

            if (inputElement) {
                // Show copied message
                messageElement.style.display = 'block';
                setTimeout(() => messageElement.style.display = 'none', 2000);
            }
        });
    });
   }
});



  


