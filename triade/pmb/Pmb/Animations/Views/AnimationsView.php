<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationsView.php,v 1.8.4.1 2025/06/04 12:39:33 qvarin Exp $

namespace Pmb\Animations\Views;

use Pmb\Common\Views\VueJsView;

class AnimationsView extends VueJsView
{

    public function render()
    {
        global $pmb_javascript_office_editor, $javascript_path;

        $content = parent::render();
        $content .= "<script type='text/javascript'>pmb_include('$javascript_path/tinyMCE_interface.js');</script>";
        $content .= $pmb_javascript_office_editor;
        return $content;
    }
}

