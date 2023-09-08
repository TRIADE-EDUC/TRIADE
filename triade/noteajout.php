<?php
session_start();
unset($_SESSION["profpclasse"]);
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

include_once("./librairie_php/verifEmailEnregistre.php");
error_reporting(0);
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
 *
 ***************************************************************************/

if ($_COOKIE["tri_eleve"] == "classe") {
	$selectTrieClasse="selected='selected'";
	$selectTrieNom="";
}elseif ($_COOKIE["tri_eleve"] == "nomEleve") {
	$selectTrieNom="selected='selected'";
	$selectTrieClasse="";
}else{
	$selectTrieNom="selected='selected'";
	$selectTrieClasse="";
}


include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
include_once("./common/config2.inc.php");
$cnx=cnx();

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

// données DB utiles pour cette page
$sql="
SELECT
	a.code_classe,
	trim(c.libelle),
	a.code_matiere,
";
$sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(IFNULL(langue,''))), ";
$sql .= "
	a.code_groupe,
	trim(g.libelle)
FROM
	${prefixe}affectations a,
	${prefixe}matieres m,
	${prefixe}classes c,
	${prefixe}groupes g
WHERE
	code_prof='$mySession[Spid]'
	AND a.code_classe = c.code_class
	AND a.code_matiere = m.code_mat
	AND a.code_groupe = group_id
	AND (a.visubull = '1' OR a.visubullbtsblanc = '1')
	AND c.offline = '0'
	AND a.annee_scolaire = '$anneeScolaire'
	
GROUP BY a.code_matiere,a.code_classe,a.code_groupe

ORDER BY c.libelle,m.libelle
	";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array()); // nécessaire pour compatibilité
