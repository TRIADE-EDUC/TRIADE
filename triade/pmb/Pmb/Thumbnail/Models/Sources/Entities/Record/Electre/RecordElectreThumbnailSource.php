<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordElectreThumbnailSource.php,v 1.8 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Electre;

use Pmb\Thumbnail\Models\Sources\Entities\Common\Electre\ElectreThumbnailSource;
use Pmb\Thumbnail\Models\Sources\Entities\Common\Electre\ElectreAPIClient;
use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;

class RecordElectreThumbnailSource extends ElectreThumbnailSource
{
    /**
     * valeurs par defaut
     * @var array
     */
    const DEFAULT_VALUES = [
            "user" => "",
            "client_id" => "",
            "client_secret" => "",
            "access_token" => "",
            "curl_timeout" => RootThumbnailSource::CURL_TIMEOUT,
    ];
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getParameters()
     */
    public function getParameters() : array
    {
        $value = ! empty($this->settings) ? $this->settings : RecordElectreThumbnailSource::DEFAULT_VALUES;
        //On n'envoie pas le token d'accès à la vue
        unset($value['access_token']);
        return $value;
    }
    
    /**
     * Dérivation de setParameters afin de prendre en compte le token d'acces
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::setParameters()
     */
    public function setParameters(array $settings) : void
    {
        //Recuperation du token d'acces depuis les parametres
        $access_token = !empty($this->settings['access_token']) ? $this->settings['access_token'] : '';
        $new_settings = $settings[0];
        $new_settings->access_token = $access_token;
        $this->settings = $new_settings;
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
        
        $client_id = (!empty($this->settings['client_id']) ? $this->settings['client_id']: '');
        $client_secret = (!empty($this->settings['client_secret']) ? $this->settings['client_secret']: '');
        $user = (!empty($this->settings['user']) ? $this->settings['user']: '');
        
        $client = new  ElectreAPIClient(
            $client_id,
            $client_secret,
            $user
        );
        //Recuperation du token d'acces depuis les parametres
        $access_token = !empty($this->settings['access_token']) ? $this->settings['access_token'] : '';
        $client->setAccessToken($access_token);
        
        $images = $client->getImagesFromEan($code);
        
        //Enregistrement du token d'acces dans les parametres
        $this->settings['access_token'] = $client->getAccessToken();
        $this->save();
        
        if(!count($images)) {
            return '';
        }
        
        $image = '';
        foreach($images as $image_url) {
            $image = $this->loadImageWithCurl($image_url);
            if(!empty($image)) {
                break;
            }
        }
        return $image;
    }
}