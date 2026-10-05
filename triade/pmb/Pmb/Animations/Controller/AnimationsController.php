<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationsController.php,v 1.63.2.4.2.2 2025/06/04 12:39:33 qvarin Exp $

namespace Pmb\Animations\Controller;

use Pmb\Animations\Views\AnimationsView;
use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Models\AnimationPdfModel;
use Pmb\Common\Views\VueJsView;
use Pmb\Animations\Models\AnimationStatusModel;
use Pmb\Common\Controller\Controller;
use Pmb\Common\Models\DocsLocationModel;
use Pmb\Animations\Models\RegistrationModel;
use Spipu\Html2Pdf\Html2Pdf;
use Pmb\Animations\Models\RegistrationStatusModel;
use Pmb\Animations\Event\AnimationsEvent;
use Pmb\Common\Models\MailtplModel;
use Pmb\Animations\Models\MailtplAnimationModel;
use Pmb\Animations\Models\MailingListModel;
use Pmb\Animations\Models\MailingAnimationModel;
use Pmb\Animations\Models\AnimationTypesModel;
use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Models\MailingTypeModel;
use Pmb\Common\Library\CSRF\CollectionCSRF;

class AnimationsController extends Controller
{
    /**
     *
     * @param string $action
     * @return mixed
     */
    public function proceed($action = "")
    {
        switch ($action) {
            case "edit":
                return $this->editAnimationAction(intval($this->data->id));
            case "duplicate":
                return $this->duplicateAnimationAction(intval($this->data->id));
            case "delete":
                return $this->deleteAnimationAction(intval($this->data->id), $this->data->delChildrens);
            case "add":
                return $this->addAnimationAction();
            case "view":
                return $this->viewAnimationAction(intval($this->data->id));
            case "save":
                return $this->saveAction($this->data);
            case "list":
                return $this->listAction();
            case "search":
                return $this->searchAction();
            case "saveParentChild":
                return $this->saveParentChild($this->data);
            case "gestion":
                return $this->gestionAnimationAction();
            case "printRegistrationList":
                return $this->printRegistrationListAction(intval($this->data->id));
            case "editorial":
                return $this->pluginEditorial(intval($this->data->id));
            case "mailing":
                return $this->mailing(intval($this->data->id));
            case "export":
                return $this->exportAction($this->data);
            case "repeatEventAnimation":
                return $this->repeatEventAnimationAction($this->data);
            case "deleteSelectedAnimations":
                return $this->deleteSelectedAnimationsAction($this->data);
            case "initEvent":
                return $this->initEventAction();
            case "pagination":
                return $this->paginationAction();
            default:
                return $this->formSearchAction();
        }
    }

    protected function paginationAction()
    {
        $page = $this->data->page ?? 1;
        $page--;

        if ($page <= 0) {
            $page = 0;
        }

        if (isset($this->data->numParent) && is_numeric($this->data->numParent)) {
            $this->ajaxJsonResponse(AnimationModel::paginationDaughterList($this->data->numParent, $page));
        } else {
            $this->ajaxJsonResponse(AnimationModel::pagination($page));
        }
    }

    public function listAction()
    {
        $animView = new AnimationsView("animations/animations", [
            // 'animations' => AnimationModel::getAnimationsList(),
            'pagination' => AnimationModel::pagination(),
            'action' => "list",
        ]);
        print $animView->render();
    }

    public function editAnimationAction(int $id)
    {
        if ($id == 0 || !AnimationOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }

        try {
            $animation = new AnimationModel($id);
        } catch (\Exception $e) {
            return $this->listAction();
        }

        $collectionCSRF = new CollectionCSRF();
        $animView = new AnimationsView("animations/animations", [
            "animations" => $animation->getEditAddData(),
            "formData" => AnimationModel::getFormData($id),
            "action" => "edit",
            "csrftokens" => $collectionCSRF->getArrayTokens()
        ]);

        print $animView->render();
    }

    public function duplicateAnimationAction(int $id)
    {
        if ($id == 0 || !AnimationOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }

        try {
            $animation = new AnimationModel($id);
        } catch (\Exception $e) {
            return $this->listAction();
        }

        $duplicate = true;
        $collectionCSRF = new CollectionCSRF();
        $animView = new AnimationsView("animations/animations", [
            "animations" => $animation->getEditAddData($duplicate),
            "formData" => AnimationModel::getFormData($id),
            "action" => "duplicate",
            "csrftokens" => $collectionCSRF->getArrayTokens()
        ]);

        print $animView->render();
    }

