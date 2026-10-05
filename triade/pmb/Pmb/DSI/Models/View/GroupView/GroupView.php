<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GroupView.php,v 1.6.2.1 2024/10/01 08:24:25 jparis Exp $

namespace Pmb\DSI\Models\View\GroupView;

use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Models\Group\RootGroup;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\RootView;

class GroupView extends RootView
{
    protected $entityNamespace;

    /**
     * Correspond au groupement
     *
     * @var RootGroup|null
     */
    protected $group;

    public function render(Item $item, int $entityId, int $limit, string $context)
    {
        global $use_opac_url_base;
        $use_opac_url_base = 1;

        $data = $this->getDataFromContext($item, $context);
        if (empty($data)) {
            return "";
        }

        $this->entityNamespace = strtolower(HelperEntities::get_entities_namespace()[$this->settings->entityType]);

        $this->filterData($data, $entityId);
        $this->limitData($data, $limit);
        $this->groupData($data, $item::TYPE);

        return $this->renderGroup("{$this->id}-{$item->id}", $data);
    }

    public function preview(Item $item, int $entityId, int $limit, string $context)
    {
        return $this->render($item, $entityId, $limit, $context);
    }

    public function renderGroup(string $prefixID, array $data, int $rank = 1)
    {
        global $charset;

        $tpl = "";

        // On affichage ceux qui n'ont pas de groupement en premier
        if (
            !empty($data[RootGroup::EMPTY_GROUP_KEY]) &&
            !empty($data[RootGroup::EMPTY_GROUP_KEY][RootGroup::RESULT_KEY])
        ) {
            $tpl.="<div class='dsi_notices_rang_$rank'>";
            foreach ($data[RootGroup::EMPTY_GROUP_KEY][RootGroup::RESULT_KEY] as $id) {
                $tpl .= $this->renderEntity($id)."<br />" ;
            }
            $tpl.="</div>";

            unset($data[RootGroup::EMPTY_GROUP_KEY]);
        }

        // On parcour chaque groupement
        foreach ($data as $group => $result) {

            $lvlTitle = $rank <= 6 ? $rank : 6;
            $margin = $this->getSetting('margin');
            $style = "";
            if (!empty($margin)) {
                $margin->value = intval($margin->value);
                $margin->unit = addslashes($margin->unit);
                $style = "style='margin-left: {$margin->value}{$margin->unit}'";
            }
            $groupLabel = $result['label'] ?? $group;
            $groupLabel = htmlentities($groupLabel,ENT_QUOTES,$charset);
            $tpl.="<a name='[{$prefixID}-{$groupLabel}-{$rank}]'></a><h{$lvlTitle} class='dsi_rank_{$rank}'>" .
                $groupLabel .
            "</h{$lvlTitle}>";
            $tpl.="<div class='dsi_contents_rank_{$rank}' {$style}>";

            if (!empty($result[RootGroup::RESULT_KEY])) {
                foreach($result[RootGroup::RESULT_KEY] as $id) {
                    $tpl .= $this->renderEntity($id)."<br />" ;
                }
            } else {
                $tpl .= $this->renderGroup($prefixID, $result, $rank+1);
            }
            $tpl.="</div>";
        }

        return $tpl;
    }

    protected function renderEntity($id)
    {
        $template = $this->getTemplate();
        if (is_file($template)) {

            switch ($this->settings->entityType) {
                case TYPE_NOTICE:
                    $element = new \record_datas($id);
                    break;
                case TYPE_CMS_ARTICLE:
                    $element = new \cms_editorial_data($id, "article");
                    break;
                case TYPE_CMS_SECTION:
                    $element = new \cms_editorial_data($id, "section");
                    break;
                default:
                    return "";
            }

            \H2o::addLookup([SubscriberHelper::class, 'h2oLookup']);
            \H2o::addLookup([LookupHelper::class, 'h2oLookup']);
            $h2o = \H2o_collection::get_instance($template);
            return $h2o->render([
                $this->entityNamespace => $element,
            ]);
        }

        return "";
    }

    /**
     * Permet d'appliquer le grouement sur les items
     * Utilise par la classe SummaryView
     *
     * @param array $data
     * @param integer $entityType
     * @return void
     */
    public function groupData(array &$data, int $entityType)
    {
        $this->formatGroups();
        if (isset($this->group)) {
            $this->group->addItems($entityType, array_keys($data));
            $data = $this->group->group();
        } else {
            // On a aucun groupe de choisi
            $data = [
                RootGroup::EMPTY_GROUP_KEY => [
                    RootGroup::RESULT_KEY => array_keys($data)
                ]
            ];
        }
    }

    /**
     * Format les groupements
     *
     * @return void
     */
    protected function formatGroups()
    {
        $groups = $this->getSetting('groups', new \stdClass());
        $groupParent = null;

        foreach ($groups as $group) {
            $namespace = array_search($group->id, RootGroup::IDS_TYPE, true);
            if (!class_exists($namespace)) {
                continue;
            }

            $group = new $namespace($group->settings);
            if (isset($groupParent)) {
                $groupParent->setSubGroup($group);
            } else {
                $groupParent = $group;
                if (empty($this->group)) {
                    $this->group = $group;
                }
            }
        }
    }

    /**
     * Retourne le path du template
     *
     * @param mixed $element (compatibilite avec RootView)
     * @param mixed $type (compatibilite avec RootView)
     * @return string
     */
    protected function getTemplate($element = null, $type = null)
    {
        if (defined('GESTION')) {
            $path = "./opac_css/includes/templates/dsi/";
        } else {
            $path = "./includes/templates/dsi/";
        }

        $path .= strtolower($this->entityNamespace) .
            '/' . $this->settings->templateDirectory .
            '/' . strtolower($this->entityNamespace) . '_in_result_display.tpl.html';

        return $path;
    }
}