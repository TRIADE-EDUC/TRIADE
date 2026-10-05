<?php
session_start();
$optionCodeHtml = 0;
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script language="JavaScript" src="../librairie_js/acces.js"></script>
<script language="JavaScript" src="../librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="../librairie_js/function.js"></script>
<title>Triade - Forum</title>
<style>
.frm-msg-card   { max-width:860px; margin:12px auto; border-radius:7px; box-shadow:0 2px 8px rgba(8,10,102,.11); overflow:hidden; }
.frm-msg-meta   { background:#f0f2fa; padding:10px 16px; border-bottom:1px solid #dde3f3; }
.frm-msg-sujet  { font-size:1.05em; font-weight:700; color:#080A66; }
.frm-msg-info   { font-size:.84em; color:#555; margin-top:4px; }
.frm-msg-info b { color:#333; }
.frm-msg-body   { padding:16px 20px; background:#fff; min-height:60px; line-height:1.75; font-size:.93em; }
.frm-msg-footer { background:#f8f9ff; border-top:1px solid #eaecf7; padding:8px 16px; display:flex; gap:8px; align-items:center; }
.frm-nav-card   { max-width:860px; margin:8px auto; background:#fff; border-radius:7px; box-shadow:0 1px 4px rgba(8,10,102,.08); overflow:hidden; }
.frm-nav-header { background:#e8edff; padding:6px 14px; font-size:.83em; font-weight:600; color:#080A66; border-bottom:1px solid #dde3f3; }
.frm-nav-row    { padding:7px 14px; font-size:.9em; border-bottom:1px solid #f0f2fa; }
.frm-nav-row:last-child { border-bottom:none; }
.frm-nav-row:hover { background:#f5f7ff; }
.frm-footer-links { max-width:860px; margin:10px auto 4px; display:flex; gap:10px; justify-content:center; }
</style>
</head>
<body id='bodyforum' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("../librairie_php/lib_licence_forum.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php echo defined('LANGFORUM24') ? LANGFORUM24 : 'Message'; ?></font></b>
</td></tr>
<tr id='cadreCentral0' valign='top'>
<td>
<?php

$repforum = "../data/forum/" . $_SESSION["membre"];
if (!file_exists("{$repforum}/index.dat")) {
    $crfic = fopen("{$repforum}/index.dat", "w+");
    fputs($crfic, "Fichier Index. Ne pas éditer !");
    fclose($crfic);
}

$tabindex   = file("{$repforum}/index.dat");
$nombremsgs = count($tabindex) - 1;

for ($compt = 1; $compt <= $nombremsgs; $compt++) {
    $index[$compt][1] = strtok($tabindex[$compt], "#");
    $index[$compt][2] = strtok("#");
    $chainetemp        = strtok("#");
    $index[$compt][3]  = strtok($chainetemp, "|");
    $index[$compt][4]  = strtok("|");
    $index[$compt][5]  = strtok("|");
}

$msg = isset($_GET["msg"]) ? $_GET["msg"] : "";
if (!$msg && isset($index)) $msg = $index[$compt][1];

$nomfichiermsg = "{$repforum}/msg" . $msg . ".dat";

if (!file_exists($nomfichiermsg)) {
    if ($nombremsgs < 1) {
        echo '<div style="padding:24px;text-align:center;color:#7a82a6;">';
        echo '<i class="bi bi-chat-square" style="font-size:2em;display:block;margin-bottom:8px;"></i>';
        echo defined('LANGFORUM25') ? LANGFORUM25 : 'Aucun message.';
        echo '<br><a href="post.php" class="btn btn-sm" style="margin-top:10px;">';
        echo (defined('LANGFORUM26bis') ? LANGFORUM26bis : 'Poster') . '</a>';
        echo '</div>';
    } else {
        echo '<div style="padding:24px;text-align:center;color:#7a82a6;">';
        echo defined('LANGFORUM27') ? LANGFORUM27 : 'Message introuvable.';
        echo ' <a href="forum.php" class="btn btn-sm" style="margin-left:8px;">';
        echo (defined('LANGFORUM28') ? LANGFORUM28 : 'Retour') . '</a>';
        echo '</div>';
    }
} else {

    $tabmessage = file($nomfichiermsg);
    $nlignes    = count($tabmessage) - 1;

    $message[1] = $tabmessage[1]; // date
    $message[2] = $tabmessage[2]; // auteur
    $message[3] = $tabmessage[3]; // email
    $message[4] = $tabmessage[4]; // sujet

    // Message card
    echo '<div class="frm-msg-card">';
    echo '<div class="frm-msg-meta">';
    echo '<div class="frm-msg-sujet">'
       . stripslashes(htmlentities(strip_tags($message[4]))) . '</div>';
    echo '<div class="frm-msg-info">';

    if (trim($message[3]) == "noemail") {
        echo '<b>' . (defined('LANGFORUM30') ? LANGFORUM30 : 'Auteur') . ' :</b> '
           . stripslashes(htmlentities(strip_tags($message[2])));
    } else {
        echo '<b>' . (defined('LANGFORUM30') ? LANGFORUM30 : 'Auteur') . ' :</b> '
           . '<a href="mailto:' . htmlspecialchars(trim($message[3])) . '">'
           . htmlspecialchars(trim($message[2])) . '</a>';
    }

    echo ' &nbsp;&bull;&nbsp; <i class="bi bi-calendar3"></i> ' . htmlspecialchars(trim($message[1]));
    echo '</div>';
    echo '</div>';

    echo '<div class="frm-msg-body">';
    for ($compt = 5; $compt <= $nlignes; $compt++) {
        if (!$optionCodeHtml) {
            echo stripslashes(htmlentities(strip_tags($tabmessage[$compt]))) . "<br>\n";
        } else {
            echo stripslashes($tabmessage[$compt]) . "<br>\n";
        }
    }
    echo '</div>';

    echo '<div class="frm-msg-footer">';
    echo '<a href="forumposter.php?refer=' . intval($msg) . '" class="btn btn-sm">'
       . '<i class="bi bi-reply"></i> '
       . (defined('LANGFORUM32') ? LANGFORUM32 : 'Répondre') . '</a>';
    echo '<a href="forum.php" class="btn btn-sm btn-outline">'
       . '<i class="bi bi-list-ul"></i> '
       . (defined('LANGFORUM8') ? LANGFORUM8 : 'Liste des messages') . '</a>';
    echo '</div>';
    echo '</div>';

    // Find position of current message
    $testrangmsg = 1;
    while (isset($index[$testrangmsg][1]) && $index[$testrangmsg][1] != $msg) {
        $testrangmsg++;
    }
    $rangmsg = $testrangmsg;

    // Message précédent
    if (isset($index[$rangmsg][2]) && $index[$rangmsg][2] > 1) {
        $testrangmsgMP = $rangmsg;
        $testrangmsgMP--;
        while (isset($index[$testrangmsgMP][2]) && $index[$testrangmsgMP][2] >= $index[$rangmsg][2]) {
            $testrangmsgMP--;
        }
        $rangmsgMP = $testrangmsgMP;

        echo '<div class="frm-nav-card" style="margin-top:10px;">';
        echo '<div class="frm-nav-header"><i class="bi bi-arrow-up-circle"></i> '
           . (defined('LANGFORUM33') ? LANGFORUM33 : 'Message précédent') . '</div>';
        echo '<div class="frm-nav-row">';
        echo '<a href="lire.php?msg=' . intval($index[$rangmsgMP][1]) . '">'
           . stripslashes(htmlentities(strip_tags($index[$rangmsgMP][5]))) . '</a>';
        echo ' &mdash; ' . stripslashes(htmlentities(strip_tags($index[$rangmsgMP][4])));
        echo ' <span style="color:#888;font-size:.84em;">(' . htmlspecialchars($index[$rangmsgMP][3]) . ')</span>';
        echo '</div>';
        echo '</div>';
    }

    // Messages suivants
    function couleuralt() { return ''; }
    function tabulation($n = 1) { return (30 * ($n - 1) + 10); }

    $rangmsgMS = $rangmsg + 1;
    if (isset($index[$rangmsgMS][2]) && $index[$rangmsgMS][2] > $index[$rangmsg][2]) {
        echo '<div class="frm-nav-card" style="margin-top:8px;">';
        echo '<div class="frm-nav-header"><i class="bi bi-arrow-down-circle"></i> '
           . (defined('LANGFORUM34') ? LANGFORUM34 : 'Messages suivants') . '</div>';

        while (isset($index[$rangmsgMS][2]) && $index[$rangmsgMS][2] > $index[$rangmsg][2]) {
            $indent = tabulation($index[$rangmsgMS][2] - $index[$rangmsg][2] - 1);
            echo '<div class="frm-nav-row" style="padding-left:' . ($indent + 14) . 'px;">';
            if ($indent > 0) echo '<i class="bi bi-arrow-return-right" style="color:#8a94b8;font-size:.8em;margin-right:4px;"></i>';
            echo '<a href="lire.php?msg=' . intval($index[$rangmsgMS][1]) . '">'
               . stripslashes(htmlentities(strip_tags($index[$rangmsgMS][5]))) . '</a>';
            echo ' &mdash; ' . stripslashes(htmlentities(strip_tags($index[$rangmsgMS][4])));
            echo ' <span style="color:#888;font-size:.84em;">(' . htmlspecialchars($index[$rangmsgMS][3]) . ')</span>';
            echo '</div>';
            $rangmsgMS++;
        }

        echo '</div>';
    }

    echo '<div class="frm-footer-links" style="margin-top:12px;">';
    echo '<a href="forumposter.php" class="btn btn-sm">'
       . '<i class="bi bi-pencil-square"></i> '
       . (defined('LANGFORUM4') ? LANGFORUM4 : 'Nouveau message') . '</a>';
    echo '<a href="forum.php" class="btn btn-sm btn-outline">'
       . '<i class="bi bi-list-ul"></i> '
       . (defined('LANGFORUM8') ? LANGFORUM8 : 'Liste') . '</a>';
    echo '</div>';
}
?>
</td></tr></table>
</BODY></HTML>
