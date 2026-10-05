<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionList.php,v 1.3 2023/05/25 12:30:27 jparis Exp $

namespace Pmb\DSI\Models\Source\Item\Entities\Diffusion\DiffusionList;

use Pmb\DSI\Models\Source\Item\ItemSource;

class DiffusionList extends ItemSource
{
    public $selector = null;

    public function __construct(\stdClass $selectors = null)
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

