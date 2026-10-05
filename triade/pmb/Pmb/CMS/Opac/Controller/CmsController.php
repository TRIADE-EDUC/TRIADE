<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsController.php,v 1.4 2022/04/15 12:16:06 dbellamy Exp $
namespace Pmb\CMS\Opac\Controller;

use Pmb\Common\Opac\Controller\Controller;
use Pmb\CMS\Library\Build\PortalBuild;

class CmsController extends Controller
{

    public function proceed()
    {     
        $portalBuild = new PortalBuild();
        return $portalBuild->transformHTML($this->data->html);
    }
}