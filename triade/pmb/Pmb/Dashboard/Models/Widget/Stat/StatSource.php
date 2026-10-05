<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: StatSource.php,v 1.8 2024/02/29 16:29:12 jparis Exp $

namespace Pmb\Dashboard\Models\Widget\Stat;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class StatSource 
{
    /* Repertoire des sources */
    public const NS_DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR . 'Sources';
 
    /* Prefixe namespace */
	public const NS_PREFIX = 'Pmb\\Dashboard\\Models\\Widget\\Stat\\Sources\\';

    public function getData($params = null)
    {
        $datasets = [];

        if(!isset($params->methods)) {
            return $datasets;
        }

        foreach($params->methods as $key => $method) {
            $nameModule = ucfirst($method->module);
            $namespace = StatSource::NS_PREFIX . $nameModule . '\\' . $nameModule . 'Source';

            if(!class_exists($namespace)) {
                continue;
            }

            $module = new $namespace();

            $datasets[] = $module->getData($method);
        }

        return $datasets;
    }
    
    public function getConfiguration()
    {
        $modules = [];
        $sources = $this->getNamespaces();

        foreach ($sources as $key => $source) {
            if(class_exists($source)) {
                $module = new $source();

                $modules[$key] = $module->getConfiguration();
                $modules[$key]['source'] = $source;
            }
        }

        return [
            "modules" => $modules
        ];
    }

    /**
     * Retourne un tableau des namespaces des formats d'affichage
     *
     * @return string[]
     */
    protected function getNamespaces()
    {
        $namespaces = [];

        $dirs = glob(StatSource::NS_DIRECTORY . DIRECTORY_SEPARATOR .'*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $dir = basename($dir);
            if ('CVS' == $dir || '.' === $dir || '..' == $dir ) {
                continue;
            }
            $filename = $dir . "Source.php";
            if ( is_file(StatSource::NS_DIRECTORY . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR .$filename) ) {
                $namespaces[strtolower($dir)] = StatSource::NS_PREFIX .  $dir . '\\'. $dir. 'Source';
            }
        }

        return $namespaces;
    }
}

