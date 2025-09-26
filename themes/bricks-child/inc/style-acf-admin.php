

<?php /**
	function my_acf_admin_head() {
?>
<style type="text/css">

	.edit-post-meta-boxes-area .postbox-header  {
         background-color:#1f1f64;
         color: #fff;
    }

    .acf-repeater.-row > table > tbody > tr > td,
    .acf-repeater.-block > table > tbody > tr > td {
        border-top: 2px solid#E86233;
    }

    .acf-repeater .acf-row-handle {
        vertical-align: top !important;
        padding-top: 16px;
    }

    .acf-repeater .acf-row-handle span {
        font-size: 20px;
        font-weight: bold;
        color:#E86233;
    }

    .imageUpload img {
        width: 75px;
    }

    .acf-repeater .acf-row-handle .acf-icon.-minus {
        top: 30px;
    }

</style>
<?php
}

add_action('acf/input/admin_head', 'my_acf_admin_head');**/