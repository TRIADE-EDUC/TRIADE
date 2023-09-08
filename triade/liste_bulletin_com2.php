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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title>Triade Bulletin</title>
<script language="JavaScript" src="./librairie_js/lib_verif_message.js"></script>
<script type="text/javascript" src="./FCKeditor/fckeditor.js"></script>
<script type="text/javascript">
window.onload = function()
{
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
	//var sBasePath = document.location.pathname.substring(0,document.location.pathname.lastIndexOf('_samples')) ;
	<?
	include_once("./common/config2.inc.php");
	if ((UPLOADIMG == "oui") && ($_SESSION["membre"] == "menuparent") ){
		$typedefen="messagerie_img_non";
	}else {
		$typedefen="messagerie";
	}
	?>

	var oFCKeditor = new FCKeditor('resultat','99%','480','<?php print $typedefen?>','') ;
	//oFCKeditor.Config['CustomConfigurationsPath'] = './fckeditor/myconfig.js'
	oFCKeditor.BasePath = './FCKeditor/' ;
	oFCKeditor.ReplaceTextarea() ;
}
</script>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
$cnx=cnx();


	print "<form method=post action='liste_bulletin_com3.php' ><br />";
	for($i=0;$i<=$_POST["nbprof"];$i++) {
		$idprof=$_POST["idprof"][$i];
	        if (trim($idprof) != "") {	
			$tabid[$idprof]=$idprof;
		}
	}

	foreach ($tabid as $val) {
		$idprof.="$val:";
	}
	print "<ul><font class=T2>Objet :  <input type=text name='saisie_objet' size=50> </font></ul>";
	?>
	<BR>
	<textarea name="resultat" id="editor"></textarea>
	<input type=hidden name=saisie_type_personne_dest value="ENS" >
	<input type=hidden name=saisie_classe value="<?php print $_POST["saisie_classe"]?>" >
	<input type=hidden name=saisie_destinataire value="<?php print $idprof ?>" ><br />
	<script language=JavaScript>buttonMagicRetour("editer_bulletin.php","_parent")</script>
	<script language=JavaScript>buttonMagicSubmit2('<?php print LANGBT4?>','rien','<?php print LANGBT5?>'); //text,nomInput</script>&nbsp;&nbsp;
	</form>

<?php Pgclose(); ?>
</body>
</html>
