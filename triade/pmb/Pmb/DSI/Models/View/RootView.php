<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootView.php,v 1.63.2.2.2.2 2025/02/11 09:17:59 qvarin Exp $

namespace Pmb\DSI\Models\View;

use Pmb\Common\Helper\Directory;
use Pmb\Common\Helper\Helper;
use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\Root;
use Pmb\DSI\Orm\ViewOrm;

class RootView extends Root
{
    protected const EXCLUDED_PROPERTIES = [
        "idView",
        "viewModel",
        'numModel',
        'model',
        'entityId',
        'limit',
        'context',
    ];

    public const TAG_TYPE = 3;

    public const IDS_TYPE = [
        "Pmb\DSI\Models\View\DjangoView\DjangoView" => 1,
        "Pmb\DSI\Models\View\WYSIWYGView\WYSIWYGView" => 2,
        "Pmb\DSI\Models\View\SimpleView\SimpleView" => 3,
        "Pmb\DSI\Models\View\RawTextView\RawTextView" => 4,
        "Pmb\DSI\Models\View\AggregatedDjangoView\AggregatedDjangoView" => 5,
        "Pmb\DSI\Models\View\AggregatedRawTextView\AggregatedRawTextView" => 6,
        "Pmb\DSI\Models\View\ParserHtmlView\ParserHtmlView" => 7,
        "Pmb\DSI\Models\View\RssView\RssView" => 8,
        "Pmb\DSI\Models\View\WYSIWYGPDFView\WYSIWYGPDFView" => 9,
        "Pmb\DSI\Models\View\ExportView\ExportView" => 10,
        "Pmb\DSI\Models\View\GroupView\GroupView" => 11,
        "Pmb\DSI\Models\View\SummaryView\SummaryView" => 12,
        "Pmb\DSI\Models\View\CartView\CartView" => 13,
        "Pmb\DSI\Models\View\AgnosticDjangoView\AgnosticDjangoView" => 14,
        "Pmb\DSI\Models\View\PreviousDSIView\PreviousDSIView" => 15,
        "Pmb\DSI\Models\View\PreviousDSIPDFView\PreviousDSIPDFView" => 16,
        "Pmb\DSI\Models\View\CartSimpleView\CartSimpleView" => 17
    ];

    public const IDS_TYPE_AGNOSTIC = [
        14
    ];

    public const IDS_TYPE_PARENT_VIEWS = [
        2,
        9,
        14
    ];

    protected $ormName = "Pmb\DSI\Orm\ViewOrm";

    public $id = 0;
    public $name = "";
    public $type = 0;
    public $model = false;
    public $settings = "";
    public $childs = [];
    public $tags = null;

    public $msg = null;

    // ORM props
    protected $idView = 0;

    public $numModel = 0;
    protected $numParent = 0;

    protected $viewModel = null;
    protected $parentView = null;

