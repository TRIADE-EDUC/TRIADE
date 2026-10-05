<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: VueJsView.php,v 1.6 2023/10/27 14:14:11 jparis Exp $

namespace Pmb\Common\Opac\Views;

use Pmb\Common\Views\VueJsView as ViewVueJs;
use Pmb\Common\Helper\Helper;

class VueJsView extends ViewVueJs
{
    protected $path = "./includes/templates/vuejs/";
    
    protected $distPath = "./includes/javascript/vuejs/";
    
    public function render()
    {
        global $babelHasImport;

        $content = "";
        if(file_exists($this->path.$this->name."/".basename($this->name).".html")){
            $content = file_get_contents($this->path.$this->name."/".basename($this->name).".html");
        }
        
        $explodedName = explode('/', $this->name);
        $length = count($explodedName);
        $moduleName = Helper::camelize($explodedName[$length-1]."Data");
        
        $content.= "<script type='text/javascript'>var \$".$moduleName." = ".\encoding_normalize::json_encode($this->data).";</script>";

        if(!$babelHasImport) {
            $content.= "<script type='text/javascript' src='".$this->distPath."babel-polyfill.js'></script>";
            $babelHasImport = true;
        }

        $content.= "<script type='text/javascript' src='".$this->distPath.$this->name.".js' defer></script>";
        return $content;
    }
}

