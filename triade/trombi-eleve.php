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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();

$idEleve      = $_SESSION["id_pers"];
$saisie_classe= chercheIdClasseDunEleve($idEleve);
$sql = "SELECT libelle, elev_id, nom, prenom, photo
        FROM {$prefixe}eleves, {$prefixe}classes
        WHERE classe='$saisie_classe' AND code_class='$saisie_classe'
        ORDER BY nom";
$res  = execSql($sql);
$data = chargeMat($res);
$nomClasse = (countTriade($data) > 0) ? ucwords(strtolower($data[0][0])) : "";

$membre   = $_SESSION["membre"];
$idSelf   = $_SESSION["id_pers"];
$hideOthers = ((TROMBIELEVE == "non" && $membre == "menueleve") ||
               (TROMBIPARENT == "non" && $membre == "menuparent"));
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'>
<td>
  <div class="vrp-topbar">
    <span class="vrp-title"><?php print LANGTITRE32 ?></span>
    <?php if ($nomClasse !== "") { ?>
    <span style="color:#f9c600;font-size:12px;font-weight:700"><?php print htmlspecialchars($nomClasse) ?></span>
    <?php } ?>
  </div>
</td>
</tr>
<tr id='cadreCentral0'>
<td>

<?php if (countTriade($data) <= 0) { ?>
  <div class="tro-empty"><?php print LANGRECH1 ?></div>
<?php } else { ?>
  <div class="tro-grid">
  <?php for ($i=0; $i<countTriade($data); $i++) {
      $elevId  = $data[$i][1];
      $nom     = strtoupper($data[$i][2]);
      $prenom  = trunchaine(ucwords(strtolower($data[$i][3])), 15);
      $hidden  = $hideOthers && ($idSelf != $elevId);
      $imgSrc  = $hidden
                 ? "./image/commun/photo_vide.jpg"
                 : "image_trombi.php?idE=".$elevId;
  ?>
    <div class="tro-card">
      <img src="<?php print $imgSrc ?>" class="tro-photo" alt="<?php print htmlspecialchars($nom) ?>">
      <?php if (!$hidden) { ?>
      <span class="tro-name"><?php print htmlspecialchars($nom) ?><br><?php print htmlspecialchars($prenom) ?></span>
      <?php } ?>
    </div>
  <?php } ?>
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
Pgclose();
?>
</BODY>
</HTML>
