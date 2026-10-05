<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PDFChecker.php,v 1.1 2024/04/10 13:58:13 qvarin Exp $

namespace Pmb\Common\Library\PDF;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class PDFChecker
{
    /**
     * Vérifie si le fichier est un PDF
     *
     * @param string $file Chemin du fichier
     * @return boolean
     */
    public static function isPDF(string $file)
    {
        // Vérifie l'extension du fichier
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (strtolower($extension) === 'pdf') {
            return true;
        }

        // Vérifie si le type MIME est un PDF
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file);
        finfo_close($finfo);

        return $mime_type === 'application/pdf';
    }
}
