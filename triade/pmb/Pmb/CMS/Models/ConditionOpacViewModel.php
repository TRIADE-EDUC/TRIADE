<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConditionOpacViewModel.php,v 1.2 2022/03/30 10:30:04 qvarin Exp $
namespace Pmb\CMS\Models;

class ConditionOpacViewModel extends ConditionModel
{

    /**
     *
     * @return bool
     */
    public function check(): bool
    {
        $this->formatData();
        
        $opac_view = $this->data['opac_view'] ?? ""; 
        if ($this->opac_view_defined() && !empty($opac_view)) {
            return ($opac_view == $_SESSION['opac_view']);
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
    
    private function opac_view_defined() :bool
    {
        return isset($_SESSION['opac_view']);
    }
}