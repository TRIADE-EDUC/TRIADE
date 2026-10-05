<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordOrbThumbnailSource.php,v 1.4 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Orb;

use Pmb\Thumbnail\Models\Sources\Entities\Common\Orb\OrbThumbnailSource;
use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;
use Pmb\Common\Library\ISBN\ISBN;

class RecordOrbThumbnailSource extends OrbThumbnailSource
{
    /**
     * url de l'api
     * @var string
     */
    const API_URL = "https://api.base-orb.fr/v1/";
    
    /**
     * valeurs par defaut
     * @var array
     */
    const DEFAULT_VALUES = [
            "user" => "",
            "api_key" => "",
            "curl_timeout" => RootThumbnailSource::CURL_TIMEOUT,
    ];
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getParameters()
     */
    public function getParameters() : array
    {
        $value = ! empty($this->settings) ? $this->settings : RecordOrbThumbnailSource::DEFAULT_VALUES;
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
        $q = "select code from notices where notice_id = ".$object_id." limit 1";
        $r = pmb_mysql_query($q);
        if(!pmb_mysql_num_rows($r)) {
            return '';
        }
        $code = trim(pmb_mysql_result($r, 0, 0));
        if(!$code) {
            return '';
            
        }        
        $ean = ISBN::toEAN13($code);
        if('' == $ean) {
            return "";
        }
        
        $apiContent = $this->getApiContent($ean);
        
        if (!isset($apiContent['data']) && !is_array($apiContent["data"])) {
            return "";
        }
        
        $image = '';
        
        $image_url = $apiContent['data'][0]["images"]["front"]["thumbnail"]["src"] ?? "";
        if (empty($image_url)) {
            $image_url = $apiContent['data'][0]["images"]["front"]["original"]["src"] ?? "";
        }
        if ($image_url) {
            $image = $this->loadImageWithCurl($image_url);
        }
        return $image;
    }
    
    /**
     * recuperation du contenu de l'api
     * @param string $ean13
     * @return array|mixed
     */
    protected function getApiContent(string $ean13)
    {
        $curl = new \Curl();
        $curl->timeout = $this->settings["curl_timeout"] ?? RootThumbnailSource::CURL_TIMEOUT;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = 0;
        $curl->options['CURLOPT_ENCODING'] = '';
        
        $url = RecordOrbThumbnailSource::API_URL."products?eans=".$ean13."&sort=ean_asc";
        
        $user = (!empty($this->settings['user']) ? $this->settings['user']: '');
        $api_key = (!empty($this->settings['api_key']) ? $this->settings['api_key']: '');
        
        $curl->headers['Authorization'] = "Basic ".base64_encode($user.":".$api_key);
        $content = $curl->get($url);
        
        if($content->headers['Status-Code'] != 200) {
            return [];
        }
        if(empty($content->body)) {
            return [];
        }
        if('application/json' != $content->headers['Content-Type']) {
            return [];
        }
        return json_decode($content->body, true);
    }
}