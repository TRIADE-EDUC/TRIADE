<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: camera_upload.php,v 1.6 2023/02/01 16:03:22 qvarin Exp $

use Pmb\Common\Library\Image\UploadImage;

$base_path     = ".";
$base_noheader = 1;
$base_nobody   = 1;

require_once "{$base_path}/includes/init.inc.php";

global $class_path;
require_once "{$class_path}/encoding_normalize.class.php";

function response(int $status = 0, string $message = "")
{
    print encoding_normalize::json_encode(array('status' => $status, 'message' => $message));
    exit;
}

global $empr_pics_folder, $empr_pics_url, $id_empr, $pmb_book_pics_url;

$empr_pics_folder = trim($empr_pics_folder);
$empr_pics_url = trim($empr_pics_url);
if (empty($empr_pics_folder)) {
    response(0, "Parameters not defined !");
}

$id_empr = intval($id_empr);
$query = "SELECT empr_cb FROM empr WHERE id_empr = {$id_empr}";
$result = pmb_mysql_query($query);
if (!pmb_mysql_num_rows($result)) {
    response(0, "Unknown loaner !");
}

$cb = pmb_mysql_result($result, 0, 0);
$upload_filename = str_replace("!!num_carte!!", $cb, $empr_pics_folder);
$upload_url = str_replace("!!num_carte!!", $cb, $empr_pics_url);

// On supprime le fichier mis en cache
$manag_cache = getimage_cache('', '', '', $upload_url, '', $pmb_book_pics_url, 1);
if ($manag_cache["location"]) {
    unlink($manag_cache["location"]);
}

if (empty($_POST['imgBase64'])) {
    response(0, "Not a image !");
}

$filename = substr($upload_filename, strrpos($upload_filename, '/') + 1);
$dir = substr($upload_filename, 0, strrpos($upload_filename, '/'));

try {
    $uploadImage = new UploadImage($dir, $filename);

    $filteredData = explode(',', $_POST['imgBase64']) ?? [];
    $imageString = base64_decode($filteredData[1] ?? "");

    $uploadImage->setImageString($imageString);
} catch (\Exception $e) {
    response(0, $e->getMessage());
}

if ($uploadImage->moveImage()) {
    response(1, "File uploaded !");
}
response(0, "File not uploaded !");
