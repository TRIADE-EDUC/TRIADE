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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title>Triade</title>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');

validerequete("profadmin");

include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();
$ok=1;



if (isset($_POST["valide"])) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$ideleve=$_POST["saisie_ideleve"];
	$nb=$_POST["saisie_nb"];
	$idcarnet=$_POST["idcarnet"];

	$evalprogress="";
	$evaldiff="";
	for($i=0;$i<=$nb;$i++) {
		$eval="eval_$i";
		$ideval="ideval_$i";
		$eval=$_POST[$eval];
		$ideval=$_POST[$ideval];
		if ($eval == 1) {
			$evalprogress.="$ideval,";
		}
		if ($eval == 2) {
			$evaldiff.="$ideval,";
		}
		
	}
	$evalprogress="{".$evalprogress."}";
	$evaldiff="{".$evaldiff."}";
	$evalprogress=preg_replace('/,\}/',"}",$evalprogress);
	$evaldiff=preg_replace('/,\}/',"}",$evaldiff);
	insertFicheLiaison($idclasse,$trimes,$evalprogress,$evaldiff,$ideleve);
	history_cmd($_SESSION["nom"],"AJOUT","Fiche Liaison $nomclasse $nommatiere");
	alertJs(LANGDONENR);
}

/***************************************************************************/
if (isset($_GET["MT1"])) {
	$moyenClasseGenT1=$_GET["MT1"];
	$moyenClasseGenT2=$_GET["MT2"];
	$moyenClasseGenT3=$_GET["MT3"];
	$tri=$_GET["saisie_trimestre"];
	$idcarnet=$_GET["idcarnet"];
	
}else{
	$tri=$_POST["saisie_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$idcarnet=$_POST["idcarnet"];
	// recherche des dates de debut et fin
	$dateRecup=recupDateTrim("trimestre1");
	for($j=0;$j<count($dateRecup);$j++) {
       	 	$dateDebut=$dateRecup[$j][0];
	       	 $dateFin=$dateRecup[$j][1];
	}
	$dateDebutT1=dateForm($dateDebut);
	$dateFinT1=dateForm($dateFin);
	//-----/
	$dateRecup=recupDateTrim("trimestre2");
	for($j=0;$j<count($dateRecup);$j++) {
	       	 $dateDebut=$dateRecup[$j][0];
        	$dateFin=$dateRecup[$j][1];	
	}
	$dateDebutT2=dateForm($dateDebut);
	$dateFinT2=dateForm($dateFin);
	//-----/
	$dateRecup=recupDateTrim("trimestre3");
	for($j=0;$j<count($dateRecup);$j++) {
        	$dateDebut=$dateRecup[$j][0];
	        $dateFin=$dateRecup[$j][1];
	}
	$dateDebutT3=dateForm($dateDebut);
	$dateFinT3=dateForm($dateFin);
	//-----/

	$ordre=ordre_matiere($idclasse); // recup ordre matiere
	$eleveT=recupEleve($idclasse); // recup liste eleve

	$moyenClasseGenT1="";
	$moyenClasseGenT2="";
	$moyenClasseGenT3="";

	// idclasse,tableaueleve,datedebut,datefin,ordrematriere
	$moyenClasseGenT1=calculMoyenClasse($idclasse,$eleveT,$dateDebutT1,$dateFinT1,$ordre);
	$moyenClasseGenT2=calculMoyenClasse($idclasse,$eleveT,$dateDebutT2,$dateFinT2,$ordre);
	$moyenClasseGenT3=calculMoyenClasse($idclasse,$eleveT,$dateDebutT3,$dateFinT3,$ordre);
	if (($moyenClasseGenT1 == "") || ($moyenClasseGenT1 < 0)) {$moyenClasseGenT1=""; }
	if (($moyenClasseGenT2 == "") || ($moyenClasseGenT2 < 0))  {$moyenClasseGenT2=""; }
	if (($moyenClasseGenT3 == "") || ($moyenClasseGenT3 < 0)) {$moyenClasseGenT3=""; }
}




// Fin du Calcul moyenne classe
//
/*****************************************************************************/

// via precendent ou suivant
if (isset($_GET["apres"])) {
	$i=$_GET["apres"];
	$trimes=$_GET["saisie_trimestre"];
	$idclasse=$_GET["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	if ($i <= 0) {$i=0;}
	$ideleve=$data_eleve[$i][1];
	$ok=0;
	$iplus= $i + 1;
	$imoins = $i - 1;
}

// en direct avec le select
if (isset($_POST["direct_eleve"])) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$ok=0;
	$ideleve=$_POST["direct_eleve"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<count($data_eleve);$j++) {
		if ($ideleve == $data_eleve[$j][1]) {
			$i=$j;
			break;
		}
	}
	$iplus= $i + 1;
	$imoins = $i - 1;
}

// premier acces
if ($ok == 1) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$idcarnet=$_POST["idcarnet"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	$ideleve=$data_eleve[0][1];
	$i=0;
	$iplus = $i + 1;
	$imoins = $i - 1;
}
//-----------------------------------------------//

$dateRecup=recupDateTrim($tri);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

?>
<table border="0" width="100%" align="center"  height="100%">
<tr>
<td colspan=2 valign=top height=10 width=100%>
	<table width=100% border=0 ><tr>
	<td valign=top >
	<form method=post onsubmit="return valide_supp_choix('direct_eleve','un élève')" name=formulaire>
	<input type=button class=BUTTON value="<-- Précédent" onclick="open('carnet_liaison.php?idcarnet=<?php print $idcarnet ?>&apres=<?php print $imoins?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&MT1=<?php print $moyenClasseGenT1?>&MT2=<?php print $moyenClasseGenT2?>&MT3=<?php print $moyenClasseGenT3?>','_parent','')">
	</td><td width=200 valign="top"><?php 
	include_once("./librairie_php/lib_conexpersistant.php"); 
	connexpersistance("font-weight:bold;font-size:11px;text-align: center;"); 
	?>
	</td>
        <td align=center valign=top >
<!--	<input type=text  value="<?php print ucwords($trimes)?>" size=10 class=BUTTON readonly> -->
	&nbsp;&nbsp;&nbsp;
	<input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
	<input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
	<select name="direct_eleve">
	<option > <?php print LANGCHOIX?> </option>
	<?php
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<count($data_eleve);$j++) {
	?>
	<option STYLE='color:#000066;background-color:#CCCCFF'  value="<?php print $data_eleve[$j][1]?>"><?php print ucwords(trim($data_eleve[$j][2]))." ".trim($data_eleve[$j][3])?></option>
	<?php
	}
	?>
	</select> <input type=submit class=BUTTON value="Visualiser" >
	</td>
	<td align=right valign=middle ><input type=button class=BUTTON value="Suivant -->" onclick="open('carnet_liaison.php?idcarnet=<?php print $idcarnet ?>&apres=<?php print $iplus ?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&MT1=<?php print $moyenClasseGenT1?>&MT2=<?php print $moyenClasseGenT2?>&MT3=<?php print $moyenClasseGenT3?>','_parent','')">
	</form>
	</td></tr></table>
