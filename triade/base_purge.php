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
<script language="JavaScript" src="./librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script language="javaScript">
var nbElems=0;
function calcul(op) {
	// calcul le nombre d'élèment
	nbElems = eval(nbElems + op);
	if (nbElems < 0 ) { nbElems = 0; }
	document.formulaire.saisie_nb_recherche.value=nbElems;
}

function prepEnvoi() {
	var hid = new String();
	var tab = new Array();
	var data = window.document.formulaire.saisie_recherche.options;
	for (i=0;i<data.length;i++)
	{
		tab.push(data[i].value);
	}
	document.formulaire.saisie_recherche_final.value=tab.join(",");
}

</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >

<?php 
include("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete2($_SESSION["adminplus"]);
?>

<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION["membre"].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION["membre"].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPURG1?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>

<?php
if (isset($_POST["create"])) {

$cnx=cnx();
error($cnx);
purgerepertoirerecherche();
$liste=explode(",", $_POST["saisie_recherche_final"]);
foreach ($liste as $value) {


	if ($value == "eleves")  { 	
		DirPurge("./data/image_eleve"); 
		purge_element_eleve(); 
		history_cmd($_SESSION["nom"],"PURGE","eleves");
	}
	if ($value == "notes")       	{ vide_notes(); vide_notes_scolaire(); history_cmd($_SESSION["nom"],"PURGE","notes");  }
	if ($value == "notesscolaire")  { vide_notes_scolaire(); history_cmd($_SESSION["nom"],"PURGE","notes vie scolaire");  }
	if ($value == "groupe")      	{ purge_groupe();validGroup();history_cmd($_SESSION["nom"],"PURGE","groupe"); }
	if ($value == "discipline")  	{ vide_discipline_retenue();vide_discipline_sanction();purge_discipline_prof();history_cmd($_SESSION["nom"],"PURGE","dscipline");  }
	if ($value == "abs" ) 	     	{ vide_absences();history_cmd($_SESSION["nom"],"PURGE","abs"); }
	if ($value == "retard" )     	{ vide_retards();history_cmd($_SESSION["nom"],"PURGE","retard"); }
	if ($value == "present" )     	{ purge_present();history_cmd($_SESSION["nom"],"PURGE","présent"); }
	if ($value == "dispenses")   	{ vide_dispenses();history_cmd($_SESSION["nom"],"PURGE","dispenses");}
	if ($value == "news")   	{ purge_news();history_cmd($_SESSION["nom"],"PURGE","news"); }
	if ($value == "profpsupp")	{ purge_delete_profp();vide_message_prof_p();purgeprofPsupp();history_cmd($_SESSION["nom"],"PURGE","profpsupp");}
	if ($value == "messprofP") 	{ vide_message_prof_p();history_cmd($_SESSION["nom"],"PURGE","messages profP"); }
	if ($value == "devoirscolaire")	{ vide_devoir_scolaire();history_cmd($_SESSION["nom"],"PURGE","devoir scolaire"); }
	if ($value == "deleguesupp") 	{ purge_delete_delegue();history_cmd($_SESSION["nom"],"PURGE","delegue");}
	if ($value == "dst") 		{ purge_dst();history_cmd($_SESSION["nom"],"PURGE","dst"); }
	if ($value == "purgevenement" ) { purge_evenement();history_cmd($_SESSION["nom"],"PURGE","evenement");}
	if ($value == "purgaffectation"){ purge_affectation();history_cmd($_SESSION["nom"],"PURGE","affectation"); }
	if ($value == "hist_periode") 	{ purge_history_periode();history_cmd($_SESSION["nom"],"PURGE","hist. periode");}
	if ($value == "hist_bulletin") 	{ purge_bulletin();history_cmd($_SESSION["nom"],"PURGE","hist. bulletin");}
	if ($value == "purgcirculaire") { purge_circulaire();DirPurge("./data/circulaire");history_cmd($_SESSION["nom"],"PURGE","circulaire"); }
	if ($value == "parametude") 	{ purgeParamEtude();purgeEleveEtude();history_cmd($_SESSION["nom"],"PURGE","para. etude"); }
	if ($value == "trimestre") 	{ purge_trimestre();history_cmd($_SESSION["nom"],"PURGE","trimestre"); }

	if ($value == "forum") 		{ DirPurgeForum();history_cmd($_SESSION["nom"],"PURGE","forum"); }
	if ($value == "livreor") 	{ DirPurgeLivreor();history_cmd($_SESSION["nom"],"PURGE","livre d or"); }
	if ($value == "reservation") 	{ purgeresa();history_cmd($_SESSION["nom"],"PURGE","reservation");   }
	if ($value == "equipement") 	{ purgeequip(); purgeresa();history_cmd($_SESSION["nom"],"PURGE","equipement");   }

	if ($value == "purgimport") 	{ purgeimport();history_cmd($_SESSION["nom"],"PURGE","import");   }
	if ($value == "purgcertificat") { DirPurge("./data/pdf_certif");history_cmd($_SESSION["nom"],"PURGE","certificat");  }
	if ($value == "stockage")	{ purge_rep_membre("./data/stockage");history_cmd($_SESSION["nom"],"PURGE","stockage");  }

	if ($value == "direction") 	{ purgepersonnel("ADM");history_cmd($_SESSION["nom"],"PURGE","personnel direction"); }
	if ($value == "enseignant") 	{ purgepersonnel("ENS");purge_prof_com_bull();purgeEntretienEnseignentPourEtudiant;history_cmd($_SESSION["nom"],"PURGE","personnel enseignant");   }
	if ($value == "vie scolaire") 	{ purgepersonnel("MVS");history_cmd($_SESSION["nom"],"PURGE","personnel vie scolaire");   }

	if ($value == "agenda") 	{ purgeagenda();history_cmd($_SESSION["nom"],"PURGE","agenda"); }
	if ($value == "entretien") 	{ purgeEntretien();history_cmd($_SESSION["nom"],"PURGE","Les entretiens"); }

	if ($value == "com_bulletin") 	{ purgeComBulletin();history_cmd($_SESSION["nom"],"PURGE","Commentaire Bulletin"); }

	if ($value == "photodeFrance") 	{ purge_photographe_de_france();history_cmd($_SESSION["nom"],"PURGE","Photographe de France"); }

	if ($value == "brevetcollege") 	{ purge_brevetCollege();history_cmd($_SESSION["nom"],"PURGE"," Brevet collège"); }
	if ($value == "elevesansclasse") { vide_eleves_sans_classe();history_cmd($_SESSION["nom"],"PURGE"," Elève sans classe"); }
	if ($value == "inforesponsableeleve") { purge_responsable_info_eleve();history_cmd($_SESSION["nom"],"PURGE"," Info responsable Elève"); }
	if ($value == "edt") { purge_edt();history_cmd($_SESSION["nom"],"PURGE"," Info EDT"); }
	if ($value == "comptabilite") { purgeComptabilite();history_cmd($_SESSION["nom"],"PURGE"," Info Comptabilité"); }
	if ($value == "grpmail") { purgegrpmail();history_cmd($_SESSION["nom"],"PURGE"," Groupe mail"); }
	if ($value == "contrerendustage") { purgecontrerendustage() ;history_cmd($_SESSION["nom"],"PURGE"," Contre rendu stage"); }
	if ($value == "cantine") { purgecantine() ;history_cmd($_SESSION["nom"],"PURGE"," Gestionnaire de cantine"); }
	if ($value == "abssconet") { purge_abs_sconet() ;history_cmd($_SESSION["nom"],"PURGE"," Absences sconet"); }
	if ($value == "entretienduree") { purgeEntretienEnseignentPourEtudiant() ;history_cmd($_SESSION["nom"],"PURGE","Temps d'accompagnement"); }
	if ($value == "datestage") { purgeDateStage() ;history_cmd($_SESSION["nom"],"PURGE","Date de stage"); }
	if ($value == "affectationstage") { purgeAffectationStage() ;history_cmd($_SESSION["nom"],"PURGE","Affectation élève / stage"); }
	if ($value == "entreprises") { purgeEntreprise() ;history_cmd($_SESSION["nom"],"PURGE","Entreprises"); }
//purgeprofPmedsupp();

}

?>
<br><br>
<center><font class=T2><?php print LANGPUR5?></font></center>
<?php
}else {
?>

<form method=post name="formulaire" >

<br><br>
<font class=T2>
<ul><?php print LANGPUR7 ?></ul>
<table border=0 width=100%>
<tr><td width=33% align=center>
<select size=37 name="saisie_depart"  style="width:150px">
<?php include("./librairie_php/lib_purge_liste.php")?>
</select><br><?php print LANGPUR8?>
</td>
<td width=33% align=center>
<input type="button" value="<?php print LANGSTAGE3?> >>>" onClick="calcul('+1');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'<?php print LANGBASE39 ?>')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
<br><br><br><br><br>
<input type="button" value="&lt;&lt;&lt; <?php print LANGCHER6 ?>" onClick="calcul('-1');Deplacer(this.form.saisie_recherche,this.form.saisie_depart,'<?php print LANGBASE39 ?>')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
</td>
<td width=33% align=center>
     		<select size=37 name="saisie_recherche" style="width:150px" multiple="multiple">
		<OPTION>-------------</OPTION>
		</select><br><?php print LANGPUR9 ?>
		<script language="javascript">
			// suppression de la ligne  mais on la garde pour la largeur
			document.formulaire.saisie_recherche.options.length=0;
		</script>
</td>
</tr>
<tr><td colspan=3 align=center><br><ul><input type="submit" value='<?php print LANGBTS?>' class="BUTTON" onclick="prepEnvoi()" name=create></ul></td></tr>
</table>
<input type=hidden name="saisie_nb_recherche" size=6>
<input type=hidden name="saisie_recherche_final" size=6>
</form>
<font size=1><i><?php print LANGPUR6 ?> </i></font>
<?php } ?>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
</BODY></HTML>
