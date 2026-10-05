<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WYSIWYGPDFView.php,v 1.4 2023/07/26 13:41:08 qvarin Exp $

namespace Pmb\DSI\Models\View\WYSIWYGPDFView;

use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\Item\Item;
use Spipu\Html2Pdf\Html2Pdf;
use Pmb\DSI\Models\View\WYSIWYGView\WYSIWYGView;
use Pmb\DSI\Models\View\WYSIWYGPDFView\Render\PDFRenderer;

class WYSIWYGPDFView extends WYSIWYGView
{
    public function render(Item $item, int $entityId, int $limit, string $context)
    {
        $this->entityId = $entityId;
        $this->limit = $limit;
        $this->context = $context;

        $rootElement = $this->settings->layer->blocks[0];
        if (empty($rootElement)) {
            return "";
        }

        $diffusion = new Diffusion($entityId);
        $name = "attachment" . $this->id;

        foreach ($diffusion->settings->attachments as $attachment) {
            if ($attachment->view == $this->id) {
                $name = $attachment->name;
            }
        }

        $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', 0);
        $renderer = new PDFRenderer($this, $item);

        $html2pdf->setTestTdInOnePage(false);
        $html2pdf->writeHTML($renderer->render($rootElement, true));
        $html2pdf->pdf->SetTitle($name);
        $content = $html2pdf->output($name, 'S');

        return [
            "nomfichier" => $name . ".pdf",
            "contenu" => $content,
        ];
    }

    public function preview(Item $item, int $entityId, int $limit, string $context)
    {
        return $this->render($item, $entityId, $limit, $context);
    }

    protected function formatHTML(string $body, bool $html5 = true)
    {
        global $opac_default_style;

        $html = \HtmlHelper::getInstance()->getStyleOpac($opac_default_style);
        $html .= $body;
        return $html;
    }
}