</td></tr>
<tr><td valign=top height=10 >
<font class=T2>
<?php
$sql="SELECT  elev_id,nom,prenom,c.libelle,lv1,lv2,`option`,regime,date_naissance,numero_eleve  FROM ${prefixe}eleves, ${prefixe}classes c WHERE elev_id='$ideleve' AND c.code_class='$idclasse'";
$res=execSql($sql);
$data=chargeMat($res);
if( count($data)  <= 0 ) {
	print("<b><font color=red>Données introuvables</font></b>");
}else { //debut else
	?>
	<img src="image_trombi.php?idE=<?php print $ideleve?>" align=left>
	<font class=T2 >Nom : <b><?php print strtoupper(trim($data[0][1]))?></b><br>
	Prénom : <b><?php print ucwords(trim($data[0][2]))?></b> <br>
	Age : <b><?php print dateForm($data[0][8])?>&nbsp;&nbsp;</b>(<?php print calculAge(dateForm($data[0][8])) ?> ans)</font> 
	<?php
}

?>
<br><br>
Moyenne de la classe : <b><?php print $moyenClasseGenT1 ?></b> (Premier Trimestre) /
<b><?php print $moyenClasseGenT2 ?></b> (Deuxième Trimestre) /
<b><?php print $moyenClasseGenT3 ?></b> (Troisième Trimestre) 


