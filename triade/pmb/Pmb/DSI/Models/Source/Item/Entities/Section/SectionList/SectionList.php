<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionList.php,v 1.1.4.2 2025/03/26 08:25:59 rtigero Exp $

namespace Pmb\DSI\Models\Source\Item\Entities\Section\SectionList;

use Pmb\DSI\Models\Source\Item\ItemSource;

class SectionList extends ItemSource
{
    public $selector = null;

    public function __construct($selectors = null)
    {
        if (!empty($selectors->selector->namespace)) {
            $this->selector = new $selectors->selector->namespace($selectors->selector);
        }
    }

    public function getData()
    {
        return $this->selector->getData();
    }

    public function getResults()
    {
        return $this->selector->getResults();
    }
}

