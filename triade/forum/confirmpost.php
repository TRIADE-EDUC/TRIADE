<?php
session_start();
error_reporting(0);
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
</head>
<body id='bodyforum' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("../librairie_php/lib_licence_forum.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>
  <i class="bi bi-check2-circle"></i>
  <?php echo defined('LANGFORUM15') ? LANGFORUM15 : 'Confirmation'; ?>
  </font></b>
</td></tr>
<tr id='cadreCentral0'>
<td valign='top'>
<?php

if ($_SESSION["membre"] == "menueleve")  { if ((defined('ACCESFORUMELEVE'))  && (ACCESFORUMELEVE  == "non")) exit; }
if ($_SESSION["membre"] == "menuprof")   { if ((defined('ACCESFORUMPROF'))   && (ACCESFORUMPROF   == "non")) exit; }
if ($_SESSION["membre"] == "menuparent") { if ((defined('ACCESFORUMPARENT')) && (ACCESFORUMPARENT == "non")) exit; }

$repforum = "../data/forum/" . $_SESSION["membre"];
if (!file_exists("{$repforum}/index.dat")) {
    $crfic = fopen("{$repforum}/index.dat", "w+");
    fputs($crfic, "Fichier Index. Ne pas éditer !");
    fclose($crfic);
}

$ficindex   = file("{$repforum}/index.dat");
$nombremsgs = count($ficindex) - 1;

for ($compt = 1; $compt <= $nombremsgs; $compt++) {
    $index[$compt][1] = strtok($ficindex[$compt], "#");
    $index[$compt][2] = strtok("#");
    $index[$compt][3] = strtok("#");
}

$nom   = isset($_POST["nom"])   ? $_POST["nom"]   : "";
$sujet = isset($_POST["sujet"]) ? $_POST["sujet"] : "";
$adel  = isset($_POST["adel"])  ? $_POST["adel"]  : "";
$texte = isset($_POST["texte"]) ? $_POST["texte"] : "";
$refer = isset($_POST["refer"]) ? $_POST["refer"]  : "";

function showAlert($type, $icon, $msg) {
    echo '<div class="alert alert-' . $type . '" style="margin:16px;">';
    echo '<i class="bi bi-' . $icon . '"></i> ' . $msg;
    echo '</div>';
}

if ((!$nom) && (!$adel) && (!$sujet) && (!$texte)) {
    showAlert('warning', 'exclamation-triangle', defined('LANGFORUM16') ? LANGFORUM16 : 'Formulaire vide.');

} elseif (!$texte) {
    showAlert('warning', 'exclamation-triangle', defined('LANGFORUM17') ? LANGFORUM17 : 'Message vide.');

} elseif (!$nom) {
    showAlert('warning', 'exclamation-triangle', defined('LANGFORUM18') ? LANGFORUM18 : 'Nom manquant.');

} else {

    for ($compt = 1; $compt <= $nombremsgs; $compt++) {
        $tabidents[$compt] = intval($index[$compt][1]);
    }
    $IDnouvM = isset($tabidents) ? max($tabidents) + 1 : 1;
    unset($tabidents);

    function determin($valeur) {
        global $index, $nombremsgs;
        if ($valeur) {
            $compt = 1;
            while (isset($index[$compt][1]) && $index[$compt][1] != $valeur) $compt++;
            $rangMP   = $compt;
            $niveauMP = $index[$compt][2];
            $compt    = $compt + 1;
            while (isset($index[$compt][2]) && $index[$compt][2] > $niveauMP) $compt++;
            $rangNM   = $compt - 1;
            $niveauNM = $niveauMP + 1;
        } else {
            $rangNM   = $nombremsgs;
            $niveauNM = 1;
        }
        return array(1 => $rangNM, 2 => $niveauNM);
    }

    if (!$refer) $refer = "";
    $res        = determin($refer);
    $RANGnouvM  = $res[1];
    $NIVEAUnouvM = $res[2];

    include_once("../librairie_php/timezone.php");
    $date = dateDMY() . ", " . dateHIS();

    if (!$refer)  $refer = "n";
    if (!$nom)    $nom   = "Pas de nom";
    if (empty($adel) || !filter_var($adel, FILTER_VALIDATE_EMAIL)) $adel = "noemail";
    if (!$sujet)  $sujet = "Pas de sujet";

    function stripSpeCar($chaine) {
        $chaine = str_replace("#", "", $chaine);
        $chaine = str_replace("|", "", $chaine);
        return $chaine;
    }

    $nom   = trim(stripslashes(stripSpeCar($nom)));
    $adel  = trim(stripslashes(stripSpeCar($adel)));
    $sujet = trim(stripslashes(stripSpeCar($sujet)));
    $texte = trim(stripslashes(stripSpeCar($texte)));

    $nomfichier = "{$repforum}/msg{$IDnouvM}.dat";
    $fic        = fopen($nomfichier, "w+");

    if (!$fic) {
        showAlert('danger', 'x-circle', defined('LANGFORUM19') ? LANGFORUM19 : 'Erreur d\'écriture.');
    } else {
        fputs($fic, $refer . "\n");
        fputs($fic, $date . "\n");
        fputs($fic, $nom . "\n");
        fputs($fic, $adel . "\n");
        fputs($fic, $sujet . "\n");
        fputs($fic, $texte . "\n");
        fclose($fic);

        $find = fopen("{$repforum}/index.dat", "w+");
        if (!$find) {
            showAlert('danger', 'x-circle',
                (defined('LANGFORUM20') ? LANGFORUM20 : 'Erreur index.') . ' '
               . (defined('LANGFORUM21') ? LANGFORUM21 : ''));
        } else {
            fputs($find, "Fichier Index. Ne pas éditer !\n");
            for ($compt = 1; $compt <= $RANGnouvM; $compt++) {
                fputs($find, $ficindex[$compt]);
            }
            fputs($find, $IDnouvM . "#");
            fputs($find, $NIVEAUnouvM . "#");
            fputs($find, $date . "|" . $nom . "|" . $sujet . "|" . "#\n");
            for ($compt = $RANGnouvM + 1; $compt <= $nombremsgs; $compt++) {
                fputs($find, $ficindex[$compt]);
            }
            fclose($find);

            echo '<div style="max-width:640px;margin:24px auto;text-align:center;">';
            echo '<div class="alert alert-success">';
            echo '<i class="bi bi-check2-circle"></i> ';
            echo defined('LANGFORUM22') ? LANGFORUM22 : 'Message publié avec succès.';
            echo '</div>';
            echo '<a href="forum.php" class="btn" style="margin-top:8px;">'
               . '<i class="bi bi-list-ul"></i> '
               . (defined('LANGFORUM23') ? LANGFORUM23 : 'Retour au forum') . '</a>';
            echo '</div>';
        }
    }
}
?>
</td></tr></table>
</BODY></HTML>
