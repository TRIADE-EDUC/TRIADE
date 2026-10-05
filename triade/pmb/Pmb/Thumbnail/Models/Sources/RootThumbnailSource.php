<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootThumbnailSource.php,v 1.18 2024/03/13 10:43:13 qvarin Exp $
namespace Pmb\Thumbnail\Models\Sources;

use Pmb\Common\Models\Model;
use Pmb\Thumbnail\Orm\SourcesORM;
use Pmb\Common\Helper\ParserMessage;

class RootThumbnailSource extends Model
{
    /**
     * taille minimale de l'image
     * @var integer
     */
    const IMG_MIN_SIZE = 100;

    /**
     * taille de l'image
     * @var integer
     */
    const IMG_MAX_SIZE = 1024*1024;

    /**
     * temps d'attente en seconde avant expiration du curl
     * @var integer
     */
    const CURL_TIMEOUT = 5;

    use ParserMessage;

    /**
     * orm utilise pour la manipulation de la base de donnes
     * @var string
     */
    protected $ormName = "Pmb\\Thumbnail\\Orm\\SourcesORM";

    /**
     * namespace de la classe
     * @var string
     */
    protected $class = "";

    /**
     * paramterage de la source
     * @var array
     */
    protected $settings = [];

    /**
     * source activee ou non
     * @var integer
     */
    protected $active = 1;

    /**
     * tableau des headers
     * @var array
     */
    protected $imageHeaders = [];

    /**
     * source autorisant le cache
     * @var boolean
     */
    protected $allowedCache = true;

    private function __construct(int $id = 0)
    {
        parent::__construct($id);
    }

    /**
     *
     * @return \Pmb\Thumbnail\Models\Sources\RootThumbnailSource
     */
    public static function getInstance() : RootThumbnailSource
    {
        $resuts = SourcesORM::find("class", static::class);
        $id = ! empty($resuts) ? $resuts[0]->id : 0;
        return new static($id);
    }

    /**
     * recuperation des parametres
     * @return array
     */
    public function getParameters() : array
    {
        return $this->settings;
    }

    /**
     * definition des parametres
     * @param array $settings
     */
    public function setParameters(array $settings) : void
    {
        $this->settings = $settings;
    }

    /**
     * retourne le contenu de l'image
     * @param int $object_id
     * @return string
     */
    public function getImage(int $object_id) : string
    {
        return "";
    }

    /**
     * sauvegarde de la source
     * @return bool
     */
    public function save() : bool
    {
        $orm = new $this->ormName($this->id);
        $orm->class = static::class;
        $orm->settings = \encoding_normalize::json_encode($this->settings);
        $orm->active = true;
        $orm->save();

        $this->id = $orm->id;
        return ! empty($this->id) && $this->id != 0;
    }

    /**
     * telechargement de l'image distante avec curl
     * @param string $image_url
     * @return string
     */
    protected function loadImageWithCurl(string $image_url) : string
    {
        $curl = new \Curl();
        $curl->limit = RootThumbnailSource::IMG_MAX_SIZE;
        $curl->timeout = $this->settings["curl_timeout"] ?? RootThumbnailSource::CURL_TIMEOUT;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] =0;
        $curl->options['CURLOPT_ENCODING'] = '';

        $content = $curl->get($image_url);
        if (
            !isset($content->headers['Status-Code']) ||
            $content->headers['Status-Code'] != 200
        ) {
            return '';
        }
        if(empty($content->body)) {
            return '';
        }
        $image = $content->body;
        if( empty($content->headers['Content-Length']) && strlen($image) ) {
            $content->headers['Content-Length'] = strlen($image);
        }
        if( ($content->headers['Content-Length'] > RootThumbnailSource::IMG_MAX_SIZE)
            || ($content->headers['Content-Length'] < RootThumbnailSource::IMG_MIN_SIZE)
            )  {
            return '';
        }
        $this->imageHeaders = $content->headers;
        return $image;
    }

    /**
     * source active
     * @return bool
     */
    public function isActive() : bool
    {
        if (!empty($this->active)) {
            return true;
        }
        return false;
    }

    /**
     * recuperation du watermark
     * @return string
     */
    public function getWatermark() : string
    {
        return $this->settings["watermark"] ?? "";
    }

    /**
     * recuperation des headers
     * @return array
     */
    public function getImageHeaders() : array
    {
    	return $this->imageHeaders;
    }

    /**
     * source ayant autorise le cache
     * @return bool
     */
    public function hasAllowedCache() : bool
    {
        return $this->allowedCache;
    }
}