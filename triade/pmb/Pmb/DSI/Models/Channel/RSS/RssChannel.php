<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RssChannel.php,v 1.3 2023/06/29 09:49:48 qvarin Exp $

namespace Pmb\DSI\Models\Channel\RSS;

use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\View\RssView\RssView;

class RssChannel extends RootChannel
{
    public function send($subscriberList, $renderedView, $diffusion = null)
    {
        if (defined('GESTION')) {
            return false;
        } else {
            global $charset;
            @header("Content-type: text/xml; charset=".$charset);

            $context = json_decode($renderedView);
            $view = new RssView();
            $view->rebuildContext($context);

            echo trim($view->buildXML($context->items));
        }
    }
}
