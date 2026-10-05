<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<style>
.abs-section-hdr {
    margin: 12px 0 0;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    border-radius: 6px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
    letter-spacing: .4px;
}
.abs-hdr-retard  { background: #1565c0; }
.abs-hdr-absence { background: #b71c1c; }
.abs-hdr-dispense{ background: #2e7d32; }
.abs-none {
    padding: 7px 14px;
    font-size: 12px;
    color: #999;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
    font-style: italic;
}
.abs-card-name {
    font-size: 13px;
    font-weight: 700;
    color: #1a1a2e;
    line-height: 1.3;
}
.abs-card-name a { color: #1a1a2e; text-decoration: none; }
.abs-card-name a:hover { text-decoration: underline; }
.abs-card-sub {
    font-size: 11px;
    color: #666;
    margin-top: 3px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.abs-card-info {
    font-size: 12px;
    color: #333;
    margin-top: 5px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
    line-height: 1.5;
}
.abs-card-info i { color: #888; font-size: 11px; }
.abs-badge-justif {
    display: inline-block;
    margin-left: 8px;
    font-size: 10px;
    font-weight: 700;
    color: #e65100;
    background: #fff3e0;
    border-radius: 3px;
    padding: 1px 6px;
    vertical-align: middle;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.abs-badge-proba {
    vertical-align: middle;
}
.abs-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 26px;
    background: #eef0fb;
    border: 1px solid #c5cae9;
    border-radius: 5px;
    text-decoration: none;
    transition: background .12s;
}
.abs-icon-btn:hover { background: #dde0f5; }
.abs-icon-col {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex-shrink: 0;
    padding: 2px 0 0 12px;
}
</style>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_POST["modif_date"])) {
	$date=$_POST["saisie_date"];
}else{
	$date=dateDMY();
}

if (isset($_POST["sClasseGrp"])) {
	$filtreCLasse=$_POST["sClasseGrp"];
}else{
	$filtreCLasse="tous";
}
?>
<script language="JavaScript">
function print_abs_rtd_du_jour(){
	var ok=confirm(langfunc3);
	if (ok) {
		open('gestion_abs_retard_du_jour_print.php?id=<?php print dateFormBase($date) ?>&filtre=<?php print $filtreCLasse?>','_blank','');
	}
}

function print_abs_rtd_du_jour_2(){
	var ok=confirm(langfunc3);
	if (ok) {
		open('gestion_abs_retard_du_jour_print.php?id=<?php print dateFormBase($date) ?>&filtre=<?php print $filtreCLasse?>&inconnu=1','_blank','');
	}
}
</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS35 ?> <?php print $date ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:6px 5px 4px;">
  <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;"><?php print LANGABS37 ?> :</span>
  <a href="#" onclick="print_abs_rtd_du_jour();"><img src="./image/commun/print.gif" border="0" alt="Imprimer tous"></a>
  <a href="#" onclick="print_abs_rtd_du_jour_2();"><img src="./image/commun/print2.gif" border="0" alt="Imprimer seulement les inconnus"></a>
  <button type="button" class="btn-enr" onclick="open('gestion_abs_retard_du_jour_misaj.php?date=<?php print dateFormBase($date)?>&filtre=<?php print $filtreCLasse?>','_parent','')"><?php print LANGABS74 ?></button>
</div>

<form method="post" name="formulaire">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Date :</span>
    <input type="text" name="saisie_date" value="<?php print $date ?>" class="cc-select" style="width:90px;" onclick="this.value=''" onKeyPress="onlyChar(event)">
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.saisie_date",$_SESSION["langue"],"0"); ?>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS158 ?> :</span>
    <select name="sClasseGrp" class="cc-select">
      <?php
      if ($filtreCLasse != "tous") {
          $classeS=chercheClasse($filtreCLasse);
          print "<option value='$filtreCLasse'>".$classeS[0][1]."</option>";
          print "<option value='tous'>Aucun</option>";
      }else{
          print "<option value='tous'>".LANGCHOIX."</option>";
      }
      select_classe(); ?>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","modif_date");</script>
<br><br>
</form>

<?php
$data=recup_abs_rtd_aucun($date);
if (countTriade($data) > 0) { ?>
<div style="margin:8px 5px;padding:10px 14px;background:#f5f6fd;border:1px solid #dde0f0;border-radius:8px;">
  <div style="font-size:13px;font-weight:700;color:#080A66;margin-bottom:8px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">Abs, rtd effectués</div>
  <div style="max-height:180px;overflow:auto;font-size:12px;">
    <?php for($j=0;$j<countTriade($data);$j++) {
        $nbabs=$data[$j][5];
        $nbrtd=$data[$j][6];
        if (empty($nbabs)) {
            print ucwords(LANGABS33)." ".$data[$j][1]." (".trim(trunchaine($data[$j][4],20)).") ".LANGABS76." ".timeForm($data[$j][3])."<br><i style='padding-left:12px;'>0 absence / 0 retard</i><br>";
        }else{
            print ucwords(LANGABS33)." ".$data[$j][1]." (".trim(trunchaine($data[$j][4],20)).") ".LANGABS76." ".timeForm($data[$j][3])."<br><i style='padding-left:12px;'>$nbabs absence(s) / $nbrtd retard(s)</i><br>";
        }
    } ?>
  </div>
</div>
<hr style="width:50%;margin:8px auto;border:none;border-top:1px solid #c5cae9;">
<?php } ?>

<!-- ── RETARDS ── -->
<div class="abs-section-hdr abs-hdr-retard">Retards</div>
<div class="dest-list" style="margin:4px 0 10px;">
<?php
$data_2=affRetarddujour3($date);
//  elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, smsenvoye
$nbCards=0;
for($j=0;$j<countTriade($data_2);$j++) {
	$ideleve=$data_2[$j][0];
	$idmatiere=$data_2[$j][7];
	$originesaisie=$data_2[$j][4];
	$etude="";
	if ($idmatiere < 0) { $etude="En Etude "; }
	$imgsms=($data_2[$j][11]=='1') ? "<img src='./image/commun/sms.gif' title='SMS ENVOYE' width='16' height='14' style='vertical-align:middle;margin-left:4px;'/>" : "";
	if ($idmatiere != null) { $nomMatiere=chercheMatiereNom($idmatiere); }
	$isJustifie=((strtolower($data_2[$j][6]) != "inconnu") && ($data_2[$j][5] != 0));
	$classe=chercheIdClasseDunEleve($ideleve);
	if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) { continue; }
	$classe=chercheClasse($classe);
	$heuresignale=($data_2[$j][9] != "") ? timeForm($data_2[$j][9]) : "??:??";
	if ($nomMatiere == "") { $nomMatiere=LANGSMS2; }
	$photoeleve="image_trombi.php?idE=".$ideleve;
	$regime=recupRegime($ideleve);
	if ($regime != "") $regime=" (<i>$regime</i>)";
	$infoProba=getProbaEleve($ideleve);
	$infoprobatoire=($infoProba==1) ? "<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" class='abs-badge-proba' />" : "";
	$motiftext=$data_2[$j][6];
	if ($data_2[$j][6]=="inconnu") { $motiftext=LANGINCONNU; }
	if (trim($data_2[$j][6])=="0") { $motiftext=LANGINCONNU; }
	$motiftext=preg_replace('/"/',"",$motiftext);
	$motiftext=preg_replace("/'/","\'",$motiftext);
	$nbCards++;
?>
<div class="dest-row" style="align-items:flex-start;padding:10px 14px;">
  <div class="dest-row-label">
    <div class="abs-card-name">
      <?php print $infoprobatoire ?>
      <span class="htip-wrap"><a target='_blank' href='gestion_abs_retard_modif_donne.php?ideleve=<?php print $ideleve ?>'><?php print strtoupper(recherche_eleve_nom($ideleve)) ?></a><span class="htip" style="min-width:auto;padding:4px"><img src='<?php print $photoeleve ?>' style="display:block;max-width:120px;border-radius:6px"></span></span>
      <?php print " ".ucwords(strtolower(trunchaine(recherche_eleve_prenom($ideleve),10))).$regime ?>
      <?php if ($isJustifie) { ?><span class="abs-badge-justif">Justifié</span><?php } ?>
      <?php print $imgsms ?>
    </div>
    <div class="abs-card-sub">Classe : <?php print $classe[0][1] ?> &mdash; Matière : <?php print trunchaine($nomMatiere,25) ?></div>
    <div class="abs-card-info">
      En retard à <?php print timeForm($data_2[$j][1]) ?> le <?php print dateForm($data_2[$j][2]) ?><br>
      <i>Signalé le <?php print dateForm($data_2[$j][3])." - ".$heuresignale ?> par <?php print $originesaisie ?></i>
    </div>
  </div>
  <div class="abs-icon-col">
    <span class="htip-wrap"><a class="abs-icon-btn" href="#"><i class="bi bi-search" style="font-size:13px;color:#3949ab"></i></a><span class="htip htip-r" style="min-width:180px"><?php print $motiftext ?></span></span>
    <span class="htip-wrap"><a class="abs-icon-btn" href="#"><i class="bi bi-telephone" style="font-size:13px;color:#3949ab"></i></a><span class="htip htip-r" style="min-width:240px"><b><?php print LANGABS41 ?> :</b> <?php print cherchetel($ideleve) ?><br><b>Portable 1 :</b> <?php print cherchetelportable1($ideleve) ?><br><b>Portable 2 :</b> <?php print cherchetelportable2($ideleve) ?><br><b><?php print LANGABS39 ?> :</b> <?php print cherchetelpere($ideleve) ?><br><b><?php print LANGABS40 ?> :</b> <?php print cherchetelmere($ideleve) ?><br><b>Email :</b> <?php print cherchemail($ideleve) ?></span></span>
  </div>
</div>
<?php
}
if ($nbCards == 0) { ?><div class="abs-none">Aucun</div><?php } ?>
</div>

<!-- ── ABSENCES ── -->
<div class="abs-section-hdr abs-hdr-absence">Absences</div>
<div class="dest-list" style="margin:4px 0 10px;">
<?php
$data_3=affAbsence4($date);
//  elev_id, date_ab, date_saisie, origin_saisie, duree_ab, date_fin, motif, duree_heure, id_matiere, heure_saisie, justifier, heuredabsence
$nbCards=0;
for($j=0;$j<countTriade($data_3);$j++) {
	$ideleve=$data_3[$j][0];
	$idmatiere=$data_3[$j][8];
	$heureabs=timeForm($data_3[$j][11]);
	$nomMatiere=chercheMatiereNom($idmatiere);
	$classe=chercheIdClasseDunEleve($data_3[$j][0]);
	$regime=recupRegime($ideleve);
	if ($regime != "") $regime=" (<i>$regime</i>)";
	if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) { continue; }
	$classe=chercheClasse($classe);
	$isJustifie=((strtolower($data_3[$j][6]) != "inconnu") && ($data_3[$j][4] != 0));
	$photoeleve="image_trombi.php?idE=".$ideleve;
	if ($nomMatiere == "") { $nomMatiere=LANGSMS2; }
	$imgsms=($data_3[$j][13]=='1') ? "<img src='./image/commun/sms.gif' title='SMS ENVOYE' width='16' height='14' style='vertical-align:middle;margin-left:4px;'/>" : "";
	$heuresignale=($data_3[$j][9] != "") ? timeForm($data_3[$j][9]) : "??:??";
	$infoProba=getProbaEleve($ideleve);
	$infoprobatoire=($infoProba==1) ? "<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" class='abs-badge-proba' />" : "";
	$motiftext=$data_3[$j][6];
	if ($data_3[$j][6]=="inconnu") { $motiftext=LANGINCONNU; }
	if (trim($data_3[$j][6])=="0") { $motiftext=LANGINCONNU; }
	$motiftext=preg_replace('/"/',"",$motiftext);
	$motiftext=preg_replace("/'/","\'",$motiftext);
	// Build duration string
	if ($data_3[$j][4] >= 0) {
		$dureeStr=dateForm($data_3[$j][1])." à ".$heureabs." ".LANGABS43." ";
		$dureeStr.=($data_3[$j][4]==0) ? "???" : $data_3[$j][4]." ".LANGABS44;
	}else{
		$dureeStr=dateForm($data_3[$j][1])." à ".$heureabs." ".LANGABS43." ".preg_replace('/\./','h',$data_3[$j][7]);
	}
	$nbCards++;
?>
<div class="dest-row" style="align-items:flex-start;padding:10px 14px;">
  <div class="dest-row-label">
    <div class="abs-card-name">
      <?php print $infoprobatoire ?>
      <span class="htip-wrap"><a href='#'><?php print strtoupper(recherche_eleve_nom($ideleve)) ?></a><span class="htip" style="min-width:auto;padding:4px"><img src='<?php print $photoeleve ?>' style="display:block;max-width:120px;border-radius:6px"></span></span>
      <?php print " ".ucwords(strtolower(recherche_eleve_prenom($ideleve))).$regime ?>
      <?php if ($isJustifie) { ?><span class="abs-badge-justif">Justifié</span><?php } ?>
      <?php print $imgsms ?>
    </div>
    <div class="abs-card-sub">Classe : <?php print $classe[0][1] ?> &mdash; Matière : <?php print trunchaine($nomMatiere,25) ?></div>
    <div class="abs-card-info">
      <?php print LANGABS42 ?> <?php print $dureeStr ?><br>
      <i>Signalé le <?php print dateForm($data_3[$j][2])." - ".$heuresignale ?></i>
    </div>
  </div>
  <div class="abs-icon-col">
    <span class="htip-wrap"><a class="abs-icon-btn" href="#"><i class="bi bi-search" style="font-size:13px;color:#3949ab"></i></a><span class="htip htip-r" style="min-width:180px"><?php print $motiftext ?></span></span>
    <span class="htip-wrap"><a class="abs-icon-btn" href="#"><i class="bi bi-telephone" style="font-size:13px;color:#3949ab"></i></a><span class="htip htip-r" style="min-width:240px"><b><?php print LANGABS41 ?> :</b> <?php print cherchetel($ideleve) ?><br><b>Portable 1 :</b> <?php print cherchetelportable1($ideleve) ?><br><b>Portable 2 :</b> <?php print cherchetelportable2($ideleve) ?><br><b><?php print LANGABS39 ?> :</b> <?php print cherchetelpere($ideleve) ?><br><b><?php print LANGABS40 ?> :</b> <?php print cherchetelmere($ideleve) ?><br><b>Email :</b> <?php print cherchemail($ideleve) ?></span></span>
  </div>
</div>
<?php
}
if ($nbCards == 0) { ?><div class="abs-none">Aucun</div><?php } ?>
</div>

<!-- ── DISPENSES ── -->
<div class="abs-section-hdr abs-hdr-dispense">Dispenses</div>
<div class="dest-list" style="margin:4px 0 10px;">
<?php
$data_4=affDispence3($date);
//  elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3
$nbCards=0;
for($j=0;$j<countTriade($data_4);$j++) {
	$aujourdhui=dateD();
	if ($aujourdhui == "Mon") { $aujourdhui="Lundi";    }
	if ($aujourdhui == "Tue") { $aujourdhui="Mardi";    }
	if ($aujourdhui == "Wed") { $aujourdhui="Mercredi"; }
	if ($aujourdhui == "Thu") { $aujourdhui="Jeudi";    }
	if ($aujourdhui == "Fri") { $aujourdhui="Vendredi"; }
	if ($aujourdhui == "Sat") { $aujourdhui="Samedi";   }
	if ($aujourdhui == "Sun") { $aujourdhui="Dimanche"; }
	$heure_jour="";
	if ($aujourdhui == trim($data_4[$j][9]))  { $heure_jour=" à ".trim($data_4[$j][8])." (heure)";  }
	if ($aujourdhui == trim($data_4[$j][11])) { $heure_jour=" à ".trim($data_4[$j][10])." (heure)"; }
	if ($aujourdhui == trim($data_4[$j][13])) { $heure_jour=" à ".trim($data_4[$j][12])." (heure)"; }
	$ideleve=$data_4[$j][0];
	$classe=chercheIdClasseDunEleve($ideleve);
	if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) { continue; }
	$classe=chercheClasse($classe);
	$photoeleve="image_trombi.php?idE=".$ideleve;
	$k=$data_4[$j][1];
	$sql="SELECT code_mat, libelle FROM {$prefixe}matieres WHERE code_mat='$k' ORDER BY code_mat";
	$res=execSql($sql);
	$data_matiere=chargeMat($res);
	$regime=recupRegime($ideleve);
	if ($regime != "") $regime=" (<i>$regime</i>)";
	$infoProba=getProbaEleve($ideleve);
	$infoprobatoire=($infoProba==1) ? "<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" class='abs-badge-proba' />" : "";
	$motiftext=$data_4[$j][7];
	if ($data_4[$j][7]=="inconnu") { $motiftext=LANGINCONNU; }
	if (trim($data_4[$j][7])=="0") { $motiftext=LANGINCONNU; }
	$motiftext=preg_replace('/"/',"",$motiftext);
	$motiftext=preg_replace("/'/","\'",$motiftext);
	$nbCards++;
?>
<div class="dest-row" style="align-items:flex-start;padding:10px 14px;">
  <div class="dest-row-label">
    <div class="abs-card-name">
      <?php print $infoprobatoire ?>
      <span class="htip-wrap"><a href='#'><?php print strtoupper(recherche_eleve_nom($ideleve)) ?></a><span class="htip" style="min-width:auto;padding:4px"><img src='<?php print $photoeleve ?>' style="display:block;max-width:120px;border-radius:6px"></span></span>
      <?php print " ".ucwords(strtolower(recherche_eleve_prenom($ideleve))).$regime ?>
    </div>
    <div class="abs-card-sub"><?php print LANGPER25 ?> : <?php print $classe[0][1] ?></div>
    <div class="abs-card-info">
      Dispense de <b><?php print $data_matiere[0][1] ?></b><br>
      du <?php print dateForm($data_4[$j][2]) ?> au <?php print dateForm($data_4[$j][3]) ?><?php print $heure_jour ?>
    </div>
  </div>
  <div class="abs-icon-col">
    <span class="htip-wrap"><a class="abs-icon-btn" href="#"><i class="bi bi-search" style="font-size:13px;color:#3949ab"></i></a><span class="htip htip-r" style="min-width:180px"><?php print $motiftext ?></span></span>
    <span class="htip-wrap"><a class="abs-icon-btn" href="#"><i class="bi bi-telephone" style="font-size:13px;color:#3949ab"></i></a><span class="htip htip-r" style="min-width:240px"><b><?php print LANGABS41 ?> :</b> <?php print cherchetel($ideleve) ?><br><b>Portable 1 :</b> <?php print cherchetelportable1($ideleve) ?><br><b>Portable 2 :</b> <?php print cherchetelportable2($ideleve) ?><br><b><?php print LANGABS39 ?> :</b> <?php print cherchetelpere($ideleve) ?><br><b><?php print LANGABS40 ?> :</b> <?php print cherchetelmere($ideleve) ?><br><b>Email :</b> <?php print cherchemail($ideleve) ?></span></span>
  </div>
</div>
<?php
}
if ($nbCards == 0) { ?><div class="abs-none">Aucun</div><?php } ?>
</div>

<br>
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
