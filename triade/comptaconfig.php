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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaSupp.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
?>
<div style="padding:16px;">

<div class="card">
  <div class="card-header">Navigation</div>
  <div class="card-body" style="padding:0;">

    <form action='comptaconfigclasse.php' method='post'>
    <div class="form-row" style="justify-content:space-between;">
      <label class="form-label" style="flex:1;">Echéanciers et versements par classe</label>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
        <span class="htip-wrap"><img src="./image/help.gif" border="0"><span class="htip">Configuration des modalités de règlement à l'ensemble d'une classe.</span></span>
      </div>
    </div>
    </form>

    <form action='comptaconfigmodele.php' method='post'>
    <div class="form-row" style="justify-content:space-between;">
      <label class="form-label" style="flex:1;">Configuration des modalités d'échéance</label>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
        <span class="htip-wrap"><img src="./image/help.gif" border="0"><span class="htip">Configuration de modèle de modalité de règlement.</span></span>
      </div>
    </div>
    </form>

    <form action='comptaconfigeleve0.php' method='post'>
    <div class="form-row" style="justify-content:space-between;">
      <label class="form-label" style="flex:1;">Echéanciers et versements par élève</label>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
        <span class="htip-wrap"><img src="./image/help.gif" border="0"><span class="htip">Configuration des modalités de règlement pour un élève.</span></span>
      </div>
    </div>
    </form>

  </div>
</div>
<br><br>
<div class="card" style="border-color:#ffb74d;">
  <div class="card-header" style="background:#e65100;border-radius:8px 8px 0 0;">Opération de maintenance</div>
  <div class="card-body" style="padding:0;">
    <form action='base_de_donne_key.php' method='post'>
    <input type="hidden" name="modulepost" value="suppversement">
    <div class="form-row" style="justify-content:space-between;">
      <label class="form-label" style="flex:1;">Supprimer tous les versements effectués</label>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","suppversement");</script>
        <span class="htip-wrap"><img src="./image/help.gif" border="0"><span class="htip">Suppression de tous les versements. À utiliser lors du changement d'année scolaire.</span></span>
      </div>
    </div>
    </form>
  </div>
</div>

</div>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
