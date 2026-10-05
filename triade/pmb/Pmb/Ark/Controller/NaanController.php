<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: NaanController.php,v 1.2 2023/05/04 09:36:38 gneveu Exp $
namespace Pmb\Ark\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Views\VueJsView;
use Pmb\Ark\Models\NaanModel;

class NaanController extends Controller
{

    public $action;

    /**
     *
     * @param string $action
     * @return
     */
    public function proceed(string $action = "", $data = null)
    {
        $this->action = $action;
        switch ($action) {
            default:
            case "edit":
                return $this->editAction();
                break;
            case "save":
                return $this->saveAction($data);
                break;
        }
    }

    public function editAction()
    {
        $naan = new NaanModel();
        $newVue = new VueJsView("ark/naan", [
            "action" => $this->action,
            "naan" => $naan->getNaanData(),
            'img' => [
                'plus' => get_url_icon('plus.gif'),
                'minus' => get_url_icon('minus.gif'),
                'expandAll' => get_url_icon('expand_all'),
                'collapseAll' => get_url_icon('collapse_all'),
                'tick' => get_url_icon('tick.gif'),
                'error' => get_url_icon('error.png'),
                'patience' => get_url_icon('patience.gif'),
                'sort' => get_url_icon('sort.png'),
                'iconeDragNotice' => get_url_icon('icone_drag_notice.png')
            ]
        ]);
        print $newVue->render();
    }

    public function saveAction(object $data)
    {
        NaanModel::update($data);
    }
}