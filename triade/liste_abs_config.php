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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript">
_editor_url = "./HTMLArea";
_editor_lang = "en";
</script>
<script type="text/javascript" src="./HTMLArea/htmlarea-pdf.js"></script>
<script type="text/javascript">
HTMLArea.loadPlugin("ContextMenu");
HTMLArea.loadPlugin("TableOperations");
function initDocument() {
  var editor = new HTMLArea("editor");
  editor.registerPlugin(ContextMenu);
  //editor.registerPlugin(TableOperations);
  editor.generate();
}
</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="initDocument()" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
error($cnx);
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE35 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br>

<?php
if (isset($_POST["create"])) {
	if ($_FILES['rtf']['name'] != "") {
		$fichier=$_FILES['rtf']['name'];
		$type=$_FILES['rtf']['type'];
		$tmp_name=$_FILES['rtf']['tmp_name'];
		$size=$_FILES['rtf']['size'];
		//alertJs($type);
		if ( (!empty($fichier)) &&  ($size <= 8000000)) {
			if  (($type == "application/msword") || (preg_match('/\.rtf$/',$fichier) ))  {
				@unlink("./data/parametrage/courrier_abs.rtf");
				move_uploaded_file($tmp_name,"./data/parametrage/courrier_abs.rtf");
				print "<BR><center><font class='T2'>"."Fichier enregistré"."</font></center>";
				config_param_ajout("{FICHIERRTFABS}",'param_abs');
				$texte="{FICHIERRTFABS}";
				
   			}
		} 
	}else{
		$texte=$_POST["suite"];
		$texte=preg_replace('/\\\"/','"',$texte);
		$texte=preg_replace("/\\\'/","'",$texte);
		//--------------------
		config_param_ajout($_POST["suite"],'param_abs');
	}

?>
<font class=T2><b><?php print LANGCONFIG1 ?></b></font>
<br><br>
<font class=T1><?php print LANGCONFIG2 ?></font>:<br><br>
<table width=100% height=200 bgcolor="#FFFFFF" border=1 cellpadding="5" >
<tr><td valign=top>
<?php 
if ($texte != "{FICHIERRTFABS}") { 
	print $texte ;
}else{
	print "<i>Fichier 'rtf' comme matrice de donnée</i>";
}
?>
</td></tr></table>
<br><br>
<script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>
<br><br><br>
<?php
}else{
	$data=config_param_visu('param_abs');
	print LANGPARAM1;
	if (trim($data[0][0]) != "{FICHIERRTFABS}") {
		$textes=$data[0][0];
	}else{
		$textes="Fichier 'rtf' comme matrice de donnée";
	}
?>
<form method=post ENCTYPE="multipart/form-data">
<textarea id="editor" style="height: 48em; width:695" name=suite><?php print $textes ?>
</textarea><br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT19?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>
<input type="file" name="rtf" /> (Fichier matrice au format "rtf")
</form>
<br><br>
<?php } ?>
</td></tr></table>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
