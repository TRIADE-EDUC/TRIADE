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
 *<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>

 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content ="no-cache">
<META http-equiv="pragma" content ="no-cache">
<META http-equiv="expires" content ="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond2' >
<script type="text/javascript">
function affichemessage() {
	document.getElementById('alerte').style.visibility='visible';
}
</script>
<?php
include("librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
	print "<script>";
        print "location.href='./base_de_donne_key.php'";
        print "</script>";
        exit;
}
include_once('librairie_php/db_triade.php');

$cnx=cnx();
//variables utiles
// code_class(classes) de la classe concernée par l'affectation
$cid=$_GET["saisie_classe_envoi"];
// tableau 2 valeurs : id,libelle pour classe
$idclasse=$_GET["saisie_classe_envoi"];
$anneeScolaire=$_GET["anneeScolaire"];
$dataClasse=chercheClasse($_GET["saisie_classe_envoi"]);
$nom_classe=$dataClasse[0][1];
$matGroup=matGroup($nom_classe,$anneeScolaire);
$tri=$_GET["saisie_tri"];
$semestre=preg_replace('/trimestre/','',$tri);

// création de la matrice pour le select matiere
// 	sql
//		code_mat,libelle,sous_matiere
$sql=<<<SQL
SELECT
	code_mat,
	libelle,
	sous_matiere
FROM
	${prefixe}matieres
WHERE 
	offline='0'
ORDER BY
	libelle
SQL;

$cursor=execSql($sql);
$data=chargeMat($cursor);
freeResult($cursor);
for($l=0;$l<count($data);$l++){
	for($c=0;$c<count($data);$c++){
			if(empty($data[$l][2])):
				$bool=0;
			else:
				$bool=true;
			endif;
		$matMat[$l][0]=$data[$l][0].":".$bool;
		$sl=trim($data[$l][1])." ".trim($data[$l][2]);
		$matMat[$l][1]=$sl;
	}
}

$ajoutligne=0;
if (isset($_GET["ligne"])) {
	$ajoutligne=1;
}

$readonly="";
$readonlycheckbox="";
$disabledENS="";
if ($_SESSION["membre"] == "menuprof") {
	$readonly="readonly='readonly'"; 
	$readonlycheckbox="onclick='return false;' onkeydown='return false;'";
	$disabledENS="disabled='disabled'";
}

?>

