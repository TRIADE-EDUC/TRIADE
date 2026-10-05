<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RawTextView.php,v 1.5 2023/04/07 07:53:10 rtigero Exp $

namespace Pmb\DSI\Models\View\RawTextView;

use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\DjangoView\DjangoView;

class RawTextView extends DjangoView
{	
    public function render(Item $item, int $entityId, int $limit, string $context)
    {   
        parent::render($item, $entityId, $limit, $context);
        return trim(strip_tags($this->html));
    }

    public function preview(Item $item, int $entityId, int $limit, string $context)
	{
		return $this->render($item, $entityId, $limit, $context);
	}

    protected function getTemplate($element, $type = null)
    {
        if(! isset($type)) {
            $type = $this->settings->entityType;
        }
        if(empty($this->settings->templateDirectory)) {
            $this->settings->templateDirectory = "common";
        }
        return parent::getTemplate($element, $type);
    }
}

