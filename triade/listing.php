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
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGAFF4?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="display:flex;flex-direction:column;gap:16px;padding:12px 8px">

<!-- Edition d'une classe -->
<div class="card">
  <div class="card-body">
    <form method="post" action="listing1.php" name="formulaire" onsubmit="return valide_supp_choix('saisie_classe_edition','une classe')">
      <div class="form-row">
        <span class="form-label"><?php print LANGBULL3 ?> :</span>
        <select name="anneeScolaire" class="form-control">
          <option id='select0'><?php print LANGCHOIX ?></option>
          <?php filtreAnneeScolaireSelect($anneeScolaire); ?>
        </select>
      </div>
      <div class="form-row">
        <span class="form-label"><?php print LANGPER25 ?> :</span>
        <select name="saisie_classe_edition" class="form-control">
          <option style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
          <option value="tous"><?php print LANGAFF5?></option>
          <?php select_classe(); ?>
        </select>
      </div>
      <div class="form-row" style="border-bottom:none;padding-top:8px">
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGAFF6 ?>","rien")</script>
      </div>
    </form>
  </div>
</div>

<!-- Edition d'un enseignant -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Edition d'un enseignant</span>
  </div>
  <div class="card-body">
    <form method="post" action="listingE1.php" name="formulaire" onsubmit="return valide_supp_choix('saisie_classe_edition','une classe')">
      <div class="form-row">
        <span class="form-label"><?php print LANGBULL3 ?> :</span>
        <select name="anneeScolaire" class="form-control">
          <option id='select0'><?php print LANGCHOIX ?></option>
          <?php filtreAnneeScolaireSelect($anneeScolaire); ?>
        </select>
      </div>
      <div class="form-row">
        <span class="form-label"><?php print LANGNA1." ".LANGNA2 ?> :</span>
        <select name="idprof" class="form-control">
          <option id='select0'><?php print LANGCHOIX ?></option>
          <option value="tous">Tous sélectionnés</option>
          <?php select_personne('ENS'); ?>
        </select>
      </div>
      <div class="form-row" style="border-bottom:none;padding-top:8px">
        <script language="JavaScript">buttonMagicSubmit("Consulter","rien")</script>
      </div>
    </form>
  </div>
</div>

</div>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' ";
    print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
    print "</SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' ";
    print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
    print "</SCRIPT>";

    top_d();

    print "<SCRIPT language='JavaScript' ";
    print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
    print "</SCRIPT>";
endif;
?>
</BODY></HTML>
