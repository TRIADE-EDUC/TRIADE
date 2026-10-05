<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSelector.php,v 1.3 2023/04/05 09:55:15 qvarin Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Record;

use Pmb\DSI\Models\Selector\SourceSelector;

class RecordSelector extends SourceSelector
{
    public $selector = null;
    public $data = [];

    public function __construct(array $selectors)
    {
        if (!empty($selectors)) {
            $this->selector = new $selectors["subselector"]["namespace"]($selectors["subselector"]);
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