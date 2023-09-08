<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
}
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
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
$cnx=cnx();



if (isset($_POST["activite"])) { $activite=$_POST["activite"]; }
if (isset($_POST["departement"])) { 
	$departement=$_POST["departement"];
	$departement=preg_replace("/\\*/","%",$departement); 
}


if (isset($_POST["ville"])) { $ville=$_POST["ville"]; $ville=preg_replace("/\\*/","%",$ville);  }
if (isset($_POST["secteureconomique"])) { $secteureconomique=$_POST["secteureconomique"]; $secteureconomique=preg_replace("/\\*/","%",$secteureconomique);  }
if (isset($_POST["siren"])) { $siren=$_POST["siren"]; $siren=preg_replace("/\\*/","%",$siren);  }
if (isset($_POST["formejuridique"])) { $formejuridique=$_POST["formejuridique"]; $formejuridique=preg_replace("/\\*/","%",$formejuridique);  }
if (isset($_POST["typeorganisation"])) { $typeorganisation=$_POST["typeorganisation"]; $typeorganisation=preg_replace("/\\*/","%",$typeorganisation);  }
if (isset($_POST["NAFAPE"])) { $NAFAPE=$_POST["NAFAPE"]; $NAFAPE=preg_replace("/\\*/","%",$NAFAPE);  }
if (isset($_POST["NACE"])) { $NACE=$_POST["NACE"]; $NACE=preg_replace("/\\*/","%",$NACE);  }


if (isset($_GET["id"])) { $activite=$_GET["id"]; }

$fichier="gestion_stage_ent_visu_rech.php";
$table="stage_entreprise";
$champs="secteur_ac";
$iddest=$activite;
$nbaff=6;

if (isset($_GET["nba"])) {
	$depart=$_GET["limit"];
	$departement=$_GET["departement"];
	$departement=preg_replace("/\\*/","%",$departement);
}else {
	$depart=0;
}

$activitename=($activite == -1) ? "Tous les secteurs" : $activite;

//if ($departement == "") { $departement='%'; }

print "<font class=T1><ul>";

	print "<font class=T2>".LANGSTAGE31.": <b> $activitename </b> </font> ";
	print "<br><br>";
	$data=recherche_activite_limit($activite,$depart,$nbaff,$departement,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE);
	print LANGSTAGE96." : ".recherche_activite_nb($activite,$depart,$nbaff,$departement,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE);
	print "<br><br><br>";
/* 
	id_serial,
	nom,
	contact,
	adresse,
	code_p,
	5 - ville,
	secteur_ac,
	activite_prin,
	tel,
	fax,
	10 - email,
	info_plus,
	bonus,
	registrecommerce,
	siren,
	15 - siret,
	formejuridique,
	secteureconomique,
	INSEE,
	NAFAPE,
	20 - NACE,
	typeorganisation
*/
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
		<div align=right>[ <a href="#" onclick="slidedown_showHide('box<?php print $i ?>');return false;"><?php print LANGSTAGE62 ?> +</a> ]&nbsp;&nbsp;&nbsp;</div>
		</td></tr></table>
		<div id="dhtmlgoodies_control"></div>
		<div style="width:368px;" class="dhtmlgoodies_contentBox" id="box<?php print $i ?>">
			<div class="dhtmlgoodies_content" id="subBox<?php print $i ?>">
			<font class=T2>
			<?php
			$siren=($data[$i][14] == "Choix...") ? "" : $data[$i][16] ;
			$siret=($data[$i][15]  == "Choix...") ? "" : $data[$i][17] ;
			$naf=($data[$i][19] == "Choix...") ? "" : $data[$i][21] ;
			?>	
			<?php print "Registre du commerce" ?> : <b><?php print stripslashes($data[$i][13]) ?></b>  <br><br>
			<?php print "SIREN" ?> : <b><?php print $siren ?></b>  <br><br>
			<?php print "SIRET" ?> : <b><?php print $siret ?></b>  <br><br>
			<?php print "Forme Juridique" ?> : <b><?php print stripslashes($data[$i][16]) ?></b>  <br><br>
			<?php print "Secteur Economique" ?> : <b><?php print stripslashes($data[$i][17]) ?></b>  <br><br>
			<?php print "INSEE" ?> : <b><?php print stripslashes($data[$i][18]) ?></b>  <br><br>
			<?php print "NAF (APE)" ?> : <b><?php print $naf ?></b>  <br><br>
			<?php print "Type Organisation" ?> : <b><?php print stripslashes($data[$i][21]) ?></b>  <br><br>

			<?php print LANGSTAGE28 ?> : <b><?php print $data[$i][3] ?></b>  <br><br>
			<?php print LANGSTAGE30 ?> : <b><?php print $data[$i][5] ?> </b> <br><br>
			<?php print LANGSTAGE29 ?> : <b><?php print $data[$i][4] ?></b> <br><br>
			<?php print LANGAGENDA73 ?> : <b><?php print ""  ?></b> <br><br>
			<?php print LANGSTAGE27 ?> : <b><?php print $data[$i][2] ?> </b>(<?php print $data[$i][13] ?> )<br><br>
			<?php print LANGSTAGE42 ?> : <b><?php print $data[$i][8] ?> / <?php print $data[$i][9] ?></b> <br><br>
			<?php print LANGSTAGE36 ?> : <b><?php print $data[$i][10] ?> </b><br><br>	
			<?php print LANGSTAGE37 ?> : <b><?php print $data[$i][11] ?></b> <br><br>
			Plan : <a href="#" onclick="open('https://support.triade-educ.org/support/google-map-triade.php?etablissement=<?php print  urlencode($data[$i][1])?>&adresse=<?php print urlencode($data[$i][3]) ?>&ville=<?php print urlencode($data[$i][5]) ?>&pays=<?php print urlencode($data[$i][14])?>','_blank','width=450,height=350')" /><img src="image/commun/loupe.png" border="0" /></a><br><br><br>

			<hr>
			<u>Historique des élèves</u> :<br><br>
			<?php
			//identreprise,ideleve,nomprenomeleve,classeeleve,periodestage
			$datalisting=listingHistorique($data[$i][0]);
			for ($j=0;$j<count($datalisting);$j++) {
				$nomprenom=$datalisting[$j][2];
				$classe=$datalisting[$j][3];
				$periode=$datalisting[$j][4];
				print ucwords($nomprenom)." en $classe durant la période $periode <br />";
			}
?>	
 			</font>
		<br><br>	
			</div>
		</div>

		<br><br>
		<?php
	}

print "</ul></font>";


	print "&nbsp;&nbsp;&nbsp;[<a href='gestion_stage_ent_visu.php'>".LANGSTAGE41."</a>]<br><br> ";
?>
<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent_entr($fichier,$table,$depart,$nbaff,$champs,$iddest,$departement,$activite,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE); ?><br><br></td>
<td align=right width=33%><br><?php suivant_entr($fichier,$table,$depart,$nbaff,$champs,$iddest,$departement,$activite,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE); ?>&nbsp;<br><br></td>
</tr></table>


</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
