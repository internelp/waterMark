<?php

// 本文件提取自 phpwind


function ImgWaterMark( $db_waterfonts, $db_watermark,$source, $w_pos = 0, $w_img = '', $w_text = '', $w_font = 12, $w_color = '#FF0000', $w_pct, $w_quality, $dstsrc = null) {

	$sourcedb = $waterdb = array();

	if (!($sourcedb = GetImgInfo($source))) {
		return false;
	}

	if ($db_watermark == 1 && GetImgInfo("$w_img")) {
		$waterdb = GetImgInfo("$w_img");
	} elseif ($db_watermark == 2 && $w_text) {
		empty($db_waterfonts) && $db_waterfonts = 'en/PilsenPlakat';
		empty($w_font) && $w_font = 12;
		$fontsfile = "fonts/$db_waterfonts.ttf";
		$temp = imagettfbbox($w_font, 0, $fontsfile, $w_text); //取得使用 TrueType 字体的文本的范围
		$waterdb['width'] = $temp[2] - $temp[6];
		$waterdb['height'] = $temp[3] - $temp[7];
		unset($temp);
	} else {
		return false;
	}
	if ($w_pos == 0) {
		$wX = rand(0, ($sourcedb['width'] - $waterdb['width']));
		$wY = $db_watermark == 1 ? rand(0, ($sourcedb['height'] - $waterdb['height'])) : rand($waterdb['height'], $sourcedb['height']);
	} elseif ($w_pos == 1) {
		$wX = 5;
		$wY = $db_watermark == 1 ? 5 : $waterdb['height'];
	} elseif ($w_pos == 2) {
		$wX = ($sourcedb['width'] - $waterdb['width']) / 2;
		$wY = $db_watermark == 1 ? 5 : $waterdb['height'];
	} elseif ($w_pos == 3) {
		$wX = $sourcedb['width'] - $waterdb['width'] - 5;
		$wY = $db_watermark == 1 ? 5 : $waterdb['height'];
	} elseif ($w_pos == 4) {
		$wX = 5;
		$wY = $db_watermark == 1 ? ($sourcedb['height'] - $waterdb['height']) / 2 : ($sourcedb['height'] + $waterdb['height']) / 2;
	} elseif ($w_pos == 5) {
		$wX = ($sourcedb['width'] - $waterdb['width']) / 2;
		$wY = $db_watermark == 1 ? ($sourcedb['height'] - $waterdb['height']) / 2 : ($sourcedb['height'] + $waterdb['height']) / 2;
	} elseif ($w_pos == 6) {
		$wX = $sourcedb['width'] - $waterdb['width'] - 5;
		$wY = $db_watermark == 1 ? ($sourcedb['height'] - $waterdb['height']) / 2 : ($sourcedb['height'] + $waterdb['height']) / 2;
	} elseif ($w_pos == 7) {
		$wX = 5;
		$wY = $db_watermark == 1 ? $sourcedb['height'] - $waterdb['height'] - 5 : $sourcedb['height'] - 5;
	} elseif ($w_pos == 8) {
		$wX = ($sourcedb['width'] - $waterdb['width']) / 2;
		$wY = $db_watermark == 1 ? $sourcedb['height'] - $waterdb['height'] - 5 : $sourcedb['height'] - 5;
	} elseif ($w_pos == 9) {
		$wX = $sourcedb['width'] - $waterdb['width'] - 5;
		$wY = $db_watermark == 1 ? $sourcedb['height'] - $waterdb['height'] - 5 : $sourcedb['height'] - 5;
	} else {
		$wX = rand(0, ($sourcedb['width'] - $waterdb['width']));
		$wY = $db_watermark == 1 ? rand(0, ($sourcedb['height'] - $waterdb['height'])) : rand($waterdb['height'], $sourcedb['height']);
	}
	imagealphablending($sourcedb['source'], true);
	if ($db_watermark == 1) {
		if ($waterdb['type'] == 'png') {
			$tmp = imagecreatetruecolor($sourcedb['width'], $sourcedb['height']);
			imagecopy($tmp, $sourcedb['source'], 0, 0, 0, 0, $sourcedb['width'], $sourcedb['height']);
			imagecopy($tmp, $waterdb['source'], $wX, $wY, 0, 0, $waterdb['width'], $waterdb['height']);
			$sourcedb['source'] = $tmp;
			//imagecopy($sourcedb['source'], $waterdb['source'], $wX, $wY, 0, 0, $waterdb['width'], $waterdb['height']);
		} else {
			imagecopymerge($sourcedb['source'], $waterdb['source'], $wX, $wY, 0, 0, $waterdb['width'], $waterdb['height'], $w_pct);
		}
	} else {
		if (strlen($w_color) != 7) return false;
		$R = hexdec(substr($w_color, 1, 2));
		$G = hexdec(substr($w_color, 3, 2));
		$B = hexdec(substr($w_color, 5));
		//imagestring($sourcedb['source'],$w_font,$wX,$wY,$w_text,imagecolorallocate($sourcedb['source'],$R,$G,$B));
		if (strpos($db_waterfonts, 'ch/') !== false && strtoupper($GLOBALS['db_charset']) != 'UTF-8') {
			$w_text = pwConvert($w_text, 'UTF-8', $GLOBALS['db_charset']);
		}
		imagettftext($sourcedb['source'], $w_font, 0, $wX, $wY, imagecolorallocate($sourcedb['source'], $R, $G, $B), $fontsfile, $w_text);
	}
	//	P_unlink($source);
	$dstsrc && $source = $dstsrc;
	MakeImage($sourcedb['type'], $sourcedb['source'], $source, $w_quality);
	isset($waterdb['source']) && imagedestroy($waterdb['source']);
	imagedestroy($sourcedb['source']);
	return true;
}

