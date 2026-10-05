<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordExternallinksThumbnailSource.php,v 1.8 2024/04/08 14:06:25 tsamson Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Externallinks;

use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;
use Pmb\Common\Library\ISBN\ISBN;
use Pmb\Common\Helper\GlobalContext;

class RecordExternallinksThumbnailSource extends RootThumbnailSource
{
    /**
     * url par defaut
     * @var array
     */
    const DEFAULT_IMG_URL = [
        
        "links" =>[
            [
                "name" => "amazon",
                "url" => "https://images.amazon.com/images/P/!!isbn!!.08.MZZZZZZZ.jpg"
            ],
            [
                "name" => "abebooks",
                "url" => "https://pictures.abebooks.com/isbn/!!isbn!!-fr-300.jpg"
            ],
            [
                "name" => "gamannecy",
                "url" => "https://www.gamannecy.com/upload/albums/201612/!!isbn!!_thumb.jpg"
            ]
        ],
        "curl_timeout" => RootThumbnailSource::CURL_TIMEOUT,
    ];
    
    /**
     * copyright utilise pour le watermark
     * @var string
     */
    protected $copyright = "";

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getImage()
     */
    public function getImage(int $object_id) : string
    {
        if(!$object_id) {
            return '';
        }
        $links = $this->getLinksList($object_id);
        $image = "";
        if (!empty($links)) {
            foreach ($links as $name => $urls) {
                foreach ($urls as $url) {
                    if (empty($url)) {
                        continue;
                    }
                    $image = $this->loadImageWithCurl($url);
                    if (!empty($image) && !$this->imgIsWhitePixel($image)) {
                        $this->copyright = $name;
                        return $image;
                    }
                }
            }
        }
        return "";
    }
    
    /**
     * l'image est un pixel blanc
     * @param string $image
     * @return bool
     */
    private function imgIsWhitePixel(string $image) : bool
    {
        if (file_exists(GlobalContext::get("base_path").'/images/white_pixel.jpg') && file_get_contents(GlobalContext::get("base_path").'/images/white_pixel.jpg') == $image) {
            return true;
        }
        if (file_exists(GlobalContext::get("base_path").'/images/white_pixel_2x2.jpg') && file_get_contents(GlobalContext::get("base_path").'/images/white_pixel_2x2.jpg') == $image) {
            return true;
        }
      return false;
    }
    
    /**
     * liste des liens externes a tester
     * @param int $object_id
     * @return array
     */
    private function getLinksList(int $object_id) : array
    {
        $q = "SELECT code FROM notices WHERE notice_id = ".$object_id." LIMIT 1";
        $r = pmb_mysql_query($q);
        if(!pmb_mysql_num_rows($r)) {
            return [];
        }
        $record_code = trim(pmb_mysql_result($r, 0, 0));
        if(!$record_code) {
            return [];
        }
        //numero a tester
        $codes = [];
        if (ISBN::isEAN($record_code)) {
            if (ISBN::isISBN($record_code)) {
                if (ISBN::isISBN10($record_code)) {
                    $codes[] = ISBN::ISBN10ToEAN13($record_code);
                } else {
                    $codes[] = ISBN::toISBN10($record_code);
                }
            }
        }
        $codes[] = str_replace("-","",$record_code);
        //$codes[] = $record_code; 
        $links = [];
        if (empty($this->settings)) {
            $this->settings = $this->getParameters();
        }
        //pour la retrocompatibilite
        $setLinks = $this->settings["links"] ?? $this->settings;
        foreach ($setLinks as $link) {
            if (empty($link["name"]) || empty($link["url"])) {
                continue;
            }
            foreach ($codes as $code) {
                if (!isset($links[$link["name"]])) {
                    $links[$link["name"]] = [];
                }
                $links[$link["name"]][] = str_replace("!!isbn!!", $code, $link["url"]);
            }
        }
        return $links;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getWatermark()
     */
    public function getWatermark() : string
    {
        if (!empty($this->copyright)) {
            return "Copyright ".$this->copyright;
        }
        return "";
    }
    
    
    /**
     *
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getParameters()
     */
    public function getParameters() : array
    {
        $value = ! empty($this->settings) ? $this->settings : RecordExternallinksThumbnailSource::DEFAULT_IMG_URL;
        //retrocompatibilite
        if (!isset($value["links"])) {
            $value = [
                "links" => $value
            ]; 
        }
        return $value;
    }
    
    /**
     * Dérivation de setParameters pour ne plus manipuler un tableau
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::setParameters()
     */
    public function setParameters(array $settings) : void
    {
        $this->settings = $settings[0];
    }
}

