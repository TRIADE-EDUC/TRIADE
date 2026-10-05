<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalChannel.php,v 1.3 2023/11/28 11:29:16 rtigero Exp $

namespace Pmb\DSI\Models\Channel\Portal;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\Diffusion;

class PortalChannel extends RootChannel
{
    public function send($subscriberList, $renderedView, $diffusion = null)
    {
        echo $renderedView;
    }

    public function getPortalDiffusionLink($idDiffusion = 0, $idHistory = 0)
    {
        $url = "";
        if($idDiffusion) {
			if(! $idHistory) {
				$diffusion = new Diffusion($idDiffusion);
				$lastHistory = $diffusion->getLastHistorySent("Pmb\\DSI\\Models\\Channel\\Portal\\PortalChannel");
				$idHistory = $lastHistory->id;
			}
			$url = GlobalContext::get("opac_url_base");
			$url .= "index.php?lvl=dsi&diff=$idDiffusion";
			if($idHistory) {
				$url .= "&hist=$idHistory";
			}
		}
        return $url;
    }
}