// patch pour problème sous-matière à 0
for($i=0;$i<count($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=trim($tmp[0].' '.$tmp[1]);
}
// fin patch
genMatJs('affectation',$data);
freeResult($curs);
unset($curs);
//htmlTableMat($data);
?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript">
<?php
$choixmatiere='1';
if (defined("CHOIXMATIEREPROF")) {
	$choixmatiere=CHOIXMATIEREPROF;
}
if (trim($choixmatiere) == "") { $choixmatiere='1'; }
?>
function upSelectMat(arg) {
	for(i=1;i<document.formulaire.sMat.options.length;i++){
		document.formulaire.sMat.options[i].value='';
		document.formulaire.sMat.options[i].text='';
	}
	var tmp=arg.value.split(":");
	var clas=tmp[0];
	var grp=tmp[1];
	var opt='<?php print $choixmatiere ?>';
	for(i=0;i<affectation.length;i++) {
		if(affectation[i][0] == clas && affectation[i][4] == grp) {
		myOpt=new Option();
		myOpt.value = affectation[i][2];
		myOpt.text = affectation[i][3];
		myOpt.text = myOpt.text.replace(/ 0 *$/,"");   // supprime le 0 de la matiere ajout ET
		document.formulaire.sMat.options[opt]=myOpt;
		opt++;
		}
	}
	return true;
}


function upSelectMat2(arg) {
	for(i=1;i<document.formulaire2.sMat.options.length;i++){
		document.formulaire2.sMat.options[i].value='';
		document.formulaire2.sMat.options[i].text='';
	}
	var tmp=arg.value.split(":");
	var clas=tmp[0];
	var grp=tmp[1];
	var opt='<?php print $choixmatiere ?>';;
	for(i=0;i<affectation.length;i++) {
		if(affectation[i][0] == clas && affectation[i][4] == grp) {
		myOpt=new Option();
		myOpt.value = affectation[i][2];
		myOpt.text = affectation[i][3];
		myOpt.text = myOpt.text.replace(/ 0 *$/,"");   // supprime le 0 de la matiere ajout ET
		document.formulaire2.sMat.options[opt]=myOpt;
		opt++;
		}
	}
	return true;
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_note.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFF?> <?php print LANGMESS77 ?> </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td>
        <br />
        <ul>
	<form method="post" action="noteajout.php" >
        <font class="T2"><?php print LANGBULL29 ?> :</font>
        <select name='anneeScolaire' onChange="this.form.submit()"  >
        <?php
        filtreAnneeScolaireSelectNote($anneeScolaire,4);
        ?>
        </select>
        <br/>
        </form>
<!-- // fin  -->
<?php
$res=vsuite();
$res=0;
if ($res) {
        print $message;
}else {

	if ($choixmatiere == 0) {
		$onsubmit="onsubmit=\"return verifAccesNotebis()\"";
	}else{
		$onsubmit="onsubmit=\"return verifAccesNote()\"";
	}

?>
<form method="POST" <?php print $onsubmit ?> name="formulaire" action="noteajout2.php" >
<font class="T2"><?php print LANGPROFG ?> :</font>
<select name="sClasseGrp" size="1" onChange="upSelectMat(this)">
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"> <?php print LANGCHOIX3 ?> </option>
	 <?php
		 for($i=1;$i<count($data);$i++){
			 	if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
					continue;
				}
				else {
				// utilisation de l'opérateur ternaire expr1?expr2:expr3;
					$libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
					if (isset($verif[$libelle])) continue;
                                        $verif[$libelle]=$libelle;
					print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".ucfirst($libelle)."</option>\n";
				}
				$gtmp=$data[$i][4];
				$ctmp=$data[$i][0];
			 }
			 unset($gtmp);
			 unset($ctmp);
			 unset($libelle);
			 unset($verif);
			 ?>
			 </select>
			 <br /><br />

			 <font class="T2"><?php print LANGPROF1 ?> :</font>

			<select name="sMat" size="1"> <!-- saisie_matiere -->
                <option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
                <!--
				<option></option>
				<option></option>
				<option></option>
				<option></option>
				-->
				</select>

				<BR><BR>

		<font class="T2"><?php print LANGMESS78  ?> :</font>   <select name="trier">
		<option value="nomeleve" <?php print $selectTrieNom ?> STYLE='color:#000066;background-color:#CCCCFF'>Nom</option>
		<option value="classe"  <?php print   $selectTrieClasse ?>  STYLE='color:#000066;background-color:#CCCCFF'>Classe</option>
                                       </select>


				<BR><BR>
				<?php
                 if (NOTEUSA == "oui") {
                 	?>
                 	<font class="T2"><?php print LANGDISC59  ?> :</font>   <select name="NoteUsa">
					                    <option value="non" STYLE="color:#000066;background-color:#FCE4BA">Non</option>
					                    <option value="oui" STYLE='color:#000066;background-color:#CCCCFF'>Oui</option>
                                       </select>
                 	<?php
                 	print "<br><br>";
                 }
                 ?>
                 <font class="T2"><?php print LANGPROF2 ?> :</font>
                 				<?php
                 					   print "<select name='sNbNote'>";
                 					   for($j=1;$j<=NOTEPROF;$j++) {
                 					   		if ($j == 1) {
                 					   			$couleur="STYLE='color:#000066;background-color:#CCCCFF'";
                 					   		}else{
                 					   			$couleur="STYLE='color:#000066;background-color:#CCCCFF'";
                 					   		}
                 				       		print "<option value='$j' $couleur >$j</option>";
                                       }
                                 ?>
                 </select><BR><BR>
		<font class="T2"><?php print LANGGRP56 ?> :</font>
                 <select name='NotationSur'>                 					   	
			<?php 
			      $info=0;
			      if (NOTATION40 == "oui") { $info=1;?>
				   <option value="40" STYLE='color:#000066;background-color:#CCCCFF'>40</option>
			<?php }
			      if (NOTATION30 == "oui") { $info=1;?>
				   <option value="30" STYLE='color:#000066;background-color:#CCCCFF'>30</option>
			<?php } 
			      if (NOTATION20 == "oui") { $info=1;?>
				   <option value="20" selected='selected' STYLE='color:#000066;background-color:#CCCCFF'>20</option>
			<?php } 
			      if (NOTATION15 == "oui") { $info=1;?>
				   <option value="15" STYLE='color:#000066;background-color:#CCCCFF'>15</option>
			<?php } 
			      if (NOTATION10 == "oui") { $info=1;?>
				   <option value="10" STYLE='color:#000066;background-color:#CCCCFF'>10</option>
			<?php } ?>
			<?php if (NOTATION5 == "oui") { $info=1; ?>
				   <option value="5" STYLE='color:#000066;background-color:#CCCCFF'>05</option>
			<?php } ?>
			<?php if (NOTATION6 == "oui") { $info=1; ?>
				   <option value="6" STYLE='color:#000066;background-color:#CCCCFF'>06</option>
			<?php } ?>
			<?php if ($info == 0) { ?>
				   <option value="20" STYLE='color:#000066;background-color:#CCCCFF'>20</option>
			<?php } ?>
		
                 
                 </select><BR><BR>
		<?php
                 if (NOTEEXAMEN == "oui") {
			 // voir aussi fichier notemodif3.php si ajout d'élèment
			 // voir aussi fichier notevisuadmin.php si ajout d'élèment
                 	?>

			<font class="T2"><?php print LANGDISC60  ?> :</font>   <select name="NoteExam">
							<option value="" STYLE="color:#000066;background-color:#FCE4BA">non</option>
					<?php if (EXAMENBLANC == "oui") { ?>
						<optgroup label="Blanc" />
						<?php if (PRODUCTID != "2b85614b9c7cc3e8f7f02fe4fd52e907") { ?>
					        	<option value="Brevet Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Brevet Blanc</option>
						        <option value="Brevet Professionnel Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Brevet Professionnel Blanc</option>
						        <option value="BAC Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BAC Blanc</option>
						        <option value="CAP Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>CAP Blanc</option>
						        <option value="BEP Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BEP Blanc</option>
						<?php } ?>
					        <option value="BTS Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BTS Blanc</option>
					        <option value="Partiel Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Partiel Blanc</option>
						<?php if (PRODUCTID != "2b85614b9c7cc3e8f7f02fe4fd52e907") { ?>
							<option value="Concours Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Concours Blanc</option>
						<?php } ?>
					<?php } ?>
					<?php if (EXAMENNAMUR == "oui") { ?>							
							<optgroup label="Spécif. Namur" />
					                <option value="décembre"  STYLE='color:#000066;background-color:#CCCCFF'>Décembre</option>
							<option value="juin" STYLE='color:#000066;background-color:#CCCCFF'>Juin</option>
					<?php } ?>
				<?php if (EXAMENKINSHASA == "oui") { ?>
                                                        <optgroup label="Spécif. Kinshasa" />
                                                        <option value="1er Session"  STYLE='color:#000066;background-color:#CCCCFF'>1er Session</option>
                                                        <option value="Rattrapage" STYLE='color:#000066;background-color:#CCCCFF'>Rattrapage</option>
                                        <?php } ?>
					<?php if (EXAMENPIGIERNIMES == "oui") { ?>
							<optgroup label="PIGIER" />
							<option value="ND" STYLE='color:#000066;background-color:#CCCCFF'>Note Devoir (DS)</option>
						        <option value="NP" STYLE='color:#000066;background-color:#CCCCFF'>Note Participation</option>
							<option value="DS" STYLE='color:#000066;background-color:#CCCCFF'>DS</option>
							<option value="examen" STYLE='color:#000066;background-color:#CCCCFF'>Examen</option>
							<option value="examen blanc" STYLE='color:#000066;background-color:#CCCCFF'>Examen Blanc</option>
					<?php } ?>
					<?php if (EXAMENISMAP == "oui") { ?>
						    <optgroup label="ISMAP" />
						    <option value="CC" STYLE='color:#000066;background-color:#CCCCFF'>CC - Participation</option>
						    <option value="DST" STYLE='color:#000066;background-color:#CCCCFF'>DST</option>
						   <!-- <option value="Partiel" STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option> -->
						  <!--  <option value="Soutenance" STYLE='color:#000066;background-color:#CCCCFF'>Soutenance</option> -->
						    <option value="Rapport" STYLE='color:#000066;background-color:#CCCCFF'>Rapport</option>
						    <option value="Fiche de lecture" STYLE='color:#000066;background-color:#CCCCFF'>Fiche de lecture</option>
						    <option value="Exposé" STYLE='color:#000066;background-color:#CCCCFF'>Exposé</option>
  						    <option value="Dad" STYLE='color:#000066;background-color:#CCCCFF'>Dad</option>
  						    <option value="Lecture" STYLE='color:#000066;background-color:#CCCCFF'>Lecture</option>
  						    <option value="Examen écrit" STYLE='color:#000066;background-color:#CCCCFF'>Examen écrit</option>
  						    <option value="Recopiage vocabulaire" STYLE='color:#000066;background-color:#CCCCFF'>Recopiage vocabulaire</option>
						    <option value="Mémoire Ip" STYLE='color:#000066;background-color:#CCCCFF'>Mémoire Ip</option>
                                                    <option value="Evaluation Tutorat" STYLE='color:#000066;background-color:#CCCCFF'>Evaluation Tutorat</option>

					<?php } ?>
					<?php if (EXAMENDS == "oui") { ?>
							<optgroup label="DS" />
							<option value="DS1"  STYLE='color:#000066;background-color:#CCCCFF'>DS1</option>
							<option value="DS2"  STYLE='color:#000066;background-color:#CCCCFF'>DS2</option>
							<option value="DS3"  STYLE='color:#000066;background-color:#CCCCFF'>DS3</option>
							<option value="DS4"  STYLE='color:#000066;background-color:#CCCCFF'>DS4</option>
					<?php } ?>
					<?php if (EXAMEN == "oui") { ?>	
							<optgroup label="Examen" />
							<option value="Partiel"  STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option>
					<?php } ?>
					<?php if (EXAMENISPACADEMIES == "oui") { ?>
						    <optgroup label="ISP ACADEMIES" />
						    <option value="ISP" STYLE='color:#000066;background-color:#CCCCFF'>ISP</option>
					<?php } ?>
					<?php if (EXAMENCIEFORMATION == "oui") { ?>							
							<optgroup label="Spécif. Cie. Formation" />
					                <option value="TAS"  STYLE='color:#000066;background-color:#CCCCFF'>TAS</option>
							<option value="BTS Blanc" STYLE='color:#000066;background-color:#CCCCFF'>BTS Blanc</option>
							<option value="Partiel Blanc" STYLE='color:#000066;background-color:#CCCCFF'>Partiel Blanc</option>
					<?php } ?>
					<?php if (EXAMENEEPP == "oui") { ?>
							<optgroup label="Spécif. EEPP" />
   							<option value="semestre" STYLE='color:#000066;background-color:#CCCCFF'>Semestriel</option>
   							<option value="2session" STYLE='color:#000066;background-color:#CCCCFF'>2ème session</option>
					<?php } ?>
					<?php if (EXAMENJTC == "oui") { ?>
                                                        <optgroup label="Spécif. JTC" />
                                                        <option value="jtc" STYLE='color:#000066;background-color:#CCCCFF'>Carnet</option>
                                        <?php } ?>
					<?php if (EXAMENIPAC == "oui") { ?>
							<optgroup label="IPAC" />
							<option value="Partiel" STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option>
   							<option value="Rattrapage" STYLE='color:#000066;background-color:#CCCCFF'>Rattrapage</option>
   							<option value="Examen complémentaire" STYLE='color:#000066;background-color:#CCCCFF'>Examen complémentaire</option>
   							<option value="Contrôle continu" STYLE='color:#000066;background-color:#CCCCFF'>Contrôle continu</option>
					<?php } ?>

					<?php if (EXAMENBREVETCOLLEGE == "oui") { ?>
							<optgroup label="Brevet Collège" />
						   	<option value="Brevet EPS" STYLE='color:#000066;background-color:#CCCCFF'>Brevet EPS</option>
							<option value="Brevet PREV. SANTE ENV." STYLE='color:#000066;background-color:#CCCCFF'>Brevet PREV. SANTE ENV.</option>
					<?php } ?>


					<?php
                                                $dataexam=recupExamenConfig();
                                                 //id, libelle , coef
                                                if (count($dataexam)>0) {
                                                        print "<optgroup label='Examen Config' />";
                                                }
                                                for($ex=0;$ex<count($dataexam);$ex++) {
                                                        $libelle=$dataexam[$ex][1];
                                                        $coef=$dataexam[$ex][2];
                                                        print "<option value='$libelle###$coef' STYLE='color:#000066;background-color:#CCCCFF'>$libelle</option>";
                                                }

                                        ?>



                                       </select>
                 	<?php
                 	print "<br><br>";
                 }
                 ?>
		<font class="T2"><?php print LANGMESS79 ?> :</font>
		<input type="text" name="notevisiblele" size=12  class=bouton2 value="<?php print dateDMY(); ?>" readonly="readonly" >
		<?php
 		include_once("librairie_php/calendar.php");
 		calendar("id1","document.formulaire.notevisiblele",$_SESSION["langue"],"0");
		?> 
<br><br>
<UL><UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script><br><br>
</UL></UL></UL></UL></UL>
</form>
<?php
}
?>
</td></tr></table><br /><br />


<?php //----------------------------------------------------------------------------------------------  ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFF?> <?php print LANGMESS80 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // fin  -->
<?php
$res=vsuite();
$res=0;
if ($res) {
        print $message;
}else {
	if ($choixmatiere == 0) {
		$onsubmit="onsubmit=\"return verifAccesNote2bis()\"";
	}else{
		$onsubmit="onsubmit=\"return verifAccesNote2()\"";
	}
?>
	<form method="POST" <?php print $onsubmit ?> name="formulaire2" action="noteajoutviescolaire2.php" >
<br />
<ul>
<font class="T2"><?php print LANGBULL29 ?> :</font>
                 <select name='anneeScolaire' >
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,4);
                 ?>
                 </select>

                 <br /><br />

<font class="T2"><?php print LANGPROFG ?> :</font>

<select name="sClasseGrp" size="1" onChange="upSelectMat2(this)">
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"> <?php print LANGCHOIX3 ?> </option>
		 <?php
			 for($i=1;$i<count($data);$i++){
				 	if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
						continue;
						}
					else {
						// utilisation de l'opérateur ternaire expr1?expr2:expr3;
						$libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
						if (isset($verif[$libelle])) continue;
        	                                $verif[$libelle]=$libelle;
						print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".$libelle."</option>\n";
					}
					$gtmp=$data[$i][4];
					$ctmp=$data[$i][0];
				 }
				 unset($gtmp);
				 unset($ctmp);
				 unset($libelle);
				 unset($verif);
				 ?>
				 </select>
				 <br /><br />

				 <font class="T2"><?php print LANGPROF1 ?> :</font>

				<select name="sMat" size="1"> <!-- saisie_matiere -->
                <option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
                <!--
				<option></option>
				<option></option>
				<option></option>
				<option></option>
				-->
				</select>
				<BR><BR>
