<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
if (isset($_POST["idelevetuteur"])) {
	$Seid=$_POST["idelevetuteur"];
	$_SESSION["idelevetuteur"]=$Seid;
	$idclasse=chercheClasseEleve($Seid);
	$_SESSION["idClasse"]=$idclasse;
}
if (isset($_SESSION["idelevetuteur"])) {
	$Seid=$_SESSION["idelevetuteur"];	
	$idclasse=chercheClasseEleve($Seid);
}
if ($idclasse == "") {
	if (isset($_POST["saisie_classe"])) {
		$idclasse=$_POST["saisie_classe"];
		$inputclasse="<input type=hidden name='saisie_classe' value='$idclasse' />";
		$getclasse="&saisie_classe=$idclasse";
	}else{
		$idclasse=chercheIdClasseDunEleve($mySession[Spid]);
		$inputclasse="";
		$getclasse="";
	}
}
$nomclasse=chercheClasse($idclasse);
$date=dateDMY();
if (isset($_GET["iddate"])) {
	$date=dateForm($_GET["iddate"]);
	if (isset($_GET["saisie_classe"])) { 
		$idclasse=$_GET["saisie_classe"]; 
		$nomclasse=chercheClasse($idclasse);
		$getclasse="&saisie_classe=$idclasse";  
		$inputclasse="<input type=hidden name='saisie_classe' value='$idclasse' />";
	}
}
if (isset($_POST["saisie_date"])) {
	$date=$_POST["saisie_date"];
}


if (isset($_GET["choix"])) $choix=$_GET["choix"]; 
if ($choix == "") $choix=2;

?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/menu-tab.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/menu-tab.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="600">
<form method="post" name="formulaire" action="cahiertext_visu.php" >
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print LANGPROF37 ?> - </b><font id="color2"><?php print ucwords($nomclasse[0][1]) ?></font>
<?php
include_once("librairie_php/calendar.php");

if ($_SESSION["membre"] == "menututeur") {
?>
	&nbsp;&nbsp;
	<select name='idelevetuteur' onchange="this.form.submit()" >
		<?php 
		if ($Seid != "") {
			$nom=recherche_eleve_nom($Seid);
			$prenom=recherche_eleve_prenom($Seid);
	        	print "<option id='select1' value='$Seid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
		}else{
			print "<option id='select0' >".LANGCHOIX."</option>";
		}
		listEleveTuteur($_SESSION["id_pers"],30)
		?>
	</select>
<?php
}
?>
</td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>
<!-- // fin  -->
<table border=0>
<tr><td>
<?php print LANGAGENDA229 ?> <input type='text' value="<?php print $date ?>" name="saisie_date" size="12" onKeyPress="onlyChar(event)" class="bouton2" />
<?php
calendar("id1","document.formulaire.saisie_date",$_SESSION["langue"],"0");
?>
</td><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","create"); //text,nomInput</script></td></tr></table>
<br>
<?php print $inputclasse ?>
</form>


<table width=100% border=0>
<tr><td colspan=2>
<?php
$dateS=datesuivante($date);
$dateP=dateprecedent($date);
?>
<table border=0 width=100% align=center >
<tr><td align=left>
&nbsp;&nbsp;<input type=button value="<-- <?php print LANGPROFR ?>"  class="bouton2" onclick="open('cahiertext_visu.php?iddate=<?php print $dateP.$getclasse ?>','_parent','')" >
</td>
<td align=center>
<input type=button value="<?php print LANGPROF34 ?>"  class="bouton2" onclick="open('cahiertext_visu_global.php?iddate=<?php print dateFormBase($date).$getclasse ?>','devoir','width=1100,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')" >
</td>

