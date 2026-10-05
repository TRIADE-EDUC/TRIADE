<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CalendarController.php,v 1.2.6.1.2.1 2025/03/07 10:44:56 jparis Exp $

namespace Pmb\Animations\Controller;

use Pmb\Common\Views\VueJsView;
use Pmb\Animations\Models\AnimationCalendarModel;
use Pmb\Animations\Orm\AnimationCalendarOrm;

class CalendarController
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
                if(!isset($data->name)) {
                    return false;
                }
                
                return $this->checkExistTypeAction($data->name);
        }
    }

    public function listAction()
    {
        $calendar = AnimationCalendarModel::getAnimationCalendarList();
        $newVue = new VueJsView("animations/calendar", [
            "calendar" => $calendar,
            "action" => $this->action
        ]);
        print $newVue->render();
    }

    public function saveAction(object $data)
    {
        return AnimationCalendarModel::save($data);
    }

    public function deleteAction(int $id)
    {
        if ($id && 1 != $id) {
            AnimationCalendarModel::delete($id);
        }
    }

    public function addAction()
    {
        $this->showForm(new AnimationCalendarModel());
    }

    public function editAction(int $id)
    {
        if (!AnimationCalendarOrm::exist($id)) {
            http_response_code(404);
            $this->action = "list";
            return $this->listAction();
        }

        $calendar = new AnimationCalendarModel($id);
        $this->showForm($calendar);
    }

    public function checkExistTypeAction(string $name)
    {
        return AnimationCalendarModel::checkExistCalendar($name);
    }

    private function showForm(AnimationCalendarModel $calendar)
    {
        $newVue = new VueJsView("animations/calendar", [
            "calendar" => $calendar->getEditAddData(),
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