function GetImgInfo($srcFile) {
	$imgdata = array(
		'width'		=> 0,
		'height'	=> 0,
		'type'		=> 0,
		);
	$imgdata =  GetImgSize($srcFile);
	if ($imgdata['type'] == 1) {
		$imgdata['type'] = 'gif';
	} elseif ($imgdata['type'] == 2) {
		$imgdata['type'] = 'jpeg';
	} elseif ($imgdata['type'] == 3) {
		$imgdata['type'] = 'png';
	} elseif ($imgdata['type'] == 6) {
		$imgdata['type'] = 'bmp';
	} else {
		return false;
	}

	if (empty($imgdata) || !function_exists('imagecreatefrom' . $imgdata['type'])) {
		return false;
	}
	$imagecreatefromtype = 'imagecreatefrom' . $imgdata['type'];
	$imgdata['source'] = $imagecreatefromtype($srcFile);
	!$imgdata['width'] && $imgdata['width'] = imagesx($imgdata['source']);
	!$imgdata['height'] && $imgdata['height'] = imagesy($imgdata['source']);
	return $imgdata;
}
function MakeImage($type, $image, $filename, $quality = '75') {
	$makeimage = 'image' . $type;
	if (!function_exists($makeimage)) {
		return false;
	}
	if ($type == 'jpeg') {
		$makeimage($image, $filename, $quality);
	} else {
		$makeimage($image, $filename);
	}
	return true;
}
function GetImgSize($srcFile, $srcExt = null) {
	empty($srcExt) && $srcExt = strtolower(substr(strrchr($srcFile, '.'), 1));
	$srcdata = array(
		'width'		=> 0,
		'height'	=> 0,
		'type'		=> 0,
		);
	$exts = array('jpg', 'jpeg', 'jpe', 'jfif');
	if (function_exists('read_exif_data') && in_array($srcExt, $exts)) {
		$datatemp = @read_exif_data($srcFile);
		$srcdata['width'] = $datatemp['COMPUTED']['Width'];
		$srcdata['height'] = $datatemp['COMPUTED']['Height'];
		$srcdata['type'] = 2;
		unset($datatemp);
	}
	!$srcdata['width'] && list($srcdata['width'], $srcdata['height'], $srcdata['type']) = @getimagesize($srcFile);
	if (!$srcdata['type'] || ($srcdata['type'] == 1 && in_array($srcExt, $exts))) { //noizy fix
		return false;
	}
	return $srcdata;
}

?>