<td align=right>
&nbsp;&nbsp;<input type=button value="<?php print LANGPROFQ ?> --> "  class="bouton2" onclick="open('cahiertext_visu.php?iddate=<?php print $dateS.$getclasse ?>','_parent','')" >
</td></tr>
</table>
<br>
<div id="dhtmlgoodies_tabView1">
	
	<div class="dhtmlgoodies_aTab">
		<?php
		$bgcolor="#FFFFCC";

		print "<table border=0 width='97%' align='center' >";
		$data=affcontenuScolaireParent($idclasse,$date,"date_contenu");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, contenu, classorgrp, id, number, idprof
		for($j=0;$j<count($data);$j++) {
			if (trim($data[$j][5]) == "") continue;
			$datafile=recupPieceJointe($data[$j][8]); //md5,nom,etat,idpiecejointe
			$lienFichier="<br>";
			$contenu=$data[$j][5];
			for($F=0;$F<count($datafile);$F++) {
				$fichier=$datafile[$F][1];
				$md5=$datafile[$F][0];
				$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,30)."</a><br>";			
			}
			print "<div style=\" border:solid 1px black;background-color:$bgcolor \"> ";
			print "&nbsp;".LANGPER17." : <font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		        print "<font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
			print "&nbsp;&nbsp;".$contenu;
			print "$lienFichier";
			print "</div>";
		}
		print "</td>";
		print "</tr></table>";
			
		?>

	</div>

	<div class="dhtmlgoodies_aTab">
		<?php
		print "<table border=0 width='97%' align='center' >";
		$data=affobjectifScolaireParent($idclasse,$date,"date_contenu");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, objectif, classorgrp, id, number_obj, idprof
		for($j=0;$j<count($data);$j++) {
			if (trim($data[$j][5]) == "") continue;
			$contenu=$data[$j][5];
			$datafile=recupPieceJointe($data[$j][8]); //md5,nom,etat,idpiecejointe
			$lienFichier="<br>";
			for($F=0;$F<count($datafile);$F++) {
				$fichier=$datafile[$F][1];
				$md5=$datafile[$F][0];
				$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,30)."</a><br>";			
			}
			print "<div style=\"  border:solid 1px black;background-color:$bgcolor \"> ";
			print "&nbsp;".LANGPER17." : <font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		        print "<font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
			print "&nbsp;&nbsp;".$contenu;
			print "$lienFichier";
			print "</div>";
		}
		print "</td>";
		print "</tr></table>";	
		?>		
	</div>

	<div class="dhtmlgoodies_aTab">
	<?php
		print "<table border=0 width='97%' align='center' >";
		$data=affdevoirScolaireParent($idclasse,$date,"date_devoir"); 
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number, idprof, tempsestimedevoir
		for($i=0;$i<count($data);$i++) {
			if (trim($data[$i][5]) == "") continue;
			$number=$data[$i][8];
			$tempsestime=$data[$i][10];
			$idprof=$data[$i][9];
			$contenu=$data[$i][5];
			$nomprof=recherche_personne2($idprof);
			if ((trim($tempsestime) != "") && (trim($tempsestime) != "00:00:00"))  {
				$tempsestime="<br>&nbsp;&nbsp; - <font class='T1'>".LANGMESS104." ".timeForm($tempsestime)."</font><br>";
			}else{
				$tempsestime="";
			}
			$datafile=recupPieceJointe($data[$i][8]); //md5,nom,etat,idpiecejointe
			$lienFichier="<br>";
			for($F=0;$F<count($datafile);$F++) {
				$fichier=$datafile[$F][1];
				$md5=$datafile[$F][0];
				$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,30)."</a><br>";			
			}

			print "<div style=\" border:solid 1px black;background-color:$bgcolor \"> ";
			print "&nbsp;".LANGPER17." : <font color=blue>".ucfirst(chercheMatiereNom($data[$i][1]))."</font> ";
		        print "<font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$i][2]).")</i></font><br>";
			print $contenu."<br>";
			print $tempsestime;
			print "$lienFichier";
			print "</div>";

	}
		print "</td>";
		print "</tr></table>";	
?>
	</div>
</div>




<SCRIPT language='JavaScript'>InitBulle('#000000','#FCE4BA','red',1);</SCRIPT>

<script type="text/javascript">initTabs('dhtmlgoodies_tabView1',Array('<?php print addslashes(LANGMESS92) ?>','<?php print addslashes(LANGMESS95) ?>','<?php print addslashes(LANGMESS98) ?>'),<?php print $choix ?>,'100%',430,Array(false,false,false));</script>

     </td></tr></table>
     <!-- // fin  -->
     </td></tr></table>
     <?php
     // Test du membre pour savoir quel fichier JS je dois executer
     if (($_SESSION['membre'] == "menuadmin") || ($_SESSION['membre'] == "menuscolaire")) :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
       print "</SCRIPT>";
     else :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
       print "</SCRIPT>";
       top_d();
	   print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
       print "</SCRIPT>";
     endif ;
     ?>
<?php include_once("./librairie_php/finbody.php"); ?>
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
