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
<?php
include_once("./librairie_php/lib_licence.php");
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

<!-- ── Filtre date + tri ── -->
<form method="post" name="formulaire">
<div class="na-card" style="margin-bottom:10px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC2 ?> :</span>
    <a href="#" onclick="print_retenue_du_jour_2('<?php print $date ?>','<?php print $dateFin ?>','<?php print $tri ?>');return false;">
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

<!-- ── Tableau retenues ── -->
<form method="post" action="gestion_discipline_retenue_effectuer.php">
<?php
$data=recherche_retenue_du_jour_2bis($date,$dateFin,$tri);
// id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait
$a=0;
?>
<table class="cc-data-table">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th"><?php print LANGNA1 ?> <?php print LANGNA2 ?></th>
  <th class="cc-th"><?php print LANGELE4 ?></th>
  <th class="cc-th">Date et <?php print LANGAGENDA144 ?></th>
  <th class="cc-th cc-th-center"><?php print LANGDISC16 ?></th>
  <th class="cc-th cc-th-center"><?php print LANGABS12 ?></th>
  <th class="cc-th cc-th-center"><?php print LANGDISC17 ?>.</th>
</tr>
</thead>
<tbody>
<?php
for ($i=0; $i<countTriade($data); $i++) {
	$a++;
	$ideleve=$data[$i][0];
	$classe=chercheClasse(chercheIdClasseDunEleve($data[$i][0]));
	$message1=html_quotes($data[$i][7]);
	$message2=html_quotes($data[$i][11]);
	$fait=html_quotes($data[$i][12]);
	$checked = (($data[$i][6]==1) || ($data[$i][6]=='t')) ? "checked" : "";
?>
<tr id="tr<?php print $i ?>" class="cc-tr-data">
  <td class="cc-td"><?php print infoBulleEleveSansLoupe($data[$i][0],ucwords(recherche_eleve_nom($data[$i][0]))) ?> <?php print ucwords(recherche_eleve_prenom($data[$i][0])) ?></td>
  <td class="cc-td"><?php print $classe[0][1] ?></td>
  <td class="cc-td">Le <?php print dateForm($data[$i][1]) ?><br>à <?php print timeForm($data[$i][2]) ?> durant <?php print timeForm($data[$i][10]) ?></td>
  <td class="cc-td cc-td-center">
    <input type="checkbox" <?php print $checked ?> name="saisie_<?php print $a ?>" onclick="DisplayLigne('tr<?php print $i ?>');">
  </td>
  <td class="cc-td cc-td-center">
    <a href="#" onMouseOver="AffBulle('<font class=T2><font color=#FFFFFF><u><?php print LANGPARENT15 ?></u></font><font color=#000000> : <?php print $message1 ?></font><br/><font color=#FFFFFF><u>Description des faits</u></font><font class=T2 color=#000000> : <?php print $fait ?></font><br/><font color=#FFFFFF><u>Devoir à faire</u></font><font class=T2 color=#000000> : <?php print $message2 ?></font><br/><font class=T2 color=#FFFFFF>Saisie le </font><font class=T2 color=#000000><?php print dateForm($data[$i][3]) ?></font>');" onMouseOut="HideBulle()">
      <img src="./image/visu.gif" align="center" border="0">
    </a>
  </td>
  <td class="cc-td cc-td-center">
    <a href="#" onMouseOver="AffBulle('<font size=2><?php print LANGABS41 ?> : <b><?php print cherchetel($ideleve) ?></b><BR><?php print "Portable 1" ?> : <b><?php print cherchetelportable1($ideleve) ?></b><br><?php print "Portable 2" ?> : <b><?php print cherchetelportable2($ideleve) ?></b><BR><?php print LANGABS39 ?> : <b><?php print cherchetelpere($ideleve) ?></b><BR><?php print LANGABS40 ?> : <b><?php print cherchetelmere($ideleve) ?></b><br>Email : <b><?php print cherchemail($ideleve) ?></b></font>');" onMouseOut="HideBulle()">
      <img src="./image/l_port.gif" align="center" border="0">
    </a>
    <input type="hidden" name="saisie_date_<?php print $a ?>"  value="<?php print $data[$i][1] ?>">
    <input type="hidden" name="saisie_heure_<?php print $a ?>" value="<?php print $data[$i][2] ?>">
    <input type="hidden" name="saisie_id_<?php print $a ?>"   value="<?php print $data[$i][0] ?>">
  </td>
</tr>
<?php
	if ($checked) {
		print "<script>document.getElementById('tr$i').style.backgroundColor='#C0C0C0';</script>";
	}
}
?>
</tbody>
</table>
<br>
<input type="hidden" name="saisie_nb" value="<?php print $a ?>">
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("Mise à jour des retenues","rien");</script>
</div>
</form>

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
