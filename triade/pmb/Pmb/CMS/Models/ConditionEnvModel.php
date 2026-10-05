<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConditionEnvModel.php,v 1.2 2022/03/17 15:49:52 qvarin Exp $
namespace Pmb\CMS\Models;

class ConditionEnvModel extends ConditionModel
{

    /**
     *
     * @return bool
     */
    public function check(): bool
    {
        $this->formatData();
        $globalName = $this->data['global'] ?? "";
        if (! empty($globalName)) {
            global ${$globalName};
            return isset(${$globalName});
        }
        return true;
    }

    public function formatData()
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }
        return $this->data;
    }
}