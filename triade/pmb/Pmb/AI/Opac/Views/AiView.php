<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiView.php,v 1.1 2024/02/01 10:29:29 rtigero Exp $

namespace Pmb\AI\Opac\Views;

use Pmb\Common\Opac\Views\VueJsView;

class AiView extends VueJsView
{
    protected $name = "ai";
    protected $data = [];
    public function __construct(string $name, $data = [], $path = "")
    {
        $this->name = $name;
        $this->data = $data;
        if (!empty($path)) {
            $this->path = $path;
        }
    }
}
