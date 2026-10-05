<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UploadFileValidator.php,v 1.1.2.2 2024/09/30 13:12:06 qvarin Exp $

namespace Pmb\Common\Library\Upload;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class UploadFileValidator
{
    /**
     * Extension non autorisee pour l'upload
     *
     * @var array
     */
    public const DISALLOWED_EXT = [
        'php',
        'php2',
        'php3',
        'php4',
        'php5',
        'php6',
        'php7',
        'phps',
        'phps',
        'pht',
        'phtm',
        'phtml',
        'pgif',
        'shtml',
        'phar',
        'module',
        'inc',
        'hphp',
        'ctp',
    ];

    /**
     * Verifie l'extension d'un fichier
     *
     * @param string $extension
     * @return boolean
     */
    public static function isExtensionAllowed(string $extension): bool
    {
        if (in_array(strtolower($extension), static::DISALLOWED_EXT, true)) {
            return false;
        }
        return true;
    }

    /**
     * Retourne l'extension d'un fichier par son contenu
     *
     * @param string $content
     * @return string
     */
    public static function findExtensionWithContent(string $content): string
    {
        $finfo = new \finfo();
        $mimeType = $finfo->buffer($content, FILEINFO_MIME_TYPE);
        return substr($mimeType, strrpos($mimeType, '/') + 1);
    }

    /**
     * Retourne l'extension d'un fichier par son contenu
     *
     * @param string $filename
     * @return string
     */
    public static function findExtensionWithFile(string $filename): string
    {
        return substr($filename, strrpos($filename, '.') + 1);
    }
}