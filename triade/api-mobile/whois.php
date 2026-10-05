<?php 

/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 */


header('Content-Type: application/json; charset=utf-8');

include_once("../common/version.php");
include_once("../common/productId.php");
include_once("../common/lib_patch.php");
$version=VERSION;
$productId=PRODUCTID;
$release=VERSIONPATCH;

$info = [
    "logiciel" => "TRIADE",
    "version" => "$version",
    "revision" => "$release",
    "productID" => "$productId"
];

echo json_encode($info);

?>
