<?php
session_start();
$taille=2000000;
if ($_SESSION["membre"] == "menuadmin") {
	$fichier=$_FILES['Filedata']['name'];
        $titre=$_POST["saisie_titre"];
        $type=$_FILES['Filedata']['type'];
        $tmp_name=$_FILES['Filedata']['tmp_name'];
        $size=$_FILES['Filedata']['size'];
        if ( (!empty($fichier)) &&  ($size <= $taille) && (($type=="audio/mpeg") || ($type=="audio/x-mpeg")) ) {
                // supprimer l'ancien
                $fichier="actu.mp3";
                $f=fopen("./data/parametrage/audio.txt","r");
                $donnee=fread($f,900000);
                $tab=explode("#||#",$donnee);
                fclose($f);
                @unlink("./data/parametrage/audio.txt");
                @unlink("./data/audio/actu.mp3");
                // nouveau
                move_uploaded_file($tmp_name,"./data/audio/actu.mp3");
	}
}
?>
