<?php
session_start();
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
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
include_once("common/config.inc.php"); // futur : auto_prepend_file
include_once("librairie_php/db_triade.php");
$cnx=cnx();

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
// données DB utiles pour cette page
$mySessionSpid=$mySession[Spid];
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
	code_prof='$mySessionSpid'
AND a.code_classe = c.code_class
AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id
AND a.visubull != '0'
AND c.offline = '0'
ORDER BY
	c.libelle,m.libelle
";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array()); // nécessaire pour compatibilité
// patch pour problème sous-matière à 0
for($i=0;$i<count($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=$tmp[0].' '.$tmp[1];
}
// fin patch
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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<?php 
genMatJs('affectation',$data);
$choixmatiere='1';
if (defined("CHOIXMATIEREPROF")) {
	$choixmatiere=CHOIXMATIEREPROF;
}
if (trim($choixmatiere) == "") { $choixmatiere='1'; }
?>
<script type="text/javascript">
function upSelectMat(arg) {
	for(i=1;i<document.formulaire.sMat.options.length;i++){
		document.formulaire.sMat.options[i].value='';
		document.formulaire.sMat.options[i].text='';
	}
	var tmp=arg.value.split(":");
	var clas=tmp[0];
	var grp=tmp[1];
	var opt=1;
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
	var opt=1;
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
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFB1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // fin  -->

<form method="post" onsubmit="return verifAccesNote5()" name="formulaire5" action="bulletin_comm_classe2.php">
<br />
<ul>
<b><font class='T2'><?php print "Commentaire bulletin de la classe" ?></font></b><br><br><br>
<font class="T2"><?php print LANGBULL29 ?> :</font>
                 <select name='anneeScolaire' >
                 <?php
                 filtreAnneeScolaireSelectNote($_COOKIE["anneeScolaire"],3);
                 ?>
                 </select>
                 <br><br>

<font class="T2"><?php print LANGPROFG ?> :</font>
<select name="sClasseGrp" size="1" onChange="upSelectMat(this)">
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?> </option>
 <?php
	$dataList=$data;
	 for($i=1;$i<count($data);$i++){
	 	if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
			continue;
		}
		else {
			// utilisation de l'opérateur ternaire expr1?expr2:expr3;
			$libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
			print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".$libelle."</option>\n";
		}
		$gtmp=$data[$i][4];
		$ctmp=$data[$i][0];
	 }
	 unset($gtmp);
	 unset($ctmp);
	 unset($libelle);
 ?>
</select>
<br /><br />
<font class='T2'>
<?php print LANGPROF5 ?> :
<?php
$choix_tri=recherche_trimestre_en_cours_via_classe($cid);

$choix_tri_text=$choix_tri;
//$tri=recherche_intervalle_trimestre($choix_tri_text);

if ($choix_tri_text == "trimestre1") {
	$choix_tri_text=LANGPROJ3. " ou ".LANGPROJ19;
}
if ($choix_tri_text == "trimestre2") {
    $choix_tri_text=LANGPROJ4. " ou ".LANGPROJ20;
}
if ($choix_tri_text == "trimestre3") {
    $choix_tri_text=LANGPROJ5;
}
?>
<select name="choix_trimestre">
	<option value='<?php print $choix_tri?>' STYLE="color:#000066;background-color:#FCE4BA"><?php print ucfirst($choix_tri_text)?></option>
	<option value='trimestre1' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3. " ou ".LANGPROJ19?></option>
	<option value='trimestre2' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4. " ou ".LANGPROJ20?></option>
	<option value='trimestre3' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</select>
</font>

<br><BR><BR>
<UL><UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script>
<br><br>
</UL></UL></UL></UL>
</form>

<br><br>
     <!-- // fin  -->
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
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
