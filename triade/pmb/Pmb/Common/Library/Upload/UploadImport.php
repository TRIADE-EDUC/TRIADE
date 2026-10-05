<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UploadImport.php,v 1.2.2.1 2024/08/14 12:13:58 dbellamy Exp $

namespace Pmb\Common\Library\Upload;

class UploadImport
{
    /**
     * Liste des erreurs d'upload possibles
     *
     * @var array
     */
    public const ERRORS = [
        \UPLOAD_ERR_INI_SIZE,
        \UPLOAD_ERR_FORM_SIZE,
        \UPLOAD_ERR_PARTIAL,
        \UPLOAD_ERR_NO_FILE,
        \UPLOAD_ERR_NO_TMP_DIR,
        \UPLOAD_ERR_CANT_WRITE,
        \UPLOAD_ERR_EXTENSION
    ];

    /**
     * Liste des parametres valides
     *
     * @var array
     */
    public const VALID_STRUCTURE = [
        'name',
        'full_path', // PHP8.1+
        'type',
        'tmp_name',
        'error',
        'size'
    ];

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
     * Correspond aux informations du fichier uploade
     *
     * @var array
     */
    public $infos = [
        'name' => '',
        'type' => '',
        'tmp_name' => '',
        'error' => \UPLOAD_ERR_NO_FILE,
        'size' => 0
    ];

    /**
     * Nom du fichier uploade
     *
     * @var string
     */
    public $filename = "";

    /**
     *
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name)
    {
        if (!$this->isValid($name)) {
            throw new \InvalidArgumentException("Unable to find file {$name} or is it  not a valid file");
        }
        $this->infos = $_FILES[$name];
        unset($_FILES[$name]);
    }

    /**
     * Permet de verifier la validite du fichier
     *
     * @param string $name
     * @return boolean
     */
    protected function isValid(string $name)
    {
        $file = $_FILES[$name] ?? null;

        if (empty($file) || !is_array($file)) {
            return false;
        }

        if (
            empty($file['name']) ||
            !is_uploaded_file($file['tmp_name']) ||
            $file['size'] === 0 ||
            in_array($file['error'], static::ERRORS, true)
        ) {
            return false;
        }

        return $this->isValidStructure($file) && $this->isValidFile($file);
    }

    /**
     * Permet de trouver et definir l'extension du fichier en fonction du binaire
     *
     * @param array $file
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function findExtension(array $file)
    {
        $content = file_get_contents($file['tmp_name']);
        if (false === $content) {
            throw new \InvalidArgumentException("Unable to read file {$file['tmp_name']}");
        }

        $finfo = new \finfo();
        $mimeType = $finfo->buffer($content, FILEINFO_MIME_TYPE);
        return substr($mimeType, strrpos($mimeType, '/') + 1);
    }

    /**
     * Permet de verifier la validite du fichier
     *
     * @param array $file
     * @return bool
     */
    public function isValidFile(array $file)
    {
        $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
        if (in_array(strtolower($ext), static::DISALLOWED_EXT, true)) {
            return false;
        }

        if (in_array(strtolower($this->findExtension($file)), static::DISALLOWED_EXT, true)) {
            return false;
        }
        return true;
    }

    /**
     * Permet de verifier la structure du tableau file donne
     *
     * @param array $file
     * @return boolean
     */
    protected function isValidStructure(array $file)
    {
        foreach (array_keys($file) as $k) {
            if ( ! in_array($k, static::VALID_STRUCTURE)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Genere un nom de fichier sans extension.
     *
     * @param string $prefixFilename
     * @return void
     */
    protected function generateFilename(string $prefixFilename = "")
    {
        $randomSalt = random_bytes(random_int(10, 30));
        $this->filename = $prefixFilename . md5($this->infos['name'] . $randomSalt);
    }

    /**
     * Copie le fichier et le supprime du repertoire temporaire
     *
     * @param string $to
     * @param string $prefixFilename
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function copy(string $to, string $prefixFilename = "")
    {
        $to = trim($to, DIRECTORY_SEPARATOR);
        if (! is_dir($to) || ! is_writable($to)) {
            throw new \InvalidArgumentException("Directory {$to} does not exist or is not writable");
        }

        $this->generateFilename($prefixFilename);
        $success = @copy($this->infos['tmp_name'], join(DIRECTORY_SEPARATOR, [
            $to,
            $this->filename
        ]));

        if ($success) {
            @unlink($this->infos['tmp_name']);
        }

        return $success;
    }
}
