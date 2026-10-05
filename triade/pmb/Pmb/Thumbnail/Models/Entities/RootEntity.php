<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootEntity.php,v 1.3 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Models\Entities;

use Pmb\Thumbnail\Models\ThumbnailParserManifest;

abstract class RootEntity
{
    /**
     * chemin des pivots
     * @var string
     */
    const PATH_PIVOTS = "";
    
    /**
     * manifests associes aux pivots
     * @var array
     */
    protected $pivotsManifests = [];

    /**
     * 
     * @return array
     */
    public function getPivots(): array
    {
        if (empty(static::PATH_PIVOTS)) {
            return [];
        }
        $path = realpath(__DIR__ . "/../Pivots/Entities/" . static::PATH_PIVOTS);
        if (false === $path || ! is_dir($path)) {
            return [];
        }
        if (empty($this->pivotsManifests)) {
            $this->pivotsManifests = [];
            $dirs = glob($path . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                if ('CVS' === basename($dir)) {
                    continue;
                }

                $file = "{$dir}/manifest.xml";
                if (is_file($file)) {
                    $this->pivotsManifests[] = new ThumbnailParserManifest($file);
                }
            }
        }
        return $this->pivotsManifests;
    }
}