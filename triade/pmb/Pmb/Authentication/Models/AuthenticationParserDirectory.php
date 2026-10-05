<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationParserDirectory.php,v 1.2 2023/06/23 12:38:09 dbellamy Exp $

namespace Pmb\Authentication\Models;

use Pmb\Common\Library\Parser\ParserDirectory;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationParserDirectory extends ParserDirectory
{

    protected $baseDir = __DIR__;

    protected $parserManifest = "\Pmb\Authentication\Models\AuthenticationParserManifest";

    /**
     *
     * @param string $path
     * @param array $ignoredManifest
     * @return AuthenticationParserManifest[]|array
     */
    protected function loadManifests(string $path, array $ignoreManifest = [])
    {
        $path = realpath($path);
        $ignoreManifest = array_map('realpath', $ignoreManifest);

        if (! is_dir($path)) {
            return [];
        }

        $manifest = [];
        $dirs = glob($path . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            if (strpos($dir, 'CVS') !== false) {
                continue;
            }
            $file = "{$dir}/manifest.xml";
            if (is_file($file) && ! in_array($file, $ignoreManifest)) {
                if (empty($this->pathManifest[$file])) {
                    $this->pathManifest[$file] = new $this->parserManifest($file);
                }
                $index = $this->pathManifest[$file]->name;
                $manifest[$index] = $this->pathManifest[$file];
            }
            $manifest = array_merge($manifest, $this->loadManifests($dir, $ignoreManifest));
        }
        return $manifest;
    }
}

