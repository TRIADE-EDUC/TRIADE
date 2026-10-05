<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionsPrivateView.php,v 1.1 2023/10/25 10:39:26 rtigero Exp $

namespace Pmb\DSI\Opac\Views;
use Pmb\Common\Opac\Views\VueJsView;

class DiffusionsPrivateView extends VueJsView
{
    protected $name = "diffusionsPrivate";
    protected $data = [];
    public function __construct(string $name, $data = [], $path = "")
    {
        global $opac_url_base;
        
        $this->name = $name;
        $this->data = $data;
        $this->data["webservice_url"] = $opac_url_base . "rest.php/dsiOpac/";
        if (! empty($path)) {
            $this->path = $path;
        }
    }
}