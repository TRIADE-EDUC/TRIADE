<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingController.php,v 1.11.6.1.2.1 2025/03/07 10:44:56 jparis Exp $

namespace Pmb\Animations\Controller;

use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Models\RegistredPersonModel;
use Pmb\Common\Models\DocsLocationModel;
use Pmb\Common\Models\MailtplModel;
use Pmb\Common\Views\VueJsView;
use Pmb\Animations\Models\MailingAnimationModel;
use Pmb\Animations\Models\MailingTypeModel;
use Pmb\Animations\Models\RegistrationModel;
use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Orm\MailingTypeOrm;

class MailingController
{
    public $data;

    /**
     *
     * @var string
     */
    public $action = "list";

    public function __construct($data)
    {
        $this->data = $data ?? new \stdClass();
        $this->getIdAnimationFromNumCaddie();
    }

    public function proceed($action = "", $data = [])
    {
        $this->action = $action;

        switch ($action) {
            case "mailing":
                return $this->mailingAction(intval($this->data->id));
            case "add":
                return $this->addMailingAction();
            case "save":
                return $this->saveMailingAction($data);
            case "delete":
                if(!isset($data->id)) {
                    return false;
                }
                
                return $this->deleteMailingAction(intval($data->id));
            case "edit":
                return $this->updateMailingTypeAction(intval($this->data->id));
            case "sendManualMail":
                return $this->sendManualMailAction($this->data);
            case "checkTypeMail":
                return $this->checkTypeMailAction($this->data);
            case "list":
            default:
                return $this->listMailingTypesAction();
        }
    }

    public static function proceedSms($type, $level, $instance)
    {
        global $empr_sms_msg_animation;

        switch ($type) {
            case 'registration':
                return MailingAnimationModel::sendRegistrationSms(2, 0, $instance, $empr_sms_msg_animation);
            default:
                break;
        }
    }

    public function getIdAnimationFromNumCaddie()
    {
        $this->data->id_animation = 0;
        if (!empty($this->data->id_empr_caddie)) {
            $this->data->id_animation = AnimationModel::getIdAnimationFromNumCaddie($this->data->id_empr_caddie);
        }
        return $this->data->id_animation;
    }

    public function getAnimation()
    {
        $this->animation = null;
        if (!empty($this->data->id_animation)) {
            $this->animation = AnimationModel::getAnimationForMailing($this->data->id_animation);
        }
        return $this->animation;
    }

    public function getUnsubscribeLink()
    {
        $link = '';
        if (!AnimationOrm::exist($this->data->id_animation)) {
            http_response_code(404);
            return $link;
        }

        if (!empty($this->data->emprunteur_datas->id) && !empty($this->data->id_animation)) {
            $registrationId = RegistrationModel::getIdRegistrationFromEmprAndAnimation($this->data->emprunteur_datas->id, $this->data->id_animation);
            $registrationPerson = RegistredPersonModel::getRegistredPersonByEmprAndRegistration($this->data->emprunteur_datas->id, $registrationId);
            $link = $registrationPerson->getUnsubscribeLink();
        }

        return $link;
    }

    public function getInfosLocs()
    {
        $infosLoc = "";

        if (!empty($this->animation)) {
            $infosLoc = DocsLocationModel::getInfosLocs($this->animation);
        }

        return $infosLoc;
    }

    public function mailingAction(int $idAnimation)
    {
        global $msg;
        // On recupere le panier d'emprunteurs et on l'alimente des inscrits
        $result = MailingAnimationModel::computeCartEmpr($idAnimation);
        $idCaddie = $result["idCaddie"];

        $can_mailin = "";
        // Certains contacts ne sont pas emprunteurs
        if (!empty($result["notEmpr"])) {
            $can_mailin = "can_mailing = confirm(\"" . $msg["mailing_contact_no_empr"] . "\")";
        }

        echo "<script>
                var can_mailing = true;
                $can_mailin
                if (can_mailing) {
                    //  On redirige vers l'etape de mailing dans les paniers
                    window.location='circ.php?categ=caddie&sub=action&quelle=mailing&action=envoi&idemprcaddie=" . $idCaddie . "&item=" . $idCaddie . "'
                } else {
                    history.go(-1);
                }
            </script>";
    }

    public function listMailingTypesAction()
    {
        $mailingsTypes = MailingTypeModel::getMailingsTypeList();
        $newVue = new VueJsView("animations/mailing", [
            "mailingsTypes" => $mailingsTypes,
            "action" => $this->action,
            "typeComIsSet" => MailingTypeModel::getTypeComIsSet()
        ]);
        print $newVue->render();
    }

    public function updateMailingTypeAction(int $id)
    {
        global $type;
        if ($id == 0 || !MailingTypeOrm::exist($id)) {
            http_response_code(404);
            $this->action = "list";
            return $this->listMailingTypesAction();
        }

        try {
            $mailingsTypes = new MailingTypeModel($id);
        } catch (\Exception $e) {
            return $this->listMailingTypesAction();
        }
        $newVue = new VueJsView("animations/mailing", [
            "mailingsTypes" => $mailingsTypes->getEditAddData(),
            "mailtplList" => MailtplModel::getMailtplList(),
            "action" => $this->action,
            "type" => $type,
            "senders" => MailingAnimationModel::getSenders(),
            "typeComIsSet" => MailingTypeModel::getTypeComIsSet(),
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

    public function addMailingAction()
    {
        global $type;
        $mailingsTypes = new MailingTypeModel();
        $newVue = new VueJsView("animations/mailing", [
            "mailingsTypes" => $mailingsTypes->getEditAddData(),
            "mailtplList" => MailtplModel::getMailtplList(),
            "action" => $this->action,
            "type" => $type,
            "senders" => MailingAnimationModel::getSenders(),
            "typeComIsSet" => MailingTypeModel::getTypeComIsSet(),
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

    public function saveMailingAction(object $data)
    {
        if (!empty($data->id)) {
            MailingTypeModel::updateMailingType($data->id, $data);
        } else {
            MailingTypeModel::addMailingType($data);
        }
    }

    public function deleteMailingAction(int $id)
    {
        if (!empty($id)) {
            MailingTypeModel::deleteMailing($id);
        }
    }

    public static function sendManualMailAction($data)
    {
        return MailingAnimationModel::computeManualMail($data);
    }

    public function checkTypeMailAction($data)
    {
        return MailingTypeModel::checkTypeMail($data);
    }
}
