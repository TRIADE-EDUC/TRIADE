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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<style>
.rp-form { display: contents; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGRESA11 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin -->
<div class="dest-wrap">
  <div class="dest-list">

    <form class="rp-form" action="resr_equip.php" method="post">
      <div class="dest-row">
        <span class="dest-row-label"><?php print LANGRESA12 ?></span>
        <div style="display:flex;gap:6px">
          <button type="submit" name="rien" class="btn-dest"><?php print LANGRESA14 ?></button>
          <a href="edt_visu.php?equip" target="_blank" class="btn-dest" style="text-decoration:none">via E.D.T.</a>
        </div>
      </div>
    </form>

    <form class="rp-form" action="resr_salle.php" method="post">
      <div class="dest-row">
        <span class="dest-row-label"><?php print LANGRESA13 ?></span>
        <div style="display:flex;gap:6px">
          <button type="submit" name="rien" class="btn-dest"><?php print LANGRESA14 ?></button>
          <a href="edt_visu.php?equip" target="_blank" class="btn-dest" style="text-decoration:none">via E.D.T.</a>
        </div>
      </div>
    </form>

    <form class="rp-form" action="resr_liste.php" method="post">
      <div class="dest-row">
        <span class="dest-row-label">Liste de vos réservations</span>
        <div style="display:flex;gap:6px">
          <button type="submit" name="rien" class="btn-dest">Consulter</button>
        </div>
      </div>
    </form>

  </div>
</div>
<!-- // fin -->
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
