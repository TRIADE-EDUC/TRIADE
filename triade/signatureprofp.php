<?php
session_start();
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
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
validerequete("menuprof");
$cnx=cnx();
$idclasse=$_GET["sClasseGrp"];



verif_profp_class($_SESSION["id_pers"],$idclasse);



if (isset($_GET["supp"])) {
	$idclasse=$_GET["sClasseGrp"];
	@unlink("./data/signature/profp/$idclasse/signature.jpg");
}


if (isset($_POST["create"])) {

	$photo=$_FILES['photo']['name'];
	$type=$_FILES['photo']['type'];
	$tmp_name=$_FILES['photo']['tmp_name'];
	$size=$_FILES['photo']['size'];
	$idclasse=$_POST["idclasse"];

    $taille = getimagesize($tmp_name);
    if ( (!empty($photo)) &&  ($size <= 2000000)) {
	    	$type=str_replace("image/","",$type);
		$type=str_replace("jpeg","jpg",$type);
		$type=str_replace("pjpg","jpg",$type);
		$type=str_replace("x-png","png",$type);
		if (verifImageJpg($type))  {
			$nomphoto="signature.jpg";
			if (!is_dir("data/signature")) mkdir("data/signature");
			if (!is_dir("data/signature/profp")) mkdir("data/signature/profp");
			if (!is_dir("data/signature/profp/$idclasse")) mkdir("data/signature/profp/$idclasse");
			move_uploaded_file($tmp_name,"data/signature/profp/$idclasse/$nomphoto");
			history_cmd($_SESSION["nom"],"PHOTO","AJOUT Signature ProfP");
			print "<script>alert(\"Signature Enregistrée \\n\\n L'Equipe Triade\");parent.window.close();</script>";
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
	if (file_exists("./data/signature/profp/$idclasse/signature.jpg")) {
		$logo="<img src='image.php?id=./data/signature/profp/$idclasse/signature.jpg' width='96' height='96' ><br>[<a href='signatureprofp.php?supp=logo&sClasseGrp=$idclasse'>".LANGacce21."</a>]";
		$alert="<div style=\"position:absolute;top:10;left:140;color:red;z-index:1;width:200;height:30 \" ><i>Information : Image réduite </i></div>";
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
<input type='hidden' name='idclasse' value='<?php print $idclasse ?>' />
</form>
</BODY></HTML>



