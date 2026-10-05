<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConditionFRBRModel.php,v 1.1 2022/02/07 09:01:59 jparis Exp $
namespace Pmb\CMS\Models;

class ConditionFRBRModel extends ConditionModel
{

    /**
     *
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }
}