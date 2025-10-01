/* 
 * Disable some "Select Theme" options in single and global
*/
let selectElementInTncPvfwPdfViewerFieldsInSelectTheme = document.querySelector("select[name='tnc_pvfw_pdf_viewer_fields[appearance-select-theme]']");
if (selectElementInTncPvfwPdfViewerFieldsInSelectTheme != null && selectElementInTncPvfwPdfViewerFieldsInSelectTheme != '') {
  selectElementInTncPvfwPdfViewerFieldsInSelectTheme.querySelector("option[value='smart-red']").disabled = true;
  selectElementInTncPvfwPdfViewerFieldsInSelectTheme.querySelector("option[value='material-blue']").disabled = true;
  selectElementInTncPvfwPdfViewerFieldsInSelectTheme.querySelector("option[value='louis-purple']").disabled = true;  
  selectElementInTncPvfwPdfViewerFieldsInSelectTheme.querySelector("option[value='sea-green']").disabled = true;  
}

let selectElementInPvfwCsfOptionsInSelectTheme = document.querySelector("select[name='pvfw_csf_options[appearance-select-theme]']");
if (selectElementInPvfwCsfOptionsInSelectTheme != null && selectElementInPvfwCsfOptionsInSelectTheme !== '') {
  selectElementInPvfwCsfOptionsInSelectTheme.querySelector("option[value='smart-red']").disabled = true;
  selectElementInPvfwCsfOptionsInSelectTheme.querySelector("option[value='material-blue']").disabled = true;
  selectElementInPvfwCsfOptionsInSelectTheme.querySelector("option[value='louis-purple']").disabled = true;
  selectElementInPvfwCsfOptionsInSelectTheme.querySelector("option[value='sea-green']").disabled = true;
}



/* 
 * Disable icon options in single and global
*/
let selectElementInTncPvfwPdfViewerFieldsInSelectIcon = document.querySelector("select[name='tnc_pvfw_pdf_viewer_fields[appearance-select-icon]']");
if (selectElementInTncPvfwPdfViewerFieldsInSelectIcon != null && selectElementInTncPvfwPdfViewerFieldsInSelectIcon != '') {
  selectElementInTncPvfwPdfViewerFieldsInSelectIcon.querySelector("option[value='dark-icons']").disabled = true;
  selectElementInTncPvfwPdfViewerFieldsInSelectIcon.querySelector("option[value='light-icons']").disabled = true;
}

let selectElementInPvfwCsfOptionsInSelectIcon = document.querySelector("select[name='pvfw_csf_options[appearance-select-icon]']");
if (selectElementInPvfwCsfOptionsInSelectIcon != null && selectElementInPvfwCsfOptionsInSelectIcon !== '') {
  selectElementInPvfwCsfOptionsInSelectIcon.querySelector("option[value='dark-icons']").disabled = true;
  selectElementInPvfwCsfOptionsInSelectIcon.querySelector("option[value='light-icons']").disabled = true;
}


// JavaScript to show/hide additional fields based on selected option
document.getElementById('pvfw_shortcode_option').addEventListener('change', function() {
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

