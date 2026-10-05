<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx = cnx();

if ($_SESSION['membre'] == "menueleve") {
    $libelle  = "acces_mail_".$_SESSION['id_pers'];
    $data33   = aff_structure($libelle);
    $valeur   = $data33[0][1];
    if ($valeur == "inactif") {
        header("Location:messagerie_restriction.php");
        exit;
    }
}

$urlbrouillon = "&brouillon=0";
$idbrouillon  = 0;
$brouillon    = "";
if (isset($_GET["brouillon"])) {
    $brouillon    = " <font color='color3'>(Type Brouillon)</font>";
    $urlbrouillon = "&brouillon=1";
    $idbrouillon  = 1;
}

$forwarding = isset($_GET["f"])                  ? $_GET["f"]                  : 0;
$idMessage  = isset($_GET["saisie_id_message"])  ? $_GET["saisie_id_message"]  : 0;
?>
<html>
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
/* ── Messagerie Envoi – Design 2026 ── */
.dest-wrap {
    padding: 6px 8px 12px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.dest-transfer-banner {
    background: #fff8e1; border-left: 3px solid #f5c842;
    padding: 7px 12px; margin-bottom: 8px;
    font-size: 12px; color: #7a5a00; font-weight: 600;
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
.dest-row-sub {
    font-size: 11px; color: #888; font-weight: 400;
}
.dest-card-link {
    font-size: 11px; color: #080A66; margin-left: 6px;
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
.dest-select {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 5px 8px; font-size: 12px; color: #222;
    background: #fff; cursor: pointer; flex-shrink: 0;
    max-width: 190px;
}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
    <script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
    <script type="text/javascript" src="./librairie_js/verif_creat.js"></script>
    <script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
    <script type="text/javascript" src="./librairie_js/function.js"></script>
    <script type="text/javascript" src="./librairie_js/lib_css.js"></script>
    <?php include("./librairie_php/lib_licence.php"); ?>
    <SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
    <?php include("./librairie_php/lib_defilement.php"); ?>
    </TD><td width="472" valign="middle" rowspan="3" align="center">
    <div align='center'><?php top_h(); ?>
    <SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
    <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
    <tr id='coulBar0'><td height="2">
        <b><font id='menumodule1'><?php print LANGMESS1 ?><?php print dateDMY().$brouillon ?></font></b>
    </td></tr>
    <tr id='cadreCentral0'><td>

<div class="dest-wrap">

<?php
/* ── Bannière transfert ── */
if (isset($_GET["f"]) && $idMessage > 0) {
    print "<div class='dest-transfer-banner'>".LANGMESST704."</div>";
}

if ($_GET["autorise"] == "non") {
    print "<script>alert(\"".LANGMESST705."\")</script>";
    history_cmd($_SESSION["nom"], "MESSAGERIE", "Tentative d'envoi pour ".$_GET["aqui"]);
}

/* ── Droits d'envoi ── */
$valid = 0;
if (($_SESSION["membre"] == "menututeur") && (ACCESMESSENVOITUTEUR == "non")) { $valid = 1; }
if (($_SESSION["membre"] == "menuparent") && (ACCESMESSENVOIPARENT == "non")) { $valid = 1; }
if (($_SESSION["membre"] == "menueleve")  && (ACCESMESSENVOIELEVE  == "non")) { $valid = 1; }
if ($idbrouillon == 1) { $valid = 0; }

if ($valid == 1) {
    if (verifdelegue($_SESSION["id_pers"], $_SESSION["membre"], chercheIdClasseDunEleve($_SESSION["id_pers"]))) {
        if ((MESSDELEGUEELEVE == "oui") && ($_SESSION["membre"] == "menueleve"))  { $valid = 0; }
        if ((MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent")) { $valid = 0; }
    }
}
?>

<?php if ($valid == 1) { ?>
    <div class="dest-blocked"><?php print LANGMESS37 ?>.</div>
<?php } else { ?>

<div class="dest-list">

<?php
/* ── Mail externe ── */
$mailperso = mess_mail_forward($_SESSION["nom"], $_SESSION["prenom"], $_SESSION["id_pers"], $_SESSION["membre"]);
if ((MAILEXTERNE == "oui") && (ValideMail($mailperso))) {
    if (
        (($_SESSION["membre"] == "menueleve")    && (ELEVEENVOIEXT == "oui"))    ||
        ($_SESSION["membre"] == "menuadmin")                                     ||
        (($_SESSION["membre"] == "menuprof")     && (PROFENVOIEXT == "oui"))     ||
        (($_SESSION["membre"] == "menuparent")   && (PARENTENVOIEXT == "oui"))   ||
        ($_SESSION["membre"] == "menuscolaire")                                  ||
        (($_SESSION["membre"] == "menupersonnel") && (PERSONNELENVOIEXT == "oui")) ||
        (($_SESSION["membre"] == "menututeur")   && (TUTEURENVOIEXT == "oui"))
    ) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS45 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=mailexterne<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
    <?php }
}

/* ── Administrateurs ── */
if (
    (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOIDIREC == "oui"))   ||
    ($_SESSION["membre"] == "menuadmin")                                        ||
    (($_SESSION["membre"] == "menuprof")      && (PROFENVOIDIREC == "oui"))    ||
    (($_SESSION["membre"] == "menuparent")    && (PARENTENVOIDIREC == "oui"))  ||
    ($_SESSION["membre"] == "menuscolaire")                                     ||
    ($_SESSION["membre"] == "menupersonnel")                                    ||
    (($_SESSION["membre"] == "menututeur")    && (TUTEURENVOIDIREC == "oui"))
) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS2 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=administrateur<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
<?php }

/* ── Scolaire ── */
if (
    (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOISCOLAIRE == "oui"))  ||
    ($_SESSION["membre"] == "menuadmin")                                           ||
    (($_SESSION["membre"] == "menuprof")      && (PROFENVOISCOLAIRE == "oui"))   ||
    (($_SESSION["membre"] == "menuparent")    && (PARENTENVOISCOLAIRE == "oui")) ||
    ($_SESSION["membre"] == "menuscolaire")                                        ||
    ($_SESSION["membre"] == "menupersonnel")                                       ||
    (($_SESSION["membre"] == "menututeur")    && (TUTEURENVOISCOLAIRE == "oui"))
) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS3 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=scolaire<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
<?php }

/* ── Enseignants ── */
if (
    (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOIPROF == "oui"))      ||
    ($_SESSION["membre"] == "menuadmin")                                           ||
    (($_SESSION["membre"] == "menuprof")      && (PROFENVOIPROF == "oui"))       ||
    ($_SESSION["membre"] == "menuscolaire")                                        ||
    (($_SESSION["membre"] == "menuparent")    && (PARENTENVOIPROF == "oui"))     ||
    (($_SESSION["membre"] == "menupersonnel") && (PERSONNELENVOIPROF == "oui"))  ||
    (($_SESSION["membre"] == "menututeur")    && (TUTEURENVOIPROF == "oui"))
) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS4 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=enseignant<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
<?php }

