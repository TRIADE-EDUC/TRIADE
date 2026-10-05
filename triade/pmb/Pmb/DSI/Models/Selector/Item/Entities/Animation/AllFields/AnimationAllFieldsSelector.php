<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationAllFieldsSelector.php,v 1.1.4.2 2025/05/21 08:03:11 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Animation\AllFields;

use Pmb\DSI\Models\Selector\Item\Entities\Animation\AnimationSubSelector;

class AnimationAllFieldsSelector extends AnimationSubSelector
{

    protected function checkData()
    {
        if (! $this->data->search) {
            return false;
        }
        return true;
    }

    protected function getGlobalsSearch()
    {
        $results = new \stdClass();
        $results->tlc = $this->data->search ?? "";
        $results->dateStart = "";
        $results->dateEnd = "";
        $results->inputSearchExactDate = true;
        return $results;
    }
}
