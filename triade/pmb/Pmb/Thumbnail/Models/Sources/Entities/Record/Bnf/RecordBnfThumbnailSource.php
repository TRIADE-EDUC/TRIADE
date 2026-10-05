<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordBnfThumbnailSource.php,v 1.8 2024/04/08 08:13:54 tsamson Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Bnf;

use Pmb\Common\Library\ISBN\ISBN;
use Pmb\Thumbnail\Models\Sources\Entities\Common\Bnf\BnfThumbnailSource;
use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;

class RecordBnfThumbnailSource extends BnfThumbnailSource
{
    /**
     * url de l'api sru
     * @var string
     */
    const URL_API_SRU = 'https://catalogue.bnf.fr/api/SRU?version=1.2&operation=searchRetrieve&query=bib.ean%20all%20%22!!ean!!%22&recordSchema=dublincore&maximumRecords=1&startRecord=1';
    
    /**
     * url de l'api couverture
     * @var string
     */
    const URL_API_COUVERTURE = 'https://catalogue.bnf.fr/couverture?&appName=NE&idArk=!!ark!!&couverture=1&largeur=500&hauteur=500';

    /**
     * valeur par defaut
     * @var array
     */
    const DEFAULT_VALUES = [
        "using_default_img" => 0,
        "curl_timeout" => RootThumbnailSource::CURL_TIMEOUT,
    ];
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getParameters()
     */
    public function getParameters() : array
    {
        $value = ! empty($this->settings) ? $this->settings : RecordBnfThumbnailSource::DEFAULT_VALUES;
        return $value;
    }

    /**
     * Dérivation de setParameters pour ne plus manipuler un tableau
     *
     * {@inheritdoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::setParameters()
     */
    public function setParameters(array $settings) : void
    {
        $this->settings = $settings[0];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getImage()
     */
    public function getImage(int $object_id) : string
    {
        if (! $object_id) {
            return '';
        }
        $q = "select code from notices where notice_id = " . $object_id . " limit 1";
        $r = pmb_mysql_query($q);
        if (! pmb_mysql_num_rows($r)) {
            return '';
        }
        $code = trim(pmb_mysql_result($r, 0, 0));
        if (! $code) {
            return '';
        }
        $ean = ISBN::toEAN13($code);
        if ('' == $ean) {
            return "";
        }

        $arkIdentifier = $this->getArkIdentifier($ean);
        if (empty($arkIdentifier)) {
            return "";
        }

        $using_default_img = $this->settings['using_default_img'] ?? 0;

        $image = '';
        $image_url = str_replace("!!ark!!", $arkIdentifier, RecordBnfThumbnailSource::URL_API_COUVERTURE);
        if ($image_url) {
            $image = $this->loadImageWithCurl($image_url);
            if (empty($using_default_img) && md5($image) === BnfThumbnailSource::HASH_DEFAULT_IMG) {
                return "";
            }
        }
        return $image;
    }

    /**
     * recuperation de l'identifiant ark via l'api sru en fonction de l'ean
     * @param string $ean13
     * @return string
     */
    protected function getArkIdentifier(string $ean13) : string
    {
        $curl = new \Curl();
        $curl->timeout = $this->settings["curl_timeout"] ?? RootThumbnailSource::CURL_TIMEOUT;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = 0;
        $curl->options['CURLOPT_ENCODING'] = '';

        $url = str_replace("!!ean!!", $ean13, RecordBnfThumbnailSource::URL_API_SRU);

        $content = $curl->get($url);
        if ($content->headers['Status-Code'] != 200) {
            return "";
        }
        if (empty($content->body)) {
            return "";
        }
        $dom = new \DOMDocument();
        $dom->loadXML($content->body);
        $arkNode = $dom->getElementsByTagNameNS("http://www.loc.gov/zing/srw/", "recordIdentifier");
        return $arkNode[0]->textContent ?? "";
    }
}

