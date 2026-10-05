<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordListItem.php,v 1.11.4.1 2025/05/21 14:15:04 rtigero Exp $

namespace Pmb\DSI\Models\Item\Entities\Record\RecordListItem;

use Pmb\DSI\Models\Item\SimpleItem;
use search;

class RecordListItem extends SimpleItem
{
    public const TYPE = TYPE_NOTICE;
    public function getTree($parent = true)
    {
        $msg = static::getMessages();
        $tree = [
            [
                'var' => "records",
                'desc' => $msg['tree_records_desc'],
                'children' => [
                    [
                        'var' => "records[i].content",
                        'desc' => $msg['tree_record_content_desc'],
                    ],
                ],
            ],
        ];
        return $parent ? array_merge($tree, parent::getTree()) : $tree;
    }

    public function getLabels(array $ids)
    {
        $records = [];
        foreach ($ids as $id) {
            $title = @\notice::get_notice_title($id);
            if (!empty($title)) {
                $records[$id] = $title;
            }
        }

        return $records;
    }

    /**
     * Convertit une recherche sérialisée en item utilisable pour la DSI
     *
     * @param string $requete
     * @return RecordListItem
     */
    public static function transformEquationToItem($requete)
    {
        global $msg;
        $requete = stripslashes($requete);
        //On instancie un nouvel item
        $newItem = new self();
        $newItem->name = $msg["dsi_item_from_transformation_default_name"];
        $newItem->model = true;
        $newItem->type = self::TYPE;

        //On prépare la search
        $search = new search(false, "search_fields");
        $search->unserialize_search($requete);

        //On remplit convenablement les settings
        $settings = new \stdClass();
        $settings->namespace = "Pmb\\DSI\\Models\\Source\\Item\\Entities\\Record\\RecordList\\RecordList";
        $settings->selector = new \stdClass();
        $settings->selector->namespace = "Pmb\\DSI\\Models\\Selector\\Item\\Entities\\Record\\RMC\\RecordRMCSelector";
        $settings->selector->data = new \stdClass();
        $settings->selector->data->human_query = $search->make_human_query();
        $settings->selector->data->search = $search->json_encode_search();
        $settings->selector->data->search_serialize = $requete;

        $newItem->settings = $settings;

        //On enregistre
        $newItem->create();

        return $newItem;
    }
}
