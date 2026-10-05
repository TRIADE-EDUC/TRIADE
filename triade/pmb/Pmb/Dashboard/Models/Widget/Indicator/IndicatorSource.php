<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IndicatorSource.php,v 1.1 2024/02/26 14:28:54 dbellamy Exp $

namespace Pmb\Dashboard\Models\Widget\Indicator;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class IndicatorSource
{
    /* Repertoire des sources */
    public const NS_DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR . 'Sources';

    /* Prefixe namespace */
	public const NS_PREFIX = 'Pmb\\Dashboard\\Models\\Widget\\Indicator\\Sources\\';

    public function getData($params = null)
    {
        if(!isset($params->module)) {
            return [];
        }

        $configuration = $this->getConfiguration();
        $module = $configuration["modules"][$params->module] ?? null;

        if(!class_exists($module)) {
            return [];
        }

        $source = new $module();

        return $source->getData($params);
    }

    public function getConfiguration()
    {
        return [
            "modules" => $this->getNamespaces()
        ];
    }

    /**
     * Retourne un tableau des namespaces des modules
     *
     * @return string[]
     */
    protected function getNamespaces()
    {
        $namespaces = [];

        $dirs = glob(IndicatorSource::NS_DIRECTORY . DIRECTORY_SEPARATOR .'*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $dir = basename($dir);
            if ('CVS' == $dir || '.' === $dir || '..' == $dir ) {
                continue;
            }
            $filename = $dir . "Source.php";
            if ( is_file(IndicatorSource::NS_DIRECTORY . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR .$filename) ) {
                $namespaces[strtolower($dir)] = IndicatorSource::NS_PREFIX .  $dir . '\\'. $dir. 'Source';
            }
        }

        return $namespaces;
    }
}

