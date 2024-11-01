<?php

// WP Photos
// version 1.2, 2004-02-16
//
// Copyright (c) 2002-2004 Alex King
// http://www.alexking.org/software/wordpress/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************


class wpphotos {
	var $display_URL;
	var $preview_align;
	var $grid_columns;
	var $import_IPTC;
	var $photographer;
	var $type;

	function wpphotos() {
		$this->display_URL = 'wp-photo.php';
		$this->preview_align = 'right';
		$this->grid_columns = 3;
		$this->photographer = '';
		$this->import_IPTC = 0;
	}
}

$wpphotos = new wpphotos;

// get settings

if (!@include('wp-photos.config.php')) {
	if(!@include('../wp-photos.config.php')) {
		die('error, could not include WP Photos settings.');
	}
}

class photo {
	var $ID;
	var $post_ID;
	var $photo;
	var $thumb;
	var $caption;
	var $photographer;
	var $preview;
	var $post;
	
	function photo() {
		$this->ID = '';
		$this->post_ID = '';
		$this->photo = '';
		$this->thumb = '';
		$this->caption = '';
		$this->photographer = '';
		$this->preview = 0;
		$this->post = 0;
	}
	
	function retrieve($id) {
		global $tablephotos;
		$result = mysql_query("SELECT * from $tablephotos WHERE ID = '$id'");
		while ($photo = mysql_fetch_object($result)) {
			$this->ID = $photo->ID;
			$this->post_ID = $photo->post_ID;
			$this->photo = $photo->photo;
			$this->thumb = $photo->thumb;
			$this->caption = $photo->caption;
			$this->photographer = $photo->photographer;
			$this->preview = $photo->preview;
			$this->post = $photo->post;
		}
	}

	function store() {
		global $tablephotos;
		$result = mysql_query("INSERT INTO $tablephotos "
		                     ."(post_ID "
		                     .",photo "
		                     .",thumb "
		                     .",caption "
		                     .",photographer "
		                     .",preview "
		                     .",post "
		                     .") VALUES "
		                     ."('$this->post_ID' "
		                     .",'$this->photo' "
		                     .",'$this->thumb' "
		                     .",'$this->caption' "
		                     .",'$this->photographer' "
		                     .",'$this->preview' "
		                     .",'$this->post' "
		                     .")"
		                     );
		if (!$result) {
			return false;
		}
		else {
			return true;
		}
	}

	function update() {
		global $tablephotos;
		$result = mysql_query("UPDATE $tablephotos SET "
		                     ."photo = '$this->photo', "
		                     ."thumb = '$this->thumb', "
		                     ."caption = '$this->caption', "
		                     ."photographer = '$this->photographer', "
		                     ."preview = '$this->preview', "
		                     ."post = '$this->post' "
		                     ."WHERE ID = '$this->ID'"
		                     );
		if (!$result) {
			return false;
		}
		else {
			return true;
		}
	}
	
	function delete() {
		global $tablephotos;
		$result = mysql_query("DELETE FROM $tablephotos "
		                     ."WHERE ID = '$this->ID'"
		                     );
		if (!$result) {
			return false;
		}
		else {
			return true;
		}
	}
	
	function preview_thumbnail() {
		global $wpphotos, $siteurl;
		if (empty($this->thumb)) {
			return false;
		}
		$url = get_permalink();
		$thumb = '<img src="'.$siteurl.'/'.$this->thumb.'" alt="Copyright '.$this->photographer.'" class="wp-photo_thumbnail" />';
		$string = '<table border="0" cellpadding="0" cellspacing="0" align="'.$wpphotos->preview_align.'"><tr><td class="wp-photo_preview">';
		$string .= '<a href="'.$url.'">'.$thumb.'</a><br /><a href="'.$url.'" class="wp-caption">View Photos</a>';
		$string .= '</td></tr></table>';
		return $string;
	}

	function grid_thumbnail() {
		global $wpphotos, $siteurl;
		if (empty($this->thumb)) {
			return false;
		}
		$string = '<img src="'.$siteurl.'/'.$this->thumb.'" alt="Copyright '.$this->photographer.'" class="wp-photo_thumbnail" />';
		if (!empty($this->photo)) {
			$string = '<a href="'.$siteurl.'/'.$wpphotos->display_URL.'?id='.$this->ID.'" class="wp-caption">'.$string.'</a>';
		}
		return $string;
	}

	function display() {
		return '<img src="'.$this->photo.'" alt="'.htmlentities($this->caption).'" class="wp-photo" onclick="location.href=\''.$this->post_URL().'\'" onmouseover="window.status=\'back to post\'; return true;" onmouseout="window.status=\'\'; return true;">';
	}

	function post_URL() {
		global $querystring_start, $querystring_equal, $querystring_separator, $blogfilename, $siteurl;
		$url = $siteurl.'/'.$blogfilename.$querystring_start.'p'.$querystring_equal.$this->post_ID.$querystring_separator.'c'.$querystring_equal.'1';
		return $url;
	}
}

// should select photos as part of posts loop, since it is all global scope anyway

function get_preview_photo($post_ID) {
	global $tablephotos;
	$photo = new photo;
	$result = mysql_query("SELECT * from $tablephotos WHERE post_ID = '$post_ID' AND preview = '1'");
	while ($preview = mysql_fetch_object($result)) {
		$photo->ID = $preview->ID;
		$photo->post_ID = $preview->post_ID;
		$photo->photo = $preview->photo;
		$photo->thumb = $preview->thumb;
		$photo->caption = stripslashes($preview->caption);
		$photo->photographer = stripslashes($preview->photographer);
		$photo->preview = $preview->preview;
		$photo->post = $preview->post;
	}
	return $photo;
}

