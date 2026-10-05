<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConditionInterfaceModel.php,v 1.1 2022/02/08 15:26:47 qvarin Exp $
namespace Pmb\CMS\Models;

interface ConditionInterfaceModel
{

    /**
     *
     * @return bool
     */
    public function check(): bool;
}