<?php
      session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");	
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<form  method=post onsubmit="return verifcreatesalle()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGRESA20?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php


if(isset($_POST["create"])):
	  $classenom=stripslashes($_POST["saisie_creat_classe"]);
	  $info=stripslashes($_POST["saisie_info"]);
	  $id=$_POST["idsalleequip"];
	  if (!empty($classenom)) {
		if ($id == "") {
	        	$cr=create_salle($classenom,$info);
        		if($cr){
                		alertJs(LANGRESA22. " \\n\\n L'Equipe Triade ");
			}	
		}else{
	        	$cr=modif_salle($classenom,$info,$id);
        		if($cr){
                		alertJs("Salle modifiée \\n\\n L'Equipe Triade ");
			}
		}
	  }
	$info="";   
endif;


$bt=LANGRESA17;
if (isset($_GET["id"])) {
	$data=rechercheInfoSalleEquipement($_GET["id"]); //  id,libelle,info,type
	$libelle=$data[0][1];
	$id=$data[0][0];
	$info=$data[0][2];
	$bt="Modifier la salle";
}

Pgclose();
?>
<!-- // fin  -->
<blockquote><BR>
<font class='T2'><?php print LANGRESA21?> :</font> <input type=text name="saisie_creat_classe" size=30  maxlength=15 value="<?php print $libelle ?>" ><BR>
<BR><bR>
<font class='T2'><?php print LANGRESA18?> :</font> <input type=text name="saisie_info" size=25 value="<?php print $info ?>" ><BR>
<BR><bR>
<script language=JavaScript>buttonMagicSubmit("<?php print $bt ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicRetour("resr_admin.php","_parent")</script>&nbsp;&nbsp;
<br><br>
</blockquote>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
<input type='hidden' name="idsalleequip"  value="<?php print $id ?>" />
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
