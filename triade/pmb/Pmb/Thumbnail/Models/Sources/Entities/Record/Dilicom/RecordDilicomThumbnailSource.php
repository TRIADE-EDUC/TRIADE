<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordDilicomThumbnailSource.php,v 1.4 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Dilicom;

use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;
use Pmb\Common\Library\ISBN\ISBN;

class RecordDilicomThumbnailSource extends RootThumbnailSource
{
    /**
     * url de base dilicom par defaut
     * @var string
     */
    const DEFAULT_DILICOM_IMG_URL_BASE = "http://images1.centprod.com";
    
    /**
     * url serveur dilicom par defaut
     * @var string
     */
    const DEFAULT_DILICOM_SERVER_URL = "https://distrimage.pmbservices.fr/image/";
    
    /**
     * taille par defaut
     * @var string
     */
    const DEFAULT_DILICOM_IMG_SIZE = "cover-medium.jpg";
    
    /**
     * tailes d'images chez dilicom
     * @var array
     */
    const DILICOM_IMG_SIZES = [
        'thumbnail' => "cover-thumb.jpg",
        'medium' => "cover-medium.jpg",
        'large' => "cover-large.jpg",
        'full' => "cover-full.jpg",
    ];
    
    /**
     * valeur par defaut
     * @var array
     */
    const DEFAULT_VALUES = [
        0=>[
            "client_gln" => "",
            "client_key" => "",
            "server_url" => RecordDilicomThumbnailSource::DEFAULT_DILICOM_SERVER_URL,
            "api_key" => "",
            "curl_timeout" => RootThumbnailSource::CURL_TIMEOUT,
        ]
    ];
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getParameters()
     */
    public function getParameters() : array
    {
        $value = ! empty($this->settings) ? $this->settings : RecordDilicomThumbnailSource::DEFAULT_VALUES;
        return $value;
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
        $ean13 = ISBN::toEAN13($code);
        
        //Si pas un EAN, on prend le code brut
        if(!$ean13) {
            $ean13 = $code;
        }
        
        $provider_glns = $this->getProviderGLNs($ean13);

        //Pour l'instant, on ne teste que le premier enregistrement
        if(empty($provider_glns[0]['gln13']))  {
            return '';
        }
        $provider_gln = $provider_glns[0]['gln13'];

        $encrypted_text = $this->encryptText($ean13, $provider_gln);
        $image_url = RecordDilicomThumbnailSource::DEFAULT_DILICOM_IMG_URL_BASE;
        $image_url.= '/'.(!empty($this->settings[0]['client_gln']) ? $this->settings[0]['client_gln'] : '');
        $image_url.= '/'.$encrypted_text;
        $image_url.= '-'.RecordDilicomThumbnailSource::DEFAULT_DILICOM_IMG_SIZE;
        $image = $this->loadImageWithCurl($image_url);
        return $image;
    }
    
    /**
     * Genere le TOKEN chiffre pour interrogation du serveur DILICOM
     * 
     * @param string $ean13 : EAN13
     * @param string $provider_gln : GLN
     * @return string
     */
    protected function encryptText(string $ean13, string $provider_gln) : string
    {
        $iv = $this->generateIV();
        $key = ($this->settings[0]['client_key']) ?? '';
        $unencrypted_text = 'DILICOM:'.$ean13.'-'.$provider_gln;
        $encrypted_text = openssl_encrypt($unencrypted_text,'BF-CBC',$key, OPENSSL_RAW_DATA ,$iv);
        
        $encrypted_text = base64_encode($encrypted_text);
        
        $encrypted_text = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            $encrypted_text);
        return $encrypted_text;
    }
    
    /**
     * Recuperation des glns distributeur
     * Interroge le serveur qui centralise les EAN pouvant être interrogés chez DILICOM
     * 
     * @param string $ean13
     * @return array
     */
    protected function getProviderGLNs(string $ean13)
    {
        $curl = new \Curl();
        $curl->timeout = $this->settings[0]["curl_timeout"] ?? RootThumbnailSource::CURL_TIMEOUT;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = 0;
        $curl->options['CURLOPT_ENCODING'] = '';

        $url = !empty($this->settings[0]['server_url']) ? $this->settings[0]['server_url'] :  RecordDilicomThumbnailSource::DEFAULT_DILICOM_SERVER_URL;
        $url.= $ean13;
        $curl->headers['Api-Key'] = $this->generateApiKey();
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
        $payload = json_decode($content->body, true);
        return ($payload['data']) ?? '';
        
    }
    
    /**
     * Generation d'un vecteur d'initialisation pour le chiffrement BLOWFISH
     * 
     * Fige à "00000000", pas de reponse du service DILICOM avec une autre valeur
     * 
     * @return string
     */
    protected function generateIV() : string
    {
        $iv = "00000000";
        return $iv;
    }
    
    /**
     * Generation cle d'api pour l'interrogation de serveur de mutualisation
     * 
     * @return static
     */
    protected function generateApiKey()
    {
        $api_key = ($this->settings[0]['api_key']) ?? '' ;
        return $api_key;
    }
}