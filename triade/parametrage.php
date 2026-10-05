<?php
session_start();
error_reporting(0);
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
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_once("./common/config5.inc.php") ?>
    <meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
    <meta http-equiv="CacheControl" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="Copyright" content="Triade©, 2001" />
    <link rel="SHORTCUT ICON" href="./favicon.ico" />
    <link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
    <title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<style>
/* ── Paramétrage – Design 2026 ── */
* { box-sizing: border-box; }
.param-wrap {
    padding: 10px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.param-card {
    background: #fff; border: 1px solid #dde0f0; border-radius: 8px;
    overflow: hidden; margin-bottom: 8px;
}
.param-card-header {
    background: #CACCEF; color: #080A66;
    padding: 7px 14px; font-size: 12px; font-weight: 700;
    border-bottom: 1px solid #b0b4e8;
}
.param-row {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-bottom: 1px solid #eef0f8;
    flex-wrap: wrap;
}
.param-row:last-child { border-bottom: none; }
.param-icon { flex-shrink: 0; }
.param-label {
    font-size: 13px; color: #222; font-weight: 600;
    min-width: 140px; flex-shrink: 0;
}
.param-hint { font-size: 11px; color: #888; margin-top: 3px; font-weight: 400; }
.param-field { display: flex; align-items: center; gap: 8px; flex: 1; flex-wrap: wrap; }
.param-input {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 5px 10px; font-size: 13px; color: #222;
    background: #fff; flex: 1; min-width: 160px;
}
.param-input:focus { border-color: #080A66; outline: none; box-shadow: 0 0 0 2px rgba(8,10,102,.1); }
.param-input[readonly], .param-input[disabled] { background: #f4f4f8; color: #999; cursor: not-allowed; }
.param-select {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 5px 10px; font-size: 13px; color: #222;
    background: #fff; cursor: pointer;
}
.param-select:focus { border-color: #080A66; outline: none; }
.param-checkbox { accent-color: #080A66; cursor: pointer; width: 16px; height: 16px; }
.param-submit-row {
    text-align: center; padding: 12px 14px;
}
</style>
</head>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<?php
include_once("./librairie_php/lib_rss.php");
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();
if (isset($_POST["info"])) {
    $val = recup_lien_rss($_POST["info"]);
    $val = preg_replace('/{MEMBRE}/', $_SESSION["membre"], $val);
    $val = preg_replace('/{IDPERS}/', $_SESSION["id_pers"], $val);
}
?>
<script type="text/javascript">
function frss(lrss) {
    if (document.forms[0].rss.checked == false) {
        document.forms[0].lienrss.value = "";
    } else {
        if (lrss == "resa") { document.forms[0].lienrss.value = "<?php print $val ?>"; }
        if (lrss == "actu") { document.forms[0].lienrss.value = "<?php print $val ?>"; }
    }
}
</script>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS167 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
if ($_SESSION["membre"] == "menuprof") {
    $idpers = $_SESSION["id_suppleant"];
} else {
    $idpers = $_SESSION["id_pers"];
}

$mail = mess_mail_forward($_SESSION["nom"], $_SESSION["prenom"], $idpers, $_SESSION["membre"]);

if (isset($_POST["create"])) {
    if ($_POST["info"] == "mess") {
        $valid = 0;
        if (ValideMail($_POST["mail"])) $valid = 1;
        $cr = mess_forward(trim($_POST["mail"]), $valid, $_SESSION["nom"], $_SESSION["prenom"], $idpers, $_SESSION["membre"]);
    } else {
        $cr = enreg_param($_POST["mail"], $_POST["sms"], $_POST["rss"], $idpers, $_SESSION["membre"], $_POST["info"], $_POST["numero"]);
        if ($_SESSION["membre"] == "menuprof") { config_param_ajout($_POST["connexion"], "pagecnx".$_SESSION["id_pers"]); }
    }
    if ($cr == 1) {
        history_cmd($_SESSION["nom"], "MODIFIER", "parametrage");
        alertJs("Enregistrement effectué");
    }
}

$data = recherche_param($idpers, $_POST["info"], $_SESSION["membre"]);

if ($_POST["info"] == "actu") { $choixoption0 = "selected='selected'"; }
if ($_POST["info"] == "resa") { $choixoption1 = "selected='selected'"; }
if ($_POST["info"] == "mess") { $choixoption2 = "selected='selected'"; }

for ($i = 0; $i < countTriade($data); $i++) {
    if (preg_match('/^sms/', $data[$i][1])) {
        $checkedsms = "checked='checked'";
        $numero = preg_replace('/sms\//', '', $data[$i][1]);
    }
    if ($data[$i][1] == "rss") {
        $checkedrss = "checked='checked'";
        if ($data[$i][0] == "resa") { $lienrss = $val; }
        if ($data[$i][0] == "actu") { $lienrss = $val; }
    }
    if (preg_match('/@/', $data[$i][1])) { $mail = $data[$i][1]; }
}

if ((defined("SMS")) && (SMS != "oui")) {
    $disabled = "disabled='disabled'";
    $numero   = LANGTMESS483;
}
if ((defined("FORWARDMAIL")) && (FORWARDMAIL != "oui")) {
    $mail         = LANGTMESS483;
    $disabledmail = "disabled='disabled'";
}
if ($_POST["info"] == "mess") {
    $disabledrss = "disabled='disabled'";
}

if ($_SESSION["membre"] == "menuprof") {
    $datap         = config_param_visu("pagecnx".$_SESSION["id_pers"]);
    $pageconnexion = $datap[0][0];
    if ($pageconnexion == "abs")        { $selected2 = "selected='selected'"; }
    if ($pageconnexion == "messagerie") { $selected3 = "selected='selected'"; }
}
?>

<form method="post">
<div class="param-wrap">

    <!-- ── Catégorie ── -->
    <div class="param-card">
        <div class="param-card-header"><?php print LANGPARAM44 ?></div>
        <div class="param-row">
            <div class="param-icon"><img src="image/commun/ico_conf.gif" alt="conf"></div>
            <div class="param-label"><?php print LANGPARAM44 ?></div>
            <div class="param-field">
                <select name="info" class="param-select" onchange="document.forms[0].submit()">
                    <option value="rien" id="select0"><?php print LANGCHOIX ?></option>
                    <?php
                    print "<option value='actu' $choixoption0 id='select1'>".LANGMESS168."</option>";
                    if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) {
                        print "<option value='resa' $choixoption1 id='select1'>".LANGMESS169."</option>";
                    }
                    print "<option value='mess' $choixoption2 id='select1'>".LANGMESS170."</option>";
                    ?>
                </select>
            </div>
        </div>
    </div>

    <!-- ── Paramètres ── -->
    <div class="param-card">
        <div class="param-card-header">Paramètres</div>

        <!-- Email -->
        <div class="param-row">
            <div class="param-icon"><img src="image/commun/email.gif" alt="mail"></div>
            <div class="param-label">
                <?php print LANGTMESS421 ?>
                <div class="param-hint"><?php print LANGMESS171 ?></div>
            </div>
            <div class="param-field">
                <?php
                if ((EMAILCHANGEELEVE == "non") && ($_SESSION["membre"] == "menueleve")) {
                    $readonly = "readonly='readonly'";
                } else {
                    $readonly = "";
                }
                ?>
                <input type="text" name="mail" class="param-input"
                       onblur="verifEmail(this)" size="35"
                       value="<?php print $mail ?>"
                       <?php print $disabledmail ?> <?php print $readonly ?>>
            </div>
        </div>

        <!-- SMS -->
        <div class="param-row">
            <div class="param-icon"><img src="image/l_port.gif" alt="Tel"></div>
            <div class="param-label">
                <?php print LANGTMESS422 ?>
                <div class="param-hint"><?php print LANGMESS172 ?></div>
            </div>
            <div class="param-field">
                <input type="checkbox" name="sms" value="sms" class="param-checkbox"
                       <?php print $checkedsms ?> <?php print $disabled ?>
                       onclick="document.forms[0].numero.value=''">
                <input type="text" name="numero" class="param-input" maxlength="27"
                       value="<?php print $numero ?>" <?php print $disabled ?> style="max-width:160px">
            </div>
        </div>

        <!-- RSS -->
        <div class="param-row">
            <div class="param-icon"><img src="image/commun/rss-icon.gif" alt="rss"></div>
            <div class="param-label"><?php print LANGTMESS423 ?></div>
            <div class="param-field">
                <input type="checkbox" name="rss" value="rss" class="param-checkbox"
                       onclick="frss(document.forms[0].info.options[document.forms[0].info.options.selectedIndex].value)"
                       <?php print $checkedrss." ".$disabledrss ?>>
                <input type="text" name="lienrss" class="param-input"
                       readonly="readonly" <?php print $disabledrss ?>
                       value="<?php print $lienrss ?>" style="max-width:200px">
            </div>
        </div>

        <!-- Page de connexion (prof uniquement) -->
        <?php if ($_SESSION["membre"] == "menuprof") { ?>
        <div class="param-row">
            <div class="param-icon"><img src="image/commun/actif.gif" alt="cnx"></div>
            <div class="param-label"><?php print LANGTMESS424 ?></div>
            <div class="param-field">
                <select name="connexion" class="param-select">
                    <option value="rien" id="select0"><?php print LANGMESS168 ?></option>
                    <option value="abs" id="select1" <?php print $selected2 ?>><?php print LANGTMESS425 ?></option>
                    <option value="messagerie" id="select1" <?php print $selected3 ?>><?php print LANGASS14 ?></option>
                </select>
            </div>
        </div>
        <?php } ?>

    </div><!-- /.param-card -->

    <!-- Bouton enregistrer -->
    <div class="param-submit-row">
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
    </div>

</div><!-- /.param-wrap -->
</form>

<?php
print "</td></tr></table>";
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
</BODY></HTML>
