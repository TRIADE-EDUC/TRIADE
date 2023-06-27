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
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
if (($_SESSION["membre"] == "menueleve") && (MODIFTROMBIELEVE == "oui" )) {
	$_GET['idelevesupp']=$_SESSION["id_pers"];
}else{
	validerequete("3");
}	
$cnx=cnx();

if (isset($_GET["idelevesupp"])) {
	$photoLocal=recherche_photo_eleve($_GET["idelevesupp"]);
	@unlink("./data/image_eleve/$photoLocal");
	$nomeleve=recherche_eleve_nom($_GET["idelevesupp"]);
	$prenomeleve=recherche_eleve_prenom($_GET["idelevesupp"]);
	history_cmd($_SESSION["nom"],"PHOTO","SUPPRESSION $nomeleve $prenomeleve");
}

if (isset($_POST["create"])) {
	$photo=$_FILES['photo']['name'];
	$type=$_FILES['photo']['type'];
	$tmp_name=$_FILES['photo']['tmp_name'];
	$size=$_FILES['photo']['size'];

	$taille = getimagesize($tmp_name);
	//if ((!empty($photo)) &&  ($size <= 2000000) &&  ($taille[0] <= 96) && ($taille[1] <= 96)   ) {
	if ((!empty($photo)) &&  ($size <= 2000000)) {
		$type=str_replace("image/","",$type);
		$type=str_replace("pjpeg","jpg",$type);
		$type=str_replace("x-png","png",$type);
		$type=str_replace("jpeg","jpg",$type);
		if (verifImageJpg($type))  {
			$nomphoto="$_POST[ideleve].$type";
			move_uploaded_file($tmp_name,"data/image_eleve/$nomphoto");
			history_cmd($_SESSION["nom"],"PHOTO","AJOUT $nomphoto");
			modif_photo($nomphoto,$_POST["ideleve"]);
			print "<script>alert(\"Photo Enregistrée \\n\\n L'Equipe Triade\"); parent.window.close();</script>";
		 }else{
			print "<font class=T1 color=red>".LANGTRONBI3."</font>";
		 }
	} else {
		print "<font class=T1 color=red>".LANGTRONBI4."</font>";
	}
}
?>
<form method='post' ENCTYPE="multipart/form-data" action="photoajouteleve.php">
<table border=0 width=100%>
<tr>
<?php
	
	if (isset($_GET["idelevesupp"]))  { $ideleve=$_GET["idelevesupp"]; }
	if (isset($_GET["ideleve"]))  { $ideleve=$_GET["ideleve"]; }
	if (isset($_POST["ideleve"])) { $ideleve=$_POST["ideleve"]; }
	$text1="modifier";
	
?>
<td align=center>
	<br>
	<div >
	<div style="position:absolute;top:-1px;left:8px;z-index:1000000" ><img src='image/commun/paperclip.png'></div>
	<img src="image_trombi.php?idE=<?php print $ideleve?>"  width=96 height=96 />
	</div>
</td>
<td width=65%>
<br>
<font class=T2>
<?php print LANGTRONBI5 ?> : <b><?php print recherche_eleve_nom($ideleve); ?></b> <br><br>
<?php print LANGTRONBI6?> : <b><?php print recherche_eleve_prenom($ideleve); ?></b> <br><br>
<?php print LANGELE4 ?> : <b><?php $idclasse=chercheIdClasseDunEleve($ideleve); print chercheClasse_nom($idclasse); ?></b> <br><br>
</font>
<br>
<tr><td colspan=2 align=center><br> <?php print $text1?> <?php print LANGTRONBI7 ?> : <input type="file" name="photo" size=30 > <br> <?php print "L'image doit être <b>au format jpg</b><br>Recommandé 96px sur 96px  de dimension"?> </td></tr>
<tr><td colspan=2 align=center><br>
<table align=center><tr><td><br>
<script language=JavaScript>buttonMagicSubmit('<?php print LANGBT46?>','create'); //text,nomInput</script> <script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr>
</tr></table>
<input type='hidden' name='ideleve' value="<?php print $ideleve ?>" />
</form>
<BR>
<?php
Pgclose();
?>
</BODY></HTML>
