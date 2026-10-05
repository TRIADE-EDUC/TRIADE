<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UnsubscribeView.php,v 1.1 2023/07/27 13:01:34 rtigero Exp $

namespace Pmb\DSI\Opac\Views;
use Pmb\Common\Opac\Views\VueJsView;

class UnsubscribeView extends VueJsView
{
    protected $name = "unsubscribe";
    protected $data = [];
    public function __construct(string $name, $data = [], $path = "")
    {
        $this->name = $name;
        $this->data = $data;
        if (! empty($path)) {
            $this->path = $path;
        }
    }
}