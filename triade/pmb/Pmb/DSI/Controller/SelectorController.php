<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SelectorController.php,v 1.1 2023/03/29 14:39:32 qvarin Exp $
namespace Pmb\DSI\Controller;

class SelectorController extends CommonController
{
    protected const VUE_NAME = "";

    public function search()
    {
        if (
            empty($this->data) ||
            empty($this->data->selector) ||
            empty($this->data->selector->namespace) ||
            !class_exists($this->data->selector->namespace)
        ) {
            $this->ajaxError('Unknown selector !');
        }

        $selector = new $this->data->selector->namespace($this->data->selector);
        $this->ajaxJsonResponse($selector->trySearch());
    }
}

