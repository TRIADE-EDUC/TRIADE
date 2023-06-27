<?php
session_start();
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
 ***************************************************************************/
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/recupnoteperiode.php");
if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel"))  {
	validerequete("3");
}else{
	if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menuprof")) {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
			accesNonReserveFen();
			exit();
		}
		Pgclose();
	}else{
		validerequete("profadmin");
	}
}
$cnx=cnx();

if(isset($_POST["create"])) {
	$cgrp=$_POST["sClasseGrp"];
	$cgrp=explode(":",$cgrp);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_POST["sMat"];
	$choix_tri=$_POST["choix_trimestre"];

}else {
	$cgrp=$_GET["sClasseGrp"];
	$cgrp=explode(":",$cgrp);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_GET[sMat];
	if (VISUTRIAUTO == "oui")  {
                $choix_tri=recherche_trimestre_en_cours_via_classe($cid);
        }else{
                $choix_tri="trimestre1";
        }
}

$anneeScolaire=$_COOKIE["anneeScolaire"];

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
	        $cl=chercheClasse($HPV[cid]);
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
<?php include_once("./librairie_php/lib_licence.php"); ?>
<br>
<ul>
<table border=0>
<tr><td>
<form method='post' >
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


$idprof=$_SESSION["id_pers"];
$adminIdprof=$_GET["idad"];

if (isset($_POST["adminIdprof"])) { $adminIdprof=$_GET["adminIdprof"]; }

if ($adminIdprof != "") { $idprof=$adminIdprof; }


?>
<select name="choix_trimestre">
	<?php if (VISUTRIAUTO != "non") { ?>
		<option value='<?php print $choix_tri?>' STYLE="color:#000066;background-color:#FCE4BA"><?php print ucfirst($choix_tri_text)?></option>
	<?php } ?>
	<option value='trimestre1' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3. " ou ".LANGPROJ19?></option>
	<option value='trimestre2' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4. " ou ".LANGPROJ20?></option>
	<option value='trimestre3' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</select>
<input type=hidden name="sMat" value='<?php print $_GET["sMat"]; ?>' />
<input type=hidden name="sClasseGrp" value='<?php print $_GET["sClasseGrp"]; ?>' />
<input type=hidden name="anneeScolaire" value='<?php print $anneeScolaire ?>' />
</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGOK ?>","create"); //text,nomInput</script>
</td><td>
/ <?php print LANGBULL29 ?> : <?php print $anneeScolaire ?> 
</td><td valign='top'>&nbsp;&nbsp;
<?php
$fichier="./data/pdf_bull/edition_".$_SESSION["id_pers"].".pdf";
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<div id="imp" style="visibility:hidden" >
<a href="visu_pdf_prof.php?id=<?php print $fichier?>" target="_blank"><img src="image/commun/print.gif" border=0 align=center></a>
</div>
</td></tr></table>
</form>
</ul>
<br>
<?php
$nomclasse=chercheClasse_nom($cid);
if ( (defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) { 
?>
<center><table><tr><td>Information Scolaire Complémentaire :</td><td><script language=JavaScript>buttonMagic("cliquez-ici","profpprojo.php?fiche=1&idClasse=<?php print $cid?>","video","width=800,height=700,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","");</script>
</td></tr></table></center>
<?php } ?>




<script language=Javascript>
var deja=0;
function envoi() {
	document.formulaire.valide.disabled=true;
	return true;
}
</script>
<form method=post name=formulaire action="noteajoutviescolaire4.php"  onsubmit="return envoi();">
<ul>
<table  border=0 bordercolor="#000000" >
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
        ORDER BY
        	2
	";
		unset($in);
} else {
        $cid=$HPV[cid];
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
        ORDER BY
        	2
	";
        unset($cid);
}
        $curs=execSql($sql);
        unset($sql);
        $mat=chargeMat($curs);
        freeResult($curs);
	unset($curs);



	$imp=0;
	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/lib.php');
	$pdf=new RPDF();
	$pdf->AddPage();
	$pdf->SetTitle("Releve - $nomclasse");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Releve - $nomclasse"); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
	$xcoor=20;
	$ycoor=20;
	
	$texte="Note vie scolaire de : $nomClasse du $choix_tri_text / Année scolaire : $anneeScolaire ";
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor,$ycoor);
	$pdf->Write(5,$texte);
	$pdf->SetFillColor(220);
	$ycoor+=7;

	for($i=0;$i<count($mat);$i++){
			$pdf->SetXY($xcoor,$ycoor); // placement du cadre du nom de l eleve
			$nomprenom=trunchaine($mat[$i][1],20);
			$pdf->MultiCell(60,5,"$nomprenom",1,'',1);
			$pdf->SetXY($xcoor+65,$ycoor);
			$idEleve=$mat[$i][0];
			$note=cherche_note_scolaire_eleve($idEleve,$idMatiere,$idclasse,$choix_tri,$idprof,$idgroupe,'');
			if ($note != "") { $imp=1; }
			print "<tr>\n";
			$photoeleve="image_trombi.php?idE=".$idEleve;
			print "<td id='bordure' valign=top>&nbsp;<b><a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".$mat[$i][1]."</a></b> </td>";			
			?>
			<td id='bordure' valign=top>
			<input type='text' name='notescol_<?php print $i?>' value="<?php print $note ?>" size='2'><br>
			<input type='hidden' name="saisie_eleve_<?php print $i?>" value="<?php print $idEleve?>" >
			</td>
		        </tr><tr><td colspan=2 id='bordure'><hr></td></tr>
<?php
			$pdf->MultiCell(15,5,"$note",1,'',0);
			$ycoor+=5;

	   }
	if ($imp == 1) {
		print "<script>document.getElementById('imp').style.visibility='visible';</script>";	
	}

@unlink($fichier);
$pdf->output('F',$fichier);

?>
<!----------------------------------------------------->


</table><br>
<input type=hidden name=nb value="<?php print count($mat) ?>" >
<input type=hidden name="saisie_classe" value="<?php print $idclasse ?>" >
<input type=hidden name="anneeScolaire" value="<?php print $anneeScolaire ?>" >
<input type=hidden name="saisie_matiere" value="<?php print $idMatiere ?>" >
<input type=hidden name="choix_trimestre" value="<?php print $choix_tri ?>" >
<input type="hidden" name="adminIdprof" value="<?php print $adminIdprof ?>" />
<input type=hidden name="saisie_groupe" value="<?php print $idgroupe ?>" >
<script language=JavaScript>buttonMagicSubmit2("<?php print "Enregistrer les notes" ?>","valide","Patientez S.V.P."); //text,nomInput</script>
</form>
</ul>
<br /><br /><br />
<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
?>
<br />

<?php
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
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<br>
<b><center><font class='T2'><?php print LANGPROFP36 ?>.</font></center></b>
<?php
}
Pgclose() ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#CCCCCC","red",1);</SCRIPT>
</BODY>
</html>
