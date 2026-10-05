<?php
namespace Pmb\Common\Library\Image;

use Pmb\Common\Helper\GlobalContext;

class CacheInfo
{
    /**
     * donnees stockees dans le json du repertoire cache
     * @var array
     */
    private $jsonData = [
        "size" => 0,
        "nb_files" => 0,
        "last_clean_at" => "0000-00-00 00:00:00",
        "clean_avg" => 0,
        "clean_nb" => 0,
    ];

    /**
     * nom du fichier info dans le cache
     * @var string
     */
    private $cacheFile = "";

    /**
     * nom du fichier pour verrouiller les modifications de cache
     * @var string
     */
    private $lockFile = "";

    /**
     * classe gerant le fichier d'info dans le repertoire de cache
     */
    public function __construct()
    {
        $this->cacheFile = GlobalContext::get("img_cache_folder").LOCATION."_cache_info.json";
        $this->lockFile = GlobalContext::get("img_cache_folder").LOCATION."_cache_info_locked.txt";
    }

    /**
     * recuperation des infos stockees dans le json
     */
    private function initProperties() : void
    {
        if (file_exists($this->cacheFile)) {
            $content = file_get_contents($this->cacheFile);
            $jsonContent = \encoding_normalize::json_decode($content, true);
            foreach ($jsonContent as $property => $value) {
                $this->jsonData[$property] = $value;
            }
        }
    }

    /**
     * enregistrement des infos stockees dans le json
     * @return number|boolean
     */
    private function saveProperties()
    {
        $jsonContent = \encoding_normalize::json_encode($this->jsonData);
        return file_put_contents($this->cacheFile, $jsonContent);
    }

    /**
     * mise a jour du nombre de fichiers en cache
     * @param string $filename
     */
    public function update(string $filename) : void
    {
        $this->initProperties();
        $this->jsonData["size"] += filesize(GlobalContext::get("img_cache_folder").$filename);
        $this->jsonData["nb_files"]++;
        $this->saveProperties();
    }

    /**
     * test sur la taille du cache + purge si necessaire
     */
    public function checkSize() : void
    {
        $cacheSize = intval(GlobalContext::get("img_cache_size")) ? intval(GlobalContext::get("img_cache_size")) : 100;
        $cacheCleanSize = intval(GlobalContext::get("img_cache_clean_size")) ? intval(GlobalContext::get("img_cache_clean_size")) : 20;
        $this->initProperties();
        if ($this->jsonData["size"] > ($cacheSize * 2**20)) {
            if (file_exists($this->lockFile)) {
                return;
            }
            file_put_contents($this->lockFile, 1);
            $dir = opendir(GlobalContext::get("img_cache_folder"));
            $nbDeletion = intval(($this->jsonData["nb_files"] * $cacheCleanSize)/100);
            $n = 0;
            while (false !== ($entry = readdir($dir)) && $n < $nbDeletion) {
                if (strpos($entry, LOCATION) === 0) {
                    $this->jsonData["size"] -= filesize(GlobalContext::get("img_cache_folder").$entry);
                    $this->jsonData["nb_files"]--;
                    CacheImage::delete($entry);
                    $n++;
                }
            }
            closedir($dir);
            $this->updateCleanInfo();
            $this->saveProperties();
            unlink($this->lockFile);
        }
    }

    /**
     * mise a jour des infos de nettoyage dans le cache
     */
    private function updateCleanInfo() : void
    {
        if ($this->jsonData["last_clean_at"] != "0000-00-00 00:00:00") {
            $lastClean = new \DateTime($this->jsonData["last_clean_at"]);
            $newClean = new \DateTime();
            $diff = $newClean->getTimestamp() - $lastClean->getTimestamp();
            $this->jsonData["clean_avg"] = (($this->jsonData["clean_avg"] * $this->jsonData["clean_nb"]) + $diff)/($this->jsonData["clean_nb"]+1);
        }
        $this->jsonData["clean_nb"]++;
        $this->jsonData["last_clean_at"] = date("Y-m-d H:i:s");
    }
}