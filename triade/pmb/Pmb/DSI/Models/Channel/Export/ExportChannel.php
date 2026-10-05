<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExportChannel.php,v 1.1 2023/06/19 13:31:07 qvarin Exp $
namespace Pmb\DSI\Models\Channel\Export;

use Pmb\DSI\Models\Channel\RootChannel;

class ExportChannel extends RootChannel
{

    public function send($subscriberList, $renderedView, $diffusion = null)
    {
        @header("Content-type: " . $renderedView['minetype']);
        @header('Content-Disposition: attachment; filename="' . $renderedView['nomfichier'] . '"');
        echo $renderedView['contenu'];
    }
}