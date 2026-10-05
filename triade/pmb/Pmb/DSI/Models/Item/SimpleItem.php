<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SimpleItem.php,v 1.24.4.2 2025/05/21 08:03:12 rtigero Exp $

namespace Pmb\DSI\Models\Item;

use Pmb\DSI\Models\Item\Entities\Article\ArticleListItem\ArticleListItem;
use Pmb\DSI\Models\Item\Entities\Diffusion\DiffusionListItem\DiffusionListItem;
use Pmb\DSI\Models\Item\Entities\ItemWatch\ItemWatchListItem\ItemWatchListItem;
use Pmb\DSI\Models\Item\Entities\Record\RecordListItem\RecordListItem;
use Pmb\DSI\Orm\ItemOrm;
use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Models\Item\Entities\Animation\AnimationListItem\AnimationListItem;
use Pmb\DSI\Models\Item\Entities\Section\SectionListItem\SectionListItem;

class SimpleItem extends RootItem
{
    public static $ignoreResultsToArray = true;

    public static $messages = [];

    public $results = array();

    public static function getInstance(int $id = 0)
    {
        $orm = new ItemOrm($id);

        switch ($orm->type) {
            case TYPE_NOTICE:
                return new RecordListItem($id);
            case TYPE_CMS_ARTICLE:
                return new ArticleListItem($id);
            case TYPE_CMS_SECTION:
                return new SectionListItem($id);
            case TYPE_DOCWATCH:
                return new ItemWatchListItem($id);
            case TYPE_DSI_DIFFUSION:
                return new DiffusionListItem($id);
            case TYPE_ANIMATION:
                return new AnimationListItem($id);
            default:
                return new SimpleItem($id);
        }
    }

    /**
     * Retourne les données de l'item
     *
     * @return array
     */
    public function getData()
    {
        if (!empty($this->data)) {
            return $this->data;
        }
        if ($this->itemSource) {
            return $this->itemSource->getData();
        }
        return [];
    }

    /**
     * Retourne les résultats de la source
     *
     * @return array
     */
    public function getResults()
    {
        if (empty($this->results) && method_exists($this->itemSource, "getResults")) {
            $this->results = $this->itemSource->getResults();
        }
        return $this->results;
    }

    /**
     * Cette methode doit etre remplacee dans les sous-classes
     *
     * @return array
     */
    public function getTree()
    {
        $msg = static::getMessages();
        $tree = [
            [
                'var' => "env_vars",
                'desc' => $msg['tree_env_vars_desc'],
                'children' => [
                    [
                        'var' => "env_vars.script",
                        'desc' => $msg['tree_env_vars_script_desc'],
                    ],
                    [
                        'var' => "env_vars.request",
                        'desc' => $msg['tree_env_vars_request_desc'],
                    ],
                    [
                        'var' => "env_vars.opac_url",
                        'desc' => $msg['tree_env_vars_opac_url_desc'],
                    ],
                    [
                        'var' => "env_vars.platform",
                        'desc' => $msg['tree_env_vars_platform_desc'],
                    ],
                    [
                        'var' => "env_vars.browser",
                        'desc' => $msg['tree_env_vars_browser_desc'],
                    ],
                    [
                        'var' => "env_vars.server_addr",
                        'desc' => $msg['tree_env_vars_server_addr_desc'],
                    ],
                    [
                        'var' => "env_vars.remote_addr",
                        'desc' => $msg['tree_env_vars_remote_addr_desc'],
                    ],
                ],
            ],
        ];
        return array_merge($tree, SubscriberHelper::getTree());
    }

    protected function prefix_var_tree($tree, $prefix)
    {
        $index = count($tree);
        for ($i = 0; $i < $index; $i++) {
            $tree[$i]['var'] = $prefix . "." . $tree[$i]['var'];
            if (isset($tree[$i]['children']) && $tree[$i]['children']) {
                $tree[$i]['children'] = $this->prefix_var_tree($tree[$i]['children'], $prefix);
            }
        }
        return $tree;
    }

    /**
     * Retourne la liste des proprietes a ignorer pour le toArray
     *
     * @return array
     */
    protected static function getIgnorePropsToArray()
    {
        if (static::$ignoreResultsToArray) {
            return array_merge(parent::IGNORE_PROPS_TOARRAY, ['results', 'data']);
        }
        return parent::IGNORE_PROPS_TOARRAY;
    }

    public function getSearchInput()
    {
        if ($this->itemSource && $this->itemSource->selector) {
            return $this->itemSource->selector->getSearchInput();
        }
        return "";
    }

    public function getNbResults()
    {
        return count($this->getResults());
    }
}
