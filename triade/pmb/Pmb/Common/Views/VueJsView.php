<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: VueJsView.php,v 1.13 2024/03/20 14:03:08 jparis Exp $

namespace Pmb\Common\Views;

class VueJsView
{
    protected $name = "";

    protected $data = [];

    protected $path = "./includes/templates/vuejs/";

    protected $distPath = "./javascript/vuejs/";

    public function __construct(string $name, $data = [], $path = "")
    {
        $this->name = $name;
        $this->data = $data;
        if (!empty($path)) {
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

        $content .= "<script type='text/javascript'>var \$data = " . (\encoding_normalize::json_encode($this->data) ?? '{}') . "</script>".PHP_EOL;

        if(!$babelHasImport) {
            $content .= $this->scriptTemplate($this->distPath . "babel-polyfill.js");
            $babelHasImport = true;
        }

        $content .= $this->scriptTemplate($this->distPath . $this->name . ".js");
        return $content;
    }

    /**
     *
     * @param string $script
     * @return string
     */
    protected function scriptTemplate(string $script)
    {
        $modificationTime = filemtime($script);
        $modificationTime = $modificationTime === false ? time() : $modificationTime;

        return "<script type='text/javascript' src='{$script}?{$modificationTime}' defer></script>".PHP_EOL;
    }
}
