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
<script language="JavaScript" src="./librairie_js/lib_pulldown.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE38 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br><br>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_GET["recherche"])) {
	$recherche=htmlspecialchars($_GET["recherche"]);
}else{
	$recherche=htmlspecialchars($_POST["recherche"]);
}
print "<font class=T2><ul>";

print LANGASS19." : <b> ".stripslashes($recherche)." </b><br><br><br>";
$data=recherche_entreprise_nom($recherche);
// id_serial,nom,contact,adresse,code_p,ville, 5
// secteur_ac,activite_prin,tel,fax,email,info_plus, 11
// bonus,contact_fonction,pays_ent,registrecommerce,siren, 16
// siret,formejuridique,secteureconomique,INSEE, 20 
// NAFAPE,NACE,typeorganisation,qualite 24
	if (count($data) > 0 ) {

		for($i=0;$i<count($data);$i++) {
			if ($data[$i][12] == null) {
				$bonus="";
			}else{
				$bonus=$data[$i][12];
			}
?>

			<table bgcolor="#FFFFFF" border=1 bordercolor="#000000" width=80% >
			<tr><td id='bordure' ><font class="T2">
			Société : <font color=red><?php print $data[$i][1] ?></font> <br> 
			Activité principale :  <?php print  $data[$i][7] ?><br>
			Nbre d'élèves ayant effectué un stage : <b><?php print $bonus ?></b>
			</font>
			<div align=right>[ <a href="#" onclick="slidedown_showHide('box1');return false;"><?php print LANGSTAGE62 ?> +</a> ]&nbsp;&nbsp;&nbsp;</div>
			</td></tr></table>

<div>
	<div id="dhtmlgoodies_control"></div>
	<div style="width:540px;" class="dhtmlgoodies_contentBox" id="box1">
		<div class="dhtmlgoodies_content" id="subBox1">
		<font class=T2>
		<?php print "Registre du commerce" ?> : <b><?php print $data[$i][15] ?></b>  <br><br>
		<?php print "SIREN" ?> : <b><?php print $data[$i][16] ?></b>  <br><br>
		<?php print "SIRET" ?> : <b><?php print $data[$i][17] ?></b>  <br><br>
		<?php print "Forme Juridique" ?> : <b><?php print $data[$i][18] ?></b>  <br><br>
		<?php print "Secteur Economique" ?> : <b><?php print $data[$i][19] ?></b>  <br><br>
		<?php print "INSEE" ?> : <b><?php print $data[$i][20] ?></b>  <br><br>
		<?php print "NAF (APE)" ?> : <b><?php print $data[$i][21] ?></b>  <br><br>
		<?php print "NACE" ?> : <b><?php print $data[$i][22] ?></b>  <br><br>
		<?php print "Type Organisation" ?> : <b><?php print $data[$i][23] ?></b>  <br><br>

		<?php print LANGSTAGE28 ?> : <b><?php print $data[$i][3] ?></b>  <br><br>
		<?php print LANGSTAGE30 ?> : <b><?php print $data[$i][5] ?> </b> <br><br>
		<?php print LANGSTAGE29 ?> : <b><?php print $data[$i][4] ?></b> <br><br>
		<?php print LANGAGENDA73 ?> : <b><?php print $data[$i][14] ?></b> <br><br>
		<?php print LANGSTAGE27 ?> : <b><?php print $data[$i][2] ?> </b>(<?php print $data[$i][13] ?> )<br><br>
		<?php print LANGSTAGE42 ?> : <b><?php print $data[$i][8] ?> / <?php print $data[$i][9] ?></b> <br><br>
		<?php print LANGSTAGE36 ?> : <b><?php print $data[$i][10] ?> </b><br><br>
		<?php print "Qualit&eacute;" ?> : <b><?php print $data[$i][24] ?> </b><br><br>
		<?php print LANGSTAGE37 ?> : <b><?php print $data[$i][11] ?></b> <br><br>
		Plan : <a href="#" onclick="open('https://support.triade-educ.org/support/google-map-V3-triade.php?etablissement=<?php print  urlencode($data[$i][1])?>&adresse=<?php print urlencode($data[$i][3]) ?>&ville=<?php print urlencode($data[$i][5]) ?>&pays=<?php print urlencode($data[$i][14])?>','_blank','width=450,height=350')" /><img src="image/commun/loupe.png" border="0" /></a><br><br><br>
		<hr>
		<font class='T1'>
		<u>Historique des élèves</u> :<br><br>
		<?php
			//identreprise,nomprenomeleve,classeeleve,periodestage
			$datalisting=listingHistorique($data[$i][0]);
			for ($j=0;$j<count($datalisting);$j++) {
				$nomprenom=$datalisting[$j][1];
				$classe=$datalisting[$j][2];
				$periode=$datalisting[$j][3];
				print ucwords($nomprenom)." ($classe) du $periode <br />";
			}
		?>	
		</font>
		<br><br>
 		</font>
		</div>
	</div>
</div>


<script type="text/javascript">
setSlideDownSpeed(4);
</script>
			


<br><br>
<?php
		}
	}else {
		 print "<font class=T2>".LANGSTAGE43.".</font><br><br>";
	}
print "<br>";
print "</font>[<a href='gestion_stage_ent_visu.php'>".LANGSTAGE41."</a>]<br><br> ";
print "</ul>";
?>

</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
