<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AggregatedRawTextView.php,v 1.1 2023/04/07 07:53:09 rtigero Exp $

namespace Pmb\DSI\Models\View\AggregatedRawTextView;

use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\AggregatedDjangoView\AggregatedDjangoView;

class AggregatedRawTextView extends AggregatedDjangoView
{	
    public function render(Item $item, int $entityId, int $limit, string $context)
    {   
        parent::render($item, $entityId, $limit, $context);
        return trim(strip_tags($this->html));
    }
}

