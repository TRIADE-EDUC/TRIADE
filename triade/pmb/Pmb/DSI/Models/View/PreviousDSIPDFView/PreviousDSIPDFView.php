<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PreviousDSIPDFView.php,v 1.1.4.2 2024/10/01 08:24:24 jparis Exp $

namespace Pmb\DSI\Models\View\PreviousDSIPDFView;

use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\PreviousDSIView\PreviousDSIView;
use Spipu\Html2Pdf\Html2Pdf;

class PreviousDSIPDFView extends PreviousDSIView
{

    public function render($item, int $entityId, int $limit, string $context)
    {
        $diffusion = new Diffusion($entityId);
        $name = "attachment" . $this->id;

        foreach ($diffusion->settings->attachments as $attachment) {
            if ($attachment->view == $this->id) {
                $name = $attachment->name;
            }
        }

        $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', 0);
        $render = $this->cleanHtml(parent::render($item, $entityId, $limit, $context));

        $html2pdf->setTestTdInOnePage(false);
        $html2pdf->writeHTML($render);
        $html2pdf->pdf->SetTitle($name);
        $content = $html2pdf->output($name, 'S');

        return [
            "nomfichier" => $name . ".pdf",
            "contenu" => $content,
        ];
    }

    protected function cleanHtml(string $html): string
    {
        // Remove tag script
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
        return $html;
    }
}
