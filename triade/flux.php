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
/* ── Flux RSS – Design 2026 ── */
* { box-sizing: border-box; }
.flux-wrap {
    padding: 10px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
    display: flex; flex-direction: column; gap: 8px;
}
.flux-card {
    background: #fff; border: 1px solid #dde0f0; border-radius: 8px;
    overflow: hidden;
}
.flux-card-header {
    background: #CACCEF; color: #080A66;
    padding: 7px 14px; font-size: 12px; font-weight: 700;
    border-bottom: 1px solid #b0b4e8;
    display: flex; align-items: center; gap: 8px;
}
.flux-add-row {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; flex-wrap: wrap;
}
.flux-input {
    border: 1px solid #CACCEF; border-radius: 5px;
    padding: 5px 10px; font-size: 13px; color: #222;
    flex: 1; min-width: 180px; background: #fff;
}
.flux-input:focus { border-color: #080A66; outline: none; box-shadow: 0 0 0 2px rgba(8,10,102,.1); }

/* ── Liste des flux ── */
.flux-feed {
    border-bottom: 1px solid #eef0f8;
}
.flux-feed:last-child { border-bottom: none; }
.flux-feed-title {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 14px; background: #f5f6fd;
    border-bottom: 1px solid #eef0f8;
}
.flux-feed-name {
    font-size: 13px; font-weight: 700; color: #080A66;
    display: flex; align-items: center; gap: 6px;
}
.flux-feed-del {
    font-size: 11px; color: #c00; text-decoration: none;
}
.flux-feed-del:hover { text-decoration: underline; }
.flux-item {
    display: flex; align-items: baseline; justify-content: space-between;
    gap: 10px; padding: 5px 14px 5px 28px;
    border-bottom: 1px solid #f4f4f8; font-size: 12px;
}
.flux-item:last-child { border-bottom: none; }
.flux-item:hover { background: #f9f9fd; }
.flux-item-link { color: #222; text-decoration: none; flex: 1; }
.flux-item-link:hover { color: #080A66; text-decoration: underline; }
.flux-item-link.unread { font-weight: 700; color: #080A66; }
.flux-item-date { font-size: 11px; color: #999; white-space: nowrap; flex-shrink: 0; }
.flux-empty { padding: 14px; text-align: center; color: #888; font-size: 13px; }

/* ── Bouton hors carte ── */
.flux-submit-row { text-align: center; padding: 4px 0; }
.flux-error { color: red; font-size: 13px; text-align: center; padding: 8px 14px; }
</style>
</head>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/messagerie_fenetre.js"></script>
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Consultation de vos flux RSS</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
if (LAN == "oui") {
    include_once("./librairie_php/db_rss.php");
    include_once("./magpierss/rss_fetch.inc");

    $cnx    = cnx();
    $idpers = $_SESSION["id_pers"];
    if ($_SESSION["id_suppleant"] > 0) { $idpers = $_SESSION["id_suppleant"]; }

    $erreurRss = "";
    if (isset($_POST["create"])) {
        $url         = $_POST["lienrss"];
        $rss         = fetch_rss($url);
        $description = $rss->channel['title'];
        if (trim($description) != "") {
            ajoutRss($idpers, $url, $_SESSION["membre"]);
        } else {
            $erreurRss = "Cette adresse ($url) est incorrecte.";
        }
    }
    if (isset($_GET["idsupp"])) {
        suppRss($_GET["idsupp"], $idpers, $_SESSION["membre"]);
    }
?>

<script>CreerFenetreBe();</script>

<div class="flux-wrap">

    <!-- ── Ajouter un flux ── -->
    <form method="post">
    <div class="flux-card">
        <div class="flux-card-header">
            <img src="image/commun/rss-icon.gif" alt="rss"> Ajouter un flux RSS
        </div>
        <?php if ($erreurRss) { ?>
        <div class="flux-error"><?php print $erreurRss ?></div>
        <?php } ?>
        <div class="flux-add-row">
            <input type="text" name="lienrss" class="flux-input" value="http://">
        </div>
    </div>
    <div class="flux-submit-row">
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
    </div>
    </form>

    <!-- ── Vos flux ── -->
    <div class="flux-card">
        <div class="flux-card-header">
            <img src="./image/news2.gif" alt="flux"> Vos flux
        </div>

        <?php
        $dataR = consultRss($idpers, $_SESSION["membre"]);
        $tab   = [];
        for ($i = 0; $i < countTriade($dataR); $i++) {
            $tab[$dataR[$i][3]] = 1;
        }
        if (is_array($tab)) krsort($tab);

        if (empty($tab)) {
            print "<div class='flux-empty'>Aucun flux RSS configuré.</div>";
        } else {
            foreach ($tab as $key => $value) {
                if (!preg_match("/http/", $key)) continue;
                $url         = $key;
                $rss         = fetch_rss($key);
                $datemodif   = $rss->channel['pubdate'];
                $feedTitle   = htmlspecialchars(trunchaine($rss->channel['title'], 40));
                $suppUrl     = "flux.php?idsupp=".urlencode($url);
        ?>
        <div class="flux-feed">
            <div class="flux-feed-title">
                <span class="flux-feed-name">
                    <img src="./image/commun/on1.gif" height="8" width="8">
                    <?php print $feedTitle ?>
                </span>
                <a href="<?php print $suppUrl ?>" class="flux-feed-del">&#x2715; supprimer</a>
            </div>
            <?php
            foreach ($rss->items as $item) {
                $href  = $item['link'];
                $title = $item['title'];
                $date  = $item['pubdate'];
                $date  = preg_replace('/\+\.\.\.\./', "", $date);
                $date  = preg_replace('/GMT/', "", $date);
                if (trim($date) == "") { $date = $datemodif; }
                $conx  = RssDejaLu($title, $url, $_SESSION["id_pers"], $_SESSION["membre"]);
                $idrss = idRss($url, $_SESSION["id_pers"], $_SESSION["membre"]);
                $title2 = urlencode($title);
                $unreadClass = ($conx == "non") ? " unread" : "";
            ?>
            <div class="flux-item">
                <a href="#" onclick="return apercu('lect_rss.php?id=<?php print $idrss ?>&titre=<?php print $title2 ?>');"
                   class="flux-item-link<?php print $unreadClass ?>">
                   <?php print htmlspecialchars(trunchaine($title, 40)) ?>
                </a>
                <span class="flux-item-date"><?php print $date ?></span>
            </div>
            <?php } ?>
        </div>
        <?php
            } // foreach
        }
        ?>
    </div><!-- /.flux-card -->

</div><!-- /.flux-wrap -->

<?php
    Pgclose();
} else {
    print "<br><center><font class=T2>".ERREUR1."</font><br><br><i>".ERREUR1."</i></center><br><br>";
}

print "</td></tr></table>";
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