    public function viewAnimationAction(int $id)
    {
        global $class_path;

        if ($id == 0 || !AnimationOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }

        try {
            $animation = new AnimationModel($id);
        } catch (\Exception $e) {
            return $this->listAction();
        }

        require_once($class_path . '/event/events_handler.class.php');
        $event = new AnimationsEvent("animations", "template");
        $evth = \events_handler::get_instance();
        $event->set_animation_id($id);
        $evth->send($event);

        $animView = new AnimationsView("animations/animations", [
            'animations' => $animation->getViewData(true),
            'registrationList' => RegistrationModel::getOthersRegistrations($id),
            'registrationWaitingList' => RegistrationModel::getRegistrationsWaitingList($id),
            'formData' => AnimationModel::getFormData($id),
            'animationList' => AnimationModel::toArray([$animation]),
            'mailingSendList' => MailingListModel::getMailingList($id),
            'action' => 'view',
            'plugin' => array(
                'inputs' => $event->get_inputs_template(),
                'info_editorial' => $event->get_info_editorial_template()
            ),
        ]);
        print $animView->render();
    }

    public function addAnimationAction()
    {
        $anim = new AnimationModel();
        $collectionCSRF = new CollectionCSRF();
        $animView = new AnimationsView("animations/animations", [
            "animations" => $anim->getEditAddData(),
            "formData" => AnimationModel::getFormData(),
            "action" => "add",
            "csrftokens" => $collectionCSRF->getArrayTokens()
        ]);
        print $animView->render();
    }

    public function saveAction(object $data)
    {
        global $class_path;

        if (!empty($data->idAnimation)) {
            $animation = AnimationModel::updateAnimation(intval($data->idAnimation), $data);
        } else {
            $animation = AnimationModel::addAnimation($data);
        }

        require_once($class_path . '/event/events_handler.class.php');
        $event = new AnimationsEvent("animations", "save");
        $evth = \events_handler::get_instance();
        $event->set_animation_id($animation['id_animation']);
        if (!empty($data->idAnimation)) {
            $event->set_action($event::AUTOMATIC_UPDATE);
        } else {
            $event->set_action($event::AUTOMATIC_CREATE);
        }
        $evth->send($event);

        return intval($animation['id_animation']);
    }

    public function deleteAnimationAction(int $id, bool $delChildrens = false)
    {
        $animation = AnimationModel::deleteAnimation($id, $delChildrens);
        $return['success_plugins'] = $animation;
        return $return;
    }

    public function formSearchAction()
    {
        $view = new VueJsView('animations/formSearch', [
            'formData' => [
                'status' => AnimationStatusModel::getAnimationStatusList(),
                'locations' => DocsLocationModel::getLocationList(),
                'img' => [
                    'plus' => get_url_icon('plus.gif')
                ],
                'types' => AnimationTypesModel::getAnimationTypesList(),
                'communication_type' => MailingTypeModel::getMailingsTypeList(),
            ]
        ]);
        print $view->render();
    }

    public function searchAction()
    {
        $animations = [];
        foreach ($this->data->searchResult['result'] as $animationId) {
            if (!AnimationOrm::exist(intval($animationId))) {
                continue;
            }

            $anim = new AnimationModel(intval($animationId));
            $animations[] = $anim->getSimpleSearchData();
        }

        $this->data->searchResult['result'] = $animations;

        $this->ajaxResponse($this->data->searchResult);
    }

    public function saveParentChild($data)
    {
        AnimationModel::saveParentChild($data);
        return AnimationModel::getAnimationsDNDList();
    }

    public function gestionAnimationAction()
    {
        $animView = new AnimationsView("animations/animations", [
            'animations' => AnimationModel::getAnimationsDNDList(),
            'action' => "gestionAnimation"
        ]);
        print $animView->render();
    }

    public function printRegistrationListAction(int $id)
    {
        if (!AnimationOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }
        $data = AnimationPdfModel::renderRegistrationList($id);
        $html2pdf = new Html2Pdf();
        $html2pdf->writeHTML($data['template']);
        $html2pdf->pdf->SetTitle($data['title']);
        $html2pdf->output($data['fileName'], 'D');
    }

