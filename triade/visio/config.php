<?php
/*
 * TRIADE Visio — configuration locale
 *
 * VISIO_UPLOADS_BASE : répertoire de stockage des fichiers partagés en visio.
 * Doit être HORS de la racine web (non accessible via HTTP).
 *
 * Exemples :
 *   cPanel/o2switch  → /home/mon_compte/triade_visio_uploads/
 *   VPS standard     → /var/triade_visio_uploads/
 *   Hébergement mutu → adapter selon open_basedir du serveur
 *
 * Par défaut : un répertoire frère de public_html (hors racine web).
 */

// Durée de vie des fichiers après la fin de session (secondes). Défaut : 2 heures.
if (!defined('VISIO_UPLOADS_TTL')) define('VISIO_UPLOADS_TTL', 7200);

if (!defined('VISIO_UPLOADS_BASE')) {
    $docRoot  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
    $parentDir = dirname($docRoot);          // /home/user/ sur cPanel
    $candidate = $parentDir . DIRECTORY_SEPARATOR . 'triade_visio_uploads' . DIRECTORY_SEPARATOR;

    // Vérifier qu'on peut créer/écrire là (open_basedir peut bloquer)
    if (!is_dir($candidate)) {
        @mkdir($candidate, 0750, true);
    }

    if (is_writable($candidate)) {
        define('VISIO_UPLOADS_BASE', $candidate);
    } else {
        // Fallback : sous-dossier local protégé par .htaccess (Apache only)
        define('VISIO_UPLOADS_BASE', __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
    }
}
