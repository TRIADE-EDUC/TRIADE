<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./ckeditor/ckeditor2.js"></script>
<script >var panelWidth = 350;</script>
<script language="JavaScript" src="./librairie_js/lib_aide.js"></script>
<script type="text/javascript">
function _(el) {
	return document.getElementById(el);
}
</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style type="text/css">
#dhtmlgoodies_leftPanel{	/* Styling the help panel */	
	background-color:#CCCCCC;	/* Blue background color */
	color:#000000;	/* White text color */
	/* You shouldn't change these 5 options unless you need to */		
	height:100%;		
	left:0px;
	z-index:10;
	position:absolute;
	display:none;
	padding:0px;
}
</style>

</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="initDocument()" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Convention de stage" ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >

<div id="dhtmlgoodies_leftPanel">	
	<!-- This is the content -->

<img src="image/commun/info2.gif" align=left>
<font class=T1><b><?php print LANGAIDE2 ?></b> <br>
<br><table border=1 bgcolor="#FFFFFF" width="100%" style="border-collapse: collapse;" ><tr><td valign=top>
Nom de l'élève=> NomEleve<br>
Prénom de l'élève=> PrenomEleve<br>
Classe de l'élève=> classe_eleve<br>
Date naissance=> naissance_eleve<br>
Lieu naissance=> lieu_naissance<br>
Adresse élève=> adresse_eleve <br>
Code postal=> ccp_eleve<br>
Ville=> ville_eleve<br>
tél élève=> tel_eleve <br>
tél parent=> tel_parent <br>
année scolaire=> anneescolaire <br>
</td><td valign=top width='33%'>
Nom Entreprise=> ent_nom <br>
Adresse entreprise=> ent_adr<br>
Localité entreprise=> ent_localite <br>
CP entreprise=> ent_cp <br>
Pays entreprise=> ent_pays <br>
Tel entreprise=> ent_tel <br>
Fax entreprise=> ent_fax<br>
Directeur entreprise=> ent_directeur <br>
Fonction du responsable=> ent_dir_fonction<br>
Tuteur de stage=> ent_tuteur<br>
Mail entreprise=> ent_mail <br>
Web entreprise=> ent_web <br>
Logement=> ent_logement <br>
Nbr d'étoile=> ent_etoile <br>
Nbr chambre=> ent_chambre <br>
Groupe hôtelier=> ent_grp_hotelier<br>
</td><td valign=top>
Nom du stage=> nom_stage<br>
Nom du directeur=> directeur <br>
Enseignant suivi 1=> enseignant_suivi_1<br>
date suivi 1=> date_suivi_1<br>
Enseignant suivi 2=> enseignant_suivi_2<br>
date suivi 2=> date_suivi_2<br>
Date du jour=> date_du_jour <br>
Nombre de jour=> nb_jour <br>
Du xx/xx/xx au xx/xx/xx=> periode_n <br>
Date début période => debperio_n<br>
Date fin période => finperio_n<br>
&nbsp;&nbsp;=>(<i>n valeur en 1 à 9 </i>)<br>
Affiche la seule periode => periode_x&nbsp;&nbsp;<br>
Date début période => debperio_x<br>
Date fin période => finperio_x<br>
Intitulé du service=>ent_service <br>
Indemnité stage=>ent_indemnitestage <br>
</td>

</tr></table>
<!-- End content -->
</div>
<!-- End code for the left panel -->
<?php
if (isset($_POST["saisie_classe"])) {
	$idclasse=$_POST["saisie_classe"];
}else{
	$idclasse="0";
}
?>
<br>
<script language=JavaScript>buttonMagic3("<?php print LANGAIDE ?>","initSlideLeftPanel();return false"); //text,nomInput</script>
<form method="post" action="gestion_stage_param_convention.php" >
&nbsp;<font class='T2'>Visualisation de la classe : <select name="saisie_classe" onChange="this.form.submit()" > 
<?php 
if ($idclasse == 0) {
	print "<option value='0' id=select0 >Toutes les classes </option>";
}else{
	print "<option value='$idclasse' id='select0' >".chercheClasse_nom($idclasse)."</option>";
	print "<option value='0' id='select1' >Toutes les classes </option>";
}
select_classe();
?>
</select>

&nbsp;&nbsp;Convention N° : <select name='nbconv' onChange="this.form.submit()" >
<?php 
if (trim($_POST["nbconv"]) != "") {
	$nbconv=$_POST["nbconv"];
	print "<option value='$nbconv' id=select0 >";
	$nbconv=preg_replace('/_conv_/','',$nbconv);
	print "$nbconv</option>";
}
print "<option></option><option value='_conv_A' id='select1' >A</option><option value='_conv_B' id='select1' >B</option><option id='select1' value='_conv_C'>C</option></select>";
 
$fichier1="./data/parametrage/courrier_stageconvention_$idclasse.rtf";
if (file_exists($fichier1)) { ?>
<font class='T1'>[<a href='#' onclick="open('telecharger.php?fichiername=convention_stage.rtf&fichier=<?php print $fichier1?>','_blank','')" ><i>Fichier en cours RTF</i></a>]</font>
<?php } ?>
</form>
<br>
<?php

