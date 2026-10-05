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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script>window.alert = function(msg) { if (msg) alertify.error(msg); };</script>
<style>
/* ── SMS Parent – Design 2026 ── */
* { box-sizing: border-box; }
body { font-family: Electrolize, Trebuchet MS, Arial, sans-serif; }

/* ── Formulaire recherche ── */
.sms-form-wrap { padding: 10px 10px 6px; }
.sms-form-row {
    display: flex; align-items: flex-start; gap: 8px; flex-wrap: wrap;
    background: #fff; border: 1px solid #dde0f0; border-radius: 8px;
    padding: 10px 14px;
}
.sms-label { font-size: 13px; color: #222; font-weight: 600; white-space: nowrap; padding-top: 4px; }
.sms-input-block { display: flex; flex-direction: column; flex: 1; }
.sms-input {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 5px 10px; font-size: 13px; color: #222;
    width: 100%; outline: none;
}
.sms-input:focus { border-color: #080A66; box-shadow: 0 0 0 2px rgba(8,10,102,.1); }
#target {
    border: 1px solid #CACCEF; border-top: none;
    border-radius: 0 0 5px 5px; background: #fff;
    font-size: 12px; z-index: 100;
}

/* ── Résultats ── */
.sms-results-wrap { padding: 10px; }
.sms-results-header {
    background: #080A66; color: #fff;
    border-radius: 8px 8px 0 0;
    padding: 10px 14px;
    display: flex; align-items: center; justify-content: space-between;
    font-size: 13px; font-weight: 600;
}
.sms-col-head {
    font-weight: 700;
    padding: 7px 14px; font-size: 12px;
    display: grid; grid-template-columns: 120px 1fr 1fr;
    gap: 10px;
    background: #FFFBE6;
}
.sms-result-list {
    background: #fff; border: 1px solid #dde0f0;
    border-top: none; border-radius: 0 0 8px 8px; overflow: hidden;
}
.sms-result-row {
    display: grid; grid-template-columns: 120px 1fr 1fr;
    gap: 10px; padding: 8px 14px;
    border-bottom: 1px solid #eef0f8;
    font-size: 13px; transition: background .1s;
}
.sms-result-row:last-child { border-bottom: none; }
.sms-result-row:hover { background: #f5f6fd; }
.sms-result-row a {
    color: #080A66; text-decoration: underline; font-weight: 600;
}
.sms-result-row a:hover { color: #0d14b3; }
.sms-empty {
    padding: 16px; text-align: center; color: #888; font-size: 13px;
    background: #fff; border: 1px solid #dde0f0;
    border-top: none; border-radius: 0 0 8px 8px;
}
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx = cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule0'><?php print LANGSMS7 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<form method="post" onsubmit="return valide_recherche_eleve()" name="formulaire">
<div class="sms-form-wrap">
    <div class="sms-form-row">
        <span class="sms-label"><?php print LANGABS3 ?> :</span>
        <div class="sms-input-block">
            <input type="text" name="saisie_nom_eleve" id="search" class="sms-input"
                   size="20" autocomplete="off"
                   onkeyup="searchRequest(this,'eleve','target','formulaire','saisie_nom_eleve')">
            <div id="target"></div>
        </div>
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT39 ?>","create"); //text,nomInput</script>
    </div>
</div>
<?php brmozilla($_SESSION["navigateur"]); ?>
</form>

</td></tr></table>

<?php
if (isset($_POST["saisie_nom_eleve"])) {
    $saisie_nom_eleve = trim($_POST["saisie_nom_eleve"]);
    $motif = strtolower($saisie_nom_eleve);
    $sql = <<<EOF
SELECT c.libelle, e.nom, e.prenom, e.elev_id
FROM {$prefixe}eleves e, {$prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom
EOF;
    $res  = execSql($sql);
    $data = chargeMat($res);
?>

<div class="sms-results-wrap">
    <div class="sms-results-header">
        <?php print LANGRECH2 ?> : <b><?php print ucwords(stripslashes($motif)) ?></b>
    </div>

    <?php if (countTriade($data) <= 0) { ?>
    <div class="sms-empty"><?php print LANGRECH3 ?></div>
    <?php } else { ?>

    <div class="sms-col-head" id="coulBar0" style="background:#FFFBE6">
        <span id='menumodule0'><?php print ucwords(LANGIMP10) ?></span>
        <span id='menumodule0'><?php print ucwords(LANGIMP8) ?></span>
        <span id='menumodule0'><?php print ucwords(LANGIMP9) ?></span>
    </div>
    <div class="sms-result-list">
    <?php for ($i = 0; $i < countTriade($data); $i++) { ?>
        <div class="sms-result-row">
            <span><?php print $data[$i][0] ?></span>
            <span>
                <a href="sms-mess.php?eid=<?php print $data[$i][3] ?>"
                   onmouseover="AffBulle('<font face=Verdana size=1><?php print LANGSMS8 ?></FONT>'); return true;"
                   onmouseout="HideBulle()">
                   <?php print strtoupper($data[$i][1]) ?>
                </a>
            </span>
            <span><?php print ucwords($data[$i][2]) ?></span>
        </div>
    <?php } ?>
    </div>

    <?php } ?>
</div>

<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php } ?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