/* ── Délégués ── */
if (
    (($_SESSION["membre"] == "menueleve")  && (ELEVEENVOIDELEGUE == "oui"))    ||
    ($_SESSION["membre"] == "menuadmin")                                         ||
    (($_SESSION["membre"] == "menuprof")   && (PROFENVOIDELEGUE == "oui"))     ||
    ($_SESSION["membre"] == "menuscolaire")                                      ||
    (($_SESSION["membre"] == "menuparent") && (PARENTENVOIDELEGUE == "oui"))   ||
    (($_SESSION["membre"] == "menututeur") && (TUTEURENVOIDELEGUE == "oui"))
) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGTMESS485 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=delegue<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
<?php }

/* ── Groupe d'élèves ── */
if (
    (
        (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOIGRPELE == "oui"))    ||
        ($_SESSION["membre"] == "menuadmin")                                           ||
        (($_SESSION["membre"] == "menuprof")      && (PROFENVOIGRPELE == "oui"))     ||
        ($_SESSION["membre"] == "menuscolaire")                                        ||
        (($_SESSION["membre"] == "menuparent")    && (PARENTENVOIGRPELE == "oui"))   ||
        (($_SESSION["membre"] == "menupersonnel") && (PERSONNELENVOIGRPELE == "oui")) ||
        (($_SESSION["membre"] == "menututeur")    && (TUTEURENVOIGRPELE == "oui"))
    ) && (!isset($_GET["brouillon"]))
) {
    $lienCreerGrpEle = "";
    if (
        (($_SESSION["membre"] == "menuprof") && (PROFENVOIGROUPE == "oui")) ||
        ($_SESSION["membre"] == "menuscolaire") ||
        ($_SESSION["membre"] == "menuadmin")
    ) {
        $lienCreerGrpEle = "&nbsp;<a href='messagerie_creat_grpmailele.php' class='dest-card-link'>[ ".LANGMESS17bis." ]</a>";
    } ?>
    <div class="dest-row">
        <div class="dest-row-label">
            <?php print LANGMESS173 ?> <span style="font-size:11px;color:#777;font-weight:400">(<?php print INTITULEELEVES ?>)</span>
            <?php print $lienCreerGrpEle ?>
        </div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=grpmailelev<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
<?php }

