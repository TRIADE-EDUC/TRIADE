<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                : http://www.triade-educ.com
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
$anneeScolaire=$_POST["anneeScolaire"];
setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);

include_once("./librairie_php/lib_error.php");
include("common/config.inc.php"); // futur : auto_prepend_file
include("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
//variables utiles
$gid="non";

$mySession[Sn]=$_SESSION["nom"];
$mySession[Sp]=$_SESSION["prenom"];
$pid=$_SESSION["id_pers"];
$cgrp=$_POST["sClasseGrp"];
$cgrp=explode(":",$cgrp);
$cid=$cgrp[0];
$idClasse=$cgrp[0];
$gid=$cgrp[1];
$mid=$_POST[sMat];

if ($_POST["adminIdprof"] != "") { $pid=$_POST["adminIdprof"]; }


$nomClasse=chercheClasse($cid);
$nomClasse=$nomClasse[0][1];
$nomMat=chercheMatiereNom($mid);
$nomGrp=chercheGroupeNom($gid);
$libel=$nomClasse." ".$nomGrp." ".$nomMat;
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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD>
<td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="400">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF23 ?></font>  </b><font id="color2" > <?php print $libel?></font></td></tr>
<tr id='cadreCentral0'>
<td valign=top>

<?php 
if ($_SESSION["membre"] == "menuadmin") {
	print "<br /><form method='post' action='notevisuadmin.php' target='_parent' >";
	print "<div><script language='JavaScript'>buttonMagicSubmitAtt('Retour menu principal','create','');</script></div>";
	print "<input type='hidden' name='saisie_pers' value='".$_POST["adminIdprof"]."' /></form><br><br>";
} 
?>

<?php
$valeur=affDateTrimByIdclasse('',$cid,$anneeScolaire);
if (count($valeur)) {


// verif si eleve dans la classe
//$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$cid' AND code_class='$cid' ";
$sql="(SELECT e.elev_id, e.nom,e.prenom,e.classe  FROM ${prefixe}eleves e WHERE e.classe='$cid' AND e.annee_scolaire='$anneeScolaire' ) UNION (SELECT e.elev_id,e.nom,e.prenom,e.classe FROM ${prefixe}eleves e , ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve AND  h.annee_scolaire='$anneeScolaire'  group by elev_id ) $order";

$res=execSql($sql);
$verifdata=chargeMat($res);
if( count($verifdata) <= 0 )  {
	print("<br><br><br><center><font class=T1>".LANGRECH1."</font></center>");
}else {


// fournit les elev_id d'un grp donné
// sous forme d'une string avec la virgule
// comme séparateur
function ElevesDsGrp($gid,$prefixe,$anneeScolaire){
    $sqlIn="
    SELECT
    	liste_elev
    FROM
    	${prefixe}groupes
    WHERE
    	group_id='$gid' AND annee_scolaire='$anneeScolaire'
    ";
    $curs=execSql($sqlIn);
    $in=chargeMat($curs);
    $in=$in[0][0];
    $in=substr($in,1);
    $in=substr($in,0,-1);
    return $in;
}


if($gid){
	$in=ElevesDsGrp($gid,$prefixe,$anneeScolaire);
    	$sqlgroupe=" AND id_groupe='$gid' " ;
} else {
    //$cid
/*    $sql="
    SELECT
	elev_id
    FROM
	${prefixe}eleves
    WHERE
	classe='$cid'
    "; */
	$sql="(SELECT e.elev_id, e.nom,e.prenom,e.classe  FROM ${prefixe}eleves e WHERE e.classe='$cid' AND e.annee_scolaire='$anneeScolaire' ) UNION (SELECT e.elev_id,e.nom,e.prenom,e.classe FROM ${prefixe}eleves e , ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve  AND  h.annee_scolaire='$anneeScolaire' group by elev_id ) $order";

    // utilisation d'une liste de valeurs plutôt
    // que sous-req pour compatibilité avec MySQL < 4.1
    // mettre dans une transaction pour plus de fiabilité
    $curs=execSql($sql);
    $data = chargeMat($curs);
    for($i=0;$i<count($data);$i++)
    {
	$in[$i] = $data[$i][0];
    }
    $in = join($in,",");
}


$date=recupDateTrimIdclasse($cid,$anneeScolaire);
// date_debut,date_fin,trim_choix,idclasse
for($p=0;$p<count($date);$p++) {
        $tri=$date[$p][2];
        if ($tri == "trimestre1") $dateDebut=$date[$p][0];
        if ($tri == "trimestre2") $dateFin=$date[$p][1];
        if ($tri == "trimestre3") $dateFin=$date[$p][1];
}



if ($in != "") {
$sql="
SELECT
	sujet,
";
if(DBTYPE=='pgsql')
{
	$sql .= " to_char(date,'dd/mm/YYYY'), ";
}
elseif(DBTYPE=='mysql')
{
	$sql .= " DATE_FORMAT(date,'%d/%m/%Y'), ";
}
$sql .= "
	coef,noteexam
FROM
	${prefixe}notes
WHERE
	elev_id IN ($in)
AND prof_id='$pid'
AND code_mat='$mid'
AND date >= '$dateDebut' AND date <= '$dateFin' 
";
if ($sqlgroupe != "") {
	$sql.=" $sqlgroupe";
//}else{
//	$sql.="AND id_classe='$cid' ";
}
$sql.="
GROUP BY
	date,coef,sujet
ORDER BY
        date DESC
";
$curs=execSql($sql);
$mat=chargeMat($curs);
}

echo "<table width='100%' border='1'  style='border-collapse: collapse;' >";
echo "<tr>";
echo "<td bgcolor='yellow' align='center'>".LANGPROF16."</td>";
echo "<td bgcolor='yellow' align='center'>".Examen."</td>";
echo "<td bgcolor='yellow' align='center' width='10%'>".LANGPROF17."</td>";
echo "<td bgcolor='yellow' align='center' width='5%'>&nbsp;".LANGPER19."&nbsp;</td>";
echo "<td bgcolor='yellow'  align='center' width='5%'>".LANGBT50."</td>";
echo "</tr>";

	if ($gid > 0) { $cid="-1"; };
        for($i=0;$i<count($mat);$i++){
		$suj="";
		//if (preg_match('/"/',$mat[$i][0])) {
		$suj=urlencode($mat[$i][0]);
		$sujet=$mat[$i][0];
		//}
		$examen=$mat[$i][3];
		$coef=$mat[$i][2];

		array_push($mat[$i],$in,$mid,$pid);	
		$datedevoir=$mat[$i][1];
		$datedujour=dateDMY();
		$TD=recupTrimestreDevoir($cid,$datedevoir); // return 1,2,3 ou 0
		$TT=recupTrimestreDevoir($cid,$datedujour); // return 1,2,3 ou 0
		if ($_SESSION["membre"] == "menuadmin") $TT=0;
		if (MODIFNOTEAPRESARRET == "oui") $TT=0;
		if ($TT > $TD) {
			$lien ="<input type=button value='".LANGBT50."' STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\" onclick='alert(\"IMPOSSIBLE !! \\n\\nTrimestre déjà passé.\")' >";
		}else{
			$lien  = '<input type=button onclick="open(\'notesupp3.php?pid='.$pid.'&args=';
        	$lien .= urlencode(serialize($mat[$i])).'&libel='.urlencode($libel).'&gid='.$gid.'&idClasse='.$idClasse.'&sujet='.$suj.'\',\'_parent\',\'\');this.value=\''.LANGPROF18.'\'" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" value="'.LANGBT50.'">';
		}
		echo "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		echo "<td >".$sujet."</td>";
		echo "<td >".$examen."</td>";
		echo "<td align='center' >".$datedevoir."</td>";
		echo "<td >".$coef.".</td>";
		echo "<td >".$lien."</td>";
		echo "</tr>";
        }
	echo "</table>";
        ?>
<?php

}


}else {
?>
<iframe  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0  scrolling=no name=suppnote"" src="visunoteprofnon.php" width=100% height=100%></iframe>
<?php } ?>

     <!-- // fin  -->
     </td>
	 </tr>
	 </table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