<UL><UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script><br><br>
</UL></UL></UL></UL></UL>
</form>
<?php
}
?>

</td></tr></table><br /><br />


<?php //----------------------------------------------------------------------------------------------  ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTMESS502 ?> / année scolaire <?php print $anneeScolaire ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<br>
<?php
for($i=0;$i<count($data);$i++) {
	$idclasse=$data[$i][0];
	$nomClasse=$data[$i][1];
	if ($idclasse == 0) { $nomClasse="Toutes les classes"; }
	$data2=recupDateTrimIdclasse($idclasse,$anneeScolaire); //date_debut,date_fin,trim_choix,idclasse
	$dateDebutT1="";
	$dateFinT1="";
	$dateDebutT2="";
	$dateFinT2="";
	$dateDebutT3="";
	$dateFinT3="";
	for ($j=0;$j<count($data2);$j++) {

		$trim=$data2[$j][2];
		if ($trim == "trimestre1") { 
			$dateDebutT1=$data2[$j][0];
			$dateFinT1=$data2[$j][1];
		}
		if ($trim == "trimestre2") { 
			$dateDebutT2=$data2[$j][0];
			$dateFinT2=$data2[$j][1];
		}
		if ($trim == "trimestre3") { 
			$dateDebutT3=$data2[$j][0];
			$dateFinT3=$data2[$j][1];
		}
		if (($dateDebutT3 == "") && ($dateDebutT1 != "") && ($dateDebutT2 != "")) {
			$semestriel="oui";
		}

		if ($dateDebutT1 != "") { $dateDebutT1=dateForm($dateDebutT1); 	}
		if ($dateFinT1 	 != "")	{ $dateFinT1=dateForm($dateFinT1); 	}
		if ($dateDebutT2 != "") { $dateDebutT2=dateForm($dateDebutT2); 	}
		if ($dateFinT2 	 != "")	{ $dateFinT2=dateForm($dateFinT2); 	}
		if ($dateDebutT3 != "") { $dateDebutT3=dateForm($dateDebutT3); 	}
		if ($dateFinT3   != "")	{ $dateFinT3=dateForm($dateFinT3); 	}
	
	}

	if (trim($dateDebutT1) == "") continue; 
	print "&nbsp;&nbsp;<img src='image/on10.gif' /> <font class=T2>".LANGASS17." : $nomClasse </font>";
	print "<br>";	
	print "<ul> ".LANGMESS157." 1 : $dateDebutT1 - $dateFinT1 <br>";
	print LANGMESS157." 2 : $dateDebutT2 - $dateFinT2 <br>";
	print LANGMESS157." 3 : $dateDebutT3 - $dateFinT3 <br></ul>";
	print "<br>";
}

?>


     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin")  :
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
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
