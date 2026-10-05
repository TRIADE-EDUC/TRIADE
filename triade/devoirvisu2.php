<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
/***************************************************************************
*                              T.R.I.A.D.E
*                            ---------------
*
*   begin                : Janvier 2000
*   copyright            : (C) 2000 E. TAESCH -  - F. ORY
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
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

$mySession['Sn']=$_SESSION["nom"];
$mySession['Sp']=$_SESSION["prenom"];
$pid=$_SESSION["id_pers"];

$_SESSION["sClasseGrp"]=$_POST["sClasseGrp"];
$_SESSION["sMat"]=$_POST["sMat"];
$cgrp=$_POST["sClasseGrp"];
$cgrp=explode(":",$cgrp);
$cid=$cgrp[0];
$gid=$cgrp[1];
$mid=$_POST["sMat"];

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
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD>
<td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="400">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGPROF22 ?> </b> / <font id="color2"> <?php print $libel?></font> / <?php print $anneeScolaire ?></font></td></tr>
<tr id='cadreCentral0'>
<td valign=top>

<?php
$valeur=aff_Trimestre();
if (countTriade($valeur)) {

$sql="(SELECT e.elev_id, e.nom,e.prenom,e.classe  FROM {$prefixe}eleves e WHERE e.classe='$cid' AND e.annee_scolaire='$anneeScolaire' ) UNION (SELECT e.elev_id,e.nom,e.prenom,e.classe FROM {$prefixe}eleves e , {$prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve  AND h.annee_scolaire='$anneeScolaire' group by elev_id ) $order";
$res=execSql($sql);
$verifdata=chargeMat($res);
if( countTriade($verifdata) <= 0 )  {
	print("<br><br><br><center><font class=T1>".LANGRECH1."</font></center>");
}else {

	function ElevesDsGrp($gid,$prefixe,$anneeScolaire){
		$sqlIn="SELECT liste_elev FROM {$prefixe}groupes WHERE group_id='$gid' AND annee_scolaire='$anneeScolaire' ";
		$curs=execSql($sqlIn);
    		$in=chargeMat($curs);
    		freeResult($curs);
    		$in=$in[0][0];
    		$in=substr($in,1);
    		$in=substr($in,0,-1);
    		return $in;
	}

if($gid){
	$in=ElevesDsGrp($gid,$prefixe,$anneeScolaire);
	$sqlgroupe=" AND id_groupe='$gid' ";
}else{
    $sql="(SELECT e.elev_id, CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ),e.classe  FROM {$prefixe}eleves e WHERE e.classe='$cid' AND e.compte_inactif != 1 AND e.annee_scolaire='$anneeScolaire' ) UNION (SELECT e.elev_id, CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) , e.classe  FROM {$prefixe}eleves e , {$prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.compte_inactif != 1 AND e.elev_id=h.ideleve  AND h.annee_scolaire='$anneeScolaire' group by elev_id ) $order";

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
	$sql="SELECT sujet, DATE_FORMAT(date,'%d/%m/%Y'), coef FROM {$prefixe}notes WHERE elev_id IN ($in) AND prof_id='$pid' AND code_mat='$mid' AND date >= '$dateDebut' AND date <= '$dateFin' $sqlgroupe GROUP BY date,coef,sujet ORDER BY date DESC";
	$curs=execSql($sql);
	$mat=chargeMat($curs);
}
?>
<div class="dest-wrap">
<div class="dest-list">
<?php
for($i=0;$i<countTriade($mat);$i++){
    $suj=urlencode($mat[$i][0]);
    array_push($mat[$i],$in,$mid,$pid);
    $url='notevisu3.php?args='.urlencode(serialize($mat[$i])).'&libel='.urlencode($libel).'&sujet='.$suj;
    array_pop($mat[$i]);
    array_pop($mat[$i]);
    array_pop($mat[$i]);
    ?>
    <div class="dest-row">
        <div class="dest-row-label">
            <?php print stripslashes($mat[$i][0]) ?>
            <span class="dest-row-sub">&nbsp;—&nbsp;<?php print $mat[$i][1] ?>&nbsp;·&nbsp;coef <?php print $mat[$i][2] ?></span>
        </div>
        <button class="btn-dest" onclick="this.textContent='<?php print LANGPROF18 ?>'; open('<?php print $url ?>','_parent','')"><?php print LANGPER27 ?></button>
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
<iframe MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 scrolling=no name="visudevoir" src="visunoteprofnon.php" width=100% height=100%></iframe>
<?php } ?>

     </td>
	 </tr>
	 </table>
     <?php
       if ($_SESSION['membre'] == "menuadmin") :
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
