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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>

<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
<script language="JavaScript">
function fonc1() {
	document.formulaire.retard_aucun.checked=true;
	document.formulaire.rien.disabled=false;
	document.getElementById('inf').style.visibility='hidden';
}
function fonc2() {
	var op=document.formulaire.saisie_heure.options.selectedIndex;
	if (document.formulaire.saisie_heure.options[op].value == "null") {
		document.formulaire.rien.disabled=true;
		document.getElementById('inf').style.visibility='visible';
	}else{
		document.formulaire.rien.disabled=false;
		document.getElementById('inf').style.visibility='hidden';
	}
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<script language="JavaScript">var envoiform=true;</script>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS25 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
$idmatiere="";
$anneeScolaire=anneeScolaireViaIdClasse($_POST['saisie_classe']);

if (isset($_POST["class"])) {
	$idClasse=$_POST["saisie_classe"];
	$saisie_classe=$_POST["saisie_classe"];
	$typevaleur=$_POST["saisie_classe"];
	$typechamps="saisie_classe";
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
	print "<input type='hidden' name='class' value=\"".$_POST["class"]."\" form='formulaire0'>";
	print "<input type='hidden' name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" form='formulaire0'>";
}

if (isset($_POST["grp"])) {
	$gid=$_POST["saisie_groupe"];
	$idgroupe=$_POST["saisie_groupe"];
	$typevaleur=$_POST["saisie_groupe"];
	$typechamps="saisie_groupe";
	$sql="SELECT libelle,liste_elev FROM {$prefixe}groupes WHERE group_id='$gid'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
	$nomgrp=$data[0][0];
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
	$res=execSql($sql);
	$data=chargeMat($res);
	print "<input type='hidden' name='grp' value=\"".$_POST["grp"]."\" form='formulaire0'>";
	print "<input type='hidden' name='saisie_groupe' value=\"".$_POST["saisie_groupe"]."\" form='formulaire0'>";
}

if (isset($_POST["etude"])) {
	$idetude=$_POST["saisie_etude"];
	$typevaleur=$_POST["saisie_etude"];
	$typechamps="saisie_etude";
	$sql="SELECT id_etude,id_eleve FROM {$prefixe}etude_affect WHERE id_etude='$idetude'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$idmatiere="-".$data[0][0];
	$cl=rechercheEtude($data[0][0]);
	for($i=0;$i<countTriade($data);$i++) { $liste_eleves.=$data[$i][1].","; }
	$liste_eleves=preg_replace('/,$/',"",$liste_eleves);
	if ($liste_eleves != "") {
		$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
		$res=execSql($sql);
		$data=chargeMat($res);
	}
	print "<input type='hidden' name='etude' value=\"".$_POST["etude"]."\" form='formulaire0'>";
	print "<input type='hidden' name='saisie_etude' value=\"".$_POST["saisie_etude"]."\" form='formulaire0'>";
}
?>

<div style="font-size:13px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:8px 5px 4px;">
  Absences ou retards en classe de : <span style="color:#c62828;"><?php print ucwords($cl) ?></span>
</div>

<?php
if (isset($_POST["datedepart"])) {
	$datedepart=$_POST["datedepart"];
	$disabledT="";
	$mess="";
}elseif(AUTODATEABSRTD == "oui") {
	$datedepart=dateDMY();
	$disabledT="";
	$mess="";
}else{
	$datedepart="dd/mm/aaaa";
	$disabledT="disabled='disabled'";
	$mess="<b style='color:red;'>Indiquer la date</b>";
}
?>

<form method="post" name="formulaire0" id="formulaire0" action="gestion_abs_retard_suite.php">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Pour le :</span>
    <input type="text" name="datedepart" value="<?php print $datedepart ?>" onclick="this.value=''" class="cc-select" style="width:90px;" onKeyPress="onlyChar(event)" onChange="this.form.submit();">
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire0.saisie_date",$_SESSION["langue"],"0"); ?>
    <?php if ($mess) { ?><span style="margin-left:8px;"><?php print $mess ?></span><?php } ?>
  </div>
</div>
</form>

<form name="formulaire" method="post" action="gestion_abs_retard_suite2.php">

<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Horaire :</span>
    <select name="saisie_heure" class="cc-select" onChange="fonc2()" <?php print $disabledT ?>>
      <option id="select0" value="null"><?php print LANGCHOIX ?></option>
      <?php
      $disabled="disabled";
      $data3=recupCreneauDefault("creneau");
      if (countTriade($data3) > 0) {
          $data3=recupInfoCreneau($data3[0][1]);
          print "<option id='select0' value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\" selected='selected'>".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
          $disabled="";
      }else{
          print "<option id='select0' value=\"null\">".LANGCHOIX."</option>";
      }
      select_creneaux2();
      $dataEdt=recupCoursDuJourViaClasse($datedepart,$idClasse);
      if (countTriade($dataEdt)) {
          print "<optgroup label='EDT'>";
          for($i=0;$i<countTriade($dataEdt);$i++) {
              list($h,$m,$s)=preg_split('/:/',$dataEdt[$i][4]);
              $h=$h+TIMEZONE;
              $m=$m+TIMEZONEMINUTE;
              if ($h < 10) $h="0$h";
              if ($m < 10) $m="0$m";
              $dataEdt[$i][4]="$h:$m:$s";
              $secondeT=conv_en_seconde($dataEdt[$i][4]);
              $secondeT+=conv_en_seconde($dataEdt[$i][5]);
              $heureFin=calcul_hours($secondeT);
              $infotime=timeForm($dataEdt[$i][4])." - ".timeForm($heureFin);
              $infotimeText="Edt#".$dataEdt[$i][4]."#".$heureFin;
              print "<option id='select1' value='$infotimeText'>Edt : $infotime</option>";
          }
          print "</optgroup>";
      }
      ?>
    </select>
    <input type="hidden" name="datedepart" value="<?php print $datedepart ?>">
  </div>

<?php if ((defined("ISMAPP")) && (ISMAPP == 1)) {
include_once("./librairie_php/ajax-nosubmit.php");
ajax_js();
?>
  <div class="na-row">
    <span class="na-lbl">Matière :</span>
    <input type="text" name="idmatiere" id="search" autocomplete="off" class="cc-select" style="width:200px;" onkeyup="searchRequest(this,'matiere','target','formulaire','idmatiere')">
    <div id="target" style="width:200px;position:absolute;"></div>
  </div>
<?php }else{ ?>
  <div class="na-row">
    <span class="na-lbl">Matière :</span>
    <select name="idmatiere" class="cc-select" <?php print $disabledT ?>>
      <option id="select0" value=""><?php print LANGCHOIX ?></option>
      <?php select_matiere3("50") ?>
    </select>
  </div>
<?php } ?>

  <div class="na-row">
    <span class="na-lbl">Enseignant :</span>
    <select name="idprof" class="cc-select" <?php print $disabledT ?>>
      <option id="select0" value=""><?php print LANGCHOIX ?></option>
      <optgroup label="Enseignant">
      <?php select_personne_nom_len_id('ENS',25) ?>
      <optgroup label="Vie Scolaire">
      <?php select_personne_nom_len_id('MVS',25) ?>
    </select>
  </div>
</div>

<br>

<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<?php
$sub=0;
if(countTriade($data) <= 0) {
	print("<tr><td align='center' valign='center' style='padding:12px;'><b>".LANGPROJ6."</b></td></tr>");
}else{
?>
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;width:25%;"><?php print LANGNA1 ?> <?php print LANGNA2 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:5%;"><?php print LANGABS20 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:5%;"><?php print LANGABS21 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:5%;"><?php print LANGABS22 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:5%;"><a href="#" title="Justifier">Just.</a></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:5%;"><a href="#" title="Informations">Info.</a></td>
</tr>
<?php
for($i=0;$i<countTriade($data);$i++) {
	$disp=0;
	$enstage=verifSiEleveEnStage($data[$i][1],$datedepart);
	$datartd=verifsiretardAvecDate($data[$i][1],$datedepart);
	// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, creneaux
	$rtdlien="";
	$dejaFait=0;
	if (countTriade($datartd) > 0) {
		$rtdlien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('";
		$dejaFait=1;
	}
	if (countTriade($datartd) > 0) {
		$rtdlien.="<font face=Verdana size=1><font color=red><b> ".LANGABS32."</b></font>".LANGABS32bis."&nbsp;".LANGMESS63."&nbsp;</font><br>";
		$ia=0;
		for($io=0;$io<countTriade($datartd);$io++) {
			$ia++;
			$duree="(".$datartd[$io][5].")";
			if ($datartd[$io][5] == 0) { $duree="(???)"; }
			$matierenom=chercheMatiereNom($datartd[$io][7]);
			if (trim($matierenom) == "") { $matierenom="???"; }
			list($creneau,$dC,$fC)=preg_split('/#/',$datartd[$io][8]);
			$cre="($dC - $fC)";
			$rtdlien.="<font face=Verdana size=1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".addslashes(LANGABS33)."&nbsp;".addslashes($matierenom)."&nbsp;".$cre."&nbsp;".$duree."</font><br>";
		}
	}
	$datartd=verifsiabsAvecDate($data[$i][1],$datedepart);
	// elev_id, duree_heure, date_ab, date_saisie, origin_saisie, duree_ab, motif, idmatiere, creneaux
	if (countTriade($datartd) > 0) {
		if ($dejaFait == 0) { $rtdlien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('"; $dejaFait=1; }
		$rtdlien.="<font face=Verdana size=1><font color=red><b> ".LANGMESS60."</b></font>".LANGMESS60bis."&nbsp;".LANGMESS63."&nbsp;</font><br>";
		$ia=0;
		for($io=0;$io<countTriade($datartd);$io++) {
			$ia++;
			$duree="(".$datartd[$io][1]."h)";
			if ($datartd[$io][1] == 0) { $duree="(???)"; }
			$matierenom=chercheMatiereNom($datartd[$io][7]);
			if (trim($matierenom) == "") { $matierenom="???"; }
			list($creneau,$dC,$fC)=preg_split('/#/',$datartd[$io][8]);
			$cre="($dC - $fC)";
			$rtdlien.="<font face=Verdana size=1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".addslashes(LANGABS33)."&nbsp;".addslashes($matierenom)."&nbsp;".$cre."&nbsp;".$duree."</FONT><br>";
		}
	}
	if ($dejaFait == 1) {
		$rtdlien.="'); window.status=''; return true;\" onMouseOut='HideBulle()'><b>Info</b></a><font color=red>]</font>";
	}
	if ($disp == 1) {
		$displien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('<font face=Verdana size=1><B><font color=red>".LANGABS29."</font></B>".LANGABS29bis."&nbsp; <br> $matiere </FONT>'); window.status=''; return true;\" onMouseOut='HideBulle()'><b>".LANGABS30."</b></a><font color=red>]</font>";
	}

	$datedebut=$datedepart;
	$resu=dejaabsviaDate($data[$i][1],$datedebut);
	if (countTriade($resu) != 0) {
		for($ii=0;$ii<countTriade($resu);$ii++) {
			?>
			<tr id='tr<?php print $i ?>' class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
			<td style="padding:4px 6px;"><?php print ucwords($data[$i][2])." ".ucwords($data[$i][3]) ?></td>
			<td align="center" colspan="5" bgcolor="#FFFFFF" style="padding:4px 6px;"><?php print LANGABS18 ?> <?php print dateForm($resu[$ii][1]) ?> <?php print LANGABS19 ?> <?php print dateForm($resu[$ii][5]) ?></td>
			</tr>
			<?php
			continue;
		}
	}else{
		$resu=dejadispViaDate($data[$i][1],$datedebut);
		if (countTriade($resu) != 0) {
			for($ii=0;$ii<countTriade($resu);$ii++) {
				$matiere=chercheMatiereNom($resu[$ii][1]);
				$disp=1;
			}
		}
		$photoeleve="image_trombi.php?idE=".$data[$i][1];
		$infoProba=getProbaEleve($data[$i][1]);
		$infoprobatoire=($infoProba==1) ? "<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" />" : "";
		$regime=recupRegime($data[$i][1]);
		if ($regime != "") $regime=" (<i>$regime</i>)";
?>
<tr id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td style="padding:4px 6px;">
<?php
print "$infoprobatoire&nbsp;&nbsp;".infoBulleEleveSansLoupe($data[$i][1],ucwords($data[$i][2])." ".ucwords($data[$i][3]));
if ($disp == 1) { ?>&nbsp;[<A href='#' onMouseOver="AffBulle('<font face=Verdana size=1><B><font color=red>D</font></B>ispenser de :&nbsp; <br> <?php print $matiere ?>.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><b><font color=red>Disp</font></b></a>]<?php } ?>
<?php print $regime ?>
</td>
<?php
if (($enstage == true) && (VATEL != 1)) {
	print "<td align='center' colspan='5' bgcolor='#FFFFFF' style='padding:4px 6px;'><i>en stage aujourd'hui</i></td></tr>";
}else{ ?>
<td align="center" bgcolor="#FFFFFF" style="padding:4px 6px;">
<?php $val="'".$i."','".dateHI()."','".dateDMY()."'"; ?>
<select name="saisie_<?php print $i ?>" class="cc-select" style="width:80px;" onChange="DisplayLigne2('tr<?php print $i ?>',this.value);abs(<?php print $val ?>);">
<option value=0 id="select0"><?php print LANGRIEN ?></option>
<option value="absent" id="select1"><?php print LANGABS ?></option>
<option value="retard" id="select1"><?php print LANGRTD ?></option>
</select></td>
<td bgcolor="#FFFFFF" align="center" style="padding:4px 6px;">
<select name="saisie_duree_<?php print $i ?>" class="cc-select" style="width:60px;" onChange="abs3(<?php print $val ?>);verifjustifier('<?php print $i ?>')">
<option value='0' id="select0"><?php print LANGRIEN ?></option>
<?php for($o=0;$o<16;$o++) { print "<option id='select1'></option>"; } ?>
</select></td>
<td bgcolor="#FFFFFF">
<select onChange="motifabsretad22('<?php print $i ?>',this.value); verifjustifier('<?php print $i ?>')" name="saisie_motifs_<?php print $i ?>" id="motif_<?php print $i ?>" class="cc-select" style="width:80px;">
<option value="0" id="select0"><?php print LANGINCONNU ?></option>
<?php affSelecMotif() ?>
<option value="autre" id="select1">autre</option>
</select>
<input type="text" name="saisie_motif_<?php print $i ?>" size="12" value="<?php print LANGINCONNU ?>" id="saisie_motif_<?php print $i ?>" class="cc-select" style="display:none;">
</td>
<td style="padding:4px 6px;text-align:center;">
<input type="checkbox" name="saisie_justifie_<?php print $i ?>" value="1" disabled="disabled">
</td>
<td align="center" style="padding:4px 6px;">
<?php print "&nbsp;".$rtdlien."&nbsp;".$displien; ?>
<input type="hidden" size="12" name="saisie_duree1_<?php print $i ?>">
<input type="hidden" name="saisie_pers_<?php print $i ?>" value="<?php print $data[$i][1] ?>">
</td>
</tr>
<?php
}
}
$sub=1;
}
}
print "</table>";
?>

<?php if ($sub == 1) { ?>
<br>
<input type="hidden" name="saisie_id" value="<?php print countTriade($data) ?>">
<input type="hidden" name="nomclasse" value="<?php print $cl ?>">
<input type="hidden" name="nommatiere" value="">
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:6px 5px;">
  <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
    <?php print LANGABS53 ?> :
    <input type="checkbox" class="btradio1" name="retard_aucun" value="oui" onclick="fonc1();"> (<?php print LANGOUI ?>)
  </label>
</div>
<br>
<span style="display:flex;margin:0 5px;"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","rien","<?php print $disabled ?>");</script></span>
<br>
<div id="inf" style="color:red;text-align:center;"><i>Indiquer heure d'abs/rtd</i></div>
<?php if ($disabled == '') {
	print "<script>document.getElementById('inf').style.visibility='hidden';</script>";
} ?>
<br>
<?php } ?>

<input type="hidden" name="type" value="<?php print $typechamps ?>">
<input type="hidden" name="typevaleur" value="<?php print $typevaleur ?>">
</form>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
