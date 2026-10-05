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
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 10px">
  <b><font id='menumodule1'><i class="bi bi-pen-fill" style="margin-right:6px"></i>Visa Vie Scolaire</font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:16px">

<div class="card" style="max-width:560px;margin:0 auto">
<form method="post" onsubmit="return valide_consul_classe4()" name="formulaire" action="visa_scolaire2.php">
<div class="card-body" style="padding:0">

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right"><?php print LANGBULL3 ?></label>
    <select name='annee_scolaire' style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px">
      <?php
      $anneeScolaire=$_COOKIE["anneeScolaire"];
      filtreAnneeScolaireSelectNote($anneeScolaire,3);
      ?>
    </select>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right"><?php print LANGPROFG ?></label>
    <select id="saisie_classe" name="saisie_classe" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;flex:1">
      <option id='select0'><?php print LANGCHOIX?></option>
      <?php select_classe(); ?>
    </select>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right"><?php print LANGBASE40 ?></label>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <select id="tt_vs" name="typetrisem" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px">
        <option value=0><?php print LANGCHOIX?></option>
        <option value="trimestre"><?php print LANGPARAM28?></option>
        <option value="semestre"><?php print LANGPARAM29?></option>
      </select>
      <select id="st_vs" name="saisie_trimestre" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px">
        <option></option>
        <option></option>
        <option></option>
      </select>
      <script>(function(){var src=document.getElementById('tt_vs'),dst=document.getElementById('st_vs'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'';dst.options[i].value=o[i]?o[i][1]:'';}dst.selectedIndex=0;};})()</script>
    </div>
  </div>

  <div style="padding:12px 16px;display:flex;gap:8px;justify-content:center">
    <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","consult");</script>
  </div>

</div>
</form>
</div>

<div style="max-width:560px;margin:12px auto;padding:8px 12px;background:#f0f2fa;border-left:3px solid #5c6bc0;border-radius:4px;font-size:11px;color:#555;font-style:italic">
  <i class="bi bi-info-circle" style="margin-right:5px;color:#5c6bc0"></i>Ce module est lié à certains bulletins scolaires dont les commentaires ne sont pas rattachés à la matière "Vie Scolaire". Si vous souhaitez saisir un commentaire sur cette matière, consultez le module "Note vie scolaire".
</div>

<?php
if (isset($_POST["namur"])) {
  $cr=enr_comport_namur($_POST["periode_1_namur"],$_POST["periode_2_namur"],$_POST["periode_3_namur"]);
  if ($cr) {
    alertJs(LANGDONENR);
  }
}
?>

<?php if (MODNAMUR0 == "oui") { ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:8px">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 10px">
  <b><font id='menumodule1'><i class="bi bi-chat-left-text" style="margin-right:6px"></i>Information comportement social et personnel</font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:16px">

<div class="card" style="max-width:560px;margin:0 auto">
<form method="post">
<div class="card-body" style="padding:0">

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6">
    <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">1<sup>re</sup> période</label>
    <textarea cols="60" rows="4" name="periode_1_namur" onkeypress="compter(this,'250', this.form.CharRestant_1)"
      style="width:100%;font-size:12px;border:1px solid #c5cae9;border-radius:4px;padding:6px;box-sizing:border-box;resize:vertical"><?php $com=recup_comport_namur("perio_1_namur"); print "$com"; $nbtexte=strlen($com); $com=""; ?></textarea>
    <span style="font-size:11px;color:#888">Caractères : <input type=text name='CharRestant_1' size=3 disabled='disabled' value='<?php print $nbtexte ?>' style="font-size:11px;border:none;background:transparent;width:32px"></span>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6">
    <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">2<sup>e</sup> période</label>
    <textarea cols="60" rows="4" name="periode_2_namur" onkeypress="compter(this,'250', this.form.CharRestant_2)"
      style="width:100%;font-size:12px;border:1px solid #c5cae9;border-radius:4px;padding:6px;box-sizing:border-box;resize:vertical"><?php $com=recup_comport_namur("perio_2_namur"); print "$com"; $nbtexte=strlen($com); $com=""; ?></textarea>
    <span style="font-size:11px;color:#888">Caractères : <input type=text name='CharRestant_2' size=3 disabled='disabled' value='<?php print $nbtexte ?>' style="font-size:11px;border:none;background:transparent;width:32px"></span>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6">
    <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px">3<sup>e</sup> période</label>
    <textarea cols="60" rows="4" name="periode_3_namur" onkeypress="compter(this,'250', this.form.CharRestant_3)"
      style="width:100%;font-size:12px;border:1px solid #c5cae9;border-radius:4px;padding:6px;box-sizing:border-box;resize:vertical"><?php $com=recup_comport_namur("perio_3_namur"); print "$com"; $nbtexte=strlen($com); $com=""; ?></textarea>
    <span style="font-size:11px;color:#888">Caractères : <input type=text name='CharRestant_3' size=3 disabled='disabled' value='<?php print $nbtexte ?>' style="font-size:11px;border:none;background:transparent;width:32px"></span>
  </div>

  <div style="padding:12px 16px;display:flex;gap:8px;justify-content:center">
    <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","namur");</script>
  </div>

</div>
</form>
</div>

</td></tr></table>

<?php } ?>

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

</BODY>
</HTML>
