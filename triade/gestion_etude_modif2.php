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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGETUDE29 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");

if (isset($_POST["modif"])) {
	$jour="";
	if (!empty($_POST["jour1"])) { $jour.=$_POST["jour1"].","; }
	if (!empty($_POST["jour2"])) { $jour.=$_POST["jour2"].","; }
	if (!empty($_POST["jour3"])) { $jour.=$_POST["jour3"].","; }
	if (!empty($_POST["jour4"])) { $jour.=$_POST["jour4"].","; }
	if (!empty($_POST["jour5"])) { $jour.=$_POST["jour5"].","; }
	if (!empty($_POST["jour6"])) { $jour.=$_POST["jour6"]; }
	$jour=preg_replace('/,$/','',$jour);
	$cr=etude_modif($_POST["nometude"],$jour,$_POST["heure_etude"],$_POST["duree_etude"],$_POST["saisie_pers_supp"],$_POST["salleetude"],$_POST["id"]);
	if ($cr) {
		history_cmd($_SESSION["nom"],"MODIFIER","Etude");
		print "<div style='margin:10px 0;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;font-size:13px;color:#2e7d32;font-weight:600;text-align:center;'>L'étude est modifiée.</div>";
	}
}

$data=liste_etude_2($_POST["id"]);
//id,jour_semaine,heure,salle,pion,nom_etude,duree

$checked1=$checked2=$checked3=$checked4=$checked5=$checked6="";
$liste=preg_replace('/[\{\}]/','',$data[0][1]);
$tab=explode(",", $liste);
foreach ($tab as $value) {
	if ($value==1) { $checked1="checked='checked'"; }
	if ($value==2) { $checked2="checked='checked'"; }
	if ($value==3) { $checked3="checked='checked'"; }
	if ($value==4) { $checked4="checked='checked'"; }
	if ($value==5) { $checked5="checked='checked'"; }
	if ($value==6) { $checked6="checked='checked'"; }
}
?>

<form method="post" onsubmit="return valideetude()" name="formulaire">
<input type="hidden" name="id" value="<?php print $_POST["id"] ?>">

<div class="na-card">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGETUDE20 ?> :</span>
    <input type="text" value="<?php print $data[0][5] ?>" name="nometude" size="12" maxlength="15" class="cc-select">
  </div>

  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl" style="padding-top:4px;"><?php print LANGETUDE21 ?> :</span>
    <div style="display:flex;flex-direction:column;gap:5px;font-size:12px;color:#333;">
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" <?php print $checked1 ?> value="1" name="jour1"> <?php print LANGLUNDI ?></label>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" <?php print $checked2 ?> value="2" name="jour2"> <?php print LANGMARDI ?></label>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" <?php print $checked3 ?> value="3" name="jour3"> <?php print LANGMERCREDI ?></label>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" <?php print $checked4 ?> value="4" name="jour4"> <?php print LANGJEUDI ?></label>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" <?php print $checked5 ?> value="5" name="jour5"> <?php print LANGVENDREDI ?></label>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" <?php print $checked6 ?> value="6" name="jour6"> <?php print LANGSAMEDI ?></label>
    </div>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGETUDE22 ?> :</span>
    <input type="text" value="<?php print $data[0][2] ?>" name="heure_etude" size="6" maxlength="5" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGETUDE23 ?> :</span>
    <select name="duree_etude" class="cc-select">
      <?php if ($data[0][6] != 0) { ?>
      <option value="<?php print $data[0][6] ?>"><?php print $data[0][6] ?></option>
      <?php } ?>
      <option value="0"><?php print LANGCHOIX ?></option>
      <option value="1h">1h</option>
      <option value="1h30">1h30</option>
      <option value="2h">2h</option>
      <option value="2h30">2h30</option>
      <option value="3h">3h</option>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGETUDE25 ?> :</span>
    <input type="text" value="<?php print $data[0][3] ?>" name="salleetude" size="12" maxlength="15" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGETUDE26 ?> :</span>
    <select name="saisie_pers_supp" class="cc-select">
      <?php if ($data[0][4] != -1) { ?>
      <option value="<?php print $data[0][4] ?>"><?php print $data[0][4] ?></option>
      <?php } ?>
      <option value="-1" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
      <optgroup label="Enseignant"><?php select_personne_nom('ENS'); ?></optgroup>
      <optgroup label="Vie Scolaire"><?php select_personne_nom('MVS'); ?></optgroup>
      <optgroup label="Administration"><?php select_personne_nom('ADM'); ?></optgroup>
    </select>
  </div>

</div>

<br>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","modif");</script>
  <button type="button" class="btn-retour" onclick="open('gestion_etude.php','_self','')">Retour</button>
</div>
<br>

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
Pgclose();
?>
</BODY></HTML>
