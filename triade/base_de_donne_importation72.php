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
<script>function suite() { location.href="./base_de_donne_key.php?base=<?php print $_GET["id"]?>"; }</script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Module d'importation de fichier</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<div class="na-card">
  <div style="font-size:13px;font-weight:700;color:#080A66;margin-bottom:6px;">Module d'importation de fichier Excel</div>
  <div style="font-size:12px;color:#555;margin-bottom:10px;"><?php print LANGIMP7 ?></div>
  <p style="font-size:12px;color:#333;margin:0 0 10px;">Le fichier Excel à transmettre <strong>DOIT contenir 25 champs</strong> dans l'ordre suivant :</p>

  <table class="cc-data-table">
  <tbody>
  <tr class="cc-tr-data">
    <td class="cc-td">1) Nom entreprise *</td>
    <td class="cc-td">2) Registre du commerce</td>
    <td class="cc-td">3) SIREN</td>
    <td class="cc-td">4) SIRET</td>
  </tr>
  <tr class="cc-tr-data">
    <td class="cc-td">5) Forme Juridique</td>
    <td class="cc-td">6) Secteur Économique</td>
    <td class="cc-td">7) INSEE</td>
    <td class="cc-td">8) NAF/APE</td>
  </tr>
  <tr class="cc-tr-data">
    <td class="cc-td">9) NACE</td>
    <td class="cc-td">10) Type d'organisation</td>
    <td class="cc-td">11) Nom du responsable</td>
    <td class="cc-td">12) Fonction du responsable</td>
  </tr>
  <tr class="cc-tr-data">
    <td class="cc-td">13) Adresse entreprise</td>
    <td class="cc-td">14) Code postal entreprise</td>
    <td class="cc-td">15) Ville entreprise</td>
    <td class="cc-td">16) Pays entreprise</td>
  </tr>
  <tr class="cc-tr-data">
    <td class="cc-td">17) Secteur activité</td>
    <td class="cc-td">18) 2ème secteur activité</td>
    <td class="cc-td">19) 3ème secteur activité</td>
    <td class="cc-td">20) Activité principale</td>
  </tr>
  <tr class="cc-tr-data">
    <td class="cc-td">21) Téléphone</td>
    <td class="cc-td">22) Fax</td>
    <td class="cc-td">23) Email</td>
    <td class="cc-td">24) Site Web</td>
  </tr>
  <tr class="cc-tr-data">
    <td class="cc-td">25) Informations</td>
    <td class="cc-td"></td>
    <td class="cc-td"></td>
    <td class="cc-td"></td>
  </tr>
  </tbody>
  </table>

  <?php if (LANGIMP49) { ?>
  <div style="margin-top:10px;padding:8px 12px;background:#fff3e0;border:1px solid #ffb74d;border-radius:6px;font-size:12px;color:#e65100;"><?php print LANGIMP49 ?></div>
  <?php } ?>
</div>

<div class="na-foot">
  <button type="button" class="btn-retour" onclick="open('gestion_stage.php','_self','')">Retour</button>
  <button type="button" class="btn-enr" onclick="open('./librairie_php/import-entreprise.xls','_blank','')">Exemple fichier XLS</button>
  <button type="button" class="btn-enr" onclick="suite()"><?php print LANGBTS ?></button>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'2.js'?>"></SCRIPT>
</BODY></HTML>
