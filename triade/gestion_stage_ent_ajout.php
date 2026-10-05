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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/ajax-stage.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSTAGE24 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
if (isset($_GET["lien"])) {
	$lien=$_GET["lien"];
	$lienideleve=$_GET["lienideleve"];
	$lienidclasse=$_GET["lienidclasse"];
	$lienretour="$lien?id=$lienideleve&idclasse=$lienidclasse";
}
if (isset($_POST["create"])) {
	$cr=create_entreprise($_POST["nomentreprise"],$_POST["contact"],$_POST["adressesiege"],$_POST["codepostal"],$_POST["ville"],$_POST["activite"],$_POST["activiteprin"],$_POST["tel"],$_POST["fax"],$_POST["email"],$_POST["information"],$_POST["activite2"],$_POST["activite3"],$_POST["fonction"],$_POST["nbchambre"],$_POST["siteweb"],$_POST["grphotelier"],$_POST["nbetoile"],$_POST["pays"],$_POST["registrecommerce"],$_POST["siren"],$_POST["siret"],$_POST["formejuridique"],$_POST["secteureconomique"],$_POST["INSEE"],$_POST["NAFAPE"],$_POST["NACE"],$_POST["typeorganisation"],$_POST["qualite"]);
	if ($cr == 1) {
		history_cmd($_SESSION["nom"],"CREATION","ENTREPRISE STAGE");
		alertJs(LANGSTAGE58);
	} else {
		$erreur=LANGSTAGE25.".<br><br>";
	}
	$lien=$_POST["lien"];
	$lienideleve=$_POST["ideleve"];
	$lienidclasse=$_POST["idclasse"];
	$lienretour="$lien?id=$lienideleve&idclasse=$lienidclasse";
}
if (defined("VATEL")) $vatel=VATEL;
?>

<?php if ($erreur) { ?>
<div style="margin:8px 0;padding:10px 14px;background:#fff3e0;border:1px solid #ffb74d;border-radius:8px;font-size:13px;color:#e65100;font-weight:600;"><?php print $erreur ?></div>
<?php } ?>

<form method="post" name="formulaire">
<input type="hidden" value="<?php print $lienideleve ?>" name="ideleve">
<input type="hidden" value="<?php print $lienidclasse ?>" name="idclasse">
<input type="hidden" value="<?php print $lien ?>" name="lien">

<!-- ── Identification ── -->
<div class="na-card">
  <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #eef0f8;">Identification</div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE26 ?> :</span>
    <input type="text" name="nomentreprise" size="30" maxlength="50" class="cc-select" onblur="verifentreprise(this.value)">
    <span id="verifent" style="font-size:11px;color:#c62828;"></span>
  </div>

  <?php if ($vatel != "1") { ?>
  <div class="na-row">
    <span class="na-lbl">Registre du commerce :</span>
    <input type="text" name="registrecommerce" size="30" maxlength="100" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">SIREN :</span>
    <input type="text" name="siren" size="30" maxlength="100" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">SIRET :</span>
    <input type="text" name="siret" size="30" maxlength="100" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">Forme juridique :</span>
    <select name="formejuridique" class="cc-select">
      <option value="" id="select0">Choix...</option>
      <option value="SA">SA</option><option value="SARL">SARL</option>
      <option value="EURL">EURL</option><option value="SNC">SNC</option>
      <option value="SAS">SAS</option><option value="SASU">SASU</option>
      <option value="EI">EI</option><option value="Auto-Entreprise">Auto-Entreprise</option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Secteur économique :</span>
    <select name="secteureconomique" class="cc-select">
      <option value="" id="select0">Choix...</option>
      <option value="Primaire">Primaire</option>
      <option value="Secondaire">Secondaire</option>
      <option value="Tertiaire">Tertiaire</option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Secteur INSEE :</span>
    <select name="INSEE" class="cc-select">
      <option value="" id="select0">Choix...</option>
      <option value="EA Agriculture, sylviculture, pêche">EA Agriculture, sylviculture, pêche</option>
      <option value="EB Industries agricoles et alimentaires">EB Industries agricoles et alimentaires</option>
      <option value="EC Industrie des biens de consommation">EC Industrie des biens de consommation</option>
      <option value="ED Industrie automobile">ED Industrie automobile</option>
      <option value="EE Industries des biens d'équipement">EE Industries des biens d'équipement</option>
      <option value="EF Industries des biens intermédiaires">EF Industries des biens intermédiaires</option>
      <option value="EG Energie">EG Energie</option>
      <option value="EH Construction">EH Construction</option>
      <option value="EJ Commerce">EJ Commerce</option>
      <option value="EK Transports">EK Transports</option>
      <option value="EL Activités financières">EL Activités financières</option>
      <option value="EM Activités immobilières">EM Activités immobilières</option>
      <option value="EN Services aux entreprises">EN Services aux entreprises</option>
      <option value="EP Services aux particuliers">EP Services aux particuliers</option>
      <option value="EQ Éducation, santé, action sociale">EQ Éducation, santé, action sociale</option>
      <option value="ER Administration">ER Administration</option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Code NAF/APE :</span>
    <input type="text" name="NAFAPE" size="30" maxlength="100" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">Branche NACE :</span>
    <input type="text" name="NACE" size="30" maxlength="100" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">Type d'organisation :</span>
    <select name="typeorganisation" class="cc-select">
      <option value="" id="select0">Choix...</option>
      <option value="Administration">Administration</option>
      <option value="Association">Association</option>
      <option value="Entreprise">Entreprise</option>
    </select>
  </div>
  <?php } ?>
