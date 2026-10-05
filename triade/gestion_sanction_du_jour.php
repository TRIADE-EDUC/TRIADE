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
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
if (isset($_POST["saisie_date"])) {
	$date=$_POST["saisie_date"];
	$dateFin=$_POST["saisie_date_fin"];
} else {
	$date=dateDMY();
	$dateFin=dateDMY();
}
if (isset($_SESSION["triretenue"])) { $tri=$_SESSION["triretenue"]; } else { $tri="classe"; }
if (isset($_POST["tri"])) { $tri=$_POST["tri"]; $_SESSION["triretenue"]=$tri; }
$selectedTriNom=""; $selectedTriClasse="";
if ($tri=="classe") { $selectedTriClasse="selected='selected'"; }
if ($tri=="nom")    { $selectedTriNom="selected='selected'"; }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<script language="JavaScript">
function print_sanction_du_jour2(){
	if (confirm("Confirmez l'impression \n Service Triade")) {
		open('gestion_sanction_du_jour_print.php?debut=<?php print $date ?>&fin=<?php print $dateFin ?>&tri=<?php print $tri ?>','_blank','');
	}
}
</script>

<!-- ── Filtre date + tri ── -->
<form method="post" name="formulaire">
<div class="na-card" style="margin-bottom:10px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC2bis ?> :</span>
    <a href="#" onclick="print_sanction_du_jour2();return false;">
      <img src="./image/print.gif" align="center" border="0" alt="<?php print LANGaffec_cre41 ?>">
    </a>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGTE2 ?> :</span>
    <input type="text" name="saisie_date" value="<?php print $date ?>" onclick="this.value=''" size="12" class="cc-select">
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.saisie_date",$_SESSION["langue"],"0"); ?>
    <span style="font-size:12px;color:#555;margin:0 6px;"><?php print LANGTE11 ?></span>
    <input type="text" name="saisie_date_fin" value="<?php print $dateFin ?>" onclick="this.value=''" size="12" class="cc-select">
    <?php include_once("librairie_php/calendar.php"); calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0"); ?>
    <input type="submit" name="modif_date" value="<?php print LANGBT28 ?>" class="btn-enr" style="padding:4px 10px;">
  </div>
  <div class="na-row">
    <span class="na-lbl">Trier sur :</span>
    <select name="tri" class="cc-select" onchange="this.form.submit()">
      <option value="classe" <?php print $selectedTriClasse ?>>Par classe</option>
      <option value="nom"    <?php print $selectedTriNom ?>>Par Nom</option>
    </select>
  </div>
</div>
</form>

<!-- ── Tableau sanctions ── -->
<?php
$data=recherche_sanction_du_jour_2bis($date,$dateFin,$tri);
// id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire,description_fait
$a=0;
?>
<table class="cc-data-table">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th"><?php print LANGNA1 ?> <?php print LANGNA2 ?></th>
  <th class="cc-th"><?php print LANGELE4 ?></th>
  <th class="cc-th">Attribué par</th>
  <th class="cc-th cc-th-center">Info.</th>
  <th class="cc-th cc-th-center"><?php print LANGDISC17 ?>.</th>
</tr>
</thead>
<tbody>
<?php
for ($i=0; $i<countTriade($data); $i++) {
	$a++;
	$ideleve=$data[$i][1];
	$classe=chercheClasse(chercheIdClasseDunEleve($data[$i][1]));
	$datesaisie=dateForm($data[$i][4]);
	$message1=html_quotes($data[$i][2]);
	$message2=html_quotes($data[$i][9]);
	$fait=html_quotes($data[$i][10]);
?>
<tr class="cc-tr-data">
  <td class="cc-td"><?php print infoBulleEleveSansLoupe($data[$i][1],ucwords(recherche_eleve_nom($data[$i][1]))) ?> <?php print ucwords(recherche_eleve_prenom($data[$i][1])) ?></td>
  <td class="cc-td"><?php print $classe[0][1] ?></td>
  <td class="cc-td">&nbsp;<?php print preg_replace('/ /','&nbsp;',$data[$i][8]) ?>&nbsp;</td>
  <td class="cc-td cc-td-center">
    <a href="#" onMouseOver="AffBulle('<font class=T2><font color=#FFFFFF><u>Date de saisie</u> :</font> <?php print $datesaisie ?><br/><font color=#FFFFFF><u><?php print LANGPARENT15 ?></u></font><font color=#000000> : <?php print $message1 ?></font><br/><font color=#FFFFFF><u>Description des faits</u></font><font class=T2 color=#000000> : <?php print $fait ?></font><br/><font color=#FFFFFF><u>Devoir à faire</u></font><font class=T2 color=#000000> : <?php print $message2 ?></font>');" onMouseOut="HideBulle()">
      <img src="./image/visu.gif" align="center" border="0">
    </a>
  </td>
  <td class="cc-td cc-td-center">
    <a href="#" onMouseOver="AffBulle('<font size=2><?php print LANGABS41 ?> : <b><?php print cherchetel($ideleve) ?></b><BR><?php print "Portable 1" ?> : <b><?php print cherchetelportable1($ideleve) ?></b><br><?php print "Portable 2" ?> : <b><?php print cherchetelportable2($ideleve) ?></b><BR><?php print LANGABS39 ?> : <b><?php print cherchetelpere($ideleve) ?></b><BR><?php print LANGABS40 ?> : <b><?php print cherchetelmere($ideleve) ?></b><br>Email : <b><?php print cherchemail($ideleve) ?></b></font>');" onMouseOut="HideBulle()">
      <img src="./image/l_port.gif" align="center" border="0">
    </a>
    <input type="hidden" name="saisie_date_<?php print $a ?>" value="<?php print $data[$i][1] ?>">
    <input type="hidden" name="saisie_heure_<?php print $a ?>" value="<?php print $data[$i][2] ?>">
    <input type="hidden" name="saisie_id_<?php print $a ?>" value="<?php print $data[$i][0] ?>">
  </td>
</tr>
<?php } ?>
</tbody>
</table>
<input type="hidden" name="saisie_nb" value="<?php print $a ?>">
<br>

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
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
