<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DjangoView.php,v 1.25.4.1 2025/05/21 08:03:11 rtigero Exp $
namespace Pmb\DSI\Models\View\DjangoView;

use Pmb\Animations\Models\AnimationModel;
use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Models\View\CustomizableView;
use Pmb\DSI\Models\Item\Item;
use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Helper\SubscriberHelper;

class DjangoView extends CustomizableView
{

    protected $entityNamespace;

    protected $entityPluralNamespace;

    protected $html;

    protected $templatePath;

    public function render(Item $item, int $entityId, int $limit, string $context)
    {
        global $use_opac_url_base;
        $use_opac_url_base = 1;

        if (! isset($this->settings->entityType) || ! isset($this->settings->templateDirectory) || ! isset($this->settings->html)) {
            return "";
        }

        $this->setData();
        $data = $this->getDataFromContext($item, $context);

        if (empty($data)) {
            return "";
        }

        $this->filterData($data, $entityId);
        $this->limitData($data, $limit);
        $this->formatData($data, $item->type);

        file_put_contents($this->templatePath, $this->settings->html);

        $this->html = "<div id=\"dsi_diffusion_view\">";
        if (! empty($this->templatePath)) {
            \H2o::addLookup([SubscriberHelper::class, 'h2oLookup']);
            \H2o::addLookup([LookupHelper::class, 'h2oLookup']);
            $h2o = \H2o_collection::get_instance($this->templatePath);
            $h2oContext = array_merge(array(
                $this->entityPluralNamespace => $data
            ), $this->getH2oAdditionnalContext());
            $this->html .= $h2o->render($h2oContext);
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
        if (! isset($type)) {
            $type = $this->settings->entityType;
        }
        if (! isset($namespace)) {
            $namespace = $this->entityNamespace;
        }
        foreach ($data as $id => $value) {
            switch ($type) {
                case TYPE_NOTICE:
                    $data[$id] = [];
                    $data[$id]['object'] = new \record_datas($id);

                    $h2o = \H2o_collection::get_instance($this->getTemplate($data[$id]['object'], $type));
                    $data[$id]['content'] = $h2o->render(array(
                        $namespace => $data[$id]['object']
                    ));
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

    protected function setData()
    {
        global $base_path;

        $this->entityNamespace = HelperEntities::get_entities_namespace()[$this->settings->entityType];
        $this->entityPluralNamespace = HelperEntities::get_entities_namespace_plural()[$this->settings->entityType];
        $this->templatePath = $base_path . '/temp/' . LOCATION . '_dsi_view_django_template_content_' . $this->id;
    }
}
