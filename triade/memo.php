<?php
session_start();
error_reporting(0);
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
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("./common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="./favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
		<title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom] ") ?></title>
		<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/menu-tab.css">
		<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
		<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
		<script type="text/javascript" src="./librairie_js/function.js"></script>
		<script type="text/javascript" src="./librairie_js/menu-tab.js"></script>
		<script type="text/javascript" src="./librairie_js/ajax-menu-tab.js"></script>
		<script type="text/javascript" src="./librairie_js/prototype.js"></script>
		<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
		<script type="text/javascript" src="./tinymce/tinymce.min.js"></script>
	</head>

	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

	<?php 
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade.php");
	$cnx=cnx();
	?>
	
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "M&eacute;mo / Pense-B&ecirc;te" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td >
	<!-- // fin  -->

<?php
$idpers=$_SESSION["id_pers"];
$fichier="./data/memo/".$_SESSION['membre']."_$idpers";
if (!is_dir(".data/memo")) mkdir("./data/memo");
if (file_exists($fichier)) {
	$fic=fopen("$fichier","r");
	$contenu=fread($fic,filesize("$fichier"));
	fclose($fic);
}

if (isset($_POST['create'])) {
	@unlink($fichier);
	$contenu=$_POST['memo'];
	$contenu=stripslashes($contenu);
	$contenu=preg_replace('/\\\r\\\n/',"",$contenu);
	$contenu=stripslashes($contenu);

	$fp = fopen($fichier,"w");
        fwrite($fp,$contenu);
        fclose($fp);	
}

if (trim($contenu) == "") {
	$contenu="Enregistrer vos m&eacute;mos ici !!! ";
}

?>

<form method=post name="form" >
<script>
tinymce.init({
	selector: 'textarea#default-editor',
  	plugins: 'image emoticons link table',
	toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | forecolor backcolor',
	color_cols:5,
	menubar: 'file edit view insert format tools table help',
  	height: 500,
	protect: [
        /\<\/?(if|endif)\>/g,
        /\<xsl\:[^>]+\>/g,
        /<\?php.*?\?>/g,
        /\<script/ig,
        /<\?.*\?>/g
        ]
});
</script>


<textarea id="default-editor" name='memo'  >
<?php print $contenu ?>
</textarea>
<br><br>
<table align='center' ><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create"); //text,nomInput</script></td></tr></table>
</form>

	<?php
	print "</td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
		print "</SCRIPT>";
	}
	Pgclose();
?>
</BODY></HTML>
