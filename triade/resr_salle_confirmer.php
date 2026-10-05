<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0)) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGRESA4 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" name="formulaire">

<?php
if (isset($_POST["create"])) {
	for ($i=1; $i<=$_POST["nbtotal"]; $i++) {
		$valide="valide$i";
		$id="id$i";
		valide_equip($_POST[$valide],$_POST[$id],$_SESSION["id_pers"],$_SERVER["SERVER_NAME"]);
	}
}

$data=list_equip_valide('salle');
// id,idmatos,idqui,quand,heure_depart,heure_fin,info,valider
?>

<table class="cc-data-table">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th" style="width:5%">Date</th>
  <th class="cc-th"><?php print LANGRESA59 ?></th>
  <th class="cc-th cc-th-center">
    <?php print LANGVALIDE ?>&nbsp;
    <input type="radio" onclick="checkRadio('1')" name="tous">
  </th>
  <th class="cc-th cc-th-center">
    <?php print LANGRESA63 ?>&nbsp;
    <input type="radio" onclick="checkRadio('0')" name="tous">
  </th>
</tr>
</thead>
<tbody>
<?php
$j=0;
for ($i=0; $i<countTriade($data); $i++) {
	$j++;
	$heureDepart=timeForm($data[$i][4]);
	$heurefin=timeForm($data[$i][5]);
	$info=html_quotes($data[$i][6]);
	if ($data[$i][3] == "0000-00-00") { supp_resa($data[$i][0],"oui"); continue; }
?>
<tr id="tr<?php print $i ?>" class="cc-tr-data">
  <td class="cc-td" style="white-space:nowrap;">
    le <?php print dateForm($data[$i][3]) ?><br>
    de <?php print $heureDepart ?> à <?php print $heurefin ?>
  </td>
  <td class="cc-td">
    <a href="#" onMouseOver="AffBulle('<font class=\'T1\'>entre <?php print $heureDepart ?> et <?php print $heurefin ?><br/><?php print $info ?></font>'); window.status=''; return true;" onMouseOut="HideBulle()" style="color:#080A66;">
      <?php print recherche_equip($data[$i][1]) ?>
    </a>
    <input type="hidden" name="id<?php print $j ?>" value="<?php print $data[$i][0] ?>">
    <span style="font-size:11px;color:#666;"> — pour <?php print recherche_personne($data[$i][2]) ?></span>
  </td>
  <td class="cc-td cc-td-center">
    <input type="radio" name="valide<?php print $j ?>" value="1" onclick="DisplayLigne2('tr<?php print $i ?>',this.value);">
  </td>
  <td class="cc-td cc-td-center">
    <input type="radio" name="valide<?php print $j ?>" value="2" onclick="DisplayLigne2('tr<?php print $i ?>',this.value);">
  </td>
</tr>
<?php } ?>
</tbody>
</table>

<script>
function checkRadio(etat) {
	var nb="<?php print countTriade($data) * 3 + 3 ?>";
	for(var i=2; i<nb; i++) {
		if ((etat=="1") && (document.formulaire.elements[i].value=='1')) {
			document.formulaire.elements[i].checked='true';
		}
		if ((etat=="0") && (document.formulaire.elements[i].value=='2')) {
			document.formulaire.elements[i].checked='true';
		}
	}
}
</script>

<input type="hidden" name="nbtotal" value="<?php print countTriade($data) ?>">

<div class="na-foot" style="margin-top:10px;">
  <script language=JavaScript>buttonMagicRetour("resr_admin.php","_parent");</script>
  <script language=JavaScript>buttonMagicSubmit("Enregistrer","create");</script>
</div>

</form>

</td></tr></table>

<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
