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
<title>Trombinoscope</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include("./librairie_php/lib_licence.php"); 
include_once('librairie_php/db_triade.php');
if ($_GET["type"] == "TUT") {
	validerequete("3");
}else{
	validerequete("menuadmin");
}
$cnx=cnx();
$okp=0;
if (isset($_POST["create"])) {
	$photo=$_FILES['photo']['name'];
	$type=$_FILES['photo']['type'];
	$tmp_name=$_FILES['photo']['tmp_name'];
	$size=$_FILES['photo']['size'];
	
	$taille = getimagesize($tmp_name);
	//if ( (!empty($photo)) &&  ($size <= 2000000) &&  ($taille[0] <= 96) && ($taille[1] <= 96)   ) {
	if ( (!empty($photo)) &&  ($size <= 2000000)   ) {
		$type=str_replace("image/","",$type);
		$type=str_replace("pjpeg","jpg",$type);
		$type=str_replace("x-png","png",$type);
		$type=str_replace("jpeg","jpg",$type);
		// image/pjpeg ;  image/x-png ; image/gif
		if (verifImageJpg($type))  {
			$nomphoto="$_POST[idpers].$type";
			move_uploaded_file($tmp_name,"data/image_pers/$nomphoto");
			history_cmd($_SESSION["nom"],"PHOTO","AJOUT $nomphoto");
			modif_photo_pers($nomphoto,$_POST["idpers"]);
			$okp=1;
		 }
	} else {
		print "<font class=T1 color=red>".LANGMODIF17."</font>";
	}
}
?>
<form method=post ENCTYPE="multipart/form-data">
<br>
<table border=0 width=100%>
<tr>
<?php
if (isset($_GET["idpers"]))  { $idpers=$_GET["idpers"]; }
if (isset($_POST["idpers"])) { $idpers=$_POST["idpers"]; }
$text1="modifier";
$data=recherche_personne_modif($idpers);
$nom_admin=trim($data[0][1]);
$prenom_admin=trim($data[0][2]);
?>
<td align=center><br>
	<div >
	<div style="position:absolute;top:4px;left:8px;z-index:1000000" ><img src='image/commun/paperclip.png'></div>
	<img src="image_trombi.php?idP=<?php print $idpers?>" >
	</div>
<br><?php
if ($okp) {
?>
[ <a href="#" onclick="window.location.reload(true)"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;font-weight:bold;"><?php print LANGMODIF18 ?> </a> ]
<?php } ?>
</td>
<td width=65%>
<br>
<font class=T2>
<?php print LANGNA1 ?> : <b><?php print ucwords($nom_admin) ?></b> <br><br>
<?php print LANGNA2 ?> : <b><?php print ucwords($prenom_admin) ?></b> <br><br>
</font>
<br>
<tr><td colspan=2 align=center><br> <?php print $text1?> <?php print LANGTRONBI7 ?> : <input type="file" name="photo" size=30 > <br> <?php print "L'image doit &ecirc;tre <b>au format jpg</b>" ?> </td></tr>
<tr><td colspan=2 align=center><br>
<table align=center><tr><td><br>
<script language=JavaScript>buttonMagicSubmit('<?php print LANGBT46?>','create'); //text,nomInput</script> <script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr>
</tr></table>
<input type=hidden name=idpers value="<?php print $_GET["idpers"]?>" >
</form>
<BR>

<?php
Pgclose();
?>
</BODY></HTML>
