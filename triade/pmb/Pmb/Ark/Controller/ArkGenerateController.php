<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkGenerateController.php,v 1.2 2022/09/09 15:23:32 tsamson Exp $
namespace Pmb\Ark\Controller;

use Pmb\Common\Views\VueJsView;
use Pmb\Common\Controller\Controller;
use Pmb\Ark\Models\ArkModel;

global $class_path;
require_once $class_path.'/netbase/netbase.class.php';

class ArkGenerateController extends Controller
{
    protected $action;
    
    public function proceed($action = '', $data = null)
    {
        $this->action = $action;
        switch ($action)
        {
            case 'start':
            case 'generate_done':
                return $this->generateAction($data);
                break;
            default:
                return $this->formAction();
        }
    }
    
    protected function formAction()
    {
        $newVue = new VueJsView("ark/generate", [
            "action" => $this->action,
            "count" => 0,
            "start" => 0,
            "next" => 0,
            'img' => [
                'plus' => get_url_icon('plus.gif'),
                'minus' => get_url_icon('minus.gif'),
                'expandAll' => get_url_icon('expand_all'),
                'collapseAll' => get_url_icon('collapse_all'),
                'tick' => get_url_icon('tick.gif'),
                'error' => get_url_icon('error.png'),
                'patience' => get_url_icon('patience.gif'),
                'sort' => get_url_icon('sort.png'),
                'iconeDragNotice' => get_url_icon('icone_drag_notice.png'),
                'jauge' => get_url_icon('jauge.png')
            ]
        ]);
        print $newVue->render();
    }
    
    protected function generateAction($data = null)
    {
        if (is_null($data)) {
            $data = new \stdClass();
        }
        if (empty($data->count)) {
            $data->count = ArkModel::getNbEntitiesWithoutArk();
        }
        if (!isset($data->start)) {
            $data->start=0;
        }
        $lot = REINDEX_PAQUET_SIZE;
        $lot = 100;
        $data->next = $data->start + $lot;
        ArkModel::generateMassArk($lot);
        //print \netbase::get_display_progress($start, $count);
        
        $newVue = new VueJsView("ark/generate", [
            "action" => $this->action,
            "count" => $data->count,
            "start" => $data->start,
            "next" => $data->next,
            'img' => [
                'plus' => get_url_icon('plus.gif'),
                'minus' => get_url_icon('minus.gif'),
                'expandAll' => get_url_icon('expand_all'),
                'collapseAll' => get_url_icon('collapse_all'),
                'tick' => get_url_icon('tick.gif'),
                'error' => get_url_icon('error.png'),
                'patience' => get_url_icon('patience.gif'),
                'sort' => get_url_icon('sort.png'),
                'iconeDragNotice' => get_url_icon('icone_drag_notice.png'),
                'jauge' => get_url_icon('jauge.png')
            ]
        ]);
        print $newVue->render();
    }
}