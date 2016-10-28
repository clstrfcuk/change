<?php
$full_path = __FILE__;
$path = explode ( 'wp-content', $full_path );
require_once ($path [0] . '/wp-load.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript"
	src="<?php echo DESIGNTHEMES_TINYMCE_URL."/js/column-control.js";?>"></script>

<script type="text/javascript"
	src="<?php echo DESIGNTHEMES_TINYMCE_URL."/js/dialog.js";?>"></script>
</head>
<body>

	<div id="scn-dialog">

		<div id="scn-options">

			<h3>Customize the Shortcode</h3>

			<table id="scn-options-table">
			</table>

			<div style="float: left">

				<input type="button" id="scn-btn-cancel" class="button"
					name="cancel" value="Cancel" />

			</div>
			<div style="float: right">

				<input type="button" id="scn-btn-insert" class="button-primary"
					name="insert" value="Insert" />

			</div>

		</div>

	</div>

</body>
</html>