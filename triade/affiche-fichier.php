<?php
session_start();
if (empty($_SESSION["nom"]))  {
        header('Location: ./acces_refuse.php');
        exit;
}

$id_pers_ss=$_SESSION['id_pers'];
$id_pers=$_GET['idpers'];
if ($id_pers_ss != $id_pers) exit;

    
if(file_exists($_GET["fichier"])){
    // Store the file name into variable
    $file = $_GET["fichier"];
    $filename = $_GET["fichier"];
   // $fileneme = = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename));
    
    $filename2 = stripslashes(basename($file));
    switch(strrchr(basename($filename2), ".")) {
        case ".pdf": $type = "application/pdf"; break;
	case ".odt": $type = "application/vnd.oasis.opendocument.text"; break;
	case ".odp": $type = "application/vnd.oasis.opendocument.presentation"; break;
	case ".ods": $type = "application/vnd.oasis.opendocument.spreadsheet"; break;
	case ".php": exit; break;
	default: $type = "application/octet-stream"; break;
    }
    // Header content type
    header('Content-type: '.$type);
    header('Content-Disposition: inline;
    filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // Read the file
    @readfile($file);

}else{
    // Store the file name into variable
    $file = "./ViewerJS/erreur_diapo.pdf";
    $filename = "./ViewerJS/erreur_diapo.pdf";
    
    // Header content type
    header('Content-type: application/pdf');
    header('Content-Disposition: inline;
    filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // Read the file
    @readfile($file);
}
?>
