<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AggregatedDjangoView.php,v 1.6 2023/11/28 11:29:16 rtigero Exp $

namespace Pmb\DSI\Models\View\AggregatedDjangoView;

use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\DjangoView\DjangoView;
use Pmb\DSI\Helper\SubscriberHelper;

class AggregatedDjangoView extends DjangoView
{	
    public function render(Item $item, int $entityId, int $limit, string $context)
    {   
        global $use_opac_url_base;
        $use_opac_url_base = 1;

        if (! isset($this->settings->entityType) || ! isset($this->settings->templateDirectory) || ! isset($this->settings->html)) {
            return "";
        }
        $this->setData();
        $data = array();
        $data = $this->getRecursiveData($item, $entityId, $limit, $context);

        if (empty($data)) {
            return "";
        }

        file_put_contents($this->templatePath, $this->settings->html);

        $this->html = "<div id=\"dsi_diffusion_view\">";
        if (! empty($this->templatePath)) {
            \H2o::addLookup([SubscriberHelper::class, 'h2oLookup']);
            \H2o::addLookup([LookupHelper::class, 'h2oLookup']);
            $h2o = \H2o_collection::get_instance($this->templatePath);
            $this->html .= $h2o->render($data);
        }
        $this->html .= "</div>";
        return $this->html;
    }
    protected function setData()
    {
        global $base_path;
        $this->templatePath = $base_path . '/temp/' . LOCATION . '_dsi_view_django_template_content_' . $this->id;
    }

    public function getRecursiveData($item, $entityId, $limit, $context) {
        $data = array();
        foreach($item->childs as $child) {
            if($child->type != 0) {
                $childData = $this->getDataFromContext($child, $context);

                $this->formatData($childData, $child->type, HelperEntities::get_entities_namespace()[$child->type]);
                $this->filterData($childData, $entityId);
                $this->limitData($childData, $limit);
        
                $key = HelperEntities::get_entities_namespace_plural()[$child->type];
                if (isset($data[$key])) {
                    $data[$key] = array_merge($data[$key], $childData);
                } else {
                    $data[$key] = $childData;
                }
            } else if(! empty($child->childs)){
                $key = preg_replace("( )", "_", $child->name);
                if (isset($data[$key])) {
                    $data[$key] = array_merge($data[$key], $this->getRecursiveData($child, $entityId, $limit, $context));
                } else {
                    $data[$key] = $this->getRecursiveData($child, $entityId, $limit, $context);
                }
            }
    
            $data = array_merge($data, $this->getRecursiveData($child, $entityId, $limit, $context));
        }
        return $data;
    }
}

