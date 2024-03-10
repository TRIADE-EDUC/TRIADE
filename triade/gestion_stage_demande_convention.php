<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxStage.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Demande de convention de stage" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
?>

<!-- // fin  -->
<br><br>
<form method='post'  name="formulaire_2"  >
<table border=0 cellspacing=0>

<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE74 ?> :</font></td>
<td align=left>
<select name='ident' onchange='checkList(this.value)' >
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_entreprise();
?>
</select>
</td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE76 ?> :</font></td>
<td align=left><input type=text size=30 name='lieu' id='lieu' disabled="disabled" class="bouton2" ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE30 ?> :</font></td>
<td align=left><input type=text size=30 name='ville' id='ville' disabled="disabled"  class="bouton2" ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE29 ?> :</font></td>
<td align=left><input type=text size=15 name='postal' id='postal' disabled="disabled"  class="bouton2" ></td>
</tr>
<tr>
</table>

<br>
<?php
$data=listestagenum(); // numstage,nom_stage
?>

<font class="T2">&nbsp;&nbsp;Stage : </font>
<select name="idstage" >
<option value='' id='select0'><?php print LANGCHOIX ?></option>
<?php 
select_stage_nom($_SESSION["idClasse"]);
?>
</select>
<br>
<br>
<font class="T2">&nbsp;&nbsp;Message : </font> <br><br>
<font class="T2">&nbsp;&nbsp;</font><textarea cols='100' rows='5' name='message' ></textarea>
<br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create");</script>
<br><br>
</form>

<hr><br>

<?php
if (isset($_POST["create"])) {
	$cr=enregistrement_demande_stage($_POST["idstage"],$_POST["ident"],$_POST["message"],$_SESSION["id_pers"]);
	if ($cr) { 
		alertJs("Demande de convention effectuée."); 
		history_cmd($_SESSION["nom"],"DEMANDE","Convention de Stage");
	}else{ 
		alertJs("Demande de convention NON effectuée !! "); 
	}
}

?>

<font class="T2">&nbsp;&nbsp;<b>Demande en cours :</b></font><br><br>
<table border='1' width='100%' bgcolor='#FFFFF' style="border-collapse: collapse;" >
<tr>
<td bgcolor="yellow" width='5%' >&nbsp;Demandé&nbsp;le&nbsp;</td>
<td bgcolor="yellow">&nbsp;Stage&nbsp;</td>
<td bgcolor="yellow"  width='5%' >&nbsp;Etat&nbsp;</td>

</tr>

<?php 
$data=listingDemandeStage($_SESSION["id_pers"]); // id,date_envoi,date_retour,idstage,message,societe,etat
for($i=0;$i<count($data);$i++) {
	print "<tr>";
	print "<td id='bordure'>".dateForm($data[$i][2])."</td>";
	
	$stage=chercheNomStageviaId($data[$i][3]);
	print "<td id='bordure'>".stripslashes($stage)."</td>";


	if ($data[$i][6] == "0") { $etat="<font color='orange'>&nbsp;En&nbsp;attente</font>"; }
	if ($data[$i][6] == "1") { $etat="<font color='red'>A&nbsp;retourner&nbsp;signé</font>"; }
	if ($data[$i][6] == "2") { $etat="<font color='green'>Terminé</font>"; }

	print "<td id='bordure'>".$etat."</td>";
	print "</tr>";
}


?>
</table>

<br><br>
<!-- // fin  -->
</td></tr></table>




<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
