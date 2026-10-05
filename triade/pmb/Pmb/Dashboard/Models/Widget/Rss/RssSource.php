<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RssSource.php,v 1.1 2024/02/20 08:52:57 jparis Exp $

namespace Pmb\Dashboard\Models\Widget\Rss;
use Pmb\Common\Helper\RSS;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class RssSource
{
    public function getData($params = null)
    {
        if(!isset($params->link) && !isset($params->nbItems) && !isset($params->timeout)) {
            return [];
        }

        $rss = new RSS($params->link, $params->nbItems);
        $rss->setTimeout($params->timeout);

        return $rss->parseContent();
    }
}

