<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: NoImageThumbnailSource.php,v 1.12.2.1 2024/08/29 10:19:57 tsamson Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Noimage;

use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;
use Pmb\Common\Helper\GlobalContext;

class NoImageThumbnailSource extends RootThumbnailSource
{
    /**
     * correspondance par defaut
     * @var integer
     */
    public const DEFAULT_MATCH = 0;
    
    /**
     * correspondance sur le type de document et le niveau bibliographique
     * @var integer
     */
    public const TYPEDOC_NIVBIBLIO_MATCH = 1;
    
    /**
     * correspondance sur le niveau bibliographique uniquement
     * @var integer
     */
    public const NIVBIBLIO_MATCH = 2;
    
    /**
     * valeurs par defaut
     * @var array
     */
    public const DEFAULT_VALUE = [
        [
            "typedoc" => "",
            "nivbiblio" => "",
            "value" => "no_image.png"
        ]
    ];

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getParameters()
     */
    public function getParameters() : array
    {
        $typedoc = new \marc_list(\marc_list::TYPE_DOCTYPE);
        $nivbiblio = new \marc_list(\marc_list::TYPE_NIVEAU_BIBLIO);

        $value = ! empty($this->settings) ? $this->settings : NoImageThumbnailSource::DEFAULT_VALUE;
        return [
            "typedoc" => $typedoc->table ?? [],
            "nivbiblio" => $nivbiblio->table ?? [],
            "values" => $value
        ];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getImage()
     */
    public function getImage(int $objectId) : string
    {
        if (!$objectId) {
            return '';
        }
        
        $q = "SELECT niveau_biblio, typdoc FROM notices WHERE notice_id = ".$objectId." limit 1";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $record = pmb_mysql_fetch_assoc($r);
            if (!empty($this->settings)) {
                // Matches typedoc && nivbiblio
                $pivot = $this->getMatchPivot($record, NoImageThumbnailSource::TYPEDOC_NIVBIBLIO_MATCH);
                if (empty($pivot)) {
                    // Matches empty typedoc && nivbiblio
                    $pivot = $this->getMatchPivot($record, NoImageThumbnailSource::NIVBIBLIO_MATCH);
                }
                if (empty($pivot)) {
                    // Matches empty typedoc && empty nivbiblio
                    $pivot = $this->getMatchPivot($record, NoImageThumbnailSource::DEFAULT_MATCH);
                    if ($pivot["value"] == NoImageThumbnailSource::DEFAULT_VALUE[0]["value"]) {
                        $pivot["value_url"] = $this->getDefaultNoImageUrl($record["niveau_biblio"], $record["typdoc"]);
                    }
                }
                if (!empty($pivot)) {
                    $urlIcon = !empty($pivot["value_url"]) ? $pivot["value_url"] : get_url_icon($pivot["value"], true);
                    $image = $this->loadImageWithCurl($urlIcon);
                    if (!empty($image)) {
                        return $image;
                    }
                    //cas particulier si l'url opac est inaccessible (ex: mypmb)
                    if (strpos($urlIcon, GlobalContext::get("opac_url_base")) !==  false) {
                        $urlIcon = str_replace(GlobalContext::get("opac_url_base"), GlobalContext::get("pmb_url_internal")."opac_css/", $urlIcon);
                        $image = $this->loadImageWithCurl($urlIcon);
                        if (!empty($image)) {
                            return $image;
                        }
                    }
                }
            } else {
                $noImageUrl = $this->getDefaultNoImageUrl($record["niveau_biblio"], $record["typdoc"]);
                $image = $this->loadImageWithCurl($noImageUrl);
                if (!empty($image)) {
                    return $image;
                }
            }
        }
        return "";
    }
    
    /**
     * recuperation du pivot correspondant
     * @param array $record
     * @param int $match
     * @return array|NULL
     */
    protected function getMatchPivot(array $record, int $match = NoImageThumbnailSource::DEFAULT_MATCH)
    {
        switch ($match) {
            case NoImageThumbnailSource::DEFAULT_MATCH:
                foreach ($this->settings as $pivot) {
                    if (empty($pivot['typedoc']) && empty($pivot['nivbiblio'])) {
                        return $pivot;
                    }
                }
                break;

            case NoImageThumbnailSource::NIVBIBLIO_MATCH:
                foreach ($this->settings as $pivot) {
                    if (empty($pivot['typedoc']) && $pivot['nivbiblio'] == $record["niveau_biblio"]) {
                        return $pivot;
                    }
                }
                break;

            case NoImageThumbnailSource::TYPEDOC_NIVBIBLIO_MATCH:
                foreach ($this->settings as $pivot) {
                    if ($pivot['typedoc'] == $record["typdoc"] && $pivot['nivbiblio'] == $record["niveau_biblio"]) {
                        return $pivot;
                    }
                }
                break;
                
            default:
                return null;
        }
        return null;
    }
    
    /**
     * recuperation de la no image par defaut
     * @param string $niveau_biblio
     * @param string $typdoc
     * @return string
     */
    private function getDefaultNoImageUrl(string $niveau_biblio, string $typdoc) : string
    {
        $picture_url = get_url_icon("no_image_".$niveau_biblio.$typdoc.".jpg", 1);
        if($picture_url) {
            return $picture_url;
        }
        $picture_url = get_url_icon("no_image_".$niveau_biblio.".jpg", 1);
        if($picture_url) {
            return $picture_url;
        }
        return "";
    }
}