</div>

<!-- ── Contact ── -->
<div class="na-card">
  <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #eef0f8;">Contact</div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE91 ?> :</span>
    <input type="text" name="contact" size="30" maxlength="50" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE93 ?> :</span>
    <input type="text" name="fonction" size="30" maxlength="50" class="cc-select">
  </div>
</div>

<!-- ── Adresse ── -->
<div class="na-card">
  <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #eef0f8;">Adresse</div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE28." ".LANGSTAGE94 ?> :</span>
    <input type="text" name="adressesiege" maxlength="50" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE29." ".LANGSTAGE95 ?> :</span>
    <input type="text" name="codepostal" maxlength="10" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE30." ".LANGSTAGE94 ?> :</span>
    <input type="text" name="ville" maxlength="30" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS156." ".LANGSTAGE94 ?> :</span>
    <input type="text" name="pays" maxlength="50" size="30" class="cc-select">
  </div>
</div>

<!-- ── Activité ── -->
<div class="na-card">
  <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #eef0f8;">Activité</div>
  <div class="na-row" style="align-items:center;">
    <span class="na-lbl"><?php print LANGSTAGE31 ?> :</span>
    <select name="activite" class="cc-select">
      <option value="inconnu"><?php print LANGINCONNU ?></option>
      <?php $data=activite_liste(); for ($i=0;$i<countTriade($data);$i++) { print "<option value='".$data[$i][0]."'>".trunchaine(strtolower($data[$i][0]),20)."</option>"; } ?>
    </select>
    <a href="#" onclick="javascript:verifActivite();"><img src="image/commun/recycle.jpg" align="center" border="0"></a>
    [<a href="#" onclick="open('gestion_stage_activite_aj.php','stageact','width=400,height=100');" style="font-size:11px;"><?php print LANGSTAGE32 ?></a>]
  </div>
  <div class="na-row" style="align-items:center;">
    <span class="na-lbl"><?php print LANGSTAGE31bis ?> :</span>
    <select name="activite2" class="cc-select">
      <option value=""></option>
      <?php $data=activite_liste(); for ($i=0;$i<countTriade($data);$i++) { print "<option value='".$data[$i][0]."'>".trunchaine(strtolower($data[$i][0]),20)."</option>"; } ?>
    </select>
    <a href="#" onclick="javascript:verifActivite2()"><img src="image/commun/recycle.jpg" align="center" border="0"></a>
    [<a href="#" onclick="open('gestion_stage_activite_aj.php','stageact','width=400,height=100');" style="font-size:11px;"><?php print LANGSTAGE32 ?></a>]
  </div>
  <div class="na-row" style="align-items:center;">
    <span class="na-lbl"><?php print LANGSTAGE31ter ?> :</span>
    <select name="activite3" class="cc-select">
      <option value=""></option>
      <?php $data=activite_liste(); for ($i=0;$i<countTriade($data);$i++) { print "<option value='".$data[$i][0]."'>".trunchaine(strtolower($data[$i][0]),20)."</option>"; } ?>
    </select>
    <a href="#" onclick="javascript:verifActivite3();"><img src="image/commun/recycle.jpg" align="center" border="0"></a>
    [<a href="#" onclick="open('gestion_stage_activite_aj.php','stageact','width=400,height=100');" style="font-size:11px;"><?php print LANGSTAGE32 ?></a>]
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE33 ?> :</span>
    <input type="text" name="activiteprin" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGTMESS523 ?> :</span>
    <input type="text" name="grphotelier" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGTMESS524 ?> :</span>
    <input type="text" name="nbetoile" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGTMESS525 ?> :</span>
    <input type="text" name="nbchambre" size="30" class="cc-select">
  </div>
</div>

<!-- ── Coordonnées ── -->
<div class="na-card">
  <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #eef0f8;">Coordonnées</div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE34 ?> :</span>
    <input type="text" name="tel" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE35 ?> :</span>
    <input type="text" name="fax" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE36 ?> :</span>
    <input type="text" name="email" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">Qualité :</span>
    <input type="text" name="qualite" size="30" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGTMESS526 ?> :</span>
    <input type="text" name="siteweb" size="30" class="cc-select">
  </div>
  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl" style="padding-top:4px;"><?php print LANGSTAGE37 ?> :</span>
    <textarea name="information" cols="35" rows="4" style="border:1px solid #CACCEF;border-radius:5px;padding:5px 8px;font-size:12px;font-family:inherit;resize:vertical;"></textarea>
  </div>
</div>

<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
  <?php
  if ($_SESSION["membre"] == "menuprof") {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>";
  } elseif (($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menuparent")) {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage_el.php','_parent')</script>";
  } else {
      if (($lien != "") && ($lienideleve != "") && ($lienidclasse != "")) {
          print "<script language=JavaScript>buttonMagicRetour('$lienretour','_parent')</script>";
      } else {
          print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>";
      }
  }
  ?>
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
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