<form method=post onsubmit="return valide();" action="modifaffect3.php" name="formulaire">
<input type='hidden' name='anneeScolaire' value='<?php print $anneeScolaire ?>' />
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" align=center>
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE20?> <?php print $nom_classe?> pour l'ann&eacute;e scolaire <?php print $anneeScolaire ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- //  debut -->
<table border="1" bgcolor="#ffffff" width='100%' style="border-collapse: collapse;"  >
<TR><TD>
<TABLE border="1"  width=100%>
<tr>
<td align=center bgcolor='yellow' ><?php print LANGPER16?></td>
<TD align=center bgcolor='yellow' ><?php print LANGPER17?></TD>
<TD align=center bgcolor='yellow' ><?php print LANGPER18?></TD>
<TD align=center bgcolor='yellow' >&nbsp;&nbsp;<?php print LANGPER19?>&nbsp;&nbsp;</TD>
<TD align=center bgcolor='yellow' >&nbsp;&nbsp;<?php print LANGPER20?>&nbsp;&nbsp;</TD>
<TD align=center bgcolor='yellow' ><?php print LANGPER21?></TD>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Visu.<i>*</i>"?>&nbsp;</TD>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Visu.&nbsp;BTS&nbsp;Blanc<i>***</i>"?>&nbsp;</TD>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Nb&nbsp;d'heure&nbsp;**"?>&nbsp;</TD>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "ECTS"?>&nbsp;</TD>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Unités&nbsp;Enseignements"?>&nbsp;</TD>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Spécif" ?>&nbsp;</td>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Info Sem." ?>&nbsp;</td>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Coef Certif." ?>&nbsp;</td>
<TD align=center bgcolor='yellow' >&nbsp;<?php print "Note plancher" ?>&nbsp;</td>
<?php
if (!empty($_SESSION["adminplus"])) {
	
	$data=visu_affectation_detail_2($_GET["saisie_classe_envoi"],$_GET["saisie_tri"],$anneeScolaire); 
	// ordre_affichage,code_matiere,code_prof,	code_classe,coef,g.libelle,a.langue,a.avec_sous_matiere,a.visubull,nb_heure,ects,id_ue_detail,specif, a.annee_scolaire,visubullbtsblanc,num_semestre_info,trim
for ($a=0;$a<count($data);$a++) {
	$nomMatiere=ucwords(chercheMatiereNom($data[$a][1]));
	if ($data[$a][7] == TRUE ) {
		$suite=1;
	}else {
		$suitre=0;
	}
	$idMatiere=$data[$a][1].":".$suite;
	$nomProf=recherche_personne($data[$a][2]);
	$idProf=$data[$a][2];
	$coef=$data[$a][4];
	$nbheure=$data[$a][9];
	$ects=$data[$a][10];
	$ue=$data[$a][11];
	$specif=$data[$a][12];
	$visubullbtsblanc=$data[$a][14];
	$info_semestre=$data[$a][15];
	$coef_certif=$data[$a][17];
	$note_planche=$data[$a][18];
	$trim=$data[$a][16];

	$nomGrp=trim($data[$a][5]);
	$idGrp=chercheGroupeId($data[$a][5]);
	//if (ctype_space($nomGrp)) {
	if (trim($nomGrp) == "") {
		$idGrp="0";
		$nomGrp="Choix";
	}

	$idLang=$data[$a][6];
	$nomLang=$data[$a][6];
	//if (ctype_space($nomLang)) {
	if (trim($nomLang) == "") {
		$idLang="";
		$nomLang="Choix";
	}

	$visubull=$data[$a][8];
?>
<TR>
	<TD>
		<input type="text" name="ordre" value="<?php print $a?>" readonly size="3" onfocus='this.blur()'>
	</TD>
<TD>
	<?php
    	$nameSelectMat="saisie_matiere_".$a;
    	print(selectHtml2($nameSelectMat,1,false,$matMat,$nomMatiere,$idMatiere,"1","40",$disabledENS));
	?>
</TD>
<TD>
<select name="saisie_prof_<?php print $a?>">
<?php
$nomProfTitle=$nomProf;
$nomProf=trunchaine($nomProf,40);
?>
<option value="<?php print $idProf?>" STYLE="color:#000066;background-color:#FCE4BA" title="<?php print $nomProfTitle ?>" ><?php print $nomProf?></option>
<?php
select_personne_2('ENS',40);
// creation des options
// optimisation indispensable
?>
</select></TD>
<TD align=center><input type=text value="<?php print $coef?>" size=3 name=saisie_coef_<?php print $a?> <?php print $readonly ?>  ></TD>
<TD>
    <?php
    $nameSelectGrp="saisie_groupe_".$a;
    if (trim($nomGrp) != "") {
    	print(selectHtml2($nameSelectGrp,1,false,$matGroup,$nomGrp,$idGrp,0,"40",$disabledENS));
    }else{
	if ($_SESSION["membre"] == "menuadmin") { 
    		print(selectHtml($nameSelectGrp,1,false,$matGroup));
	}
    }
    ?>
</TD>
<TD><select name="saisie_langue_<?php print $a?>" >
<?php  if ( (trim($nomLang) != "") && (trim($nomLang) != "0"))  { ?>
	<option STYLE='color:#000066;background-color:#FCE4BA' value="<?php print $idLang?>"><?php print $nomLang?></option>
<?php }else { ?>
	<option value=0 STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX?></option>
<?php } ?>
<?php  if ($_SESSION["membre"] == "menuadmin") { ?>
<option value=0 STYLE="color:#000066;background-color:#FCE4BA"><?php print "" ?></option>
<option id='select1' value='LV1'>LV1</option>
<option id='select1' value='LV2'>LV2</option>
<option id='select1' value='LV3'>LV3</option>
<option id='select1' value='LV4'>LV4</option>
<option id='select1' value='OPT1'>OPT1</option>
<option id='select1' value='OPT2'>OPT2</option>
<option id='select1' value='OPT3'>OPT3</option>
<option id='select1' value='OPT4'>OPT4</option>
<option id='select1' value='DP3'>DP3</option>
</select>
<?php } ?>
</TD>
<?php 
    if ($visubull == 1) {
		$checkedvisu="checked='checked'";
    }else{
		$checkedvisu="";
    }
?>
    <td width='5%'><input type='checkbox' name="saisie_visubull_<?php print $a?>" value='1' <?php print $checkedvisu ?> <?php print $readonlycheckbox ?>  ></td>

<?php 
    if ($visubullbtsblanc == 1) {
		$checkedvisubtsblanc="checked='checked'";
    }else{
		$checkedvisubtsblanc="";
    }
?>
	<td width='5%'><input type='checkbox' name="saisie_visubull_btsblanc_<?php print $a?>" value='1' <?php print $checkedvisubtsblanc ?> <?php print $readonlycheckbox ?> ></td>

	<td width='5%'><input type='text' name="saisie_nbheure_<?php print $a?>" value="<?php print $nbheure ?>" size='3' <?php print $readonly ?>  ></td>
	<td width='5%'><input type='text' name="saisie_ects_<?php print $a?>" value="<?php print $ects ?>" size='3'  <?php print $readonly ?> /></td>
	<td width='5%'>
	<select name="ue_<?php print $a ?>" >
	<?php 
	if ($ue > 0) {
		$tab=recupNomUE($ue);
		$nom_ue=$tab[0][0];
		$ue=$tab[0][1];
		$nom_ueTitle=$nom_ue;
		$nom_ue=trunchaine($nom_ue,40);
		$sem=$tab[0][2];
		if ($sem == 0) $sem="1 et 2";
		print "<option value='$ue' STYLE='color:#000066;background-color:#FCE4BA' title=\"$nom_ueTitle\" >$nom_ue (Sem:$sem)</option>";
	}
	?>
	<?php 
	if ($_SESSION["membre"] == "menuadmin") { ?>
		<option value='' STYLE="color:#000066;background-color:#FCE4BA" ><?php print LANGCHOIX ?></option>
	<?php 
		$dataUE = vatel_liste_ueT($semestre,$idclasse,$anneeScolaire);
		for($k=0;$k<count($dataUE);$k++)  {
			if ($dataUE[$k][1] != "") {
				$sem=$dataUE[$k][2];
				if ($dataUE[$k][2] == 0) $sem="1 et 2";
				print "<option value='".$dataUE[$k][0]."' id='select1' title=\"".$dataUE[$k][1]."\" >".trunchaine($dataUE[$k][1],40)." (Sem:$sem)</option>";
			}
		}
	}
	?>    
	</select>
	</td>
	<td  width='5%'>
	<select name="specif_<?php print $a ?>" >
	<?php 
	if ($_SESSION["membre"] == "menuprof") { 
		if ($specif == "etudedecasipac" ) { ?>
			<option value="etudedecasipac" id='select1' <?php if ($specif == "etudedecasipac" ) print "selected='selected'"; ?> >Etude de cas</option>
		<?php 
		}else{
			print "<option value='' id='select0' ></option>";
		}

	}else{ ?>
		<option value="" id='select0' ></option>
		<option value="etudedecasipac" id='select1' <?php if ($specif == "etudedecasipac" ) print "selected='selected'"; ?> >Etude de cas</option>
	<?php } ?>
	</select>
	</td>
	<td>
	<select name='info_semestre_<?php print $a?>'>
	<?php  if ( (trim($info_semestre) != "") && (trim($info_semestre) > "0"))  { ?>
		<option STYLE='color:#000066;background-color:#FCE4BA' value="<?php print $info_semestre ?>"><?php print $info_semestre ?></option>
	<?php } ?>
	<option id='select0' value='0'></option>
	<option id='select1' value='1'>1</option>
	<option id='select1' value='2'>2</option>
	<option id='select1' value='3'>3</option>
	<option id='select1' value='4'>4</option>
	<option id='select1' value='5'>5</option>
	<option id='select1' value='6'>6</option>
	<option id='select1' value='7'>7</option>
	<option id='select1' value='8'>8</option>
	<option id='select1' value='9'>9</option>
	<option id='select1' value='10'>10</option>
	</select>
	</td>

	
	<td><input type='text' size='2' name="saisie_coef_certif_<?php print $a?>" value="<?php print $coef_certif ?>"  /></td>	
	<td><input type='text' size='2' name="saisie_note_planche_<?php print $a?>" value="<?php print $note_planche ?>"  /></td>	


</TR>
<?php	}
if ($ajoutligne == 1) {
$coef="";
?>
<TR>
	<TD>
	<input type="text" name="ordre" value="<?php print $a?>"  size="3" readonly></TD>
    <TD><?php
    	$nameSelectMat="saisie_matiere_".$a;
    	print(selectHtml($nameSelectMat,1,false,$matMat,40));
    	//print(selectHtml2($nameSelectMat,1,false,$matMat,$nomMatiere,$idMatiere,"1"));

	?>
	<TD>
	<select name="saisie_prof_<?php print $a?>">
	<option value=0 STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX?></option>
	<?php
	select_personne_2('ENS',40);
	// creation des options
	// optimisation indispensable
	?>
	</select></TD>
	<TD align=center><input type=text value="<?php print $coef?>" size=3 name=saisie_coef_<?php print $a?> ></TD>
	<TD>
    	<?php
    	$nameSelectGrp="saisie_groupe_".$a;
    	print(selectHtml($nameSelectGrp,1,false,$matGroup));
    	//print(selectHtml2($nameSelectGrp,1,false,$matGroup,$nomGrp,$idGrp,"2"));
    	?>
	</TD>
	<TD><select name=saisie_langue_<?php print $a?> >
	<?php if ($nomLang != "") { ?>
		<option STYLE='color:#000066;background-color:#FCE4BA' value="<?php print $idLang?>"><?php print $nomLang?></option>
	<?php }else { ?>
		<option value=0 STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX?></option>
    <?php } ?>
	<option  STYLE='color:#000066;background-color:#CCCCFF'>LV1</option>
	<option  STYLE='color:#000066;background-color:#CCCCFF'>LV2</option>
	</select></TD>
	<td><input type='checkbox' name="saisie_visubull_<?php print $a?>" value='1' /></td>
	<td><input type='checkbox' name="saisie_visubull_btsblanc_<?php print $a?>" value='1' /></td>
	<td><input type='text' name="saisie_nbheure_<?php print $a?>" value="" size=3 /></td>
	<td><input type='text' name="saisie_ects_<?php print $a?>" value="" size=3 /></td>
	<td>
	<select name="ue_<?php print $a ?>" >
	<option value='' STYLE="color:#000066;background-color:#FCE4BA" ><?php print LANGCHOIX ?></option>
	<?php 
	$dataUE = vatel_liste_ueT($semestre,$idclasse,$anneeScolaire); // code_ue,nom_ue,semestre
	for($k=0;$k<count($dataUE);$k++)  {
		if ($dataUE[$k][1] != "") {
			$sem=$dataUE[$k][2];
			if ($dataUE[$k][2] == 0) $sem="1 et 2";
			print "<option value='".$dataUE[$k][0]."' id='select1' title=\"".$dataUE[$k][1]."\" >".trunchaine($dataUE[$k][1],40)."(Sem:$sem</option>";
		}
	}
	?>    
	</select>
	</td>
	<td>
	<select name="specif_<?php print $a ?>" >
	<option value="" id='select0' ></option>
	<option value="etudedecasipac" id='select1' >Etude de cas</option>
	</select>
	</td>
	<td>	
	<select name='info_semestre_<?php print $a?>'>
	<option id='select0' value='0'></option>
	<option id='select1' value='1'>1</option>
	<option id='select1' value='2'>2</option>
	<option id='select1' value='3'>3</option>
	<option id='select1' value='4'>4</option>
	<option id='select1' value='5'>5</option>
	<option id='select1' value='6'>6</option>
	<option id='select1' value='7'>7</option>
	<option id='select1' value='8'>8</option>
	<option id='select1' value='9'>9</option>
	<option id='select1' value='10'>10</option>
	</select>
	</td>
	<td><input type='text' size='2' name="saisie_coef_certif_<?php print $a?>" /></td>	
	<td><input type='text' size='2' name="saisie_note_planche_<?php print $a?>" /></td>	
	</tr>
	<?php
	$a++;
}
?>
</TD></TR></TABLE>
<BR>

<?php 
if ($_SESSION["membre"] == "menuadmin") { ?>
<ul>
<input type='checkbox' name="suppnote" value="oui" > Supprimer les notes scolaires de cette classe. 
<br><br>
Type d'affectation : <select name='saisie_tri' >
<option value='trimestre1' <?php if ($trim == "trimestre1") print "selected='selected'" ?>  id='select1' ><?php print "Trimestre 1 / Semestre 1" ?></option>
<option value='trimestre2' <?php if ($trim == "trimestre2") print "selected='selected'" ?>  id='select1' ><?php print "Trimestre 2 / Semestre 2" ?></option>
<option value='trimestre3' <?php if ($trim == "trimestre3") print "selected='selected'" ?>  id='select1' ><?php print "Trimestre 3" ?></option>
<option value='tous' <?php if ($trim == "tous") print "selected='selected'" ?>  id='select0' ><?php print "Toute l'année" ?></option>
</select><br><br>
</ul>
<?php } ?>


<table width=100% border=0 align=center><tr><td>
<input type=hidden name='saisie_classe_envoi' value="<?php print $_GET["saisie_classe_envoi"]?>" >
<input type=hidden name='saisie_nb_matiere' value="<?php print $a-1?>">
<script language=JavaScript>buttonMagic("<?php print LANGBT20?>","./modifaffect.php","_parent","",";parent.window.close();");</script>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","rien"); //text,nomInput</script>
<?php
if ($_SESSION["membre"] == "menuadmin") { ?>
<script language=JavaScript>buttonMagic("Ajouter une ligne","modifaffect2.php?ligne=1&saisie_classe_envoi=<?php print $cid?>&saisie_tri=<?php print $_GET["saisie_tri"] ?>&anneeScolaire=<?php print $anneeScolaire ?>","_self","",""); //text,nomInput</script>
<?php } ?>
<br><br>
</td></tr></table>

<br>
<i>* Visu. : Visualiser au sein du bulletin / ** Nombre d'heure annuelle / *** Visu. : Visualiser au sein du bulletin AFTEC BTS BLANC </i>
</TD></TR></TABLE>
<!-- // fin  -->
</td></tr></table>
</form>

<!-- verif saisie -->
<script language=JavaScript>

// validation d'un champ de select
function Validselect(item){
 if (item.value == 0) {
        return (false) ;
 }else {
        return (true) ;
        }
}

//fonction de validation d'après la longueur de la chaîne
function ValidLongueur(item,len) {
   drapeau = 1;
   return (item.length >= len);
}

// affiche un message d'alerte
function error5(elem, text) {
// abandon si erreur déjà signalée
   if (flag) return;
   window.alert(text);
   elem.select();
   elem.focus();
   flag = true;
}

// affiche un message d'alerte
function error6(text) {
// abandon si erreur déjà signalée
   if (flag) return;
   window.alert(text);
   flag=true;
}

function valide() {
	flag=false;
	var nbmatiere=<?php print $a?>;
	nbmatiere=nbmatiere * 13;

	for (i=1;i<=nbmatiere;i++) {
		if (!Validselect(document.formulaire.elements[i].options.selectedIndex)) {
	                error6("Indiquez une matière S.V.P \n\n Service Triade");
       		}
		i=i+1;
		if (!Validselect(document.formulaire.elements[i].options.selectedIndex)) {
                	error6("Indiquez un enseignant S.V.P \n\n Service Triade");
       		}
		i=i+1;
		if (!ValidLongueur(document.formulaire.elements[i].value,1)) {
                	error5(document.formulaire.elements[i],"Indiquer le coef de la matière \n\n Service Triade"); }
        	if(isNaN(document.formulaire.elements[i].value)) {
                	error5(document.formulaire.elements[i],"Indiquer une valeur Numérique\n\n ex: 1 ou 1.5 (1 point 5) \n\n Service Triade"); 
		}
		i=i+10;

	}

	return !flag;
}
</script>
<?php } ?>
</BODY></HTML>
