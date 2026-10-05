<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailParserDirectory.php,v 1.4 2022/12/09 11:49:12 qvarin Exp $
namespace Pmb\Thumbnail\Models;

use Pmb\Common\Library\Parser\ParserDirectory;

class ThumbnailParserDirectory extends ParserDirectory
{

    protected $baseDir = __DIR__;

    protected $parserManifest = "\Pmb\Thumbnail\Models\ThumbnailParserManifest";

    private $entitiesList = [];

    public function getEntitiesList()
    {
        if (empty($this->entitiesList)) {
            $path = realpath(__DIR__);

            if (! is_dir($path)) {
                return [];
            }

            $dirs = glob($path . '/Entities/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                if ('CVS' === basename($dir)) {
                    continue;
                }
                
                $file = "{$dir}/manifest.xml";
                if (is_file($file)) {
                    $this->entitiesList[] = new ThumbnailParserManifest($file);
                }
            }
        }
        return $this->entitiesList;
    }
}