if (isset($_POST["create"])) {
	if ($_FILES['rtf']['name'] != "") {
		$fichier=$_FILES['rtf']['name'];
		$type=$_FILES['rtf']['type'];
		$tmp_name=$_FILES['rtf']['tmp_name'];
		$size=$_FILES['rtf']['size'];
		//alertJs($type);
		$nbconv=$_POST["nbconv"];
		if ( (!empty($fichier)) &&  ($size <= 8000000)) {
			if  (($type == "application/octet-stream") || ($type == "application/msword")  || ($type == "application/rtf") || (preg_match('/.rtf$/i',$fichier)) )  {
				@unlink("./data/parametrage/courrier_stageconvention_$idclasse$nbconv.rtf");
				move_uploaded_file($tmp_name,"./data/parametrage/courrier_stageconvention_$idclasse$nbconv.rtf");
				print "<BR><center><font class='T2'>"."Fichier enregistré"."</font></center>";
				config_param_ajout_classe("{FICHIERRTFSTAGE}","param_conv_stag#$idclasse$nbconv","$idclasse");
				$texte="{FICHIERRTFSTAGE}";
				if ($idclasse == 0) {
					$dataclasse=affClasse(); // code_class,libelle,desclong
					for ($i=0;$i<count($dataclasse);$i++) {
						$idclasse=$dataclasse[$i][0];
						config_param_ajout_classe($texte,"param_conv_stag#$idclasse$nbconv","$idclasse");
						copy("./data/parametrage/courrier_stageconvention_0.rtf","./data/parametrage/courrier_stageconvention_$idclasse$nbconv.rtf");
					}
				}	
			}
		}
	}else{
		$nbconv=$_POST["nbconv"];
		$texte=$_POST["suite"];
		$texte=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#',' ',$texte);
		$texte=preg_replace('/\\\"/','"',$texte);
		$texte=preg_replace("/\\\'/","'",$texte);
		$texte=stripslashes($texte);
		$texte=stripslashes($texte);
		//--------------------
		config_param_ajout_classe($_POST["suite"],"param_conv_stag#$idclasse$nbconv","$idclasse");
		if ($idclasse == 0) {
			$dataclasse=affClasse(); // code_class,libelle,desclong
			for ($i=0;$i<count($dataclasse);$i++) {
				$idclasse=$dataclasse[$i][0];
				config_param_ajout_classe($_POST["suite"],"param_conv_stag#$idclasse$nbconv","$idclasse");
			}
		}	
	}


	

?>
<br>
<font class=T2><b><?php print "La convention est enregistrée."  ?></b></font>
<br><br>
<font class=T1><?php print LANGCONFIG2 ?></font>:<br><br>
<table width="100%" height="200" bgcolor="#FFFFFF" border=1 cellpadding="5" style="border-collapse: collapse;" >
<tr><td valign=top>
<?php 
if ($texte != "{FICHIERRTFSTAGE}") { 
	print $texte ;
}else{
	print "<i>Fichier 'rtf' comme matrice de donnée</i>";
}
?>
</td></tr></table>
<br><br>
<script language=JavaScript>buttonMagicFermeture();</script>
<br><br><br>
<?php
}else{
	$nbconv=$_POST["nbconv"];
	$data=config_param_visu("param_conv_stag#$idclasse$nbconv");
	
	if ($texte != "{FICHIERRTFSTAGE}") { 
		$texte=$data[0][0]; ;
	}else{
		$texte="<i>Fichier 'rtf' comme matrice de donnée</i>";
	}
	
	$texte=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#',' ',$texte);

?>
<br>
<form method=post ENCTYPE="multipart/form-data"><br>
<textarea id="editor2" style="height: 48em; width: 100%;" name="suite"><?php print $texte ?>
</textarea><br><br>
<script type="text/javascript">var colorGRAPH='<?php print $GRAPH ?>';
//<![CDATA[
CKEDITOR.replace('editor2', {height:'399px',language:'<?php print ($_SESSION["langue"] == "fr") ? "fr":"en";?>',scayt_autoStartup:true,grayt_autoStartup:true,scayt_maxSuggestions:3,scayt_sLang:'en_FR',removeButtons:'PasteFromWord' });
//]]>
</script>

&nbsp;<font class='T2'>Classe : <select name="saisie_classe" > 
<?php 
if ($idclasse == 0) {
	print "<option value='0' id=select0 >Toutes les classes </option>";
}else{
	print "<option value='$idclasse' id='select0' >".chercheClasse_nom($idclasse)."</option>";
	print "<option value='0' id='select1' >Toutes les classes </option>";
}
select_classe();
?>
</select>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <font class='T1'>[<a href='./librairie_php/courrier_stageconvention.rtf' target='_blank' ><i>Fichier exemple RTF</i></a>]</font> 

&nbsp;&nbsp;&nbsp;Convention num&eacute;ro : <select name='nbconv' ><option></option><option value='_conv_A' id='select1' >A</option><option value='_conv_B' id='select1' >B</option><option id='select1' value='_conv_C'>C</option></select>
<br><br>

<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT19?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>
<input type="file" name="rtf" /> (Fichier matrice au format "rtf", max 8Mo)
</form>
<br><br>
<?php } ?>
</td></tr></table>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
