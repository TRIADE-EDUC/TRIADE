<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchAutocompleteView.php,v 1.6 2023/08/23 15:10:08 rtigero Exp $

namespace Pmb\Searchform\Views;

class SearchAutocompleteView
{

    const START_SEARCH = 2;

    private $name = "";

    protected $data = [];

    protected $path = "./includes/templates/vuejs/";

    protected $distPath = "./includes/javascript/vuejs/";

    private static $count_instances = 0;

    /**
     */
    public function __construct(string $name, $data = [], $path = "")
    {
        global $opac_url_base;
        self::$count_instances++;
        
        $this->name = $name;
        $this->data = $data;
        if (! empty($path)) {
            $this->path = $path;
        }
        $this->data["webservice_url"] = $opac_url_base . "rest.php/autocomplete/";
        $this->data["start_search"] = self::START_SEARCH;
        $this->data["id"] = "searchautocomplete_" . self::$count_instances;
    }
    
    public function render()
    {
        global $opac_rgaa_active;
        $content = "";

        $this->data['rgaaActive'] = $opac_rgaa_active ?? 0;
        if (file_exists($this->path . $this->name . "/" . basename($this->name) . ".html")) {
            $content = file_get_contents($this->path . $this->name . "/" . basename($this->name) . ".html");
            $content = str_replace("!!id!!", $this->data["id"], $content);
        }
        $content .= "<script type='text/javascript'>var \$data_".self::$count_instances." = " . \encoding_normalize::json_encode($this->data) . ";</script>";
        if(self::$count_instances == "1") {
            $content .= "<script type='text/javascript' src='" . $this->distPath . $this->name . ".js'></script>";
        }
        return $content;
    }
}

