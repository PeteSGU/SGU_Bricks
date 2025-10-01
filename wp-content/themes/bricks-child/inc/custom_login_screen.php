<?php function login_page_custom_bg_image() { ?>
	
<style type="text/css">
  body{
	background-image:url('https://s46437.p1421.sites.pressdns.com/wp-content/themes/bricks-child/images/surf.jpg') !important;
	background-size:cover !important;
	background-position:center center !important;
  }
  
  .login form {
	  border-radius: 10px;
  }
</style>
<?php }

add_action( 'login_enqueue_scripts', 'login_page_custom_bg_image' );
?>