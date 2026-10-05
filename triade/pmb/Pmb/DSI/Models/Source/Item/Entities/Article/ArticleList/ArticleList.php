<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleList.php,v 1.4 2023/05/25 12:30:27 jparis Exp $

namespace Pmb\DSI\Models\Source\Item\Entities\Article\ArticleList;

use Pmb\DSI\Models\Source\Item\ItemSource;

class ArticleList extends ItemSource
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

