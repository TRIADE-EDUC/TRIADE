<?php

// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: StatusController.php,v 1.5.8.1.2.1 2025/03/07 10:44:56 jparis Exp $

namespace Pmb\Animations\Controller;

use Pmb\Common\Views\VueJsView;
use Pmb\Animations\Models\AnimationStatusModel;
use Pmb\Animations\Orm\AnimationStatusOrm;

class StatusController
{
    /**
     *
     * @var string
     */
    public $action = "list";

    public function proceed(string $action = "", $data = null)
    {
        global $id;
        $this->action = $action;
        switch ($action) {
            default:
            case "list":
                return $this->listAction();
            case "delete":
                if(!isset($data->id)) {
                    return false;
                }
                
                return $this->deleteAction(intval($data->id));
            case "save":
                if(!is_object($data)) {
                    return false;
                }
                
                return $this->saveAction($data);
            case "add":
                return $this->addAction();
            case "edit":
                if(!isset($id)) {
                    $id = 0;
                }
                
                $id = intval($id);
                return $this->editAction($id);
            case "check":
                if(!isset($data->label)) {
                    return false;
                }
                
                return $this->checkExistStatusAction($data->label);
        }
    }

    public function listAction()
    {
        $status = AnimationStatusModel::getAnimationStatusList();
        $newVue = new VueJsView("animations/status", [
            "status" => $status,
            "action" => $this->action
        ]);
        print $newVue->render();
    }

    public function saveAction(object $data)
    {
        return AnimationStatusModel::save($data);
    }

    public function deleteAction(int $id)
    {
        if($id && 1 != $id && AnimationStatusOrm::exist($id)) {
            AnimationStatusModel::delete($id);
        } else {
            http_response_code(404);
            return $this->listAction();
        }
    }

    public function addAction()
    {
        $this->showForm(new AnimationStatusModel());
    }

    public function editAction(int $id)
    {
        if (!AnimationStatusOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }

        try {
            $status = new AnimationStatusModel($id);
        } catch (\Exception $e) {
            return $this->listAction();
        }

        $this->showForm($status);
    }

    public function checkExistStatusAction(string $label)
    {
        return AnimationStatusModel::checkExistStatus($label);
    }

    private function showForm(AnimationStatusModel $status)
    {
        $newVue = new VueJsView("animations/status", [
            "status" => $status->getEditAddData(),
            "action" => $this->action,
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
}
