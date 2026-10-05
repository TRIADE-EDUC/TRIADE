<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalNodeInterfaceBuild.php,v 1.2 2022/02/09 11:28:57 qvarin Exp $
namespace Pmb\CMS\Library\Build;

Interface PortalNodeInterfaceBuild
{

    /**
     *
     * @return string
     */
    public function getHeaders();

    /**
     *
     * @return \DOMNode
     */
    public function buildNode();
}