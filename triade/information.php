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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td><b><font id='menumodule1'><?php print LANGMESS422 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=visu_param();
// nom_ecole, adresse, postal, ville, tel, email, directeur, urlsite, accademie, pays

$nom_etablissement = "";
if (countTriade($data) > 0) {
    $nom_etablissement = trim($data[0][0]);
    $adresse           = trim($data[0][1]);
    $postal            = trim($data[0][2]);
    $ville             = trim($data[0][3]);
    $tel               = trim($data[0][4]);
    $mail              = trim($data[0][5]);
    $directeur         = trim($data[0][6]);
    $urlsite           = trim($data[0][7]);
    $accademie         = trim($data[0][8]);
    $pays              = trim($data[0][9]);
    if ($urlsite !== "" && !preg_match("/^https?:\/\//", $urlsite)) {
        $urlsite = "http://".$urlsite;
    }
}
Pgclose();

function inf_row($label, $value) {
    if (trim($value) === "") return;
    echo "<div class='inf-row'>"
       . "<span class='inf-lbl'>".htmlspecialchars($label)."</span>"
       . "<span class='inf-val'>".htmlspecialchars($value)."</span>"
       . "</div>";
}
?>

<div class="inf-card">
  <div class="inf-title"><?php print htmlspecialchars($nom_etablissement) ?></div>

  <?php inf_row(LANGPARAM37,  $accademie); ?>
  <?php inf_row(LANGPARAM9,   $adresse);   ?>
  <?php inf_row(LANGPARAM10,  $postal);    ?>
  <?php inf_row(LANGPARAM11,  $ville);     ?>
  <?php inf_row(LANGAGENDA73, $pays);      ?>
  <?php inf_row(LANGPARAM12,  $tel);       ?>
  <?php inf_row(LANGPARAM13,  $mail);      ?>

  <?php if ($urlsite !== "") { ?>
  <div class="inf-row">
    <span class="inf-lbl"><?php print htmlspecialchars(LANGPARAM34) ?></span>
    <span class="inf-val">
      <a href="<?php print htmlspecialchars($urlsite) ?>" target="_blank">
        <?php print htmlspecialchars($urlsite) ?>
      </a>
    </span>
  </div>
  <?php } ?>
</div>

<?php if ((LAN == "oui") && ($ville !== "") && ($adresse !== "") && ($pays !== "")) { ?>
<div class="inf-map">
  <iframe
    src="https://support.triade-educ.org/support/google-map-V3-triade.php?etablissement=<?php print urlencode($nom_etablissement) ?>&adresse=<?php print urlencode($adresse) ?>&ville=<?php print urlencode($ville) ?>&pays=<?php print urlencode($pays) ?>&web=<?php print urlencode($urlsite) ?>"
    width="400" height="300"
    marginwidth="0" marginheight="0" hspace="0" vspace="0"
    frameborder="0" scrolling="no">
  </iframe>
</div>
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
?>
</BODY></HTML>
