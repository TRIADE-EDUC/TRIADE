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
include_once("../librairie_php/lib_licence.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");

$fic = "../data/fic_pass.txt";
if (!file_exists($fic) && file_exists("./data/fic_pass.txt")) {
    $fic = "./data/fic_pass.txt";
}

$comptes = array();
$has_classe = false;

if (file_exists($fic)) {
    $contenu = file_get_contents($fic);
    $lignes = preg_split('/(<br\s*\/?>|\r\n|\n|\r)/i', $contenu);
    foreach ($lignes as $ligne) {
        $ligne = trim(strip_tags($ligne));
        if ($ligne === '') {
            continue;
        }
        $parts = explode(';', $ligne);
        $count = count($parts);
        if ($count >= 2) {
            $nom        = trim($parts[0]);
            $prenom     = trim($parts[1]);
            $mdp_parent = isset($parts[2]) ? trim($parts[2]) : '';
            $mdp_eleve  = isset($parts[3]) ? trim($parts[3]) : '';
            $classe     = isset($parts[4]) ? trim($parts[4]) : '';

            if ($count == 3 && $mdp_parent !== '') {
                $mdp_eleve = $mdp_parent;
            }

            if ($classe !== '') {
                $has_classe = true;
            }

            $comptes[] = array(
                'nom'        => $nom,
                'prenom'     => $prenom,
                'mdp_parent' => $mdp_parent,
                'mdp_eleve'  => $mdp_eleve,
                'classe'     => $classe
            );
        }
    }
}

// Export direct en TSV si demandé
if (isset($_GET['export']) && $_GET['export'] === 'tsv') {
    header('Content-Type: text/tab-separated-values; charset=UTF-8');
    header('Content-Disposition: attachment; filename="mots_de_passe_triade_' . date('Y-m-d') . '.tsv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "\xEF\xBB\xBF";
    echo "Nom (login)\tPrénom (login)\tMot de passe Parent\tMot de passe Élève" . ($has_classe ? "\tClasse" : "") . "\r\n";
    foreach ($comptes as $c) {
        echo $c['nom'] . "\t" . $c['prenom'] . "\t" . $c['mdp_parent'] . "\t" . $c['mdp_eleve'] . ($has_classe ? "\t" . $c['classe'] : "") . "\r\n";
    }
    exit;
}

if (file_exists($fic)) {
    @unlink($fic);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="CacheControl" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<title>Triade — Récupération des mots de passe</title>
<link rel="stylesheet" href="../librairie_css/css.css">
<link rel="stylesheet" href="../librairie_css/css-v4.css">
<link rel="stylesheet" href="../librairie_css/css-v4-2.css">
</head>
<body class="paper-wrapper">

<div class="paper-toolbar">
  <div class="paper-toolbar-info">
    <strong>Aperçu avant impression</strong> &mdash; Vous pouvez imprimer cette feuille directement via le raccourci <strong>Ctrl + P</strong>.
  </div>
  <div class="paper-toolbar-actions">
    <?php if (!empty($comptes)) : ?>
    <button type="button" class="btn-dest" onclick="telechargerTSV()">Télécharger en TSV (.tsv)</button>
    <button type="button" class="btn-enr" onclick="window.print()">Imprimer (Ctrl+P)</button>
    <?php endif; ?>
    <button type="button" class="btn-retour" onclick="window.close()">Fermer</button>
  </div>
</div>

<div class="paper-sheet">

  <div class="paper-header">
    <div>
      <h1 class="paper-title"><?php print defined('LANGBASE13') ? LANGBASE13 : "Liste des identifiants et mots de passe" ?></h1>
      <p class="paper-subtitle">Comptes générés lors de l'importation de la base élèves (SIECLE-BEE / SCONET)</p>
    </div>
    <div class="paper-date">
      Édité le : <strong><?php print dateDMY() ?></strong> à <?php print dateHIS() ?>
    </div>
  </div>

  <div class="paper-notice">
    <p><strong>Recommandations de sécurité :</strong> Veuillez conserver et communiquer ces identifiants de façon sécurisée et confidentielle.</p>
    <p>&bull; Seuls les comptes affectés à une classe sont autorisés à se connecter.</p>
  </div>

  <?php if (!empty($comptes)) : ?>
  <table class="paper-table">
    <thead>
      <tr>
        <th>Nom (Login)</th>
        <th>Prénom</th>
        <th>Mot de passe Parent</th>
        <th>Mot de passe Élève</th>
        <?php if ($has_classe) : ?>
        <th>Classe</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($comptes as $c) : ?>
      <tr>
        <td><strong><?php print htmlspecialchars($c['nom']) ?></strong></td>
        <td><?php print htmlspecialchars($c['prenom']) ?></td>
        <td><span class="paper-pwd-cell"><?php print htmlspecialchars($c['mdp_parent']) ?></span></td>
        <td><span class="paper-pwd-cell"><?php print htmlspecialchars($c['mdp_eleve']) ?></span></td>
        <?php if ($has_classe) : ?>
        <td><?php print htmlspecialchars($c['classe']) ?></td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="paper-footer">
    <div>Total : <strong><?php print count($comptes) ?></strong> compte(s) généré(s)</div>
    <div>Document confidentiel &mdash; Logiciel TRIADE Scolarité</div>
  </div>

  <script>
  function telechargerTSV() {
    var donnees = <?php print json_encode($comptes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var hasClasse = <?php print $has_classe ? 'true' : 'false' ?>;
    
    var tsv = "Nom (login)\tPr\u00e9nom (login)\tMot de passe Parent\tMot de passe \u00c9l\u00e8ve" + (hasClasse ? "\tClasse" : "") + "\r\n";
    for (var i = 0; i < donnees.length; i++) {
      var row = donnees[i];
      tsv += row.nom + "\t" + row.prenom + "\t" + row.mdp_parent + "\t" + row.mdp_eleve + (hasClasse ? "\t" + row.classe : "") + "\r\n";
    }

    var blob = new Blob(["\uFEFF" + tsv], { type: "text/tab-separated-values;charset=utf-8;" });
    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = "mots_de_passe_triade_" + (new Date().toISOString().slice(0, 10)) + ".tsv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }
  </script>

  <?php else : ?>
  <div class="paper-notice" style="text-align:center;padding:24px;">
    <strong><?php print defined('LANGBASE18') ? LANGBASE18 : "Aucun mot de passe à récupérer ou fichier déjà consulté." ?></strong>
  </div>
  <?php endif; ?>

</div>

</body>
</html>
