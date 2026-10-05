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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION['nom']).' '.htmlspecialchars($_SESSION['prenom']) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx = cnx();
if (isset($_POST["supp"])) {
    $personne = recherche_personne($_POST["saisie_pers_supp"]);
    $email    = rechercheEmailViaId($_POST["saisie_pers_supp"]);
    if (trim($email) != "support@triade-educ.org") {
        $cr = suppression_personnel($_POST["saisie_pers_supp"]);
        if ($cr) {
            @delete_comptaVacation($_POST["saisie_pers_supp"]);
            @unlink("data/image_pers/".$_POST["saisie_pers_supp"].".gif");
            @unlink("data/image_pers/".$_POST["saisie_pers_supp"].".jpg");
            alertJs(LANGSUPP6);
            history_cmd($_SESSION["nom"], "SUPPRESSION", $personne);
            reload_page('suppression_compte_admin.php');
        }
    } else {
        alertJs("Ce compte ne peut être supprimé.\\n\\n L'Equipe Triade");
    }
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<form method="post" onsubmit="return valide_supp_choix('saisie_pers_supp','<?php print LANGSUPP12 ?>')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSUPP9." ".LANGDIRECTION ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<br><br>
<div class="sca-card">
  <fieldset class="sca-fieldset">
    <legend class="sca-legend"><?php print LANGSUPP1 ?></legend>

    <div class="sca-field-row">
      <label class="sca-lbl"><?php print LANGNA1." ".LANGNA2 ?></label>
      <select name="saisie_pers_supp" class="sca-select">
        <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
        <?php select_personne('ADM'); Pgclose(); ?>
      </select>
    </div>

  </fieldset>
<br>
  <div class="sca-actions">
    <?php brmozilla($_SESSION["navigateur"]); ?>
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGSUPP10 ?>","supp");</script>
    <?php brmozilla($_SESSION["navigateur"]); ?>
  </div>
</div>
<br><br>
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
