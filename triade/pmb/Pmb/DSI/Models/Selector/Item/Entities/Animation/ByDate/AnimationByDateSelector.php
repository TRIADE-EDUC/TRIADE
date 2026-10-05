<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationByDateSelector.php,v 1.1.4.2 2025/05/21 08:03:10 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Animation\ByDate;

use Pmb\DSI\Models\Selector\Item\Entities\Animation\AnimationSubSelector;

class AnimationByDateSelector extends AnimationSubSelector
{

    protected function checkData()
    {
        if (! $this->data->dateFrom || ! $this->data->dateRange) {
            return false;
        }
        return true;
    }

    protected function getGlobalsSearch()
    {
        $results = new \stdClass();
        $results->tlc = "";
        $results->dateStart = $this->data->dateFrom ?? "";
        $results->dateEnd = $this->data->dateUntil ?? "";
        $results->inputSearchExactDate = $this->data->dateRange == 'exact';
        return $results;
    }
}
