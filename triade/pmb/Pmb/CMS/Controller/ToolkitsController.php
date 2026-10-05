<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ToolkitsController.php,v 1.1 2022/03/08 09:21:35 qvarin Exp $
namespace Pmb\CMS\Controller;

class ToolkitsController
{
    
    public function proceed()
    {
        global $msg;
        print "<h1 class='section-title'>{$msg['cms_build_toolkits']}</h1>";
        print \cms_toolkits::get_form();
    }
}