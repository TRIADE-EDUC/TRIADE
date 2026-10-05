<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SimpleView.php,v 1.29.4.1 2025/05/21 08:03:10 rtigero Exp $
namespace Pmb\DSI\Models\View\SimpleView;

use Pmb\Animations\Models\AnimationModel;
use Pmb\Common\Helper\{HelperEntities, Directory};
use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Helper\SubscriberHelper;

class SimpleView extends RootView
{
    protected $entityNamespace;

    protected $html;

    public function render(Item $item, int $entityId, int $limit, string $context)
    {
        global $opac_url_base;
        global $use_opac_url_base;
        $use_opac_url_base = 1;

        if (!isset($this->settings->entityType) || !isset($this->settings->templateDirectory)) {
            return "";
        }
        $this->setData();
        $data = $this->getDataFromContext($item, $context);

        if (empty($data)) {
            return "";
        }
        $this->filterData($data, $entityId);
        $this->limitData($data, $limit);
        $this->formatData($data);

        $this->html = "<div id=\"dsi_diffusion_view\">";
        foreach ($data as $element) {
            $template = $this->getTemplate($element);
            if (is_file($template)) {
                \H2o::addLookup([SubscriberHelper::class, 'h2oLookup']);
                \H2o::addLookup([LookupHelper::class, 'h2oLookup']);
                $h2o = \H2o_collection::get_instance($template);
                $this->html .= $h2o->render([
                    $this->entityNamespace => $element,
                ]);
            }
        }
        $this->html .= "</div>";

        return $this->html;
    }

    public function preview(Item $item, int $entityId, int $limit, string $context)
    {
        return $this->formatHTMLPreview($this->render($item, $entityId, $limit, $context));
    }

    protected function formatData(&$data, $type = null, $namespace = null)
    {
        if (!isset($type)) {
            $type = $this->settings->entityType;
        }
        if (!isset($namespace)) {
            $namespace = $this->entityNamespace;
        }

        foreach ($data as $id => $value) {
            switch ($this->settings->entityType) {
                case TYPE_NOTICE:
                    $data[$id] = new \record_datas($id);
                    break;
                case TYPE_CMS_ARTICLE:
                    $data[$id] = new \cms_editorial_data($id, "article");
                    break;
                case TYPE_CMS_SECTION:
                    $data[$id] = new \cms_editorial_data($id, "section");
                    break;
                case TYPE_DOCWATCH:
                    $data[$id] =  (new \docwatch_item($id))->get_normalized_item();
                    break;
                case TYPE_ANIMATION:
                    $data[$id] =  AnimationModel::getAnimation($id);
                    break;
                default:
                    break;
            }
        }
    }

    private function setData()
    {
        $this->entityNamespace = strtolower(HelperEntities::get_entities_namespace()[$this->settings->entityType]);
    }

    public function getTemplateDirectories($entityType = 0)
    {
        $entitiesNamespace = HelperEntities::get_entities_namespace();
        $entitiesNamespace = array_map("strtolower", $entitiesNamespace);

        if (empty($entitiesNamespace) || empty($entitiesNamespace[$entityType])) {
            return [];
        }

        if (defined('GESTION')) {
            $path = "./opac_css/includes/templates/dsi/{$entitiesNamespace[$entityType]}";
        } else {
            $path = "./includes/templates/dsi/{$entitiesNamespace[$entityType]}";
        }

        $dirs = Directory::getNameDirectories($path);
        return $dirs ?? [];
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

        $path .= strtolower($this->entityNamespace) . '/' . $this->settings->templateDirectory . '/' . strtolower($this->entityNamespace) . '_in_result_display.tpl.html';
        return is_file($path) ? $path : "";
    }
}
