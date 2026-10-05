<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationListItem.php,v 1.1.4.2 2025/05/21 08:03:10 rtigero Exp $

namespace Pmb\DSI\Models\Item\Entities\Animation\AnimationListItem;

use Pmb\Animations\Models\AnimationModel;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Item\SimpleItem;

class AnimationListItem extends SimpleItem
{
    public const TYPE = TYPE_ANIMATION;

    public function getTree($parent = true)
    {
        $tree = (new AnimationModel())->getCmsStructure("animations[i]");
        return $parent ? array_merge($tree, parent::getTree()) : $tree;
    }

    public function getLabels($ids)
    {
        if (is_object($ids)) {
            $ids = Helper::toArray($ids);
        }

        $aricles = [];
        foreach ($ids as $id => $title) {
            $article = new \cms_article($id);
            if (!empty($article->title)) {
                $aricles[$id] = $article->title;
            }
        }
        return $aricles;
    }
}
