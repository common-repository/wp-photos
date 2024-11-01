<?php
require_once("wp-blog-header.php");
require_once("wp-photos.hack.php");
if (isset($_REQUEST["id"])) {
	$photo = new photo;
	$photo->retrieve(intval($_REQUEST["id"]));
	$photos = get_post_photos($photo->post_ID);
	for ($i = 0; $i < count($photos); $i++) {
		if ($photos[$i]->ID == intval($_REQUEST["id"])) {
			$current_photo = $i;
		}
	}
}
else {
	die("Error: the variable &quot;id&quot; was not passed in.");
}
?>
<html>
<head>
<title><? bloginfo('name'); ?></title>
<style>
<!--

body, div, p, a, td, a:link, a:visited, a:active {
	color: #fff;
	font-family: verdana, arial, helvetica;
	font-size: .65em;
}
body {
	background: #000;
}
.td_hr {
	background: #999;
}
.legal {
	color: #666;
}
.wp-photo {
	border: 1px solid #fff;
}

//-->
</style>
</head>
<body>
	<table width="500" cellpadding="0" cellspacing="10" border="0" align="center">
<!-- photo -->
		<tr>
			<td colspan="3" align="center">
<?php
print($photo->display());
?>
			</td>
		</tr>
<!-- prev/caption/next -->
		<tr>
			<td width="10%">
<?php
if ($current_photo > 0) {
	print('<a href="'.$wpphotos->display_URL.'?id='.$photos[($current_photo - 1)]->ID.'">Previous</a>');
}
?>
				&nbsp;
			</td>
			<td width="80%" align="center">
<?php
print($photo->caption);
?>
				<br>&nbsp;
			</td>
			<td width="10%" align="right">
				&nbsp;
<?php
if (($current_photo + 1) < count($photos)) {
	print('<a href="'.$wpphotos->display_URL.'?id='.$photos[($current_photo + 1)]->ID.'">Next</a>');
}
?>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="10" border="0">
<!-- hr row -->
		<tr>
			<td class="td_hr" colspan="3"><img src="images/clear.gif" height="1" width="1" alt=""></td>
		</tr>
<!-- footer -->
		<tr>
			<td valign="top" colspan="3"><span class="legal">copyright &copy; <? bloginfo('name'); ?>. all rights reserved.</span></td>
		</tr>
	</table>
</body>
</html>
