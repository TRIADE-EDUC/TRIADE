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
$anneeScolaire=$_COOKIE["anneeScolaire"];
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade Vidéo-Projecteur</title>
<style>
#coulfond1 { background-image: none; }
.projo-wrap { max-width: 580px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px; padding: 16px; }
.projo-label { font-size: 12px; font-weight: 600; color: #333; }
.form-row label { font-size: 12px; font-weight: 600; }
</style>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_attente.php"); ?>
<?php include("./librairie_php/lib_licence.php"); ?>

<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}else{
	validerequete("3");
}
$valeur=aff_Trimestre();
if (countTriade($valeur)) {
	$disabled="";
}else{
	$disabled="disabled=disabled";
}
?>

<div class="projo-wrap">

<?php if (isset($_GET["info"])): ?>
<div class="alert alert-danger"><?php print LANGPROJ6; ?></div>
<?php endif; ?>

<!-- Vidéo-Projecteur classe -->
<div class="card">
  <div class="card-header"><span class="card-title"><i class="bi bi-projector-fill"></i> <?php print LANGPROFP32 ?></span></div>
  <div style="padding:14px">
    <form method=post action="video-proj-affichage.php" name="formulaire0"
      onSubmit="document.formulaire0.supp.value='Patientez S.V.P.';document.formulaire.supp.disabled=true;document.formulairean.supp.disabled=true;document.formulaire0.supp.disabled=true;AfficheAttente();">
      <input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
      <input type=hidden name="fichier_origin" value="profpprojo">
      <div class="form-row">
        <label><?php print LANGBULL3?></label>
        <select name='annee_scolaire'><?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?></select>
      </div>
      <div class="form-row">
        <label><?php print LANGPROJ2?></label>
        <select name="saisie_trimestre">
          <option value="trimestre1"><?php print LANGPROJ3?> <?php print LANGOU ?> <?php print LANGPROJ19?></option>
          <option value="trimestre2"><?php print LANGPROJ4?> <?php print LANGOU ?> <?php print LANGPROJ20?></option>
          <option value="trimestre3"><?php print LANGPROJ5?></option>
        </select>
      </div>
      <?php if ((defined("MODNAMUR0")) && (MODNAMUR0 == "oui")): ?>
      <div class="form-row">
        <label>Avec note vie scolaire</label>
        <label style="font-weight:400"><input type='radio' name='validenoteviescolaire' value='oui' checked='checked' /> Oui</label>&nbsp;
        <label style="font-weight:400"><input type='radio' name='validenoteviescolaire' value='non' /> Non</label>
      </div>
      <?php endif; ?>
      <div style="margin-top:12px"><script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script></div>
    </form>
  </div>
</div>

<!-- Tableau de notes par période -->
<div class="card">
  <div class="card-header"><span class="card-title"><i class="bi bi-table"></i> <?php print LANGPROFP31 ?></span></div>
  <div style="padding:14px">
    <form method=post action="tableaupp2.php" name="formulaire"
      onSubmit="document.formulaire.supp.value='Patientez S.V.P.';document.formulaire.supp.disabled=true;document.formulairean.supp.disabled=true;document.formulaire0.supp.disabled=true;AfficheAttente();">
      <input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
      <div class="form-row">
        <label><?php print LANGBASE40 ?></label>
        <select id="tt_pp" name="typetrisem">
          <option value=0><?php print LANGCHOIX?></option>
          <option value="trimestre"><?php print LANGPARAM28?></option>
          <option value="semestre"><?php print LANGPARAM29?></option>
        </select>
        <select id="st_pp" name="saisie_trimestre" style="margin-left:6px">
          <option></option><option></option><option></option>
        </select>
        <script>(function(){var src=document.getElementById('tt_pp'),dst=document.getElementById('st_pp'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'';dst.options[i].value=o[i]?o[i][1]:'';}dst.selectedIndex=0;};})()</script>
      </div>
      <div class="form-row">
        <label><?php print LANGBULL3?></label>
        <select name='annee_scolaire'><?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?></select>
      </div>
      <div class="form-row">
        <label>Afficher le classement</label>
        <input type="checkbox" name="affrang" value="1" id="affrang"> <label for="affrang" style="font-weight:400">Oui</label>
      </div>
      <div class="form-row">
        <label>Afficher les colonnes vides</label>
        <input type="checkbox" name="affcolvide" value="1" id="affcolvide"> <label for="affcolvide" style="font-weight:400">Oui</label>
      </div>
      <div class="form-row">
        <label>Prise en compte note examen</label>
        <input type="checkbox" name="noteexamen" id="noteexamen" value="oui"> <label for="noteexamen" style="font-weight:400">Oui</label>
      </div>
      <div class="form-row">
        <label>Seulement les notes de type examen</label>
        <?php include("typeexamen.php"); ?>
      </div>
      <div style="margin-top:12px"><script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script></div>
    </form>
  </div>
</div>

<!-- Tableau annuel -->
<div class="card">
  <div class="card-header"><span class="card-title"><i class="bi bi-bar-chart-fill"></i> <?php print LANGPROFP39 ?></span></div>
  <div style="padding:14px">
    <form method=post action="tableaupp2an.php" name="formulairean"
      onSubmit="document.formulairean.supp.value='Patientez S.V.P.';document.formulaire.supp.disabled=true;document.formulairean.supp.disabled=true;document.formulaire0.supp.disabled=true;AfficheAttente();">
      <input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
      <div class="form-row">
        <label>Jusqu'au :</label>
        <select id="tt_ppan" name="typetriseman">
          <option value=0><?php print LANGCHOIX?></option>
          <option value="trimestre"><?php print LANGPARAM28?></option>
          <option value="semestre"><?php print LANGPARAM29?></option>
        </select>
        <select id="st_ppan" name="saisie_trimestre" style="margin-left:6px">
          <option></option><option></option><option></option>
        </select>
        <script>(function(){var src=document.getElementById('tt_ppan'),dst=document.getElementById('st_ppan'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],[' ','trimestre3']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'';dst.options[i].value=o[i]?o[i][1]:'';}dst.selectedIndex=0;};})()</script>
      </div>
      <div class="form-row">
        <label><?php print LANGBULL3?></label>
        <select name='annee_scolaire'><?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?></select>
      </div>
      <div class="form-row">
        <label>Afficher le classement</label>
        <input type="checkbox" name="affrang" value="1" id="affrang2"> <label for="affrang2" style="font-weight:400">Oui</label>
      </div>
      <div class="form-row">
        <label>Afficher les colonnes vides</label>
        <input type="checkbox" name="affcolvide" value="1" id="affcolvide2"> <label for="affcolvide2" style="font-weight:400">Oui</label>
      </div>
      <div class="form-row">
        <label>Prise en compte note examen</label>
        <input type="checkbox" name="noteexamen" id="noteexamen2" value="oui"> <label for="noteexamen2" style="font-weight:400">Oui</label>
      </div>
      <div class="form-row">
        <label>Seulement les notes de type examen</label>
        <?php include("typeexamen.php"); ?>
      </div>
      <div style="margin-top:12px"><script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script></div>
    </form>
  </div>
</div>

</div>

<?php attente(); Pgclose(); ?>
</body>
</html>