    public function pluginEditorial(int $id)
    {
        global $class_path, $base_path, $msg, $event_action;

        require_once($class_path . '/event/events_handler.class.php');
        $event = new AnimationsEvent("animations", "save");
        $evth = \events_handler::get_instance();
        $event->set_animation_id($id);
        if ($event_action == "update") {
            $event->set_action($event::MANUAL_UPDATE);
        } else {
            $event->set_action($event::MANUAL_CREATE);
        }
        $evth->send($event);

        echo '<div class="msg-perio">'.$msg['animation_save_running'].'</div>';
        if ($event->has_errors()) {
            $errors = '<div class="msg-perio">'.$msg['animation_errors'].'<ul>';
            foreach ($event->get_errors() as $error) {
                $errors .= "<li>".$error."</li>";
            }
            $errors .= '</ul></div>';
            echo $errors;
        }
        echo "<script>
                setTimeout(function () {document.location='$base_path/animations.php?categ=animations&action=view&id=".intval($id)."'}, 2000);
            </script>";
    }

    public function mailing(int $id)
    {

        global $deflt_associated_campaign;

        $action = "mailingTemplate";
        $mailDetail = array();

        if ($id == 0 || !AnimationOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }

        try {
            $animation = new AnimationModel($id);
        } catch (\Exception $e) {
            return $this->listAction();
        }

        if ($this->data->idMailingList) {
            try {
                $mail = new MailingListModel($this->data->idMailingList);
                $mailingContent = json_decode(stripslashes($mail->mailingContent));
                $listPersons = json_decode(stripslashes($mail->responseContent));
                $mailDetail = [
                    "mailtplObjet" => $mailingContent->mailtplObjet,
                    "mailtplTpl" => $mailingContent->mailtplTpl,
                    "listPersons" => $listPersons

                ];
                $action = "mailingDetail";
            } catch (\Exception $e) {
                return $this->listAction();
            }
        }

        $animView = new AnimationsView("animations/mailing", [
            "animation" => $animation,
            "registration" => RegistrationModel::getRegistrations($id),
            "action" => $action,
            "formData" => [
                "mailingTemplate" => MailtplModel::getMailtplList(),
                "selVars" => MailtplAnimationModel::getSelVars(),
                "senders" => MailingAnimationModel::getSenders(),
            ],
            'img' => [
                'plus' => get_url_icon('plus.gif'),
                'minus' => get_url_icon('minus.gif'),
                'patience' => get_url_icon('patience.gif'),
            ],
            'mailingDetail' => $mailDetail,
            'deflt_associated_campaign' => $deflt_associated_campaign
        ]);
        print $animView->render();
    }

    public function exportAction($data)
    {
        if (!AnimationOrm::exist($data->id)) {
            http_response_code(404);
            return $this->listAction();
        }
        try {
            $animation = new AnimationModel(intval($data->id));
            $animation->exportPrint($data->exportType);
            return true;
        } catch (\Exception $e) {
            return $this->listAction();
        }
    }

    public function repeatEventAnimationAction($data)
    {
        if (!AnimationOrm::exist(intval($data->idanimation))) {
            http_response_code(404);
            return $this->listAction();
        }
        try {
            $animation = new AnimationModel(intval($data->idanimation));
            $animation->getFetchAnimation();
            $animation->repeatEventAnimation($data);
            return $data->idanimation;
        } catch (\Exception $e) {
            return $this->listAction();
        }
    }

    public function deleteSelectedAnimationsAction($data)
    {
        try {
            $idParent = 0;
            foreach ($data->ids as $id) {
                if (!AnimationOrm::exist(intval($id))) {
                    continue;
                }
                $animation = new AnimationModel(intval($id));
                if (empty($idParent)) {
                    $idParent = $animation->numParent;
                }
                $animation->deleteAnimation(intval($id), true);
            }
            return $idParent;
        } catch (\Exception $e) {
            return $this->listAction();
        }
    }

    public function initEventAction()
    {
        try {
            return AnimationModel::initAnimationToArticle();
        } catch (\Exception $e) {
            return $this->listAction();
        }
    }
}
