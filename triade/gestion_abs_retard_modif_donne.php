<?php
session_start();
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
	setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}else{
	$anneeScolaire=isset($_COOKIE["anneeScolaire"]) ? $_COOKIE["anneeScolaire"] : "";
}
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
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
<script>
function demandeMotif(id,valeur) {
	if (valeur == "autre") {
		document.getElementById('motif'+id).style.display='none';
		document.getElementById('saisie_motif_'+id).style.display='block';
	}else{
		document.getElementById('saisie_motif_'+id).value=valeur;
	}
}

function demandeMotif2(id,valeur) {
	if (valeur == "autre") {
		document.getElementById('motif2'+id).style.display='none';
		document.getElementById('saisie_motif2_'+id).style.display='block';
	}else{
		document.getElementById('saisie_motif2_'+id).value=valeur;
	}
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
$refRattrapage="";

if(isset($_POST["supp_retard"])) {
	$motif="saisie_modif_".$_POST["saisie_id_champ"];
	$duree_retourner="saisie_duree_retourner_".$_POST["saisie_id_champ"];
	$justifier="saisie_justifier_".$_POST["saisie_id_champ"];
	$cr=modif_retard2($_POST["saisie_eleve_id"],$_POST["saisie_heure_ret"],$_POST["saisie_date_ret"],$_POST[$duree_retourner],$_POST[$motif],dateDMY2(),$_SESSION["nom"],$_POST[$justifier],$_POST["saisie_heuredoriginsaisie"],$_POST["saisie_date_ret_origine"],$refRattrapage);
	if ($cr == "-1") { alertJs("Retard déjà enregistré pour cette même période."); }
}

if(isset($_POST["supp_absence"])) {
	$motif="saisie_modif_".$_POST["saisie_id_champ"];
	$duree_retourner="saisie_duree_retourner_".$_POST["saisie_id_champ"];
	$justifier="saisie_justifier_".$_POST["saisie_id_champ"];
	$cr=modif_absence($_POST["saisie_eleve_id_2"],$_POST["saisie_date_ret_2"],$_POST["saisie_date_saisie"],$_SESSION["nom"],$_POST[$motif],$_POST[$duree_retourner],$_POST["saisie_time"],$_POST["saisie_matiere"],$_POST[$justifier],$_POST["saisie_heuredoriginsaisie"],$_POST["saisie_date_ret_origine"],$_POST["saisie_heuredabsence"],$refRattrapage);
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS61 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
if (isset($_GET["ideleve"])) {
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id FROM {$prefixe}eleves e, {$prefixe}classes c WHERE e.elev_id = '".$_GET["ideleve"]."' AND c.code_class = e.classe ORDER BY c.libelle, e.nom, e.prenom";
	$res=execSql($sql);
	$data=chargeMat($res);
}else{
	$motif=strtolower(trim($_POST["saisie_nom_eleve"]));
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id FROM {$prefixe}eleves e, {$prefixe}classes c WHERE lower(e.nom) LIKE '%$motif%' AND c.code_class = e.classe ORDER BY c.libelle, e.nom, e.prenom";
	$res=execSql($sql);
	$data=chargeMat($res);
}

if(countTriade($data) <= 0) {
	print("<br><center><b>".LANGDISP1."</b><br></center>");
}else{
for($i=0;$i<countTriade($data);$i++) {
?>

<div class="na-card" style="margin:8px 0 4px;">
  <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
    <div><span class="na-lbl"><?php print LANGTP1 ?> :</span> <b><?php print ucwords(trim($data[$i][1])) ?></b> <?php infoBulleEleve($data[$i][3]); ?> <span style="color:#c62828;font-size:12px;">(<?php print LANGCALEN7 ?> : <?php print trim($data[$i][0]) ?>)</span></div>
    <div><span class="na-lbl"><?php print LANGTP2 ?> :</span> <b><?php print ucwords(trim($data[$i][2])) ?></b></div>
    <div style="font-size:12px;color:#555;"><?php print LANGABS62 ?></div>
  </div>
  <div style="font-size:12px;color:#080A66;margin-top:6px;">Cumul : <b><span id="<?php print "cumul$i" ?>"></span></b></div>
</div>

<!-- Retards -->
<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS13 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;">Créneaux</td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGABS60 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS12 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:10%;"><?php print LANGAGENDA30 ?></td>
</tr>
<?php
$cumulretard=0;
$nbabs=0;
$nbheureab=0;
$data_2=affRetard($data[$i][3],$anneeScolaire);
// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux, idrattrapage
$cumulretard=countTriade($data_2);
for($j=0;$j<countTriade($data_2);$j++) {
	list($creneaux,$crenDebut,$crenFin)=preg_split('/#/',$data_2[$j][10]);
	$idrattrapage=$data_2[$j][11];
	$elev_id=$data_2[$j][0];
	$heure_ret=$data_2[$j][1];
	$date_ret=$data_2[$j][2];
	$date_saisie=$data_2[$j][3];
	$duree_ret=$data_2[$j][5];
	$idmatiere=$data_2[$j][7];
	$justifier=$data_2[$j][8];
	$heure_saisie=$data_2[$j][9];
	$creneaux=addslashes($data_2[$j][10]);
	if ($idrattrapage == "") {
		$idrattrapage=verifRattrapageRetards($elev_id,$heure_ret,$date_ret,$date_saisie,$duree_ret,$idmatiere,$justifier,$heure_saisie,$creneaux);
	}
	$matiere=chercheMatiereNom($data_2[$j][7]);
	if (($matiere == "") || ($matiere < 0)) { $matiere=""; }
?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method="POST" name="formulaire_<?php print $i.$j ?>">
<td align="center" valign="top" style="padding:4px 6px;"><?php print date_jour(dateForm($data_2[$j][2])) ?><br>
<span id='ida_<?php print $j ?>'><a href="#" onclick="document.getElementById('ida_<?php print $j ?>').style.display='none';document.getElementById('idaa_<?php print $j ?>').style.display='block'; return false;"><?php print dateForm($data_2[$j][2]) ?></a></span>
<input type="text" size="9" style="display:none;" name="saisie_date_ret" id="idaa_<?php print $j ?>" value="<?php print dateForm($data_2[$j][2]) ?>" onKeyPress="onlyChar(event)">
</td>
<td align="center" valign="top" style="padding:4px 6px;"><?php print timeForm($data_2[$j][1]) ?> - <?php print $crenFin ?> (<?php print trunchaine(trim($matiere),11) ?>)</td>
<td align="center" valign="top" style="padding:4px 6px;">
<select name="saisie_duree_<?php print $i ?>" class="cc-select" style="width:70px;" onChange="chargement_pendant('','<?php print $i ?>','<?php print $i.$j ?>')">
<option STYLE='color:#000066;background-color:#FCE4BA'></option>
<?php for($o=0;$o<15;$o++) { print "<option STYLE='color:#000066;background-color:#CCCCFF'></option>"; } ?>
</select>
<input type="hidden" onfocus="this.blur()" name="saisie_duree_retourner_<?php print $i ?>" value="<?php print $data_2[$j][5] ?>">
<?php $yy=$data_2[$j][5]; if ($data_2[$j][5] == 0) { $yy="???"; } ?>
<script langage=Javascript>chargement_pendant('<?php print trim($yy) ?>','<?php print $i ?>','<?php print $i.$j ?>');</script>
</td>
<td valign="top" style="padding:4px 6px;">
<?php
$motiftext=$data_2[$j][6];
if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
if (trim($data_2[$j][6]) == "0") { $motiftext=LANGINCONNU; }
$motiftext=preg_replace('/"/'," ",$motiftext);
?>
<select onChange="demandeMotif2('<?php print $i.$j ?>',this.value)" id="motif2<?php print $i.$j ?>" class="cc-select">
<option value="<?php print $motiftext ?>" STYLE="color:#000066;background-color:#FCE4BA"><?php print $motiftext ?></option>
<?php affSelecMotif() ?>
<option value="autre" STYLE="color:red;background-color:#CCCCFF">autre</option>
</select>
<input type="text" value="<?php print $motiftext ?>" name="saisie_modif_<?php print $i ?>" style="display:none;" id="saisie_motif2_<?php print $i.$j ?>" class="cc-select">
<br>
<label style="font-size:11px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
(<input type="checkbox" name="saisie_justifier_<?php print $i ?>" value="1" <?php if ($data_2[$j][8] == 1) { print "checked='checked'"; } ?>> <?php print LANGRTDJUS ?>)
</label>
</td>
<td align="center" valign="top" style="padding:4px 6px;">
<button type="submit" name="supp_retard" class="btn-enr" style="background:#1565c0;"><?php print LANGPER30 ?></button><br><br>
<input type="hidden" name="saisie_eleve_id" value="<?php print $data[$i][3] ?>">
<input type="hidden" name="saisie_heure_ret" value="<?php print $data_2[$j][1] ?>">
<input type="hidden" name="saisie_date_ret_origine" value="<?php print $data_2[$j][2] ?>">
<input type="hidden" name="saisie_id_champ" value="<?php print $i ?>">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][1] ?>">
<input type="hidden" name="saisie_heuredoriginsaisie" value="<?php print $data_2[$j][9] ?>">
</form>
<form method="post" action="rattrapage.php" onsubmit="var Nwin = window.open('rattrapage.php', 'Nwin', 'width=430,height=230,toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,menubar=no'); return true;" target="Nwin">
<button type="submit" name="acces" class="btn-enr" style="background:#2e7d32;">Rattrap.</button>
<input type="hidden" name="idrattrappage" value="<?php print $idrattrapage ?>">
</form>
</td>
</TR>
<?php
}
?>
</table>
<br>

<!-- Absences -->
<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGPARENT8 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGABS60 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:25%;">Créneaux</td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS12 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:10%;"><?php print LANGAGENDA30 ?></td>
</tr>
<?php
$data_3=affAbsence($data[$i][3],$anneeScolaire);
// elev_id, date_ab, date_saisie, origin_saisie, duree_ab, date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie, heuredabsence, creneaux, smsenvoye, idrattrapage
$nbjoursabs=0; $nbheureabs=0;
for($j=0;$j<countTriade($data_3);$j++) {
	$idrattrapage=$data_3[$j][15];
	if (trim($idrattrapage) == "") {
		$elev_id=$data_3[$j][0];
		$date_saisie=$data_3[$j][2];
		$idmatiere=$data_3[$j][8];
		$heure_saisie=$data_3[$j][11];
		$creneaux=addslashes($data_3[$j][13]);
		$date_ab=$data_3[$j][1];
		$duree_ab=$data_3[$j][4];
		$date_fin=$data_3[$j][5];
		$time=$data_3[$j][9];
		$idrattrapage=verifRattrapageAbsences($elev_id,$date_ab,$date_saisie,$duree_ab,$date_fin,$idmatiere,$time,$heure_saisie,$creneaux);
	}
	if ($data_3[$j][13] != "") {
		list($creneaux,$crenDebut,$crenFin)=preg_split('/#/',$data_3[$j][13]);
	}else{
		$crenDebut="??:??:??"; $crenFin="??:??:??";
	}
	$heuredabsence=$data_3[$j][12];
	$matiere=chercheMatiereNom($data_3[$j][7]);
	$nomMatiere=chercheMatiereNom($data_3[$j][8]);
	if ($data_3[$j][4] > 0) { $nbjoursabs=$nbjoursabs+$data_3[$j][4]; }else{ $nbheureabs=$nbheureabs+$data_3[$j][7]; }
	if ($data_3[$j][14] == 1) { $imgsms="<img src='./image/commun/sms.gif' title='SMS ENVOYE' width='20' height='18' align='center'/>"; }else{ $imgsms=""; }
?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method="POST" name="formulaire_3_<?php print $i.$j ?>">
<td align="center" valign="top" style="padding:4px 6px;"><?php print date_jour(dateForm($data_3[$j][1])) ?><br>
<span id='idb_<?php print $i.$j ?>'><a href="#" onclick="document.getElementById('idb_<?php print $i.$j ?>').style.display='none';document.getElementById('saisie_date_ret_2_<?php print $i.$j ?>').style.display='block';return false;"><?php print dateForm($data_3[$j][1]) ?></a></span>
<input type="text" size="9" name="saisie_date_ret_2" style="display:none;" value="<?php print dateForm($data_3[$j][1]) ?>" onKeyPress="onlyChar(event)" id="saisie_date_ret_2_<?php print $i.$j ?>">
</td>
<td align="center" valign="top" style="padding:4px 6px;">
<select name="saisie_duree_<?php print $i ?>" class="cc-select" style="width:70px;" onChange="chargement_pendant_jour('','<?php print $i ?>','<?php print $i.$j ?>')">
<option STYLE='color:#000066;background-color:#FCE4BA'></option>
<?php for($o=0;$o<15;$o++) { print "<option STYLE='color:#000066;background-color:#CCCCFF'></option>"; } ?>
</select>
<input type="hidden" onfocus="this.blur()" name="saisie_duree_retourner_<?php print $i ?>" value="<?php print $data_3[$j][4] ?>">
<?php
$yy=$data_3[$j][4]." J";
if ($data_3[$j][4] == 0) { $yy="???"; }
if ($data_3[$j][4] == -1) { $yy=preg_replace('/\./','h',$data_3[$j][7]); }
?>
<script language="Javascript">chargement_pendant_jour('<?php print trim($yy) ?>','<?php print $i ?>','<?php print $i.$j ?>');</script>
</td>
<td align="center" valign="top" style="padding:4px 6px;"><?php print timeForm($crenDebut)."&nbsp;-&nbsp;".timeForm($crenFin) ?>
<?php $idclasse=chercheClasseEleve($data_3[$j][0]); ?>
<br><br><a href="#" onclick="open('edt_visu.php?idclasse=<?php print $idclasse ?>&date=<?php print $data_3[$j][1] ?>','edt','width=1050,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes'); return false;"><img src="image/commun/calendar3.gif" border="0"></a>
</td>
<td valign="top" style="padding:4px 6px;">
<?php
$motiftext=$data_3[$j][6];
if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
if (trim($data_3[$j][6]) == "0") { $motiftext=LANGINCONNU; }
$motiftext=preg_replace('/"/'," ",$motiftext);
?>
<select onchange="demandeMotif('<?php print $i.$j ?>',this.value)" id="motif<?php print $i.$j ?>" class="cc-select">
<option value="<?php print $motiftext ?>" STYLE="color:#000066;background-color:#FCE4BA" title="<?php print $motiftext ?>"><?php print trunchaine($motiftext,30) ?></option>
<?php affSelecMotif() ?>
<option value="autre" STYLE="color:red;background-color:#CCCCFF">autre</option>
</select>
<input type="text" value="<?php print $motiftext ?>" name="saisie_modif_<?php print $i ?>" style="display:none;" id="saisie_motif_<?php print $i.$j ?>" class="cc-select"><br>
<?php print $imgsms ?>
<label style="font-size:11px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
(<input type="checkbox" name="saisie_justifier_<?php print $i ?>" value="1" <?php if ($data_3[$j][10] == 1) { print "checked='checked'"; } ?>> <?php print LANGRTDJUS ?>)
</label><br>
<span style="font-size:11px;">Matière : <a title="<?php print $nomMatiere ?>"><?php print trunchaine($nomMatiere,15) ?></a></span>
</td>
<td align="center" valign="top" style="padding:4px 6px;">
<button type="submit" name="supp_absence" class="btn-enr" style="background:#1565c0;"><?php print LANGPER30 ?></button>
<br><span style="font-size:11px;">le <?php print dateJJMM($data_3[$j][2]) ?> <?php if (($data_3[$j][11] != "") && ($data_3[$j][11] != "00:00:00")) { print timeForm($data_3[$j][11]); } ?></span><br>
<input type="hidden" name="saisie_eleve_id_2" value="<?php print $data[$i][3] ?>">
<input type="hidden" name="saisie_date_ret_origine" value="<?php print $data_3[$j][1] ?>">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][1] ?>">
<input type="hidden" name="saisie_id_champ" value="<?php print $i ?>">
<input type="hidden" name="saisie_time" value="<?php print $data_3[$j][9] ?>">
<input type="hidden" name="saisie_matiere" value="<?php print $data_3[$j][8] ?>">
<input type="hidden" name="saisie_heuredoriginsaisie" value="<?php print $data_3[$j][11] ?>">
<input type="hidden" name="saisie_date_saisie" value="<?php print $data_3[$j][2] ?>">
<input type="hidden" name="saisie_heuredabsence" value="<?php print $heuredabsence ?>">
</form>
<form method="post" action="rattrapage.php" onsubmit="var Nwin = window.open('rattrapage.php', 'Nwin', 'width=430,height=230,toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,menubar=no'); return true;" target="Nwin">
<button type="submit" name="acces" class="btn-enr" style="background:#2e7d32;">Rattrap.</button>
<input type="hidden" name="idrattrappage" value="<?php print $idrattrapage ?>">
</form>
</td>
</TR>
<?php
}
$nbabs=$nbjoursabs * 2;
?>
</table>
<br>

<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:6px 0;">
  <form method="post" action="gestion_abs_retard_impr.php" style="margin:0;display:flex;">
    <button type="submit" class="btn-enr">Imprimer Rtd/Abs de <?php print ucwords(trim($data[$i][1]))." ".ucwords(trim($data[$i][2])) ?></button>
    <input type="hidden" name="idEleve" value="<?php print $data[$i][3] ?>">
    <input type="hidden" name="saisie_nom_eleve" value="<?php print trim($_POST["saisie_nom_eleve"] ?? "") ?>">
  </form>
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_self','')">Retour menu</button>
</div>
<hr style="border:none;border-top:1px solid #c5cae9;margin:16px 0;">
<br><br>

<?php
print "<script>document.getElementById('cumul$i').innerHTML=\"Nbr de retards: $cumulretard / Nbr d'absences: $nbabs demi-journée(s) - $nbheureabs heure(s)\"; </script>";
}
}
?>

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
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
