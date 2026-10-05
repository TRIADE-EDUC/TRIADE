<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailController.php,v 1.3 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Controller;

use Pmb\Common\Controller\Controller;

abstract class ThumbnailController extends Controller
{
    /**
     * 
     * @var string
     */
    protected $action = "";

    /**
     * aiguillage
     * @param string $action
     * @param unknown $data
     * @return unknown
     */
    public function proceed($action = '', $data = null)
    {
        $this->action = $action;
        switch ($action) {
            case 'edit':
            default:
                return $this->defaultAction();
        }
    }

    /**
     * action par défaut
     */
    protected function defaultAction()
    {
    }

    /**
     * donnees de base necessaires aux vues
     * @return array
     */
    protected function getViewBaseData() : array
    {
        global $pmb_url_base;
        return [
            "url_webservice" => $pmb_url_base . "rest.php/thumbnail/",
            "action" => $this->action
        ];
    }

}

