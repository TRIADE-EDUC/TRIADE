<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleListItem.php,v 1.9 2024/03/25 13:16:16 jparis Exp $

namespace Pmb\DSI\Models\Item\Entities\Article\ArticleListItem;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Item\SimpleItem;

class ArticleListItem extends SimpleItem
{
    public const TYPE = TYPE_CMS_ARTICLE;

    public function getTree($parent = true)
    {
        $msg = static::getMessages();
        $data = \cms_editorial::get_format_data_structure("article", false);
        $tree = [
            [
				'var' => "articles",
				'desc' => $msg['tree_articles_desc'],
				'children' => $this->prefix_var_tree($data, "articles[i]")
            ]
        ];
        return $parent ? array_merge($tree, parent::getTree()) : $tree;
    }

    public function getLabels($ids)
    {
        if(is_object($ids)) {
            $ids = Helper::toArray($ids);
        }

        $aricles = [];
        foreach ($ids as $id => $title) {
            $article = new \cms_article($id);
            if(!empty($article->title)) {
                $aricles[$id] = $article->title;
            }
        }
        return $aricles;
    }
}
