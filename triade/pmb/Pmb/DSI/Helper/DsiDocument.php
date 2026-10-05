<?php

namespace Pmb\DSI\Helper;

use Pmb\Common\Helper\HTML;
use Pmb\DSI\Helper\SubscriberHelper;

class DsiDocument extends \DOMDocument
{

    /**
     *
     * @param string $version
     * @param string $encoding
     */
    public function __construct(string $version = "1.0", string $encoding = "")
    {
        global $charset;
        if (empty($encoding)) {
            $encoding = $charset;
        }
        parent::__construct($version, $encoding);
    }

    /**
     *
     * {@inheritdoc}
     * @see \DOMDocument::loadHTML()
     */
    public function loadHTML($source, $options = LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED): bool
    {
        if ($this->encoding == "utf-8") {
            $source = '<?xml encoding="UTF-8">' . $source;
        }

        if (! @parent::loadHTML(HTML::cleanHTML($source, $this->encoding), $options)) {
            throw new \Exception("HTML could not be loaded");
        }

        return true;
    }

    public function formatHTML()
    {
        global $dsi_connexion_auto;

        if ($dsi_connexion_auto) {
            $this->autoConnexionLink();
        }
    }

    protected function autoConnexionLink()
    {
        global $opac_url_base;

        $domNodeList = $this->getElementsByTagName("a");
        for ($i = 0; $i < $domNodeList->length; $i++) {
            $domNode = $domNodeList->item($i);
            if ($domNode->hasAttribute('href')) {
                $link = $domNode->getAttribute('href');
                if (strpos($link, $opac_url_base) === 0) {

                    $query = parse_url($link, PHP_URL_QUERY);
                    if (!empty($query)) {
                        $link .= "&";
                    } else {
                        $link .= "?";
                    }

                    $link .= implode("&", SubscriberHelper::HTTP_QUERY_AUTO_CONNEXION);
                    $domNode->setAttribute("href", $link);
                }
            }
        }
    }
}