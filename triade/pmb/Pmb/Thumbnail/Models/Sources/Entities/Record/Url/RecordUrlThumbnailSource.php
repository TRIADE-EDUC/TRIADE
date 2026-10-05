<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordUrlThumbnailSource.php,v 1.10.2.1 2024/10/02 13:21:54 dgoron Exp $

namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Url;

use Pmb\Thumbnail\Models\Sources\Entities\Common\Url\UrlThumbnailSource;
use Pmb\Common\Helper\GlobalContext;

class RecordUrlThumbnailSource extends UrlThumbnailSource
{
    /**
     * 
     * @var string
     */
    const BASE64_STR = "base64,";
    
    protected function getThumbnailUrlAnalysis(int $object_id) : string
    {
        $query = "SELECT bulletin_notice, num_notice FROM bulletins JOIN analysis ON analysis_bulletin = bulletin_id WHERE analysis_notice = {$object_id}";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            // Héritage du lien de la vignette de la notice bulletin
            if (GlobalContext::get("pmb_bulletin_thumbnail_url_article") && !empty($row->num_notice)) {
                return GlobalContext::get("opac_url_base")."thumbnail.php?type=1&id=".$row->num_notice;
            }
            // Héritage du lien de la vignette de la notice chapeau
            if (GlobalContext::get("pmb_serial_thumbnail_url_article")) {
                return GlobalContext::get("opac_url_base")."thumbnail.php?type=1&id=".$row->bulletin_notice;
            }
        }
        return '';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getImage()
     */
    public function getImage(int $object_id) : string
    {
        if (!$object_id) {
            return '';
        }
        
        $query = "SELECT thumbnail_url, niveau_biblio FROM notices WHERE notice_id = {$object_id}";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $thumbnail_url = trim(pmb_mysql_result($result, 0, 'thumbnail_url'));
            $niveau_biblio = pmb_mysql_result($result, 0, 'niveau_biblio');
            if (empty($thumbnail_url) && ($niveau_biblio == 'a') && (GlobalContext::get("pmb_bulletin_thumbnail_url_article") || GlobalContext::get("pmb_serial_thumbnail_url_article"))) {
                $thumbnail_url = $this->getThumbnailUrlAnalysis($object_id);
            }
            if (!empty($thumbnail_url)) {
                //image stockee en base64 en base
                $ind = strpos($thumbnail_url, self::BASE64_STR);
                if (!empty($ind)) {
                    return base64_decode(substr($thumbnail_url, $ind + strlen(self::BASE64_STR)));
                }
                $image = $this->loadImageWithCurl($thumbnail_url);
                if (!empty($image)) {
                    return $image;
                }
                //cas particulier si l'url opac est inaccessible (ex: mypmb)
                if (strpos($thumbnail_url, GlobalContext::get("opac_url_base")) !==  false) {
                    $thumbnail_url = str_replace(GlobalContext::get("opac_url_base"), GlobalContext::get("pmb_url_internal")."opac_css/", $thumbnail_url);
                    $image = $this->loadImageWithCurl($thumbnail_url);
                    if (!empty($image)) {
                        return $image;
                    }
                }
            }
        }
        
        $rep_id = GlobalContext::get("pmb_notice_img_folder_id");
        $query = "SELECT repertoire_path FROM upload_repertoire WHERE repertoire_id ='".$rep_id."'";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $row = pmb_mysql_fetch_array($result,PMB_MYSQL_NUM);
            $thumbnail_path = $row[0]."img_".$object_id;
            if (file_exists($thumbnail_path)) {
                $content = file_get_contents($thumbnail_path);
                if (!empty($content)) {
                    return $content;
                }
            }
        }
        return '';
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