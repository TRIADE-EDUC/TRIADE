<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalFrameInterface.php,v 1.1 2022/07/06 08:13:26 qvarin Exp $
namespace Pmb\CMS\Library\Build;

Interface PortalFrameInterface
{

    /**
     *
     * @return bool
     */
	public function checkConditions(): bool;
}