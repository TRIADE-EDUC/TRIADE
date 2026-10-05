<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchFormView.php,v 1.2 2023/09/04 14:53:06 tsamson Exp $

namespace Pmb\Searchform\Opac\Views;

class SearchformView
{

    private $name = "";

    protected $data = [];

    protected $path = "./includes/templates/vuejs/";

    protected $distPath = "./includes/javascript/vuejs/";

    /**
     */
    public function __construct(string $name, $data = [], $path = "")
    {
        $this->name = $name;
        $this->data = $data;
        if (! empty($path)) {
            $this->path = $path;
        }
    }

    public function render()
    {
        $content = "";
        if (file_exists($this->path . $this->name . "/" . basename($this->name) . ".html")) {
            $content = file_get_contents($this->path . $this->name . "/" . basename($this->name) . ".html");
        }
        $content .= "<script type='text/javascript'>var \$data = " . \encoding_normalize::json_encode($this->data) . ";</script>";
        $content .= "<script type='text/javascript' src='" . $this->distPath . $this->name . ".js'></script>";
        return $content;
    }
}