function get_post_photos($post_ID) {
	global $tablephotos;
	$photos = array();
	$result = mysql_query("SELECT * FROM $tablephotos WHERE post_ID = '$post_ID' ORDER BY thumb");
	if ($result != false) {
		while ($item = mysql_fetch_object($result)) {
			$photo = new photo;
			$photo->ID = $item->ID;
			$photo->post_ID = $item->post_ID;
			$photo->photo = $item->photo;
			$photo->thumb = $item->thumb;
			$photo->caption = stripslashes($item->caption);
			$photo->photographer = stripslashes($item->photographer);
			$photo->preview = $item->preview;
			$photo->post = $item->post;
			$photos[] = $photo;
		}
	}
	return $photos;	
}

function get_post_photos_count($post_ID) {
	global $tablephotos;
	$result = mysql_query("SELECT COUNT(ID) FROM $tablephotos WHERE post_ID = '$post_ID'");
	if ($result != false) {
		return mysql_result($result, 0);	
	}
	else {
		return 0;
	}
}

function get_thumbnail_grid($photos) {
	global $wpphotos;
	if (count($photos) > 0) {
		$extra = count($photos) % $wpphotos->grid_columns;
		$width = ceil(100 / $wpphotos->grid_columns);
		$string = '<table width="100%" cellpadding="0" cellspacing="10" border="0">'."\n";
		for ($i = 0; $i < count($photos); $i++) {
			if ($i == 0 || $i % $wpphotos->grid_columns == 0) {
				$string .= '<tr>'."\n";
			}
			$string .= '<td align="center" width="'.$width.'%">'.$photos[$i]->grid_thumbnail().'<br />'.htmlentities($photos[$i]->caption).'</td>'."\n";
			if (($i + 1) == count($photos) && $extra > 0) {
				for ($o = 0; $o < ($wpphotos->grid_columns - $extra); $o++) {
					$string .= '<td align="center" width="'.$width.'%">&nbsp;</td>'."\n";
				}
			}
			if (($i + 1) % $wpphotos->grid_columns == 0 || ($i + 1) == count($photos)) {
				$string .= '</tr>'."\n";
			}
		}
		$string .= '</table>'."\n";
	}
	else {
		$string = '';
	}
	return $string;
}

function get_folders($path, $post_ID) {
	$unsuitable = array();
	$folders = array();
	$output = array();
	$string = '';
	
	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." &&
			    is_dir($path.'/'.$file) && is_dir($path.'/'.$file.'/t')) { 
				if ($check = opendir($path.'/'.$file)) {
					$i = 0;
					while (false !== ($photo = readdir($check))) {
						if (strtolower(substr($photo, -4, 4)) == ".jpg") { 
							$i++;
						}
					}
					closedir($check);
				}
				if ($i > 0) {
					$i = 0;
					if ($check = opendir($path.'/'.$file.'/t')) {
						$i = 0;
						while (false !== ($photo = readdir($check))) {
							if (strtolower(substr($photo, -4, 4)) == ".jpg") { 
								$i++;
							}
						}
					}
					closedir($check);
					if ($i > 0) {
						$folders[strtolower($file)] = $file;
					}
				}
			}
			else if (is_dir($path.'/'.$file) && $file != "." && $file != "..") {
				$unsuitable[strtolower($file)] = $file;
			}
		}
		closedir($handle);

		if (count($folders) > 0) {
			$string .= '<p><strong>Selectable Folders</strong></p>';
			$string .= '<p>';
			uksort($folders, "strnatcasecmp");
			foreach ($folders as $k => $v) {
				$string .= '<nobr>(<a href="wp-photos.php?action=add&post_ID='.$post_ID.'&path='.urlencode($path.'/'.$v).'">Use -&gt;</a>) <a href="wp-photos.php?post_ID='.$post_ID.'&path='.urlencode($path.'/'.$v).'">'.$v.'</a></nobr><br />'."\n";
			}
			$string .= '</p>';
		}
		else {
			$string .= '<p>No suitable folders found.</p>';
		}
		if (count($unsuitable) > 0) {
			$string .= '<p><strong>Folders</strong></p>';
			$string .= '<p>';
			uksort($unsuitable, "strnatcasecmp");
			foreach ($unsuitable as $k => $v) {
				$string .= '<nobr><a href="wp-photos.php?post_ID='.$post_ID.'&path='.urlencode($path.'/'.$v).'">'.$v.'</a></nobr><br />'."\n";
			}
			$string .= '</p>';
		}
	}
	return $string;
}

function get_photos($path) {
	$photos = array();
	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			if (strtolower(substr($path.'/'.$file, -4, 4)) == ".jpg" &&
				is_file($path.'/'.$file) && is_file($path.'/t/'.$file)) {
				$photos[] = $file; 
			}
		}
		closedir($handle);
	}
	return $photos;
}

function wpphotos_link() {
	global $wpphotos, $id;
	$path = '';
	if (strtolower($wpphotos->type) == 'wordpress') {
		$path = '../';
	}
	$string = 'Photos: <a href="'.$path.'wp-photos.php?post_ID='.$id.'" target="_blank">Add</a>/'
	         .'<a href="'.$path.'wp-photos.php?post_ID='.$id.'&action=edit" target="_blank">Edit</a>';

	print($string);
}

function get_photos_count_msg($post_ID, $tags = 1) {
	$count = get_post_photos_count($post_ID);
	if ($count > 0) {
		$msg = '(This post has '.$count.' photos.)';
		if ($tags == 1) {
			return '<p>'.$msg.'</p>';
		}
		else {
			return ' '.$msg;
		}
	}
	else {
		return '';
	}
}

?>