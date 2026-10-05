<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionListItem.php,v 1.7 2023/11/28 11:29:16 rtigero Exp $

namespace Pmb\DSI\Models\Item\Entities\Diffusion\DiffusionListItem;

use Pmb\DSI\Models\Item\SimpleItem;
use Pmb\DSI\Models\Diffusion;

class DiffusionListItem extends SimpleItem
{
    public const TYPE = TYPE_DSI_DIFFUSION;

    public function getTree($parent = false)
    {
        $msg = static::getMessages();
        $diffusion = new Diffusion();
        $tree = [
            [
				'var' => "diffusions",
				'desc' => $msg['tree_diffusions_desc'],
				'children' => $diffusion->getCmsStructure("diffusions[i]")
            ]
        ];
        return $parent ? array_merge($tree, parent::getTree()) : $tree;
    }
    
    public function getLabels(array $ids)
    {
        $diffusions = [];
        foreach ($ids as $id) {
            $diffusion = new Diffusion($id);
            if (!empty($diffusion->name)) {
                $diffusions[$id] = $diffusion->name;
            }
        }
        
        return $diffusions;
    }
}
