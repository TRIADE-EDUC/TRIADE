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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION['nom']).' '.htmlspecialchars($_SESSION['prenom']) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<form method="post" onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS395 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once('librairie_php/db_triade.php');
$cnx = cnx();
validerequete("menuadmin");
$data = affPers('ADM');
?>
<table class="la-table">
  <tbody>
  <?php for ($i = 0; $i < countTriade($data); $i++) {
      $inactive = ($data[$i][5] == 1)
          ? "&#128164;"
          : "";
      $nom    = htmlspecialchars(strtoupper(stripslashes($data[$i][2])));
      $prenom = htmlspecialchars(ucfirst(stripslashes($data[$i][3])));
      $mail   = trim($data[$i][6]);
      $imgmail = ($mail != "")
          ? "<a href='mailto:".htmlspecialchars($mail)."' target='_blank' title='".htmlspecialchars($mail)."'>"
            ."<img src='image/commun/email.gif' border='0' alt='mail'></a>"
          : "";
      $idpers = intval($data[$i][0]);
  ?>
    <tr class="la-tr">
      <td class="la-td"><?php print $inactive.civ($data[$i][1])."&nbsp;".$nom ?></td>
      <td class="la-td"><?php print $prenom ?></td>
      <td class="la-td la-td-icon"><?php print $imgmail ?></td>
      <td class="la-td la-td-action">
        <input type="button" class="la-btn-modif" value="<?php print LANGMESS396 ?>"
               onclick="open('modif_admin.php?saisie_id=<?php print $idpers ?>','_parent','');">
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY></HTML>
