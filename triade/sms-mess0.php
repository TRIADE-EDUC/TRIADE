<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
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
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<style>
/* ── SMS Destinataires – Design 2026 ── */
.dest-wrap {
    padding: 6px 8px 12px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.dest-blocked {
    padding: 12px; text-align: center;
    font-size: 13px; color: #c00; font-weight: 600;
}
.dest-list {
    display: flex; flex-direction: column;
    background: #fff;
    border: 1px solid #dde0f0;
    border-radius: 8px;
    overflow: hidden;
}
.dest-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; padding: 9px 14px;
    border-bottom: 1px solid #eef0f8;
    transition: background .12s;
}
.dest-row:last-child { border-bottom: none; }
.dest-row:hover { background: #f5f6fd; }
.dest-row-label {
    font-size: 13px; color: #222; flex: 1;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.btn-dest {
    background: #CACCEF; color: #080A66;
    border: none; border-radius: 5px;
    padding: 5px 12px; font-size: 12px; font-weight: 700;
    cursor: pointer; white-space: nowrap;
    transition: background .12s;
    flex-shrink: 0;
}
.btn-dest:hover { background: #b0b4e8; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSMS7 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
?>

<div class="dest-wrap">
<?php
if (LAN == "oui") {
    if (file_exists("./common/config-sms.php")) {
?>
<div class="dest-list">

    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGSMS10 ?></div>
        <button class="btn-dest" onclick="open('sms-mess-classe.php','_parent','')"><?php print CLICKICI ?></button>
    </div>

    <div class="dest-row">
        <div class="dest-row-label">Envoyer un SMS à un parent d'étudiant</div>
        <button class="btn-dest" onclick="open('sms-mess-parent.php','_parent','')"><?php print CLICKICI ?></button>
    </div>

    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGSMS12 ?></div>
        <button class="btn-dest" onclick="open('sms-mess.php?pid','_parent','')"><?php print CLICKICI ?></button>
    </div>

    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGSMS13 ?></div>
        <button class="btn-dest" onclick="open('sms-mess.php','_parent','')"><?php print CLICKICI ?></button>
    </div>

</div><!-- /.dest-list -->

<?php
    } else {
        print "<div class='dest-blocked'>".LANGMESS37.".</div>";
    }
} else {
    print "<br><center><font class=T2>".ERREUR1."</font><br><br><i>".ERREUR3."</i></center>";
}
?>
</div><!-- /.dest-wrap -->

</td></tr></table>
<br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
</BODY>
</HTML>
