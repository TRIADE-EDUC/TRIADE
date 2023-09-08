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
<script language="JavaScript" src="./librairie_js/lib_pulldown.js"></script>
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
validerequete("2");
?>

<!-- // fin  -->
<br>
<form method="post" name="form" action="gestion_stage_demande_convention_dir.php">
<font class="T2">&nbsp;&nbsp;<b>Afficher les :</b></font> 
<select name=filtre onchange="document.form.submit()" >
<?php 
if (isset($_POST["filtre"])) {
	$filtre=$_POST["filtre"];
}

if (isset($_GET["filtre"])) {
	$filtre=$_GET["filtre"];
}

if ($filtre == "") { $filtre=0; }

if ($filtre == 0) { print"<option value='0' id='select0' >en attente d'envoi de la convention</option>"; $menu="&nbsp;Demandé&nbsp;le&nbsp;"; }
if ($filtre == 1) { print"<option value='1' id='select0' >en attente de retour de la convention</option>"; $menu="&nbsp;Retour&nbsp;le&nbsp;"; }
if ($filtre == 2) { print"<option value='2' id='select0' >terminées</option>"; $menu="&nbsp;Terminée&nbsp;le&nbsp;"; }

if (isset($_GET["act"])) { 
	updateDemandeStageDir($_GET["id"],$_GET["act"]);
	alertJs("Donnée modifiée.");

}




?>

<option value="0" id='select1' >en attente d'envoi de la convention</option>
<option value="1" id='select1' >en attente de retour de la convention</option>
<option value="2" id='select1' >terminées</option>
</select>
</form>

<br><br>
<table border='1' width='100%' bgcolor='#FFFFF'>
<tr>
<td bgcolor="yellow" width='5%' ><?php print $menu ?></td>
<td bgcolor="yellow">&nbsp;Stage&nbsp;</td>
<td bgcolor="yellow"  width='5%' align='center' >&nbsp;Etat&nbsp;</td>

</tr>

<?php 
$data=listingDemandeStageDir($filtre); // id,date_envoi,date_retour,idstage,message,societe,etat,idpers
for($i=0;$i<count($data);$i++) {
	print "<tr>";
	print "<td id='bordure'>".dateForm($data[$i][1])."</td>";
	
	$stage=chercheNomStageviaId($data[$i][3]);
	print "<td id='bordure'>".$stage."</td>";


 	$etat="<img src='image/commun/loupe.png' border='0'/>"; 


	print "<td id='bordure' align='center'><a  href='#' onclick=\"slidedown_showHide('box$i');return false;\"> ".$etat."</a></td>";
	print "</tr>";

	$ideleve=$data[$i][7];

	$info=recupInfoEntreprise($data[$i][5]);
	//id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent

?>
	<tr><td colspan=3><div id="dhtmlgoodies_control"></div>
		<div style="width:410px;" class="dhtmlgoodies_contentBox" id="box<?php print $i ?>">
			<div class="dhtmlgoodies_content" id="subBox<?php print $i ?>">
			<font class=T2>
			Elève : <b><?php print rechercheEleveNomPrenom($ideleve); ?></b></font><br>
			<font class=T1><?php print "Société" ?> : <u><?php print $info[0][1] ?></u>  <br>
			<?php print LANGSTAGE28 ?> : <?php print $info[0][3] ?> <br>
			<?php print LANGSTAGE30 ?> : <?php print $info[0][5] ?> (<?php print $info[0][4] ?>)  -  <?php print $info[0][14] ?> <br>
			<?php print LANGSTAGE27 ?> : <?php print $info[0][2] ?> (<?php print $info[0][13] ?> )<br>
			<?php print LANGSTAGE42 ?> : <?php print $info[0][8] ?> / <?php print $info[0][9] ?></b> <br>
			<?php print LANGSTAGE36 ?> : <?php print $info[0][10] ?><br>
			<?php print LANGSTAGE37 ?> : <?php print $info[0][11] ?> <br>
			Plan : <a href="#" onclick="open('https://support.triade-educ.org/support/google-map-triade.php?etablissement=<?php print  urlencode($info[0][1])?>&adresse=<?php print urlencode($info[0][3]) ?>&ville=<?php print urlencode($info[0][5]) ?>&pays=<?php print urlencode($info[0][14])?>','_blank','width=450,height=350')" /><img src="image/commun/loupe.png" border="0" /></a><br>
			</font>
			<hr>
			<font class=T2>Commentaire : <?php print $data[$i][4] ?></font>
			<hr>
			<?php if ($data[$i][6] == 0 ) { ?>
				<font class=T2>Valider l'envoi de la convention : </font> 
				<input type=button name='fichier_conv' class="button" value="<?php print VALIDER ?>"  onclick="open('gestion_stage_demande_convention_dir.php?id=<?php print $data[$i][0]?>&filtre=<?php print $filtre ?>&act=1','_parent','')" />
			<?php } ?>
			<?php if ($data[$i][6] == 1 ) { ?>
				<font class=T2>Valider le retour de la convention : </font> 
				<input type=button name='fichier_conv' class="button" value="<?php print VALIDER ?>"  onclick="open('gestion_stage_demande_convention_dir.php?id=<?php print $data[$i][0]?>&filtre=<?php print $filtre ?>&act=2','_parent','')" />
			<?php } ?>
			<br><br>
		</div>
	</div>
	</td></tr>
<?php
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
