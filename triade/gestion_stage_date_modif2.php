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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_stage.js"></script>
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSTAGE44 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("2");

if (isset($_POST["create"])) {
	if ($_POST["id"] != "") {
		$cr=stage_modif($_POST["id"],$_POST["num"],$_POST["debutdate"],$_POST["findate"],$_POST["saisie_classe"],$_POST["nom_stage"],$_POST["jourstage"],$_POST["duree_hebdo"]);
		$cr=1;
		if ($cr == 1) {
			history_cmd($_SESSION["nom"],"MODIFICATION","date de stage");
			print "<div style='margin:10px 0;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;font-size:13px;color:#2e7d32;font-weight:600;text-align:center;'>".LANGSTAGE53."</div>";
		} else {
			print "<div style='margin:10px 0;padding:10px 14px;background:#fff3e0;border:1px solid #ffb74d;border-radius:8px;font-size:13px;color:#e65100;font-weight:600;text-align:center;'>".LANGSTAGE52."</div>";
		}
	} else {
		$cr=stage_ajout($_POST["num"],$_POST["debutdate"],$_POST["findate"],$_POST["saisie_classe"],$_POST["nom_stage"],$_POST["duree_hebdo"]);
		$cr=1;
		if ($cr == 1) {
			history_cmd($_SESSION["nom"],"CREATION","date de stage");
			print "<div style='margin:10px 0;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;font-size:13px;color:#2e7d32;font-weight:600;text-align:center;'>";
			print LANGSTAGE54." ".$_POST["debutdate"]." au ".$_POST["findate"]."<br>";
			print LANGSTAGE55." ".chercheClasse_nom($_POST["saisie_classe"])." ".LANGSTAGE56.".";
			print "</div>";
		} else {
			print "<div style='margin:10px 0;padding:10px 14px;background:#fff3e0;border:1px solid #ffb74d;border-radius:8px;font-size:13px;color:#e65100;font-weight:600;text-align:center;'>".LANGSTAGE52."</div>";
		}
	}
}

$submitform = "onsubmit='return validedatestage()'";

if (isset($_GET["id"])) {
	$data=recherchedatestage($_GET["id"]);
	// idclasse,datedebut,datefin,numstage,id,nom_stage,jourdesemaine
	for ($i=0; $i<countTriade($data); $i++) {
		$numstage   = $data[$i][3];
		$datedebut  = dateForm($data[$i][1]);
		$datefin    = dateForm($data[$i][2]);
		$nomstage   = $data[$i][5];
		$jourdesemaine = $data[$i][6];
		$duree_hebdo   = $data[$i][7];
		$idclasse = "<option style='color:#000066;background-color:#FCE4BA' value='".$data[$i][0]."'>".chercheClasse_nom($data[$i][0])."</option>";
		$id = $data[$i][4];
	}
	$submitform = "onsubmit='return validedatestage2()'";

	$liste=explode(',',$jourdesemaine);
	foreach ($liste as $key=>$value) {
		if ($value=='1') $checkL  ="checked='checked'";
		if ($value=='2') $checkMA ="checked='checked'";
		if ($value=='3') $checkME ="checked='checked'";
		if ($value=='4') $checkJ  ="checked='checked'";
		if ($value=='5') $checkV  ="checked='checked'";
		if ($value=='6') $checkS  ="checked='checked'";
		if ($value=='7') $checkD  ="checked='checked'";
	}
}

if ($duree_hebdo == "") $duree_hebdo = 35;
?>

<form method="post" <?php print $submitform ?> name="formulaire">
<input type="hidden" name="id" value="<?php print $id ?>">

<div class="na-card">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE48 ?> :</span>
    <input type="text" name="num" size="3" value="<?php print $numstage ?>" class="cc-select" style="width:70px;">
  </div>

  <div class="na-row">
    <span class="na-lbl">Nom de stage :</span>
    <input type="text" name="nom_stage" size="30" maxlength="50" value="<?php print $nomstage ?>" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE45 ?> :</span>
    <input type="text" name="debutdate" size="12" value="<?php print $datedebut ?>" class="cc-select" maxlength="10">
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.debutdate",$_SESSION["langue"],"0"); ?>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE46 ?> :</span>
    <input type="text" name="findate" size="12" value="<?php print $datefin ?>" class="cc-select" maxlength="10">
    <?php include_once("librairie_php/calendar.php"); calendar("id2","document.formulaire.findate",$_SESSION["langue"],"0"); ?>
  </div>

  <div class="na-row">
    <span class="na-lbl">Durée hebdomadaire :</span>
    <input type="number" name="duree_hebdo" size="3" value="<?php print $duree_hebdo ?>" class="cc-select" style="width:70px;">
    <span style="font-size:12px;color:#888;">heures</span>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGELE4 ?> :</span>
    <select name="saisie_classe" class="cc-select">
      <?php if (!isset($_GET['id'])) { ?>
      <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
      <?php } ?>
      <?php print $idclasse; select_classe(); ?>
    </select>
  </div>

  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl" style="padding-top:4px;">En Entreprise le :</span>
    <div style="display:flex;flex-wrap:wrap;gap:10px;font-size:12px;color:#333;">
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="1" id="j1" <?php print $checkL ?>> L</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="2" id="j2" <?php print $checkMA ?>> M</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="3" id="j3" <?php print $checkME ?>> M</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="4" id="j4" <?php print $checkJ ?>> J</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="5" id="j5" <?php print $checkV ?>> V</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="6" id="j6" <?php print $checkS ?>> S</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="7" id="j7" <?php print $checkD ?>> D</label>
    </div>
  </div>

</div>

<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE47 ?>","create");</script>
  <script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent');</script>
</div>
</form>

<!-- // fin  -->
</td></tr></table>

<?php
if ($_SESSION['membre'] == "menuadmin") :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
