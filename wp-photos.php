<?php

if (empty($_REQUEST["post_ID"])) {
	die("Whoops, no 'post_ID' passed in, I don't know what post to attach photos to.");
}

require_once('wp-config.php');
require_once('wp-includes/functions.php');
require_once('wp-photos.hack.php');

get_currentuserinfo();

if ($user_level == 0) {
	die('Whoops, not authorized.');
}

$path = 'wp-photos';
if (isset($_REQUEST["path"])) {
	$path = stripslashes($_REQUEST["path"]);
}

$action = '';
if (isset($_REQUEST["action"])) {
	$action = $_REQUEST["action"];
}
switch ($action) {
	case "add":
		ob_start();
?>
<form action="wp-photos.php" method="post">
<input type="hidden" name="action" value="save" />
<input type="hidden" name="path" value="<?=$path?>" />
<input type="hidden" name="post_ID" value="<?=intval($_REQUEST["post_ID"])?>" />
<table cellspacing="10" cellpadding="0" border="0">
<?

$photos = get_photos($path);
for ($i = 0; $i < count($photos); $i++) {
	$i == 0 ? $checked = "checked" : $checked = "";
	$caption = '';
	if ($wpphotos->import_IPTC == 1) {
		$iptc = getimagesize($path.'/'.$photos[$i], &$info); 
		$iptc = iptcparse($info["APP13"]); 
		if (!empty($iptc["2#005"][0])) {
			$caption = $iptc["2#005"][0];
		}
	}
	print('<tr>'
	     .'<td width="1%" valign="top" style="padding-top: 7px;"><input type="radio" name="preview" value="'.$i.'" '.$checked.' /></td>'
	     .'<td width="1%" align="center"><img src="'.$path.'/t/'.$photos[$i].'" /></td>'
	     .'<td>'
	     .'<p>Caption:<br /><input type="text" name="caption_'.$i.'" value="'.$caption.'" size="50" /></p>'
	     .'<p>Photographer:<br /><input type="text" name="photographer_'.$i.'" value="'.$wpphotos->photographer.'" size="20" /></p>'
	     .'</td>'
	     .'</tr>'
	     ."\n"
	     );
}
?>
<tr>
<td colspan="3"><input type="submit" name="submit" value="Save" /></td>
</tr>
</table>
</form>
<?
		$body = ob_get_contents();
		ob_end_clean();
		break;
	case "edit":
		ob_start();
?>
<form action="wp-photos.php" method="post">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="path" value="<?=$path?>" />
<input type="hidden" name="post_ID" value="<?=intval($_REQUEST["post_ID"])?>" />
<table cellspacing="10" cellpadding="0" border="0">
<?

$photos = get_post_photos($_REQUEST["post_ID"]);
$i = 0;
foreach ($photos as $photo) {
	$photo->preview == 1 ? $checked = "checked" : $checked = "";
	print('<tr>'
	     .'<td width="1%" valign="top" style="padding-top: 7px;"><input type="radio" name="preview" value="'.$photo->ID.'" '.$checked.' /></td>'
	     .'<td width="1%" align="center"><img src="'.$photo->thumb.'" /></td>'
	     .'<td>'
	     .'<p>Caption:<br /><input type="text" name="caption_'.$photo->ID.'" value="'.$photo->caption.'" size="50" /></p>'
	     .'<p>Photographer:<br /><input type="text" name="photographer_'.$photo->ID.'" value="'.$photo->photographer.'" size="20" /></p>'
	     .'<p><a href="wp-photos.php?action=delete&ID='.$photo->ID.'&post_ID='.intval($_REQUEST["post_ID"]).'" onclick="if (!confirm(\'Are you sure you want to delete this photo?\')) { return false; }">Delete This Photo</a></p>'
	     .'</td>'
	     .'</tr>'
	     ."\n"
	     );
	$i++;
}
?>
<tr>
<td colspan="3"><input type="submit" name="submit" value="Save" /></td>
</tr>
</table>
</form>
<?
		$body = ob_get_contents();
		ob_end_clean();
		break;
	case "save":
		$files = get_photos($path);
		for ($i = 0; $i < count($files); $i++) {
			$photo = new photo;
			$photo->post_ID = intval($_REQUEST["post_ID"]);
			$photo->photo = $path.'/'.$files[$i];
			$photo->thumb = $path.'/t/'.$files[$i];
			$photo->caption = $_REQUEST['caption_'.$i];
			$photo->photographer = $_REQUEST['photographer_'.$i];
			if (isset($_REQUEST["preview"]) && $_REQUEST["preview"] == $i) {
				$photo->preview = 1;
			}
			$photo->store();
		}
		$body = '<p>Added '.count($files).' photos to post #'.$_REQUEST["post_ID"].'</p>';
		break;
	case "update":
		$files = get_post_photos(intval($_REQUEST["post_ID"]));
		foreach ($files as $photo) {
			$photo->caption = $_REQUEST['caption_'.$photo->ID];
			$photo->photographer = $_REQUEST['photographer_'.$photo->ID];
			if (isset($_REQUEST["preview"]) && $_REQUEST["preview"] == $photo->ID) {
				$photo->preview = 1;
			}
			$photo->update();
		}
		$body = '<p>Updated '.count($files).' photos to post #'.$_REQUEST["post_ID"].'</p>';
		break;
	case "delete":
		$photo = new photo;
		$photo->ID = intval($_REQUEST["ID"]);
		if ($photo->delete()) {
			$body = '<p>Deleted the selected photo.</p>';
		}
		else {
			$body = '<p>Error - the selected photo was not deleted. Bummer.</p>';
		}
		break;
	default:
		$body = get_folders($path, $_REQUEST["post_ID"]);
		break;
}

?>
<html>
<head>
<title>WP Photos</title>
<style>
<!--

body {
	margin: 0px;
}
body, p, td, div, span {
	font-family: verdana, tahoma, geneva, arial, helvetica;
	font-size: 10px;
}
#header {
	background: #ccc;
	border-bottom: 1px solid #999;
	font-family: verdana, tahoma, geneva, arial, helvetica;
	font-size: 12px;
	font-weight: bold;
	padding: 5px;
	padding-right: 0px;
}
#body {
	padding: 10px;
}
#footer {
	border-top: 1px solid #999;
	padding: 5px;
	padding-right: 0px;
}

//-->
</style>
</head>
<body>
<div id="header">
	WP Photos
</div>
<div id="body">
<?=$body?>
</div>
<div id="footer">
	<p><a href="http://www.alexking.org/software/wordpress/" target="_blank">WP Photos</a> available at alexking.org.</p>
</div>
</body>
</html>