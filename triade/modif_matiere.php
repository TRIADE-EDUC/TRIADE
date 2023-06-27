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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return verifcreatmatiere()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE13?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td ><BR>
<!-- // fin  -->
&nbsp;&nbsp;
<?php 
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
// connexion P
$cnx=cnx();

$id=$_GET["id"];

if (isset($_POST["offline"])) {
	modif_matiere_actif_desactif($_POST["saisie_id_matiere"],"1"); 
	history_cmd($_SESSION["nom"],"DESACTIVE"," de ".chercheMatiereNom($_POST["saisie_id_matiere"]));
	$id=$_POST["saisie_id_matiere"];
}
if (isset($_POST["online"])) {
	modif_matiere_actif_desactif($_POST["saisie_id_matiere"],"0"); 
	history_cmd($_SESSION["nom"],"ACTIVE"," de ".chercheMatiereNom($_POST["saisie_id_matiere"]));
	$id=$_POST["saisie_id_matiere"];
}

if (isset($_GET["suppsous"])) {
	suppSousMatiere($_GET["suppsous"]);
	alertJs(LANGDONENR);
	$id=$_GET["suppsous"];
}

$matiere=trim(chercheMatiereNom2($id));
$sous_matiere=trim(chercheSousMatiereNom($id));
$matiereLong=trim(chercheMatiereLong($id));
$code_matiere=trim(chercheCodeMatiere($id));
$matiereEn=trim(chercheMatiereEn($id));


$offline=etatOfflineMatiere($id);


if(isset($_POST["modif"])):
	include_once("librairie_php/db_triade.php");
	validerequete("menuadmin");
	// creation
	$matiere=$_POST["saisie_creat_matiere"];
	$sous_matiere=$_POST["sous_matiere"];
	$id=$_POST["saisie_id_matiere"];
	$matiereLong=$_POST["saisie_creat_matiere_long"];
	$code_matiere=$_POST["saisie_code_matiere"];
	$saisie_creat_matiere_en=$_POST["saisie_creat_matiere_en"];
	$cr=modif_matiere($_POST["saisie_creat_matiere"],$_POST["saisie_id_matiere"],$sous_matiere,$_POST["saisie_creat_matiere_long"],$code_matiere,$saisie_creat_matiere_en);
        if($cr):
	       alertJs(LANGMAT5);
		$matiere=$_POST["saisie_creat_matiere"];
               history_cmd($_SESSION["nom"],"MODIFICATION","matière $matiere $sous_matiere");
        else:
               alertJs(LANGMAT6); 
	endif;
endif;

Pgclose();
?>

<font class=T2><?php print LANGGRP9?> : <input type=text name="saisie_creat_matiere" size=20 maxlength='200' value="<?php print html_quotes(stripslashes($matiere)) ?>"></font> <font class='T1'><i>Format court</i></font><BR>
<br>
&nbsp;&nbsp;&nbsp;<font class=T2><?php print LANGGRP9?> : <input type=text name="saisie_creat_matiere_en" size=20 maxlength='200' value="<?php print html_quotes(stripslashes($matiereEn)) ?>" /></font> <font class='T1'><i><?php print LANGTMESS450 ?></i></font><br>
<br>
&nbsp;&nbsp;&nbsp;<font class=T2><?php print LANGGRP9?> : <input type=text name="saisie_creat_matiere_long" size=40 maxlength='200'  value="<?php print html_quotes(stripslashes($matiereLong)) ?>" /></font> <font class='T1'><i><?php print LANGMESS209 ?>.</i></font><br>
<br>
&nbsp;&nbsp;
<font class=T2><?php print LANGMESS210 ?> :</font> <input type=text name="saisie_code_matiere" size=20 maxlength='20' value="<?php print html_quotes(stripslashes($code_matiere)) ?>" ><BR>
<BR><bR>

&nbsp;&nbsp;<?php print LANGMESS416 ?> : </font><input type=text name="sous_matiere" value="<?php print html_quotes(stripslashes($sous_matiere)) ?>">
[ <a href="modif_matiere.php?suppsous=<?php print $id ?>"><?php print LANGMESS417 ?></a> ]
<BR><bR>
<input type=hidden name="saisie_id_matiere" value="<?php print $id ?>">
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMAT4 ?>","modif"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print LANGMAT2 ?>","list_matiere.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGMAT3 ?>","suppression_matiere.php","_parent","","");</script>
<?php if ($offline == 0) { ?>
	<br><br><script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS418 ?>","offline"); //text,nomInput</script>
<?php } else {?>
	<br><br><script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS419 ?>","online"); //text,nomInput</script>
<?php } ?>
<br><br>
<br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
