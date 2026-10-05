<?php
$repforum = "../data/forum/" . $_SESSION["membre"];
if (!file_exists("{$repforum}/index.dat")) {
    $crfic = fopen("{$repforum}/index.dat", "w+");
    fputs($crfic, "Fichier Index. Ne pas éditer !");
    fclose($crfic);
}

$refer = isset($_GET['refer']) ? $_GET["refer"] : "";
if (!$refer) $refer = "";

if ($refer && !file_exists("{$repforum}/msg" . $refer . ".dat")) {
    echo '<div class="alert alert-warning" style="margin:16px;">';
    echo '<i class="bi bi-exclamation-triangle"></i> ';
    echo defined('LANGFORUM7') ? LANGFORUM7 : 'Message de référence introuvable.';
    echo ' <a href="forum.php" class="btn btn-sm" style="margin-left:8px;">'
       . (defined('LANGFORUM8') ? LANGFORUM8 : 'Retour') . '</a>';
    echo '</div>';
} else {

    $sujetMsgRef = "";
    $texteMsgRef = "";

    if ($refer) {
        $nomfichiermsg  = "{$repforum}/msg" . $refer . ".dat";
        $tabmessage     = file($nomfichiermsg);
        $nlignes        = count($tabmessage) - 1;
        $sujetMsgRef    = $tabmessage[4];
        $texteMsgRef    = defined('LANGFORUM9') ? LANGFORUM9 . "\n" : "Citation :\n";
        for ($compt = 5; $compt <= $nlignes; $compt++) {
            $texteMsgRef .= "~ " . $tabmessage[$compt];
        }
    }

    $val = ucwords($_SESSION["prenom"]) . " " . strtoupper($_SESSION["nom"]);
    if ($_SESSION["membre"] == "menuparent") {
        $val = "Parent de " . ucwords($_SESSION["prenom"]) . " " . strtoupper($_SESSION["nom"]);
    }
    if ($_SESSION["membre"] == "menueleve") {
        $val = "Élève - " . ucwords($_SESSION["prenom"]) . " " . strtoupper($_SESSION["nom"]);
    }

    echo '<div class="frm-post-card">';
    echo '<div class="frm-post-header"><i class="bi bi-chat-right-text"></i> '
       . ($refer ? (defined('LANGFORUM32') ? LANGFORUM32 : 'Répondre au message') : (defined('LANGFORUM5') ? LANGFORUM5 : 'Nouveau message'))
       . '</div>';

    echo '<div class="frm-post-body">';
    echo '<form method="POST" action="confirmpost.php">';

    echo '<div class="frm-post-row">';
    echo '<label>' . (defined('LANGFORUM10') ? LANGFORUM10 : 'Auteur') . '</label>';
    echo '<input type="text" name="nom" value="' . htmlspecialchars($val) . '" onfocus="this.blur()" '
       . 'style="color:#080A66;font-weight:600;background:#f0f2fa;">';
    echo '</div>';

    echo '<div class="frm-post-row">';
    echo '<label>' . (defined('LANGFORUM12') ? LANGFORUM12 : 'Sujet') . '</label>';
    if ($refer) {
        echo '<input type="text" name="sujet" maxlength="40" '
           . 'value="Re : ' . htmlspecialchars(stripslashes(strip_tags($sujetMsgRef))) . '">';
    } else {
        echo '<input type="text" name="sujet" maxlength="40">';
    }
    echo '</div>';

    echo '<div style="margin-bottom:12px;">';
    echo '<textarea name="texte" rows="9" class="frm-post-textarea" wrap="virtual">';
    if ($refer) {
        echo htmlspecialchars(stripslashes(strip_tags($texteMsgRef)));
    }
    echo '</textarea>';
    echo '</div>';

    if ($refer) {
        echo '<input type="hidden" name="refer" value="' . htmlspecialchars($refer) . '">';
    }

    echo '<div class="frm-post-footer">';
    echo '<input type="submit" value="' . (defined('LANGFORUM13') ? htmlspecialchars(LANGFORUM13) : 'Envoyer') . '" class="btn">';
    echo '<a href="forum.php" class="btn btn-outline">'
       . '<i class="bi bi-list-ul"></i> '
       . (defined('LANGFORUM14') ? LANGFORUM14 : 'Retour') . '</a>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';
}
?>
