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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<script language="JavaScript">
function validCheck(form) {
	if (form.value == "X") {
		form.value="";
		form.style.visibility='visible';
	}else{
		form.value="X";
		form.style.visibility='hidden';
	}
}

</script>
<?php 
$idclasse=$_POST["sClasseGrp"];
include_once("./librairie_php/lib_licence.php"); 
include_once("./common/config2.inc.php"); 
include_once("./librairie_php/db_triade.php"); 
$cnx=cnx();
if ($_SESSION["membre"] == "menuadmin") {
	validerequete("menuadmin");
}elseif (CARNETSUIVIPROF == "oui") {
	validerequete("menuprof");
}else{
	verif_profp_class($_SESSION["id_pers"],$idclasse); 
}


$idcompetence=$_POST["idcompetence"];
$competence=chercheCompetence($idcompetence);
$idcarnet=$_POST["idcarnet"];
$nom_carnet=chercheNomCarnet($idcarnet);
$notation=$_POST["notation"];
$periode=$_POST["periode"];


if (isset($_POST["validation"])) {
	$idEleve=$_POST["idEleve"];
	if ($idEleve != "null") {
		$nb=$_POST["nb"];
		for($i=0;$i<$nb;$i++) {
			$note="notation_$i";
			$idDescriptif="iddescriptif_$i";
			$note=$_POST[$note];
			$idDescriptif=$_POST[$idDescriptif];
			// print "$idEleve,$idcarnet,$idcompetence,$notation,$periode,$idclasse,$note,$idDescriptif<br>";
			enr_evaluation_carnet($idEleve,$idcarnet,$idcompetence,$notation,$periode,$idclasse,$note,$idDescriptif);
		}
		alertJs(LANGPARAM16);
	}

}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print $competence ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php 
$data=rechercheDescriptif($idcompetence,$idcarnet); //id,libelle,bold
?>
<br>
<?php
if ((count($data) > 0) && ($notation != "")) {

	print "<font class='T2' id='color3'>&nbsp;&nbsp;Evaluation pour la Période $periode</font> ";

	print "<form method=post><font class='T2'>&nbsp;&nbsp;Elève : </font><select name='direct_eleve'>";
	print "<option value='null' id='select0' >".LANGCHOIX."</option>";
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<count($data_eleve);$j++) {
		print "<option id='select1'  value=\"".$data_eleve[$j][1]."\">".ucwords(trim($data_eleve[$j][2]))." ".trim($data_eleve[$j][3])."</option>";
	}
	print "</select>&nbsp;"; 
	print "<input type=hidden name='idcarnet' value='$idcarnet' />";
	print "<input type=hidden name='idcompetence' value='$idcompetence' />";
	print "<input type=hidden name='notation' value='$notation' />";
	print "<input type=hidden name='periode' value='$periode' />";
	print "<input type=hidden name='sClasseGrp' value='$idclasse' />";
	print "<input type=submit class=BUTTON value='".LANGCARNET4."' ></form>";
	
	// ---------------------------------------------
	//

	if (isset($_POST["direct_eleve"])) {
		$idEleve=$_POST["direct_eleve"];
		$nomEleve=recherche_eleve_nom($idEleve);
		$prenomEleve=recherche_eleve_prenom($idEleve);
	}elseif (isset($_POST["validation"])) {
		$idEleve=$_POST["idEleve"];
		$nomEleve=recherche_eleve_nom($idEleve);
		$prenomEleve=recherche_eleve_prenom($idEleve);
	}else{
		$idEleve=$data_eleve[0][1];
		$nomEleve=$data_eleve[0][2];
		$prenomEleve=$data_eleve[0][3];

	}



	print "<font class='T2'>&nbsp;&nbsp;Fiche de l'élève :  <strong>".strtoupper($nomEleve)." ".ucwords($prenomEleve)."</strong>";

	print "<form name='formulaire' method='post'  >";
	if (($notation !=  "julesverne") && ($notation !=  "commentaire")) {
		print "<div align='left'><font class='T1'><i>N.B. :  X=Compétence travaillée mais non évaluée</i></font></div>";
	}

	print "<table border='1' bordercolor='#000000' bgcolor='#FFFFFF'>";
	$j=0;
	for($i=0;$i<count($data);$i++) {
		$iddescriptif=$data[$i][0];
		$libelle=$data[$i][1];
		$bold=$data[$i][2];
		if ($bold) { 
			$b="<b>"; $bb="</b>"; 
			$bgcolor="bgcolor='#CCCCCC'";
			$colspan="colspan='2'";
		}else{ 
			$b="";$bb=""; 
			$bgcolor="";
			$colspan="";
		}

		print "<tr class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\"  >\n";
		if ($bold) {
			print "<td $bgcolor $colspan >$b $libelle $bb</td>\n";
		}else{
			print "<td $bgcolor $colspan valign='top' > $b ".trunchaine($libelle,80)." $bb</td>\n";
			print "<td  valign='top' >\n";
			if (!$bold) {

				$note=rechercheEvalutionEleve($idEleve,$idcarnet,$iddescriptif,$idcompetence,$notation,$periode,$idclasse); // id,note

				if ($notation == "lettre") {
					if ($note == "A") { $checkedA="checked='checked' style='background-color:#CCCCCC' "; }else{ $checkedA=''; } 
					if ($note == "B") { $checkedB="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedB=''; } 
					if ($note == "C") { $checkedC="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedC=''; } 
					if ($note == "D") { $checkedD="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedD=''; } 
					if ($note == "X") { $checkedX="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedX=''; } 
					print "&nbsp;A&nbsp;<input type=radio name=notation_$j value='A' $checkedA />&nbsp;";
					print "B&nbsp;<input type=radio name=notation_$j value='B' $checkedB />&nbsp;";
					print "C&nbsp;<input type=radio name=notation_$j value='C' $checkedC />&nbsp;";
					print "D&nbsp;<input type=radio name=notation_$j value='D' $checkedD />&nbsp;";
					print "X&nbsp;<input type=radio name=notation_$j value='X' $checkedX />&nbsp;";
				}
				if ($notation == "julesverne") {
					if ($note == "A") { $checkedA="checked='checked' style='background-color:#CCCCCC' "; }else{ $checkedA=''; } 
					if ($note == "AR") { $checkedAR="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedAR=''; } 
					print "&nbsp;A&nbsp;<input type=radio name=notation_$j value='A' $checkedA  title='A' />&nbsp;";
					print "AR&nbsp;<input type=radio name=notation_$j value='AR' $checkedAR  title='AR' />&nbsp;";
					print "&nbsp;<input type=radio name=notation_$j value='' title='Annulé' />&nbsp;";
				}

				if ($notation == "commentaire") {
					// if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; }
					$nbcar=300;
					print "&nbsp;&nbsp;<textarea name=notation_$j rows='5' cols='50' ";
					print "onkeyup=\"Compter(this,this.form.CharRestant$j,'$nbcar')\" ";
					print "onkeypress=\"compter(this,'$nbcar')\" >$note</textarea>&nbsp; ";
					print "<input type=text size=2 name='CharRestant$j' disabled=\"disabled\">";
				}

				if ($notation == "chiffre") {
					if ($note == "1") { $checked1="checked='checked' style='background-color:#CCCCCC'"; }else{ $checked1=''; } 
					if ($note == "2") { $checked2="checked='checked' style='background-color:#CCCCCC'"; }else{ $checked2=''; } 
					if ($note == "3") { $checked3="checked='checked' style='background-color:#CCCCCC'"; }else{ $checked3=''; } 
					if ($note == "4") { $checked4="checked='checked' style='background-color:#CCCCCC'"; }else{ $checked4=''; } 
					if ($note == "X") { $checkedX="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedX=''; }
					print "&nbsp;1&nbsp;<input type=radio name=notation_$j value='1' $checked1 />&nbsp;";
					print "2&nbsp;<input type=radio name=notation_$j value='2' $checked2 />&nbsp;";
					print "3&nbsp;<input type=radio name=notation_$j value='3' $checked3 />&nbsp;";
					print "4&nbsp;<input type=radio name=notation_$j value='4' $checked4 />&nbsp;";
					print "X&nbsp;<input type=radio name=notation_$j value='X' $checkedX />&nbsp;";
				}
				if ($notation == "couleur") {
					if ($note == "vert") { $checkedvert="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedvert=''; } 
					if ($note == "bleu") { $checkedbleu="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedbleu=''; } 
					if ($note == "orange") { $checkedorange="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedorange=''; } 
					if ($note == "rouge") { $checkedrouge="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedrouge=''; } 
					if ($note == "X") { $checkedX="checked='checked' style='background-color:#CCCCCC'"; }else{ $checkedX=''; }

					print "&nbsp;<font color='green'>Vert</font>&nbsp;<input type=radio name=notation_$j value='vert' $checkedvert />&nbsp;";
					print "<font color='blue'>Bleu</font>&nbsp;<input type=radio name=notation_$j value='bleu' $checkedbleu />&nbsp;";
					print "<font color='orange'>Orange</font>&nbsp;<input type=radio name=notation_$j value='orange' $checkedorange />&nbsp;";
					print "<font color='red'>Rouge</font>&nbsp;<input type=radio name=notation_$j value='rouge' $checkedrouge />&nbsp;";
					print "X&nbsp;<input type=radio name=notation_$j value='X' $checkedX />&nbsp;";
				}
				if ($notation == "note") {
					if ($note == "X") { 
						$checkedX="checked='checked'";					    	
					}else{ 
						$checkedX=''; 
					}
					
					print "&nbsp;<input type=text size=2 name=notation_$j value=\"$note\" />&nbsp;";
					print "X&nbsp;<input type=checkbox onclick='validCheck(this.form.notation_$j)' $checkedX />&nbsp;";
					if ($note == "X") { print "<script language='JavaScript'>document.formulaire.notation_$j.style.visibility='hidden' </script>"; }
				}
				print "<input type=hidden name='iddescriptif_$j' value='$iddescriptif' />";
				$j++;
			}
		}	
			
		print "</td>\n";
		
		print "</tr>\n";
	}
	print "</table>";
	print "<input type=hidden name='nb' value='$j' />";
	print "<input type=hidden name='idcarnet' value='$idcarnet' />";
	print "<input type=hidden name='idcompetence' value='$idcompetence' />";
	print "<input type=hidden name='notation' value='$notation' /><br>";
	print "<input type=hidden name='periode' value='$periode' />";
	print "<input type=hidden name='sClasseGrp' value='$idclasse' />";
	print "<input type=hidden name='idEleve' value='$idEleve' />";

	print "<br />&nbsp;&nbsp;<input type=checkbox name=valideSaisie onClick='document.formulaire.validation.disabled=false;' id='btradio1' ><font color=red>".LANGPROFC."</font><br /><br />";
	print "&nbsp;<input type='submit' name='validation' class='button' value=\"".LANGPROFD."\" disabled='disabled' />";
	print "&nbsp;<input type='button' class='button' value=\"Evaluer une autre compétence\" onclick=\"open('carnet_editer_2.php?carnet=$idcarnet&cls=$idclasse','_parent','')\" /><br /><br /><br />";
	print "<br /><br /><br />";
	print "</form>";

	print "</font>";
	
}else{
	print "<font class='T2'><center>";
	if ($notation == "") {
		print "Merci d'indiquer le choix de notation";
	}else{
		print "Pas de notation possible pour cette compétence." ;
	}
	print "</center></font><br /><br />";
}


?>

<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
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