/* ── Tuteurs ── */
if (
    (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOITUTEUR == "oui"))   ||
    ($_SESSION["membre"] == "menuadmin")                                          ||
    (($_SESSION["membre"] == "menuprof")      && (PROFENVOITUTEUR == "oui"))    ||
    ($_SESSION["membre"] == "menuscolaire")                                       ||
    (($_SESSION["membre"] == "menuparent")    && (PARENTENVOITUTEUR == "oui"))  ||
    (($_SESSION["membre"] == "menututeur")    && (TUTEURENVOITUTEUR == "oui"))
) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS176 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=tuteur<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
<?php }

/* ── Personnel ── */
if (
    (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOIPERSONNEL == "oui"))  ||
    ($_SESSION["membre"] == "menuadmin")                                             ||
    (($_SESSION["membre"] == "menuprof")      && (PROFENVOIPERSONNEL == "oui"))   ||
    ($_SESSION["membre"] == "menuscolaire")                                          ||
    (($_SESSION["membre"] == "menuparent")    && (PARENTENVOIPERSONNEL == "oui")) ||
    ($_SESSION["membre"] == "menupersonnel")
) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS175 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=personnel<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
<?php }

/* ── Groupe de mail ── */
if (!isset($_GET["brouillon"])) {
    if (
        (($_SESSION["membre"] == "menuprof")      && (PROFENVOIGROUPE == "oui"))    ||
        ($_SESSION["membre"] == "menuscolaire")                                       ||
        ($_SESSION["membre"] == "menuadmin")                                          ||
        (($_SESSION["membre"] == "menupersonnel") && (PERSONNELENVOIPROF == "oui"))
    ) { ?>
    <div class="dest-row">
        <div class="dest-row-label">
            <?php print LANGMESS22 ?>
            &nbsp;<a href='messagerie_creat_grpmail.php' class='dest-card-link'>[ <?php print LANGMESS17bis ?> ]</a>
        </div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=grpmail<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
    <?php }

    if (($_SESSION["membre"] == "menuparent") && (GRPMAILPARENT == "oui")) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS22 ?></div>
        <button class="btn-dest" onclick="open('messagerie_envoi_suite.php?saisie_envoi=grpmail<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')"><?php print CLICKICI ?></button>
    </div>
    <?php }
}

/* ── Parents (avec sélecteur de classe) ── */
if ((ACCESMESSPARENT != "non") || (MESSDELEGUEPARENT != "non")) {
    if (
        (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOIPARENT == "oui"))     ||
        ($_SESSION["membre"] == "menuadmin")                                             ||
        (($_SESSION["membre"] == "menuprof")      && (PROFENVOIPARENT == "oui"))      ||
        ($_SESSION["membre"] == "menuscolaire")                                          ||
        (($_SESSION["membre"] == "menuparent")    && (PARENTENVOIPARENT == "oui"))    ||
        (($_SESSION["membre"] == "menupersonnel") && (PERSONNELENVOIPARENT == "oui")) ||
        (($_SESSION["membre"] == "menututeur")    && (TUTEURENVOIPARENT == "oui"))
    ) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS5 ?></div>
        <form name="formulaire1" action="messagerie_envoi_suite.php" method="GET" style="margin:0">
            <input type="hidden" name="saisie_envoi"       value="parent">
            <input type="hidden" name="saisie_id_message"  value="<?php print $idMessage ?>">
            <input type="hidden" name="f"                  value="<?php print $forwarding ?>">
            <input type="hidden" name="brouillon"          value="<?php print $idbrouillon ?>">
            <select name="saisie_classe" class="dest-select" onchange="document.formulaire1.submit()">
                <option id="select0"><?php print LANGCHOIX ?></option>
                <option id="select0" value="touslesparentsecole"><?php print LANGTMESS437 ?></option>
                <?php select_classe2(35) ?>
            </select>
        </form>
    </div>
    <?php }
}

