<?php
$img = $_POST["nb"];  // ex: "./image/Memoji-01.png" ou "./tchat/php/images/menuprof_photo.jpg"
$id  = $_POST["id"];
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();

$imgBasename = basename($img);

// Si le fichier source n'est pas déjà dans tchat/php/images/, le copier
if (strpos($img, 'tchat/php/images') === false && file_exists($img)) {
    copy($img, "./tchat/php/images/".$imgBasename);
}

updateImageIntraMsn($imgBasename, $id);
?>
