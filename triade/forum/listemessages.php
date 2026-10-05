<?php

$NombreMsgParPage               = 30;
$NombreMaxPages                 = 1;
$nombreNouveauxMessagesSignales = 5;

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

function tabulation($n = 1) {
    return (30 * ($n - 1) + 10);
}

if ($nombremsgs < 1) {

    echo '<div class="frm-empty-msg">';
    echo '<i class="bi bi-chat-square-dots"></i>';
    echo htmlspecialchars(LANGFORUM2) . '<br><br>';
    echo LANGFORUM3 . ' <a href="forumposter.php" class="btn btn-sm">'
       . '<i class="bi bi-pencil-square"></i> ' . htmlspecialchars(LANGFORUM3bis) . '</a> '
       . htmlspecialchars(LANGFORUM3ter);
    echo '</div>';

} else {

    for ($compt = 1; $compt <= $nombremsgs; $compt++) {
        $tabidents[$compt] = intval($index[$compt][1]);
    }
    rsort($tabidents);
    $limMaxDerMessa = $tabidents[0];
    $limMinDerMessa = ($nombremsgs <= $nombreNouveauxMessagesSignales)
        ? $tabidents[$nombremsgs - 1]
        : $tabidents[$nombreNouveauxMessagesSignales - 1];
    if ($nombremsgs == 1) { $limMaxDerMessa = 1; $limMinDerMessa = 1; }
    unset($tabidents);

    if (empty($p)) $p = 1;
    $rangPMax = $nombremsgs - (($p - 1) * $NombreMsgParPage);
    $rangPMin = max($nombremsgs - ($p * $NombreMsgParPage) + 1, 1);

    echo '<div class="toolbar">';
    echo '<a href="forumposter.php" class="btn btn-sm"><i class="bi bi-pencil-square"></i> '
       . htmlspecialchars(LANGFORUM4) . '</a>';
    echo '</div>';

    echo '<div style="padding:4px 10px 12px;">';
    echo '<table class="table" style="width:99%;margin:0 auto;">';
    echo '<thead><tr>';
    echo '<th class="cc-th"><i class="bi bi-chat-text"></i> Sujet</th>';
    echo '<th class="cc-th" style="width:180px;"><i class="bi bi-person"></i> Auteur</th>';
    echo '<th class="cc-th" style="width:140px;"><i class="bi bi-calendar3"></i> Date</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    for ($rangC = $rangPMax; $rangC >= $rangPMin; $rangC--) {
        if ($index[$rangC][2] == 1) {

            $isNew = ($nombreNouveauxMessagesSignales > 0
                   && $index[$rangC][1] >= $limMinDerMessa
                   && $index[$rangC][1] <= $limMaxDerMessa);

            echo '<tr class="cc-tr-data">';
            echo '<td style="padding-left:10px;">';
            echo '<i class="bi bi-chat-right-text-fill frm-thread-icon"></i>';
            echo '<a href="lire.php?msg=' . intval($index[$rangC][1]) . '">'
               . stripslashes(htmlentities(strip_tags($index[$rangC][5]))) . '</a>';
            if ($isNew) echo '<span class="frm-new-badge">Nouveau</span>';
            echo '</td>';
            echo '<td style="font-size:.88em;">'
               . stripslashes(htmlentities(strip_tags($index[$rangC][4]))) . '</td>';
            echo '<td style="white-space:nowrap;font-size:.84em;color:#666;">'
               . htmlspecialchars($index[$rangC][3]) . '</td>';
            echo '</tr>';

            $rangP = $rangC + 1;
            while (isset($index[$rangP][2]) && $index[$rangP][2] > 1) {
                $indent = tabulation($index[$rangP][2] - 1);
                $isNewR = ($nombreNouveauxMessagesSignales > 0
                        && $index[$rangP][1] >= $limMinDerMessa
                        && $index[$rangP][1] <= $limMaxDerMessa);

                echo '<tr class="cc-tr-data">';
                echo '<td style="padding-left:' . $indent . 'px;">';
                echo '<i class="bi bi-arrow-return-right frm-reply-icon"></i>';
                echo '<a href="lire.php?msg=' . intval($index[$rangP][1]) . '">'
                   . stripslashes(htmlentities(strip_tags($index[$rangP][5]))) . '</a>';
                if ($isNewR) echo '<span class="frm-new-badge">Nouveau</span>';
                echo '</td>';
                echo '<td style="font-size:.88em;color:#555;">'
                   . stripslashes(htmlentities(strip_tags($index[$rangP][4]))) . '</td>';
                echo '<td style="white-space:nowrap;font-size:.84em;color:#666;">'
                   . htmlspecialchars($index[$rangP][3]) . '</td>';
                echo '</tr>';

                $rangP++;
            }
        }
    }

    echo '</tbody></table>';
    echo '</div>';
}
?>
