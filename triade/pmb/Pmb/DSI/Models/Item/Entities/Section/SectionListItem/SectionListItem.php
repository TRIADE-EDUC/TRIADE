<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionListItem.php,v 1.1.4.2 2025/03/26 08:26:02 rtigero Exp $

namespace Pmb\DSI\Models\Item\Entities\Section\SectionListItem;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Item\SimpleItem;

class SectionListItem extends SimpleItem
{
    public const TYPE = TYPE_CMS_SECTION;

    public function getTree($parent = true)
    {
        $msg = static::getMessages();
        $data = \cms_editorial::get_format_data_structure("section", false);
        $tree = [
            [
                'var' => "sections",
                'desc' => $msg['tree_sections_desc'],
                'children' => $this->prefix_var_tree($data, "sections[i]")
            ]
        ];
        return $parent ? array_merge($tree, parent::getTree()) : $tree;
    }

    public function getLabels($ids)
    {
        if (is_object($ids)) {
            $ids = Helper::toArray($ids);
        }

        $sections = [];
        foreach ($ids as $id => $title) {
            $section = new \cms_section($id);
            if (!empty($article->title)) {
                $sections[$id] = $section->title;
            }
        }
        return $sections;
    }
}
