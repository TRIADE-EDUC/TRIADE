<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: camera_upload.php,v 1.3 2018-12-05 07:58:30 dgoron Exp $

$base_path     = ".";
$base_noheader = 1;
$base_nobody   = 1;
require_once ($base_path."/includes/init.inc.php");
require_once('./classes/encoding_normalize.class.php');

if(!isset($upload_url)) $upload_url = '';
$manag_cache=getimage_cache('', '', '', $upload_url, '', $pmb_book_pics_url, 1);
if($manag_cache["location"]){
	unlink($manag_cache["location"]);
}

$status = 0;
$upload_filename = '';
if(isset($_POST['upload_filename']) && isset($_POST['imgBase64'])) {
	$upload_filename = $_POST['upload_filename'];
	$rawData = $_POST['imgBase64'];
	
	$filteredData = explode(',', $rawData);
	$unencoded = base64_decode($filteredData[1]);
	
	$fp = fopen($upload_filename, 'w');
	if($fp) {
		fwrite($fp, $unencoded);
		fclose($fp);
		$status = 1;
	}
}
print encoding_normalize::json_encode(array('status' => $status, 'message' => $upload_filename));