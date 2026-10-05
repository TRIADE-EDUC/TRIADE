<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkAPIController.php,v 1.2 2022/03/01 08:26:35 tsamson Exp $
namespace Pmb\Ark\Controller;

use Pmb\Ark\Models\NaanModel;
use Pmb\Ark\Models\ArkModel;

class ArkAPIController
{

    /**
     *
     * @var int
     */
    protected $naan;

    /**
     *
     * @var string
     */
    protected $identifier;

    /**
     *
     * @var string
     */
    protected $qualifiers;

    public function __construct()
    {
    }

    public function resolve($naan, $identifier, $qualifiers = "")
    {
        global $opac_url_base;
        
        $this->naan = $naan;
        $this->identifier = $identifier;
        $this->qualifiers = $qualifiers;
        $this->checkNaan();
        $url = ArkModel::resolve($this->naan, $this->identifier, $this->qualifiers);
        if (strpos($url, $opac_url_base) === false) {
            $url = $opac_url_base.$url;
        }
        header("Location: ".$url, true, 302);
    }
    
    /**
     * 
     */
    private function checkNaan()
    {
        global $opac_url_base;
        
        $naanModel = new NaanModel();
        $naanList = $naanModel->getNaanData();
        if(!in_array($this->naan, $naanList['list'])){
            header("Location: $opac_url_base", true, 302);
        }
    }
}