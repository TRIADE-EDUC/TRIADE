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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/recupnoteperiode.php");

validerequete("2");

$cnx=cnx();

$anneeScolaire=$_COOKIE["anneeScolaire"];

if(isset($_POST["choix_trimestre"])) {
	$cgrp=$_POST["sClasseGrp"];
	$cgrp=explode(":",$cgrp);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_POST["sMat"];
	$choix_tri=$_POST["choix_trimestre"];
	$examen=$_POST["examen"];
}else {
	$cgrp=$_GET["sClasseGrp"];
	$cgrp=explode(":",$cgrp);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_GET["sMat"];
	$examen=$_GET["examen"];
	if (VISUTRIAUTO	== "oui")  {
		$choix_tri=recherche_trimestre_en_cours_via_classe($cid,$anneeScolaire);
	}else{
		$choix_tri="trimestre1";
	}
}

$nomClasse=chercheClasse($cid);
$nomClasse=$nomClasse[0][1];
$nomMat=chercheMatiereNom($mid);
$nomGrp=chercheGroupeNom($gid);
$libel=$nomClasse." ".$nomGrp." ".$nomMat;

if ($nomGrp != "") {
	$groupe=1;
}else{
	$groupe=0;
}

// recherche de l'intervalle de date
// creation de la requete
if ($choix_tri != "") {

$data=recherche_intervalle_trimestre_via_classe($choix_tri,$cid,$anneeScolaire);
for($i=0;$i<count($data);$i++){
	$date_debut=$data[$i][0];
	$date_fin=$data[$i][1];
	$sql2="date >= '$date_debut' AND date <= '$date_fin' ";
}

// fin de la creation

$listTmp=explode(":",$cgrp);
unset($HPV['cgrp']);
$HPV['cid']=$cid;
$HPV['gid']=$gid;
unset($listTmp);
//print_r($HPV);
if($HPV['gid']):
        $who="<font color=\"#FFFFFF\">- ".LANGPROF4." : </font> ".chercheGroupeNom($HPV['gid']);
else:
        $cl=chercheClasse($HPV['cid']);
        $who="<font color=\"#FFFFFF\">- ".strtolower(LANGELE4)." : </font>".$cl[0][1];
        unset($cl);
endif;

?>
<HTML>
<HEAD>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<br>
<ul>
<table border=0>
<tr><td>
<form method=POST>
<font class="T2"><?php print LANGPROF5 ?> :</font>
<?php
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

$dateDebut=$date_debut;
$dateFin=$date_fin;
$idgroupe=$gid;
$idMatiere=$mid;
$idclasse=$cid;

?>
<select name="choix_trimestre" onChange="this.form.submit();" >
        <?php if (isset($_POST["choix_trimestre"])) { ?>
        <option value='<?php print $choix_tri?>' id='select0' ><?php print ucfirst($choix_tri_text)?></option>
        <?php }
	if (VISUTRIAUTO != "non") { ?>
	<option value='<?php print $choix_tri?>' STYLE="color:#000066;background-color:#FCE4BA"><?php print ucfirst($choix_tri_text)?></option>
	<?php } ?>
	<option value='trimestre1' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3. " ou ".LANGPROJ19?></option>
	<option value='trimestre2' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4. " ou ".LANGPROJ20?></option>
	<option value='trimestre3' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</select>
<input type=hidden name="sMat" value='<?php print $_GET["sMat"];?>'>
<input type=hidden name="sClasseGrp" value='<?php print $_GET["sClasseGrp"];?>'>
<input type=hidden name="examen" value='<?php print $examen;?>'>
</td><td></tr>
</table>
</form>
</ul>
<br>
<?php

$nomclasse=chercheClasse_nom($cid);
if ( (defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) { 
?>
<center><table><tr><td><?php print LANGVIES6 ?> :</td><td><script language=JavaScript>buttonMagic("<?php print CLICKICI ?>","profpprojo.php?fiche=1&idClasse=<?php print $cid?>","video","width=800,height=700,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","");</script>
</td></tr></table></center>
<?php } ?>
<ul>
<form method="post" name="formulaire" action="noteviescolaire4.php"  onsubmit="return envoi();">

<font class="T2">

<?php
$recupInfo=recupCaractVieScolaire($idclasse);   //idclasse,coefbull,coefprof,coefviescolaire,personnebulletin
$pers=$recupInfo[0][4];
$coefBull=$recupInfo[0][1];
$coefProf=$recupInfo[0][2];
$coefScol=$recupInfo[0][3];
?>

<?php print LANGVIES1 ?> : <input type="text" name="saisie_per" size="15" value="<?php print $pers ?>" > <br /><br />
<?php print LANGVIES2 ?> : <input type="text" name="coef_bulletin"  size="2" value="<?php print $coefBull ?>" ><br /><br />
<?php print LANGVIES3 ?> : <input type="text" name="coef_prof" size="2" value="<?php print $coefProf ?>" > <br /><br /> 
<?php print LANGVIES4 ?> : <input type="text" name="coef_scolaire" size="2" value="<?php print $coefScol ?>" >
</font>
</ul>
<hr width="70%" >



<table border="0" bordercolor="#000000" width="100%" >
<tr><td></td><td align="center"><?php print LANGCARNET24 ?></td><td>Commentaires sur le bulletin</td></tr>
<?php
if($HPV[gid]){
        $gid=$HPV[gid];
        $sqlIn=<<<SQL
        SELECT
        	liste_elev
        FROM
        	${prefixe}groupes
        WHERE
        	group_id='$gid'
SQL;
    $curs=execSql($sqlIn);
    $in=chargeMat($curs);
    freeResult($curs);
    $in=$in[0][0];
	$in=substr($in,1);
	$in=substr($in,0,-1);
	$sql="
        SELECT
        	elev_id,
	";
        if(DBTYPE=='pgsql')
	{
		$sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
	}
	elseif(DBTYPE=='mysql')
	{
		$sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	}
	$sql .= "
        FROM
        	${prefixe}eleves
        WHERE
        	elev_id IN ($in)
		AND annee_scolaire='$anneeScolaire'
        ORDER BY
        	2
	";
		unset($in);
} else {
        $cid=$HPV[cid];

	$sql=" SELECT s.* FROM ( SELECT elev_id,CONCAT(upper(trim(nom)),' ',trim(prenom)),classe FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$cid' AND code_class=classe AND annee_scolaire='$anneeScolaire' AND compte_inactif != 1 UNION ALL SELECT e.elev_id,CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ),e.classe FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY 2";

/*
	$sql="
        SELECT
        	elev_id,
	";
	if(DBTYPE=='pgsql')
	{
        	$sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
        }
	elseif(DBTYPE=='mysql')
	{
        	$sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	}
	$sql .= "
	FROM
        	${prefixe}eleves
        WHERE
        	classe='$cid'
		AND annee_scolaire='$anneeScolaire'
        ORDER BY
        	2
	";
*/
        unset($cid);
}
        $curs=execSql($sql);
        unset($sql);
        $mat=chargeMat($curs);
        freeResult($curs);
        unset($curs);
	   for($i=0;$i<count($mat);$i++){
			$idEleve=$mat[$i][0];
			$note=cherche_note_scolaire_eleve_cpe($idEleve,$idMatiere,$idclasse,$choix_tri,$idgroupe,$examen);
			$noteencours=calculNoteVieScolaire($idEleve,$coefProf,$coefScol,$choix_tri,$examen);
			print "<tr>\n";
			$photoeleve="image_trombi.php?idE=".$idEleve;
			print "<td id='bordure' valign=top >&nbsp;<b><a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".$mat[$i][1]."</a></b> <br /><br />&nbsp;Moyenne en cours: <font color=blue>$noteencours</font> </td>";
			$com=cherche_com_scolaire_eleve_cpe($idEleve,$idMatiere,$idclasse,$choix_tri,$idgroupe);
			$nbtexte=strlen($com);			
			?>
			<td id='bordure' align='center'>
			<?php if (($note < 10.00) && ($note != "")) {
				$note="0".$note;
			}
			?>
			CPE : <input type='text' name='notescol_<?php print $i?>' value="<?php print $note ?>" size='2'><br />
			</td>
			<td><textarea cols="40" rows="3" name='comscol_<?php print $i?>' onkeypress="compter(this,'250', this.form.CharRestant_<?php print $i ?>)" ><?php print $com ?></textarea>
			    <input type=text name='CharRestant_<?php print $i ?>' size='2' disabled='disabled' value='<?php print $nbtexte ?>' />
			    <input type=hidden name="saisie_eleve_<?php print $i?>" value="<?php print $idEleve?>" >
			</td>
		        </tr><tr><td colspan=3 id='bordure' align='center' ><hr></td></tr>
<?php
	   }
?>
<!----------------------------------------------------->


</table><br>
<input type=hidden name="nb" value="<?php print count($mat) ?>" >
<input type=hidden name="saisie_classe" value="<?php print $idclasse ?>" >
<input type=hidden name="anneeScolaire" value="<?php print $anneeScolaire ?>" >
<input type=hidden name="saisie_matiere" value="<?php print $idMatiere ?>" >
<input type=hidden name="choix_trimestre" value="<?php print $choix_tri ?>" >
<input type=hidden name="saisie_groupe" value="<?php print $idgroupe ?>" >
<input type=hidden name="examen" value="<?php print $examen ?>" >
&nbsp;&nbsp;&nbsp;<input type='checkbox' name='valideenvoi' value="oui" /><font class='T2' color='red' > Confirmer l'enregistrement.</font><br> <br>
<script language=JavaScript>buttonMagicSubmit2("<?php print LANGVIES7 ?>","valide","<?php print LANGattente222 ?>"); //text,nomInput</script>
</form>

<br /><br /><br />

<br />


<script language=Javascript>
function envoi() {
	var errfound=false;
<?php
	if (defined("MAXNOTEVIESCOLAIRE")) {
		$notemax=MAXNOTEVIESCOLAIRE;
	}else{
		$notemax='20';
	}
?>
	var notationsur='<?php print $notemax ?>.00';
	var nbnote='<?php print count($mat) * 4 ?>';	
	var a=4;
	for ( a ; a <= nbnote ; a++ ) {
	//	alert(document.formulaire.elements[a].value);
		if  (document.formulaire.elements[a].value.length < 1) {
			document.formulaire.elements[a].select();
			document.formulaire.elements[a].focus();
			document.formulaire.elements[a].value=" ";
			if (document.formulaire.valideenvoi.checked == false) {
				errfound=true;
				break;
			}
			
		}
		if  (document.formulaire.elements[a].value > 0  &&  document.formulaire.elements[a].value > notationsur ) {
			alert(langfunc68+" "+notationsur);
			document.formulaire.elements[a].select();
			document.formulaire.elements[a].focus();
			errfound=true;
			break;
		}

		if (document.formulaire.elements[a].value.indexOf (',') != -1) {
			alert(langfunc69);
			document.formulaire.elements[a].select();
			document.formulaire.elements[a].focus();
			errfound=true;
			break;
		}


	<?php if ( $_SESSION["navigateur"] == "IE" ) { ?>
		if (document.formulaire.elements[a].value.indexOf ('.') != -1) {
			pos=document.formulaire.elements[a].value.indexOf ('.')
			long=document.formulaire.elements[a].value.length;
			total=long - pos ;
			if (total > 3 ) {
				alert(langfunc70);
				document.formulaire.elements[a].select();
				document.formulaire.elements[a].focus();
				errfound=true;
				break;
			}
		}
	<?php } ?>


		if (isNaN(document.formulaire.elements[a].value)) {
			document.formulaire.elements[a].value=" "; 
			if (document.formulaire.valideenvoi.checked == false) {
				document.formulaire.elements[a].select();
				document.formulaire.elements[a].focus();
				errfound=true;
				break;
			}

                }

		a=a+3;



	}

	if (!errfound) {
		if (document.formulaire.valideenvoi.checked) {
			document.formulaire.valide.disabled=true;
			return true;
		}else{
			alert("Merci de confirmer l'enregistrement.");
			document.formulaire.valide.value="<?php print LANGVIES7 ?>";
		}
	}
	document.formulaire.valide.value="<?php print LANGVIES7 ?>";
	return false;
}
</script>

<?php

include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 

}else {
?>
<HTML>
<HEAD>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<br>
<ul>
<table border=0 align='center' >
<tr><td>
<b><center><font class='T2'><?php print LANGPROFP36 ?>.</font></center></b>

</td></tr></table>
<?php
}
Pgclose() ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#CCCCCC","red",1);</SCRIPT>
</BODY>
</html>
