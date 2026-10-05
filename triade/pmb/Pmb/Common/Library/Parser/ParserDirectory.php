<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParserDirectory.php,v 1.5.4.1 2025/03/19 15:53:33 jparis Exp $

namespace Pmb\Common\Library\Parser;

class ParserDirectory
{
    /**
     * repertoire de depart du parse
     * @var string
     */
    protected $baseDir = __DIR__;

    /**
     *
     * @var array
     */
    protected $manifest = [];

    /**
     *
     * @var array
     */
    public $pathManifest = [];

    /**
     *
     * @var boolean
     */
    protected $parsed = false;

    /**
     *
     * @var ParserDirectory[]
     */
    public static $instances = [];

    /**
     * namespace complet de la classe du parser manifest a utiliser (dsi, thumbnail,...)
     * @var string
     */
    protected $parserManifest = "\Pmb\Common\Library\Parser\ParserManifest";

    private function __construct()
    {
        //$this->parse($this->baseDir);
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(static::$instances[static::class])) {
            static::$instances[static::class] = new static();
            static::$instances[static::class]->parse();
        }
        return static::$instances[static::class];
    }

    /**
     *
     * @param string $path
     */
    protected function parse()
    {
        $path = $this->baseDir;
        $this->manifest = $this->loadManifests($path);
        $this->parsed = true;
    }

    /**
     *
     * @param string $path
     * @param array $ignoredManifest
     * @return ParserManifest[]|array
     */
    protected function loadManifests(string $path, array $ignoreManifest = [])
    {
        $path = realpath($path);
        $ignoreManifest = array_map('realpath', $ignoreManifest);

        if (!is_dir($path)) {
            return [];
        }

        $manifest = [];
        $dirs = glob($path . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            if (strpos($dir, 'CVS') !== false) {
                continue;
            }

            $file = $dir . DIRECTORY_SEPARATOR . "manifest.xml";
            if (is_file($file) && !in_array($file, $ignoreManifest)) {
                if (empty($this->pathManifest[$file])) {
                    $this->pathManifest[$file] = new $this->parserManifest($file);
                }
                $manifest[] = $this->pathManifest[$file];
            }
            $manifest = array_merge($manifest, $this->loadManifests($dir, $ignoreManifest));
        }
        return $manifest;
    }

    public function getManifests(string $path = "")
    {
        if(! defined("GESTION")) {
            $path = "../" . $path;
        }

        $path = realpath($path);
        if (!is_dir($path)) {
            return $this->manifest;
        }

        $manifests = [];

        if (!empty($this->pathManifest)) {
            foreach ($this->pathManifest as $file => $manifest) {
                if (strpos($file, $path) === 0) {
                    $manifests[] = $manifest;
                }
            }
        }
        return $manifests;
    }
}
