<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationSelector.php,v 1.1.4.2 2025/05/21 08:03:12 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Animation;

use Pmb\DSI\Models\Selector\SourceSelector;

class AnimationSelector extends SourceSelector
{
    public $selector = null;
    public $data = [];

    public function __construct($selectors = null)
    {
        if (!empty($selectors)) {
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
