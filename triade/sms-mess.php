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
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<meta charset="utf-8">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<style>
/* ── SMS Mess – Design 2026 ── */
* { box-sizing: border-box; }
body { font-family: Electrolize, Trebuchet MS, Arial, sans-serif; }

.sms-wrap { padding: 10px; display: flex; flex-direction: column; gap: 8px; }

/* ── Carte destinataire ── */
.sms-card {
    background: #fff; border: 1px solid #dde0f0; border-radius: 8px;
    overflow: hidden;
}
.sms-card-header {
    background: #CACCEF; color: #080A66;
    padding: 7px 14px; font-size: 12px; font-weight: 700;
}
.sms-card-body { padding: 10px 14px; }

/* ── Nom de l'élève ── */
.sms-student-name {
    font-size: 14px; font-weight: 700; color: #080A66; margin-bottom: 10px;
}

/* ── Radios téléphones ── */
.phone-list { display: flex; flex-direction: column; gap: 6px; }
.phone-item {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: #333; cursor: pointer;
    padding: 5px 8px; border-radius: 5px; transition: background .1s;
}
.phone-item:hover { background: #f5f6fd; }
.phone-item input[type=radio] { accent-color: #080A66; cursor: pointer; }
.phone-type { color: #888; font-size: 11px; min-width: 120px; }
.phone-num  { font-weight: 700; color: #080A66; }

/* ── Select personne / input libre ── */
.sms-select {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 6px 10px; font-size: 13px; color: #222;
    background: #fff; cursor: pointer; width: 100%;
}
.sms-input {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 6px 10px; font-size: 13px; color: #222; width: 100%;
}
.sms-input:focus, .sms-select:focus {
    border-color: #080A66; outline: none;
    box-shadow: 0 0 0 2px rgba(8,10,102,.1);
}

/* ── Carte message ── */
.sms-textarea {
    width: 100%; border: 1px solid #CACCEF; border-radius: 5px;
    padding: 8px 10px; font-size: 13px; color: #222;
    resize: vertical; min-height: 80px; font-family: inherit;
}
.sms-textarea:focus { border-color: #080A66; outline: none; box-shadow: 0 0 0 2px rgba(8,10,102,.1); }
.sms-counter-row {
    display: flex; align-items: center; gap: 8px;
    margin-top: 6px; font-size: 11px; color: #888;
}
.sms-counter-row input[type=text] {
    width: 36px; text-align: center; border: 1px solid #dde0f0;
    border-radius: 4px; padding: 2px 4px; font-size: 11px; color: #555;
    background: #f8f8f8;
}
.sms-warning {
    display: flex; align-items: center; gap: 8px;
    color: #c00; font-weight: 700; font-size: 13px;
    padding: 8px 0;
}
.sms-submit-row { text-align: center; padding-top: 6px; }

/* ── Erreurs ── */
.sms-blocked {
    padding: 12px; text-align: center;
    font-size: 13px; color: #c00; font-weight: 600;
}
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSMS7 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<script>
function valide() { document.form.create.disabled = false; }
</script>

<?php
include_once("librairie_php/db_triade.php");
validerequete("2");

if (LAN == "oui") {
    if (file_exists("./common/config-sms.php")) {
        include_once("./common/config-sms.php");
        $idsms  = SMSKEY;
        $inc    = GRAPH;
        $urlsms = SMSURL;
?>

<form method="post" name="form" action="sms-mess2.php">
<div class="sms-wrap">

    <!-- ── Destinataire ── -->
    <div class="sms-card">
        <div class="sms-card-header"><?php print LANGSMS9 ?></div>
        <div class="sms-card-body">
        <?php
        $num = 0;

        if (isset($_GET["eid"])) {
            /* ── Mode élève : radios téléphones ── */
            $cnx    = cnx();
            $filtreSMS = config_param_visu('smsfiltre');
            $filtreSMS = $filtreSMS[0][0];

            $tel1 = preg_replace('/[ .\/\-_]/', '', cherchetelportable1($_GET["eid"]));
            $tel2 = preg_replace('/[ .\/\-_]/', '', cherchetelportable2($_GET["eid"]));
            $tel  = preg_replace('/[ .\/\-_]/', '', cherchetel($_GET["eid"]));
            $tel3 = preg_replace('/[ .\/\-_]/', '', cherchetelEleve($_GET["eid"]));
            $tel4 = preg_replace('/[ .\/\-_]/', '', cherchetelpere($_GET["eid"]));
            $tel5 = preg_replace('/[ .\/\-_]/', '', cherchetelmere($_GET["eid"]));

            $nom    = recherche_eleve_nom($_GET["eid"]);
            $prenom = recherche_eleve_prenom($_GET["eid"]);
            Pgclose();

            print "<div class='sms-student-name'>$nom $prenom</div>";
            print "<div class='phone-list'>";

            if (is_numeric($tel1)) {
                $num = 1;
                print "<label class='phone-item'><input type='radio' value='$tel1' name='tel' onclick='valide()'> <span class='phone-type'>Tél. Portable 1 :</span> <span class='phone-num'>$tel1</span></label>";
            }
            if (is_numeric($tel2)) {
                $num = 1;
                print "<label class='phone-item'><input type='radio' value='$tel2' name='tel' onclick='valide()'> <span class='phone-type'>Tél. Portable 2 :</span> <span class='phone-num'>$tel2</span></label>";
            }
            if (is_numeric($tel)) {
                $num = 1;
                print "<label class='phone-item'><input type='radio' value='$tel' name='tel' onclick='valide()'> <span class='phone-type'>Téléphone :</span> <span class='phone-num'>$tel</span></label>";
            }
            if (is_numeric($tel3)) {
                $num = 1;
                print "<label class='phone-item'><input type='radio' value='$tel3' name='tel' onclick='valide()'> <span class='phone-type'>Tél. Élève :</span> <span class='phone-num'>$tel3</span></label>";
            }
            if (is_numeric($tel4)) {
                $num = 1;
                print "<label class='phone-item'><input type='radio' value='$tel4' name='tel' onclick='valide()'> <span class='phone-type'>Tél. Prof. Père :</span> <span class='phone-num'>$tel4</span></label>";
            }
            if (is_numeric($tel5)) {
                $num = 1;
                print "<label class='phone-item'><input type='radio' value='$tel5' name='tel' onclick='valide()'> <span class='phone-type'>Tél. Prof. Mère :</span> <span class='phone-num'>$tel5</span></label>";
            }
            print "</div>";

        } elseif (isset($_GET["pid"])) {
            /* ── Mode personne : select ── */
            $num = 1;
            $cnx = cnx();
            print "<label style='font-size:13px;color:#222;font-weight:600'>" . LANGSMS14 . " :</label><br><br>";
            print "<select name='tel' class='sms-select' onchange='valide()'>";
            print "<option value='rien' id='select0'>" . LANGCHOIX . "</option>";
            print "<optgroup label='" . LANGGEN1 . "'>";
            select_personne_sms('ADM');
            print "<optgroup label='" . LANGGEN2 . "'>";
            select_personne_sms('MVS');
            print "<optgroup label='" . LANGGEN3 . "'>";
            select_personne_sms('ENS');
            print "</select>";
            Pgclose();

        } else {
            /* ── Mode libre : saisie numéro ── */
            $num = 1;
            print "<label style='font-size:13px;color:#222;font-weight:600'>" . LANGSMS3 . " :</label><br><br>";
            print "<input type='text' name='tel' class='sms-input' onchange='valide()'>";
        }

        if ($num == 0) {
            print "<div style='color:red;font-size:13px;margin-top:8px'>" . LANGMESS58 . "</div>";
        }
        ?>
        </div>
    </div>

    <!-- ── Message SMS ── -->
    <div class="sms-card">
        <div class="sms-card-header"><?php print LANGSMS5 ?> (<?php print LANGSMS4 ?>)</div>
        <div class="sms-card-body">
            <textarea name="message" class="sms-textarea" cols="84" rows="4"
                onkeypress="compter(this,'150', this.form.CharRestant)"></textarea>
            <div class="sms-counter-row">
                <input type="text" name="CharRestant" size="2" disabled="disabled">
                <i><?php print LANGSMS6 ?>.</i>
            </div>
        </div>
    </div>

</div><!-- /.sms-wrap -->
<ul>
<div class="sms-submit-row">
<?php
$nb = (int) file_get_contents($urlsms . "sms-info-nb.php?idsms=$idsms");
if ($nb > 0) {
    print "<script language=JavaScript>buttonMagicSubmit3('Envoyer','create','disabled'); //text,nomInput</script>";
} else {
    print "<div class='sms-warning'><img src='image/commun/warning2.gif'> <b>Crédit SMS Épuisé !</b></div>";
}
?>
</div>
<br>
</ul>
</form>

<?php
    } else {
        print "<div class='sms-blocked'>" . LANGMESS37 . ".</div>";
    }
} else {
    print "<br><center><font class=T2>" . ERREUR1 . "</font><br><br><i>" . ERREUR3 . "</i></center>";
}
?>

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
