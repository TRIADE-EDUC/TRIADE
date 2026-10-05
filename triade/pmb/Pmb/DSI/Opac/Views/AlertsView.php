<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AlertsView.php,v 1.3 2023/10/27 14:14:11 jparis Exp $

namespace Pmb\DSI\Opac\Views;
use Pmb\Common\Opac\Views\VueJsView;

class AlertsView extends VueJsView
{
    protected $name = "alerts";
    protected $data = [];
    protected $path = "./includes/templates/vuejs/";
    protected $distPath = "./includes/javascript/vuejs/";
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

    public function render()
    {
        global $babelHasImport;

        $content = "";
        if (file_exists($this->path . $this->name . "/" . basename($this->name) . ".html")) {
            $content = file_get_contents($this->path . $this->name . "/" . basename($this->name) . ".html");
        }

        if(!$babelHasImport) {
            $content .= "<script type='text/javascript' src='".$this->distPath."babel-polyfill.js'></script>";
            $babelHasImport = true;
        }
        $content .= "<script type='text/javascript'>var \$data = " . \encoding_normalize::json_encode($this->data) . ";</script>";
        $content .= "<script type='text/javascript' src='" . $this->distPath . $this->name . ".js'></script>";
        
        return $content;
    }
}