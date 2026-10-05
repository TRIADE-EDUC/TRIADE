<?php
/*
 * Vérification abonnement visio — à inclure en haut de chaque page visio
 * Redirige avec message si l'abonnement est inactif ou expiré
 */
include_once(__DIR__ . "/../librairie_php/db_visio.php");

if (!verifAbonnementVisio()) {
    $abo = getAbonnementVisio();
    $msg = "Le module visioconférence n'est pas activé pour votre établissement.";
    if ($abo && in_array($abo['statut'], ['actif', 'actif-resi']) && $abo['date_fin'] < date('Y-m-d')) {
        $msg = "Votre abonnement visioconférence a expiré le " . date('d/m/Y', strtotime($abo['date_fin'])) . ".";
    }
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
    <meta charset="UTF-8">
    <title>Visio — Accès restreint</title>
    <link rel="stylesheet" href="../librairie_css/css.css">
    <link rel="stylesheet" href="../librairie_css/css-v4.css">
    </head>
    <body>
    <div style="max-width:500px;margin:80px auto;text-align:center;padding:20px;">
        <div style="font-size:48px;margin-bottom:20px;">📹</div>
        <h2 style="color:#080A66;margin-bottom:15px;">Visioconférence TRIADE</h2>
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:16px;color:#856404;margin-bottom:20px;">
            <?php echo htmlspecialchars($msg); ?>
        </div>
        <p style="color:#666;font-size:13px;">Contactez votre administrateur pour activer ou renouveler l'abonnement.</p>
    </div>
    </body>
    </html>
    <?php
    exit;
}
