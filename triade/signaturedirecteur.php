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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Logo Etablissement </title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
$id=$_GET["id"];
$idsite=$_GET["id"];
if (isset($_GET["supp"])) {
	$idsite=$_POST["idsite"];
	$photo=recup_photo_signature_idsite($idsite);
	if (count($photo) > 0) { 
		supp_photo_bulletin($idsite);
		@unlink("./data/image_pers/".$photo[0][0]);
	}
}

if (isset($_POST["create"])) {
	$idsite=$_POST["idsite"];
	if ($idsite == 1) $idsite="";
	$photo=recup_photo_signature_idsite($idsite);
	if (count($photo) > 0) { 
		supp_photo_signature($idsite);
		@unlink("./data/image_pers/".$photo[0][0]);
	}
	$photo=$_FILES['photo']['name'];
	$type=$_FILES['photo']['type'];
	$tmp_name=$_FILES['photo']['tmp_name'];
	$size=$_FILES['photo']['size'];

    $taille = getimagesize($tmp_name);
    //if ( (!empty($photo)) &&  ($size <= 2000000) &&  ($taille[0] <= 96) && ($taille[1] <= 96)  ) {
    if ( (!empty($photo)) &&  ($size <= 2000000)) {
	    	$type=str_replace("image/","",$type);
		$type=str_replace("jpeg","jpg",$type);
		$type=str_replace("pjpg","jpg",$type);
		$type=str_replace("x-png","png",$type);
		if (verifImageJpg($type))  {
			$nomphoto="logo_signature$idsite.".$type;
			move_uploaded_file($tmp_name,"data/image_pers/$nomphoto");
			enr_photo_signature($nomphoto,$idsite);
			history_cmd($_SESSION["nom"],"PHOTO","AJOUT Signature");
			print "<script>alert(\"Photo Enregistré \\n\\n L'Equipe Triade\");parent.window.close();</script>";
		 }else{
			print "<font class=T1 color=red><center>".LANGALERT2."</center></font>";
		 }
	} else {
		print "<font class=T1 color=red><center>".LANGALERT3."</center></font>";
	}
}
?>
<form method=post ENCTYPE="multipart/form-data">
<table border=0 width=100%>
<tr>
<?php
	if ($idsite == 1) $idsite="";	
	$photo=recup_photo_signature_idsite($idsite);
	if ((count($photo) > 0 ) && (file_exists("./data/image_pers/logo_signature$idsite.jpg"))) {
		if (file_exists("./data/image_pers/".$photo[0][0])) {
			$logo="<img src='image.php?id=./data/image_pers/".$photo[0][0]."' width='96' height='96' ><br>[<a href='signaturedirecteur.php?supp=logo'>".LANGacce21."</a>]";
			$alert="<div style=\"position:absolute;top:10;left:140;color:red;z-index:1;width:200;height:30 \" ><i>Information : Image réduite </i></div>";
		}
	}else {
		$logo="";
	}
?>
<td align=center><?php print $logo?></td>
<td width=65% align=left><br><?php print "Signature à transmettre" ?> :<br> <input type="file" name="photo" size=30 ><?php print $alert ?>
<?php print "<br>le signature doit être de largeur de 4 cm <br>et la hauteur de 4 cm" ?>
</td></tr>
<tr><td colspan=2 align=center><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit('<?php print VALIDER ?>','create'); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>&nbsp;&nbsp;
</td></tr></table>
<?php print "Le fichier <b>doit être au format jpg</b>" ?>
<input type='hidden' name='idsite' value='<?php print $id ?>' />
</form>
</BODY></HTML>



