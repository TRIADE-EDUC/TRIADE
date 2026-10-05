<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleSelector.php,v 1.4 2023/05/25 12:30:27 jparis Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Article;

use Pmb\DSI\Models\Selector\SourceSelector;

class ArticleSelector extends SourceSelector
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