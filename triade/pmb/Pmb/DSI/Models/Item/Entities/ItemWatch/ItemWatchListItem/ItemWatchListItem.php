<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ItemWatchListItem.php,v 1.7 2024/03/25 13:16:16 jparis Exp $

namespace Pmb\DSI\Models\Item\Entities\ItemWatch\ItemWatchListItem;

use Pmb\DSI\Models\Item\SimpleItem;

class ItemWatchListItem extends SimpleItem
{
    public const TYPE = TYPE_DOCWATCH;

    public function getTree($parent = true)
    {
		$datasource = new \cms_module_itemslist_datasource_items();
        return $parent ? array_merge($datasource->get_format_data_structure(), parent::getTree()) : $datasource->get_format_data_structure();
    }
    
    public function getLabels(array $ids)
    {
        $itemsWatch = [];
        foreach ($ids as $id) {
            $item = (new \docwatch_item(intval($id)))->get_normalized_item();
            if (!empty($item->title)) {
                $itemsWatch[$id] = $item->title;
            }
        }
        
        return $itemsWatch;
    }
}