    protected $modifiedType = null;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        if($this->id) {
            $this->read();
        }
    }

    public static function getInstance(int $id = 0)
    {
        if (!empty($id) && ViewOrm::exist($id)) {
            $view = new ViewOrm($id);
            foreach (self::IDS_TYPE as $key => $type) {
                if (self::IDS_TYPE[$key] == $view->type) {
                    return new $key($id);
                }
            }
        }

        try {
            return new RootView($id);
        } catch (\Exception $e) {
            return new RootView();
        }
    }

    public function read()
    {
        $this->fetchData();
        $this->fetchChilds();
    }

    public function check(object $data)
    {
        if (!is_string($data->name)) {
            return [
                'error' => true,
                'errorMessage' => 'msg:data_errors',
            ];
        }

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    public function create()
    {
        $orm = new $this->ormName();
        $orm->name = $this->name;
        $orm->type = $this->type;
        $orm->model = $this->model;
        $orm->settings = json_encode($this->settings);
        $orm->num_model = $this->numModel;
        $orm->num_parent = $this->numParent;
        $orm->save();

        $this->id = $orm->{$this->ormName::$idTableName};
        $this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
    }

    public function update()
    {
        $orm = new $this->ormName($this->id);
        $orm->name = $this->name;
        $orm->type = $this->type;
        $orm->model = $this->model;
        $orm->settings = json_encode($this->settings);
        $orm->num_model = $this->numModel;
        $orm->num_parent = $this->numParent;
        $orm->save();
        if (!in_array($this->type, static::IDS_TYPE_PARENT_VIEWS)) {
            //Ici on vient de passer d'une vue compatible wysiwyg vers une vue incompatible
            //Donc on supprime les enfants de la vue
            $this->deleteChilds();
        }
    }

    public function delete()
    {
        try {
            if (!$this->checkBeforeDelete()) {
                return [
                    'error' => true,
                    'errorMessage' => "msg:model_check_use",
                ];
            }

            $this->deleteChilds();
            $this->removeEntityTags();
            $orm = new $this->ormName($this->id);
            $orm->delete();
        } catch (\Exception $e) {
            return [
                'error' => true,
                'errorMessage' => $e->getMessage(),
            ];
        }

        $this->id = 0;
        $this->{Helper::camelize($orm::$idTableName)} = 0;
        $this->name = '';
        $this->type = '';
        $this->model = false;
        $this->settings = [];
        $this->numModel = null;
        $this->numParent = null;

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    public function setFromForm(object $data)
    {
        $this->name = $data->name;
        $this->type = intval($data->type);
        $this->model = $data->model;
        $this->settings = $data->settings;
        $this->numModel = $data->numModel ?? 0;
        $this->numParent = $data->numParent ?? 0;
        $this->childs = $data->childs ?? [];
    }

    public function fetchChilds()
    {
        if ($this->id == 0) {
            return;
        }
        $fields["num_parent"] = [
            'value' => $this->id,
            'operator' => '=',
        ];
        $result = $this->ormName::finds($fields);
        foreach ($result as $child) {
            $this->childs[] = RootView::getInstance($child->id_view);
        }
    }

    public function saveChilds()
    {
        $childrenDeleted = [];
        foreach ($this->childs as $child) {
            if (0 != $child->id && !ViewOrm::exist($child->id)) {
                $childrenDeleted[] = $child->id;
                continue;
            }

            $childModel = self::getInstance($child->id);
            $child->numParent = $this->id;
            $childModel->setFromForm($child);

            if (0 == $child->id) {
                $childModel->create();
            } else {
                $childModel->update();
            }
        }

        if (!empty($childrenDeleted)) {
            $this->childs = array_filter($this->childs, function ($child) use ($childrenDeleted) {
                return !in_array($child->id, $childrenDeleted);
            });
        }
    }

    public function deleteChilds()
    {
        foreach ($this->childs as $child) {
            $childModel = self::getInstance($child->id);
            $childModel->delete();
        }
    }

    /**
     * Rendu des donnees
     *
     * @param Item $item
     * @param int $entityId
     * @param int $limit
     * @return string
     */
    public function render(Item $item, int $entityId, int $limit, string $context)
    {
        return "";
    }

    /**
     * Renvoi la prévisualisation
     *
     * @param Item $item
     * @param int $entityId
     * @param int $limit
     * @return string
     */
    public function preview(Item $item, int $entityId, int $limit, string $context)
    {
        return "";
    }

    /**
     * Reduit le tableau data selon le parametre limit
     *
     * @param array $data
     * @param int $limit
     */
    protected function limitData(&$data, int $limit)
    {
        if ($limit == 0) {
            return;
        }
        $data = array_slice($data, 0, $limit, true);
    }

    protected function filterData(&$data, int $entityId)
    {
        if (isset($this->settings->filters) && count($this->settings->filters)) {
            foreach ($this->settings->filters as $filterSettings) {
                if (empty($filterSettings->namespace)) {
                    continue;
                }
                $filter = new $filterSettings->namespace($data, $entityId);
                if (count($filter::$fields)) {
                    $filter->setFieldsValues($filterSettings->fields);
                }
                $data = $filter->filter();
            }
        }
    }

    public function getFilteredData($data, $entityId, $context)
    {
        $result = $this->getDataFromContext($data, $context);
        $this->filterData($result, $entityId);
        return $result;
    }

    protected function getTemplate($element, $type = null)
    {
        if (!isset($type)) {
            $type = $this->settings->entityType;
        }

        if (defined('GESTION')) {
            $path = "./opac_css/includes/templates/dsi/";
        } else {
            $path = "./includes/templates/dsi/";
        }

        switch ($type) {
            case TYPE_NOTICE:
                return \record_display::get_template(
                    "record_in_result_display",
                    $element->get_niveau_biblio(),
                    $element->get_typdoc(),
                    $this->settings->templateDirectory,
                    "",
                    "",
                    true,
                    true
                );

            case TYPE_AUTHORITY:
                // TODO externaliser les templates d'autoritées vers /opac_css/includes/templates/dsi/
                return $element->render([], $this->settings->templateDirectory);

            case TYPE_DSI_DIFFUSION:
            case TYPE_DOCWATCH:
            case TYPE_CMS_ARTICLE:
                $path .= $this->entityNamespace . '/' . $this->settings->templateDirectory . '/' . $this->entityNamespace . '_in_result_display.tpl.html';
                return $path;

            default:
                return "";
        }
    }

    public function getTemplateDirectories($entityType = 0)
    {
        switch ($entityType) {
            case TYPE_NOTICE:
                return \notice_tpl::get_directories();

            case TYPE_DSI_DIFFUSION:
            case TYPE_DOCWATCH:
            case TYPE_CMS_ARTICLE:
                $entitiesNamespace = HelperEntities::get_entities_namespace();
                $entitiesNamespace = array_map("strtolower", $entitiesNamespace);

                if (empty($entitiesNamespace) || empty($entitiesNamespace[$entityType])) {
                    return [];
                }

                $dirs = Directory::getNameDirectories("./opac_css/includes/templates/dsi/{$entitiesNamespace[$entityType]}");
                return $dirs ?? [];

            default:
                return \auth_templates::get_directories();
        }
    }

    protected function formatHTMLPreview(string $body, bool $html5 = true)
    {
        global $opac_default_style;

        if (!$html5) {
            $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            $html .= '<html xmlns="http://www.w3.org/1999/xhtml">';
        } else {
            $html = " <!DOCTYPE html>";
            $html .= "<html>";
        }

        $html .= "<head>";
        if (!$html5) {
            $html .= '<meta http-equiv="Content-type" content="text/html; charset=utf-8" />';
        } else {
            $html .= "<meta charset='utf-8' />";
        }
        $html .= \HtmlHelper::getInstance()->getStyleOpac($opac_default_style);
        $html .= "</head>";

        $html .= "<body>";
        $html .= $body;
        $html .= "</body>";
        $html .= "</html>";

        if ($html5) {
            return $html;
        }

        $dom = new \DOMDocument();
        if (@$dom->loadHTML($html)) {
            $head = $dom->getElementsByTagName('head')->item(0);
            $domNodeList = $dom->getElementsByTagName('style');
            for ($i = 0; $i < $domNodeList->length; $i++) {
                $node = $domNodeList->item($i);
                if (!$node->hasAttribute('type')) {
                    $node->setAttribute('type', 'text/css');
                }
                $head->appendChild($node);
            }
            $html = $dom->saveHTML();
        }
        return $html;
    }

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        $availableTypes = $this->getAvailableTypes() ?? [];
        return [
            "messages" => static::getMessages(),
            "availableTypes" => $this->getAvailableTypes(),
            "availableItems" => array_map(function ($item) {
                return $item::TYPE;
            }, $availableTypes['item'] ?? []),
        ];
    }

    public function fetchChildById($id)
    {
        foreach ($this->childs as $child) {
            if ($id == $child->id) {
                return $child;
            }

            if (!empty($child->childs)) {
                return $child->fetchChildById($id);
            }
        }
        return null;
    }

    public function getDataFromContext($item, string $context)
    {
        $data = [];
        if ($context === "DiffusionPending") {
            foreach ($item->results as $id) {
                if (!in_array($id, $item->removed)) {
                    $data[$id] = "";
                }
            }
        } else {
            $data = $item->getData();
            if (!is_array($data)) {
                $data = Helper::toArray($data);
            }
        }
        return $data;
    }

    /**
     * Retourne un parametre en fonction de son nom
     *
     * @param string $settingName
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $settingName, $default = null)
    {
        if (empty($this->settings)) {
            return $default;
        }
        return $this->settings->{$settingName} ?? $default;
    }

    /**
     * Permet de mettre à jour l'id d'un item utilisé dans la vue
     * A dériver
     */
    public function updateViewItem($oldItemId = 0, $newItemId = 0, $params = null)
    {
    }

    /**
     * @param mixed $param vue parente
     */
    public function duplicate($param = null, $changeName = true)
    {
        $newEntity = static::getInstance($this->id);

        if($changeName) {
            $newEntity->name = $this->getDuplicateName($this->name);
        }

        if (!empty($param)) {
            $oldViewId = $newEntity->id;
            $newEntity->numParent = $param->id;
        }
        $newEntity->id = 0;
        $newEntity->create();
        if (isset($oldViewId)) {
            //Mise à jour de la vue parente avec le nouvel enfant
            $param->updateViewChild($oldViewId, $newEntity->id);
            $param->update();
        }
        if ($newEntity->id != 0) {
            if (empty($this->settings->locked)) {
                $newEntity->childs = array();
                foreach ($this->childs as $child) {
                    $newEntityChild = $child->duplicate($newEntity, $changeName);
                    if ($newEntityChild !== false) {
                        $newEntity->childs[] = $newEntityChild;
                    }
                }
            }
            return $newEntity;
        }
        return false;
    }

    /**
     * Traitement pour mettre à jour les vues enfant utilisées dans les vues
     * lors de la duplication
     * @param $oldIdChild identifiant de l'enfant à dupliquer
     * @param $newIdChild identifiant de l'enfant dupliqué
     */
    public function updateViewChild($oldIdChild = 0, $newIdChild = 0)
    {
    }

    /**
     * Récupère l'item associé à une vue spécifique.
     *
     * @param mixed $searchedView La vue recherchée.
     * @param mixed $item
     * @return mixed|null L'item associé ou null s'il n'est pas trouvé.
     */
    public function getAssociatedItemOfView($searchedView = null, $item = null)
    {
        if (empty($searchedView) || empty($item)) {
            return null;
        }

        switch ($this->type) {
            case RootView::IDS_TYPE["Pmb\DSI\Models\View\WYSIWYGView\WYSIWYGView"]:
                if (!isset($this->settings->layer) || empty($this->settings->layer->blocks)) {
                    return null;
                }

                $block = $this->getViewBlockById($searchedView->id, $this->settings->layer->blocks);
                if (empty($block) && empty($block->itemSelected)) {
                    return null;
                }

                return $item->fetchChildById($block->itemSelected);

            default:
                if ($this->id == $searchedView->id) {
                    return $item;
                }
                break;
        }

        return null;
    }

    /**
     * Récupère les niveaux des types de vues
     *
     * @return array
     */
    public function getLevels()
    {
        global $msg;

        $levels = [
            "level_1" => [
                [
                    "label" => $msg["dsi_view_level_1_simple"],
                    "value" => "simple"
                ],
                [
                    "label" => $msg["dsi_view_level_1_aggregator"],
                    "value" => "aggregator"
                ]
            ],
            "level_2" => [
                [
                    "label" => $msg["dsi_view_level_2_basic"],
                    "value" => "basic"
                ],
                [
                    "label" => $msg["dsi_view_level_2_expert"],
                    "value" => "expert"
                ]
            ],
            "level_3" => [
                [
                    "label" => $msg["dsi_view_level_3_enriched"],
                    "value" => "enriched"
                ],
                [
                    "label" => $msg["dsi_view_level_3_plain_text"],
                    "value" => "plain_text"
                ],
                [
                    "label" => $msg["dsi_view_level_3_other_format"],
                    "value" => "other_format"
                ]
            ],
            "level_4" => [
                [
                    "label" => $msg["dsi_view_level_4_all_type"],
                    "value" => "all_type"
                ]
            ]
        ];

        $entitiesTypes = HelperEntities::get_entities_labels();
        $formatedTypes = [];

        foreach ($entitiesTypes as $key => $value) {
            $formatedTypes[] = [
                "label" => $value,
                "value" => HelperEntities::get_item_from_type($key)
            ];
        }

        $levels["level_4"] = array_merge($levels["level_4"], $formatedTypes);

        return $levels;
    }
}