</font></td></tr>
<tr><td valign=top  ><br>
<form method=post name="form" >
<table border=1 >
<?php // ---------------------------------------------------------- 
print "<tr><td bgcolor='yellow'><font class=T2>&nbsp;Domaines ou disciplines scolaires&nbsp;</font></td>\n";
print "<td bgcolor='yellow'><font class=T2>&nbsp;L'élève montre de l'intérêt et à progressé&nbsp;</font></td>";
print "<td bgcolor='yellow'><font class=T2>&nbsp;L'élève rencontre des difficultés scolaires&nbsp;</td>";
print "</tr>";
include_once('librairie_php/recupnoteperiode.php');
$data=ListeCompletDiscipline($idcarnet); //id,libelle,bold,ordre,idcompetence
$j=0;

$data3=consultFicheLiaisonDomain($ideleve,$idclasse,$trimes);//dom_progress,dom_difficulte,com_suj_aide,eleve_viescolaire,eleve_travscolaire,conclusion_prof,conclusion_dir
$dom_progress=$data3[0][0];
$dom_difficulte=$data3[0][1];

$dom_difficulte=preg_replace('/\{/',"",$dom_difficulte);
$dom_difficulte=preg_replace('/\}/',"",$dom_difficulte);
$tab_dom_difficulte=explode(",",$dom_difficulte);

$dom_progress=preg_replace('/\{/',"",$dom_progress);
$dom_progress=preg_replace('/\}/',"",$dom_progress);
$tab_dom_progress=explode(",",$dom_progress);
 
for($i=0;$i<count($data);$i++) {
	$iddescriptif=$data[$i][0];
	$libelle=$data[$i][1];
	$bold=$data[$i][2];
	$checkprogress="";
	$checkdifficute="";


 
	

	if ($bold) {

		foreach($tab_dom_difficulte as $key=>$value) {
			if ($iddescriptif == $value) {
				$checkdifficute="checked='checked'";
				break;
			}
		}

		foreach($tab_dom_progress as $key=>$value) {
			if ($iddescriptif == $value) {
				$checkprogress="checked='checked'";
				break;
			}
		}
		

		$j++;
		print "<tr class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\"  >\n";
		print "<td>$libelle<input type='hidden' name='ideval_$j' value='$iddescriptif' /></td>\n";
		print "<td align='center'><input type=radio name='eval_$j' value='1' $checkprogress /></td>";
		print "<td align='center'><input type=radio name='eval_$j' value='2' $checkdifficute /></td>";
		print "</tr>\n";
	}
}
?>
<tr><td colspan='3' id="bordure"><br /><br />
<input type=hidden name='saisie_nb' value='<?php print $j?>' >
<input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
<input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
<input type=hidden name="saisie_ideleve" value="<?php print $ideleve?>">
<input type=hidden name="direct_eleve" value="<?php print $ideleve?>">
<input type=hidden name="idcarnet" value="<?php print $idcarnet?>">
&nbsp;&nbsp;<input type=submit value="Enregistrer la fiche de liaison" class="bouton2" name="valide" onclick="this.value='Veuillez patientez'">
</td></tr>
</table>
</form>
</tr></td>

</td></tr></table>
<?php Pgclose(); ?>
</body>
</html>
