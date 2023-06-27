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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php");
if(isset($_POST["supp"])):
	if ($_POST["forcer"] == "oui") {
		$cr=0;
	}else{
		$cr=@verif_utiliser_salle($_POST["saisie_classe_supp"]);
	}
	if (!$cr) {
		$classenom=chercheClasse_nom($_POST["saisie_classe_supp"]);
	        $cr=suppression_salle($_POST["saisie_classe_supp"]) ;
		history_cmd($_SESSION["nom"],"SUPPRESSION","Salle: $classenom");
                alertJs(LANGRESA26 . " --  L'Equipe Triade");
	        reload_page('resr_salle_supp.php');
	}else {
		alertJs(LANGRESA28);
	}
endif;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
     <form method=post onsubmit="return valide_supp_choix('saisie_classe_supp','<?php print LANGRESA27?>')" name="formulaire">
     <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'>
<?php print LANGRESA23?></font></b></td>
     </tr>
     <tr id='cadreCentral0'>
     <td >
     <!-- // fin  -->
     <blockquote><BR>
               <font class='T2'><?php print LANGRESA24?> : </font><select name="saisie_classe_supp">
                                   <option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_salle(); // creation des options
Pgclose();
?>
</select><br>
<br>
<font class='T2'>Forcer la suppression : </font><input type='checkbox' name='forcer' value='oui' />
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGRESA25?>","supp"); //text,nomInput</script> <script language=JavaScript>buttonMagicRetour("resr_admin.php","_parent")</script>&nbsp;&nbsp;
</UL></UL></UL><br><br>
</blockquote>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