/* ── Élèves (avec sélecteur de classe) ── */
if ((ACCESMESSELEVE != "non") || (MESSDELEGUEELEVE != "non")) {
    if (
        (($_SESSION["membre"] == "menueleve")     && (ELEVEENVOIELEVE == "oui"))     ||
        ($_SESSION["membre"] == "menuadmin")                                            ||
        (($_SESSION["membre"] == "menuprof")      && (PROFENVOIELEVE == "oui"))      ||
        ($_SESSION["membre"] == "menuscolaire")                                         ||
        (($_SESSION["membre"] == "menuparent")    && (PARENTENVOIELEVE == "oui"))    ||
        (($_SESSION["membre"] == "menupersonnel") && (PERSONNELENVOIELEVE == "oui")) ||
        (($_SESSION["membre"] == "menututeur")    && (TUTEURENVOIELEVE == "oui"))
    ) { ?>
    <div class="dest-row">
        <div class="dest-row-label"><?php print LANGMESS44 ?></div>
        <form name="formulaire2" action="messagerie_envoi_suite.php" method="GET" style="margin:0">
            <input type="hidden" name="saisie_envoi"       value="eleve">
            <input type="hidden" name="saisie_id_message"  value="<?php print $idMessage ?>">
            <input type="hidden" name="f"                  value="<?php print $forwarding ?>">
            <input type="hidden" name="brouillon"          value="<?php print $idbrouillon ?>">
            <select name="saisie_classe" class="dest-select" onchange="document.formulaire2.submit()">
                <option id="select0"><?php print LANGCHOIX ?></option>
                <option id="select0" value="tousleselevesecole"><?php print LANGTMESS438 ?> <?php print INTITULEELEVES ?></option>
                <?php select_classe2(35) ?>
            </select>
        </form>
    </div>
    <?php }
}

/* ── Tuteurs de stage (avec sélecteur de classe) ── */
if (
    (($_SESSION["membre"] == "menueleve")  && (ELEVEENVOITUTEUR == "oui"))  ||
    ($_SESSION["membre"] == "menuadmin")                                       ||
    (($_SESSION["membre"] == "menuprof")   && (PROFENVOITUTEUR == "oui"))   ||
    ($_SESSION["membre"] == "menuscolaire")                                    ||
    (($_SESSION["membre"] == "menuparent") && (PARENTENVOITUTEUR == "oui")) ||
    (($_SESSION["membre"] == "menututeur") && (TUTEURENVOITUTEUR == "oui"))
) { ?>
    <div class="dest-row">
        <div class="dest-row-label">Message à un tuteur de stage en</div>
        <form name="formulaire4" action="messagerie_envoi_suite.php" method="GET" style="margin:0">
            <input type="hidden" name="saisie_envoi"       value="tuteurdestage">
            <input type="hidden" name="saisie_id_message"  value="<?php print $idMessage ?>">
            <input type="hidden" name="f"                  value="<?php print $forwarding ?>">
            <input type="hidden" name="brouillon"          value="<?php print $idbrouillon ?>">
            <select name="saisie_classe" class="dest-select" onchange="document.formulaire4.submit()">
                <option id="select0"><?php print LANGCHOIX ?></option>
                <option id="select0" value="touslestuteursdestage"><?php print LANGTMESS438 ?> tuteurs de stage</option>
                <?php select_classe2(35) ?>
            </select>
        </form>
    </div>
<?php } ?>

</div><!-- /.dest-list -->

<?php } /* end $valid == 0 */ ?>

</div><!-- /.dest-wrap -->

<?php brmozilla($_SESSION["navigateur"]); ?>
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
include_once("./librairie_php/finbody.php");
?>
</BODY></HTML>
