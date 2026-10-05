<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
$anneeScolaire=$_POST["anneeScolaire"];
setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);

include_once("./librairie_php/lib_error.php");
include("common/config.inc.php");
include("librairie_php/db_triade.php");
$cnx=cnx();

$gid="non";

$mySession['Sn']=$_SESSION["nom"];
$mySession['Sp']=$_SESSION["prenom"];
$pid=$_SESSION["id_pers"];
$cgrp=$_POST["sClasseGrp"];
$cgrp=explode(":",$cgrp);
$cid=$cgrp[0];
$idClasse=$cgrp[0];
$gid=$cgrp[1];
$mid=$_POST['sMat'];

if ($_POST["adminIdprof"] != "") { $pid=$_POST["adminIdprof"]; }

$nomClasse=chercheClasse($cid);
$nomClasse=$nomClasse[0][1];
$nomMat=chercheMatiereNom($mid);
$nomGrp=chercheGroupeNom($gid);
$libel=$nomClasse." ".$nomGrp." ".$nomMat;
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<style>
.dest-wrap {
    padding: 6px 8px 12px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.dest-list {
    display: flex; flex-direction: column;
    background: #fff;
    border: 1px solid #dde0f0;
    border-radius: 8px;
    overflow: hidden;
}
.dest-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; padding: 9px 14px;
    border-bottom: 1px solid #eef0f8;
    transition: background .12s;
}
.dest-row:last-child { border-bottom: none; }
.dest-row:hover { background: #f5f6fd; }
.dest-row-label {
    font-size: 13px; color: #222; flex: 1;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.dest-row-sub {
    font-size: 11px; color: #888; font-weight: 400;
}
.btn-dest {
    background: #CACCEF; color: #080A66;
    border: none; border-radius: 5px;
    padding: 5px 12px; font-size: 12px; font-weight: 700;
    cursor: pointer; white-space: nowrap;
    transition: background .12s;
    flex-shrink: 0;
}
.btn-dest:hover { background: #b0b4e8; }
.btn-dest-disabled {
    background: #e8e8e8; color: #aaa; cursor: not-allowed;
}
.btn-dest-disabled:hover { background: #e8e8e8; }
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD>
<td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="400">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGPROF23 ?></font>  </b><font id="color2" > <?php print $libel?></font></td></tr>
<tr id='cadreCentral0'>
<td valign=top>

<?php
if ($_SESSION["membre"] == "menuadmin") {
	print "<br /><form method='post' action='notevisuadmin.php' target='_parent' >";
	print "<div><script language='JavaScript'>buttonMagicSubmitAtt('Retour menu principal','create','');</script></div>";
	print "<input type='hidden' name='saisie_pers' value='".$_POST["adminIdprof"]."' /></form><br><br>";
}

$valeur=affDateTrimByIdclasse('',$cid,$anneeScolaire);
if (countTriade($valeur)) {

$sql="(SELECT e.elev_id, e.nom,e.prenom,e.classe  FROM {$prefixe}eleves e WHERE e.classe='$cid' AND e.annee_scolaire='$anneeScolaire' ) UNION (SELECT e.elev_id,e.nom,e.prenom,e.classe FROM {$prefixe}eleves e , {$prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve AND  h.annee_scolaire='$anneeScolaire'  group by elev_id ) $order";

$res=execSql($sql);
$verifdata=chargeMat($res);
if( countTriade($verifdata) <= 0 )  {
	print("<br><br><br><center><font class=T1>".LANGRECH1."</font></center>");
}else {

function ElevesDsGrp($gid,$prefixe,$anneeScolaire){
    $sqlIn="
    SELECT
    	liste_elev
    FROM
    	{$prefixe}groupes
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
	$sql="(SELECT e.elev_id, e.nom,e.prenom,e.classe  FROM {$prefixe}eleves e WHERE e.classe='$cid' AND e.annee_scolaire='$anneeScolaire' ) UNION (SELECT e.elev_id,e.nom,e.prenom,e.classe FROM {$prefixe}eleves e , {$prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve  AND  h.annee_scolaire='$anneeScolaire' group by elev_id ) $order";
    $curs=execSql($sql);
    $data = chargeMat($curs);
    for($i=0;$i<countTriade($data);$i++)
    {
	$in[$i] = $data[$i][0];
    }
    $in = join(",",$in);
}

$date=recupDateTrimIdclasse($cid,$anneeScolaire);
for($p=0;$p<countTriade($date);$p++) {
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
	{$prefixe}notes
WHERE
	elev_id IN ($in)
AND prof_id='$pid'
AND code_mat='$mid'
AND date >= '$dateDebut' AND date <= '$dateFin'
";
if ($sqlgroupe != "") {
	$sql.=" $sqlgroupe";
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

if ($gid > 0) { $cid="-1"; }
$datedujour=dateDMY();
?>
<div class="dest-wrap">
<div class="dest-list">
<?php
for($i=0;$i<countTriade($mat);$i++){
    $suj        = urlencode($mat[$i][0]);
    $sujet      = $mat[$i][0];
    $datedevoir = $mat[$i][1];
    $coef       = $mat[$i][2];
    $examen     = $mat[$i][3];

    array_push($mat[$i],$in,$mid,$pid);
    $url='notesupp3.php?pid='.$pid.'&args='.urlencode(serialize($mat[$i])).'&libel='.urlencode($libel).'&gid='.$gid.'&idClasse='.$idClasse.'&sujet='.$suj;
    array_pop($mat[$i]);
    array_pop($mat[$i]);
    array_pop($mat[$i]);

    $TD=recupTrimestreDevoir($cid,$datedevoir);
    $TT=recupTrimestreDevoir($cid,$datedujour);
    if ($_SESSION["membre"] == "menuadmin") $TT=0;
    if (MODIFNOTEAPRESARRET == "oui") $TT=0;
    $locked=($TT > $TD);
    ?>
    <div class="dest-row">
        <div class="dest-row-label">
            <?php print stripslashes($sujet) ?>
            <span class="dest-row-sub">&nbsp;—&nbsp;<?php print $datedevoir ?>&nbsp;·&nbsp;coef <?php print $coef ?><?php if($examen) print "&nbsp;·&nbsp;".$examen ?></span>
        </div>
        <?php if ($locked): ?>
        <button type="button" class="btn-dest btn-dest-disabled" onclick="alert('IMPOSSIBLE !!\n\nTrimestre déjà passé.')"><?php print LANGBT50 ?></button>
        <?php else: ?>
        <button type="button" class="btn-dest" onclick="this.textContent='<?php print LANGPROF18 ?>'; open('<?php print $url ?>','_parent','')"><?php print LANGBT50 ?></button>
        <?php endif ?>
    </div>
    <?php
}
?>
</div><!-- /.dest-list -->
</div><!-- /.dest-wrap -->
<?php
}

}else {
?>
<iframe MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 scrolling=no name="suppnote" src="visunoteprofnon.php" width=100% height=100%></iframe>
<?php } ?>

     </td>
	 </tr>
	 </table>
     <?php
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
