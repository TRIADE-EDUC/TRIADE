<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SummaryView.php,v 1.3.2.1 2024/10/01 08:24:24 jparis Exp $

namespace Pmb\DSI\Models\View\SummaryView;

use Pmb\DSI\Models\Group\RootGroup;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\GroupView\GroupView;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Orm\ViewOrm;

class SummaryView extends RootView
{
    const TEMPLATE_ITEM = '<li><a  href="#[!!id!!]" class="summary_elt">!!group!!</a></li>';

    const TEMPLATE_CONTAINER_LISTS = [
        '<ol style="list-style-type: initial;">!!content!!</ol>',
        '<ul>!!content!!</ul>',
        '<ol style="list-style-type: lower-alpha;">!!content!!</ol>',
        '<ol style="list-style-type: upper-alpha;">!!content!!</ol>',
        '<ol style="list-style-type: lower-roman;">!!content!!</ol>',
        '<ol style="list-style-type: upper-roman;">!!content!!</ol>'
    ];

    protected $linkedView;

    public function read()
    {
        parent::read();
        $this->fetchLinkedView();
    }

    public function render(Item $item, int $entityId, int $limit, string $context)
    {
        global $charset;

        $data = $this->getDataFromContext($item, $context);
        if (empty($data)) {
            return "";
        }

        $this->filterData($data, $entityId);
        $this->limitData($data, $limit);
        if(! ($this->linkedView instanceof GroupView)) {
            return "";
        }
        $this->linkedView->groupData($data, $item::TYPE);

        $title = $this->getSetting('title', '');
        $title = htmlentities($title, ENT_QUOTES, $charset);

        $summary = $this->renderGroup("{$this->linkedView->id}-{$item->id}", $data);
        return empty($title) ? $summary : "<h1>{$title}</h1>{$summary}";
    }

    public function preview(Item $item, int $entityId, int $limit, string $context)
    {
        return $this->render($item, $entityId, $limit, $context);
    }

    protected function renderGroup(string $prefixID, array $groups, int $rank = 1)
    {
        global $charset;
        $listType = $this->getSetting('listType', 0);

        if (empty(static::TEMPLATE_CONTAINER_LISTS[$listType])) {
            return "";
        }

        if (
            !empty($groups[RootGroup::EMPTY_GROUP_KEY]) &&
            !empty($groups[RootGroup::EMPTY_GROUP_KEY][RootGroup::RESULT_KEY])
        ) {
            unset($groups[RootGroup::EMPTY_GROUP_KEY]);
        }

        $items = "";
        foreach ($groups as $group => $subGroups) {
            $groupLabel = htmlentities($group, ENT_QUOTES, $charset);
            $items .= str_replace(
                ['!!id!!', '!!group!!'],
                ["{$prefixID}-{$groupLabel}-{$rank}",$groupLabel],
                static::TEMPLATE_ITEM
            );
            if (empty($subGroups[RootGroup::RESULT_KEY])) {
                $items .= $this->renderGroup($prefixID, $subGroups, $rank+1);
            }
        }

        return str_replace(
            '!!content!!',
            $items,
            static::TEMPLATE_CONTAINER_LISTS[$listType]
        );
    }

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        $formData = parent::getFormData();

        $views = $formData["availableTypes"]["view"] ?? [];
        $views = array_map(function ($view) {
            return RootView::IDS_TYPE[$view];
        }, $views);

        $groupViews = ViewOrm::finds([
            "type" => [
                "operator" => "in",
                "value" => $views ?? []
            ]
        ]);

        $groupViews = array_map(function ($view) {
            return [
                "value" => $view->id_view,
                "label" => $view->name,
            ];
        }, $groupViews);

        return array_merge($formData, [
            "views" => $groupViews
        ]);
    }

    protected function fetchLinkedView()
    {
        if(! isset($this->linkedView)) {
            $this->linkedView = RootView::getInstance($this->getSetting('linkedView', 0));
        }
    }
}
