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
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script>window.alert = function(msg) { if (msg) alertify.error(msg); };</script>
<style>
/* ── SMS Classe – Design 2026 ── */
* { box-sizing: border-box; }
body { font-family: Electrolize, Trebuchet MS, Arial, sans-serif; }

/* ── Formulaire sélection ── */
.sms-form-wrap { padding: 10px 10px 6px; }
.sms-form-row {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    background: #fff; border: 1px solid #dde0f0; border-radius: 8px;
    padding: 10px 14px;
}
.sms-label { font-size: 13px; color: #222; font-weight: 600; white-space: nowrap; }
.sms-select {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 5px 8px; font-size: 12px; color: #222;
    background: #fff; cursor: pointer; flex: 1; min-width: 120px;
}
.btn-dest {
    background: #CACCEF; color: #080A66;
    border: none; border-radius: 5px;
    padding: 6px 14px; font-size: 12px; font-weight: 700;
    cursor: pointer; white-space: nowrap;
    transition: background .12s; flex-shrink: 0;
}
.btn-dest:hover { background: #b0b4e8; }

/* ── Tableau résultats ── */
.sms-results-wrap { padding: 10px; }
.sms-results-header {
    background: #080A66; color: #fff;
    border-radius: 8px 8px 0 0;
    padding: 10px 14px;
    display: flex; align-items: center; justify-content: space-between;
    font-size: 13px; font-weight: 600;
}
.sms-results-header .tous-label {
    display: flex; align-items: center; gap: 6px; font-size: 12px;
}
.sms-results-header input[type=checkbox] { cursor: pointer; }
.sms-col-head {
    font-weight: 700;
    padding: 7px 14px; font-size: 12px;
    display: flex; gap: 10px;
    background: #FFFBE6;
}
.student-list { background: #fff; border: 1px solid #dde0f0; border-top: none; border-radius: 0 0 8px 8px; overflow: hidden; }
.student-row {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 8px 14px; border-bottom: 1px solid #eef0f8;
    transition: background .1s;
}
.student-row:last-child { border-bottom: none; }
.student-row:hover { background: #f5f6fd; }
.student-name {
    font-size: 13px; font-weight: 600; color: #222;
    min-width: 150px; padding-top: 2px;
}
.student-phones { display: flex; flex-direction: column; gap: 4px; flex: 1; }
.phone-item {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: #444; cursor: pointer;
}
.phone-item input[type=checkbox] { cursor: pointer; accent-color: #080A66; }
.phone-type { color: #888; min-width: 120px; font-size: 11px; }
.phone-num { font-weight: 600; color: #080A66; }
.sms-results-footer {
    text-align: center; padding: 14px;
}
.sms-empty {
    padding: 20px; text-align: center; color: #888; font-size: 13px;
    background: #fff; border: 1px solid #dde0f0; border-radius: 0 0 8px 8px;
}
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule0'><?php print LANGSMS7 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) {
    $saisie_classe = $_POST["saisie_classe"];
    $sql  = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
    $res  = execSql($sql);
    $data = chargeMat($res);
    $cl   = $data[0][0];
}
?>

<!-- ── Formulaire sélection classe ── -->
<form method="post" onsubmit="return valide_consul_classe()" name="formulaire">
<div class="sms-form-wrap">
    <div class="sms-form-row">
        <span class="sms-label"><?php print LANGPROFG ?> :</span>
        <select class="sms-select" id="saisie_classe" name="saisie_classe">
            <option id="select0"><?php print LANGCHOIX ?></option>
            <?php
            if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) {
                print "<option id='select1' selected='selected' value='".$_POST["saisie_classe"]."'>$cl</option>";
            }
            select_classe();
            ?>
        </select>
        <button type="submit" name="consult" class="btn-dest"><?php print LANGBT28 ?></button>
        <?php if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) { ?>
        <select class="sms-select" name="choixtel" onchange="document.formulaire.submit()" style="max-width:160px">
            <option id="select0"><?php print LANGCHOIX ?></option>
            <option value="cherchetelportable1">Tél. Portable 1</option>
            <option value="cherchetelportable2">Tél. Portable 2</option>
            <option value="cherchetel">Téléphone</option>
            <option value="cherchetelpere">Tél. Prof. Père</option>
            <option value="cherchetelmere">Tél. Prof. Mère</option>
            <option value="cherchetelEleve">Tél. Élève</option>
        </select>
        <?php } ?>
    </div>
</div>
</form>

</td></tr></table>

<!-- ── Résultats : liste des élèves ── -->
<?php
if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) {
    $o         = 0;
    $filtreSMS = config_param_visu('smsfiltre');
    $filtreSMS = $filtreSMS[0][0];
    $choixtel  = $_POST["choixtel"];
?>
<form method="post" action="sms-mess-classe1.php">
<div class="sms-results-wrap">

    <div class="sms-results-header">
        <span><?php print LANGELE4 ?> : <b><?php print $cl ?></b>&nbsp;&nbsp; <?php print LANGCOM3 ?> <b><?php print countTriade($data) ?></b></span>
        <label class="tous-label">
            Tous <input type="checkbox" onclick="tous()">
        </label>
    </div>

    <?php if (countTriade($data) <= 0) { ?>
    <div class="sms-empty"><?php print LANGRECH1 ?></div>
    <?php } else { ?>

    <div class="sms-col-head" id="coulBar0" style="background:#FFFBE6">
        <span id='menumodule0' style="min-width:150px"><?php print ucwords(LANGIMP8)." ".ucwords(LANGIMP9) ?></span>
        <span id='menumodule0'>Numéros</span>
    </div>

    <div class="student-list">
    <?php
    for ($i = 0; $i < countTriade($data); $i++) {
        $idEleve = $data[$i][1];
        $tel  = preg_replace('/[ \.\-_]/', '', cherchetel($idEleve));
        $tel1 = preg_replace('/[ \.\-_]/', '', cherchetelportable1($idEleve));
        $tel2 = preg_replace('/[ \.\-_]/', '', cherchetelportable2($idEleve));
        $tel3 = preg_replace('/[ \.\-_]/', '', cherchetelEleve($idEleve));
        $tel4 = preg_replace('/[ \.\-_]/', '', cherchetelpere($idEleve));
        $tel5 = preg_replace('/[ \.\-_]/', '', cherchetelmere($idEleve));
    ?>
    <div class="student-row">
        <div class="student-name"><?php print strtoupper($data[$i][2])." ".trunchaine(ucwords($data[$i][3]), 20) ?></div>
        <div class="student-phones">
        <?php
        if (is_numeric($tel1)) {
            $checked1 = ($choixtel == "cherchetelportable1") ? "checked='checked'" : "";
            $o++;
            print "<label class='phone-item'><input $checked1 type='checkbox' value='$idEleve#$tel1' name='tel$o' id='tel$o'> <span class='phone-type'>Portable 1 :</span> <span class='phone-num'>$tel1</span></label>";
        }
        if (is_numeric($tel2)) {
            $checked2 = ($choixtel == "cherchetelportable2") ? "checked='checked'" : "";
            $o++;
            print "<label class='phone-item'><input $checked2 type='checkbox' value='$idEleve#$tel2' name='tel$o' id='tel$o'> <span class='phone-type'>Portable 2 :</span> <span class='phone-num'>$tel2</span></label>";
        }
        if (is_numeric($tel)) {
            $checked = ($choixtel == "cherchetel") ? "checked='checked'" : "";
            $o++;
            print "<label class='phone-item'><input $checked type='checkbox' value='$idEleve#$tel' name='tel$o' id='tel$o'> <span class='phone-type'>Téléphone :</span> <span class='phone-num'>$tel</span></label>";
        }
        if (is_numeric($tel3)) {
            $checked3 = ($choixtel == "cherchetelEleve") ? "checked='checked'" : "";
            $o++;
            print "<label class='phone-item'><input $checked3 type='checkbox' value='$idEleve#$tel3' name='tel$o' id='tel$o'> <span class='phone-type'>Tél. Élève :</span> <span class='phone-num'>$tel3</span></label>";
        }
        if (is_numeric($tel4)) {
            $checked4 = ($choixtel == "cherchetelpere") ? "checked='checked'" : "";
            $o++;
            print "<label class='phone-item'><input $checked4 type='checkbox' value='$idEleve#$tel4' name='tel$o' id='tel$o'> <span class='phone-type'>Tél. Prof. Père :</span> <span class='phone-num'>$tel4</span></label>";
        }
        if (is_numeric($tel5)) {
            $checked5 = ($choixtel == "cherchetelmere") ? "checked='checked'" : "";
            $o++;
            print "<label class='phone-item'><input $checked5 type='checkbox' value='$idEleve#$tel5' name='tel$o' id='tel$o'> <span class='phone-type'>Tél. Prof. Mère :</span> <span class='phone-num'>$tel5</span></label>";
        }
        ?>
        </div>
    </div>
    <?php } ?>
    </div><!-- /.student-list -->

    <input type="hidden" name="nbtel" value="<?php print $o ?>">
    <div class="sms-results-footer">
        <table align="center"><tr><td><script language="JavaScript">buttonMagicSubmit("Enregistrer","envSmsClasse"); //text,nomInput</script></td></tr></table>
    </div>

    <?php } ?>
</div><!-- /.sms-results-wrap -->
</form>

<?php } ?>

<script>
var okcheck = '0';
function tous() {
    var total = <?php print (isset($o) ? $o : 0) ?>;
    okcheck = (okcheck == '0') ? '1' : '0';
    for (var i = 1; i <= total; i++) {
        var el = document.getElementById('tel' + i);
        if (el) el.checked = (okcheck == '1');
    }
}
</script>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
