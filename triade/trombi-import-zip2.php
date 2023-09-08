<?php
      session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php");
include_once('librairie_php/db_triade.php');
?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Import photo" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<?php


$fichier=$_FILES['fichier']['name'];
$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];
//alertJs($type);
$type_compte=$_POST["type_compte"];
$type_nommage=$_POST["type_nommage"]; // nomprenom ou nomprenom

$cnx=cnx();

$erreur_fichier="oui";

if ((!empty($fichier)) && ($size <= 20000000)) {
	$type=strtolower($type);
	if ((trim($type) == "application/x-zip-compressed") || (trim($type) == "application/octet-stream") || (trim($type) == "application/x-download")  || (trim($type) == "application/x-stuffit")  ||  (trim($type) == "application/zip") || (preg_match('/zip/i',$type)) || (trim($type) == "application/force-download") ) {
		$erreur_fichier="non";

		if (file_exists("./data/patch/importphoto.zip")) { unlink("./data/patch/importphoto.zip"); }
		if (!is_dir("./data/patch")) { mkdir("./data/patch"); }
		move_uploaded_file($tmp_name,"importphoto.zip");

		include_once('./librairie_php/pclzip.lib.php');
		$archive = new PclZip('importphoto.zip');
		
		nettoyage_repertoire("./data/tmp/".$_SESSION['id_pers']."/photos");
		@rmdir("./data/tmp/".$_SESSION['id_pers']."/photos");
	
		$repdest="./data/tmp/".$_SESSION['id_pers'];
		if (!is_dir("$repdest")) { mkdir("$repdest"); }
		if ($archive->extract(PCLZIP_OPT_PATH, "$repdest") == 0) {
		die(print "<center><a href='javascript:history.go(0)'><b>Cliquez ici pour réactualiser l'import photo.</a></b></center>"); }

		$repdir="$repdest/photos";
		$h=opendir($repdir);
		$nb=0;
		while($file=readdir($h)){
			if($file!="." && $file!=".." ){
				$nomFichier=preg_replace("/ /","",$file);
				$nomFichier=preg_replace("/\./","",$nomFichier);
				$nomFichier=preg_replace("/jpg/","",strtolower($nomFichier));
				$nomFichier=strtolower($nomFichier);
				if ($type_compte == "personnel") {
					$data=cherchePersonnelPhotoId(); // pers_id,nom,prenom
					for($i=0;$i<count($data);$i++) { 
						$idPers=$data[$i][0];			
						if ($type_nommage == 'nomprenom') { $nomprenom=strtolower($data[$i][1]).strtolower($data[$i][2]); }
						if ($type_nommage == 'prenomnom') { $nomprenom=strtolower($data[$i][2]).strtolower($data[$i][1]); }
						if ($type_nommage == 'nompointprenom') { $nomprenom=strtolower($data[$i][1]).strtolower($data[$i][2]); }
						if ($type_nommage == 'prenompointnom') { $nomprenom=strtolower($data[$i][2]).strtolower($data[$i][1]); }
						$nomPers=$data[$i][1];
						$nomprenom=TextNoAccent($nomprenom);
						$nomFichier=TextNoAccent($nomFichier);
						$nomprenom=preg_replace('/ /',"",$nomprenom);
						$nomprenom=preg_replace('/\./',"",$nomprenom);
						if ($nomprenom == $nomFichier) {
							copy("$repdir/$file","./data/image_pers/$idPers.jpg");
							$cr=modif_photo_pers("$idPers.jpg",$idPers);
							if ($cr) { $nb++; history_cmd($_SESSION["nom"],"PHOTO","AJOUT $nomPers"); }
							continue ;
						}
					}
				}
		
	
				if ($type_compte == "eleves") {
					$data=chercheElevePhotoId(); // elev_id,nom,prenom
					for($i=0;$i<count($data);$i++) { 
						$idPers=$data[$i][0];			
						if ($type_nommage == 'nomprenom') { $nomprenom=strtolower($data[$i][1]).strtolower($data[$i][2]); }
						if ($type_nommage == 'prenomnom') { $nomprenom=strtolower($data[$i][2]).strtolower($data[$i][1]); }
						if ($type_nommage == 'nompointprenom') { $nomprenom=strtolower($data[$i][1]).strtolower($data[$i][2]); }
						if ($type_nommage == 'prenompointnom') { $nomprenom=strtolower($data[$i][2]).strtolower($data[$i][1]); }
						$nomPers=$data[$i][1];
						$nomprenom=TextNoAccent($nomprenom);
						$nomFichier=TextNoAccent($nomFichier);
						$nomprenom=preg_replace('/ /',"",$nomprenom);
						$nomprenom=preg_replace('/\./',"",$nomprenom);
						if ($nomprenom == $nomFichier) {
							copy("$repdir/$file","./data/image_eleve/$idPers.jpg");
							$cr=modif_photo("$idPers.jpg",$idPers);
							if ($cr) { $nb++; history_cmd($_SESSION["nom"],"PHOTO","AJOUT $nomPers"); }
							continue ;
						}
					}
				}

			}
		}
		closedir($h);
		

		nettoyage_repertoire("$repdir");
		@rmdir("$repdir");
		nettoyage_repertoire("$repdes");
		@rmdir("$repdes");
		@unlink("importphoto.zip");

	} /// fin du if size et empty

	print "<center><font class=T2>Il y a $nb photo(s) d'enregistrée(s) </font></center>";

}else{
	print "<font class=T2><center>Fichier non conforme.</center></font>";
}

?>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;	
     ?>
</BODY></HTML>
