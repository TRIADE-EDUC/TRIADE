<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionSelector.php,v 1.3 2023/05/25 12:30:27 jparis Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Diffusion;

use Pmb\DSI\Models\Selector\SourceSelector;

class DiffusionSelector extends SourceSelector
{
    public $selector = null;
    public $data = [];

    public function __construct($selectors = null)
    {
        if (!empty($selectors)) {
            $namespace =  $selectors->selector->namespace;
            $this->selector = new $namespace($selectors->selector);
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