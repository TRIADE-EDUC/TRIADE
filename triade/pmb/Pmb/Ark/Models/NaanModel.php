<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: NaanModel.php,v 1.1 2022/02/23 13:45:50 tsamson Exp $
namespace Pmb\Ark\Models;

use Pmb\Ark\Naan;

class NaanModel
{
    /**
     * 
     */
    public function __construct()
    {
        
    }
    
    public function getNaanData()
    {
        $naan = new Naan();
        return ["list" => $naan->getNaan()];
    }
    
    public static function update($data) 
    {
        $naan = new Naan();
        if (isset($data->list)) {
           $naan->setNaan($data->list);
        }
        return $naan->save();
    }
}