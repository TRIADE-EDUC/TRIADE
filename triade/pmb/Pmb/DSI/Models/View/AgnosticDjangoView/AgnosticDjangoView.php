<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AgnosticDjangoView.php,v 1.1 2023/11/22 10:14:35 rtigero Exp $
namespace Pmb\DSI\Models\View\AgnosticDjangoView;

use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Models\View\CustomizableView;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Helper\SubscriberHelper;

class AgnosticDjangoView extends CustomizableView
{

    protected $entityNamespace;

    protected $entityPluralNamespace;

    protected $html;

    protected $templatePath;

    public function render(Item $item = null, int $entityId, int $limit, string $context)
    {
        global $use_opac_url_base;
        $use_opac_url_base = 1;
        
        //On nullifie explicitement l'item car la vue est agnostique
        if(isset($item)) {
            $item = null;
        }

        if (! isset($this->settings->html)) {
            return "";
        }

        $this->setData();

        file_put_contents($this->templatePath, $this->settings->html);

        $this->html = "<div id=\"dsi_diffusion_view\">";
        if (! empty($this->templatePath)) {
            \H2o::addLookup([SubscriberHelper::class, 'h2oLookup']);
            \H2o::addLookup([LookupHelper::class, 'h2oLookup']);
            $h2o = \H2o_collection::get_instance($this->templatePath);
            $h2oContext = array_merge(array(
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

    protected function setData()
    {
        global $base_path;
        $this->templatePath = $base_path . '/temp/' . LOCATION . '_dsi_view_django_template_content_' . $this->id;
    }
}

