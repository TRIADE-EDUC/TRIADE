<?php

// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RegistrationController.php,v 1.12.2.1 2024/09/05 08:44:06 gneveu Exp $

namespace Pmb\Animations\Opac\Controller;

use Pmb\Common\Opac\Controller\Controller;
use Pmb\Animations\Opac\Views\AnimationsView;
use Pmb\Animations\Opac\Models\RegistrationModel;
use Pmb\Common\Helper\HashModel;
use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Models\RegistredPersonModel;
use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Orm\RegistrationOrm;
use Pmb\Common\Library\RGAA\RGAABuilder;

class RegistrationController extends Controller
{
    public function proceed($action = "")
    {
        switch ($action) {
            case "save":
                return $this->saveRegistrationAction($this->data);
            case "add":
                return $this->addRegistrationAction(intval($this->data->id_animation), intval($this->data->id_empr), $this->data->numDaughtersAnimation);
            case "view":
                return $this->viewRegistrationAction();
            case "list":
                return $this->listRegistrationAction($this->data->empr_id);
            case "delete":
                return $this->deleteRegistrationAction(intval($this->data->id_registration), $this->data->hash, intval($this->data->id_person));
            default:
                return "";
        }
    }

    public function saveRegistrationAction()
    {
        if (!empty($_SESSION['registrationList'])) {
            // Si registrationList est rempli c'est que l'utilisateur est déjà inscript
            return array("success" => true);
        } else {
            $result = $this->check_captcha($this->data->captcha_code);
            if ($result['success']) {
                return RegistrationModel::addRegistration($this->data);
            } else {
                return $result;
            }
        }
    }

    public function viewRegistrationAction()
    {
        global $pmb_gestion_devise, $opac_rgaa_active, $msg;

        if (empty($_SESSION['registrationList'])) {
            return $this->notAllowedAction();
        } else {
            $ids = $_SESSION['registrationList'];
            unset($_SESSION['registrationList']);

            $registrationList = RegistrationModel::getRegistrationByList($ids);
            RGAABuilder::$title = \common::format_hidden_title(
                sprintf(
                    $msg['rgaa_registration_details_animation'],
                    $registrationList[0]->animation->name
                )
            );
            $animView = new AnimationsView("animations/registration", [
                "action" => "save",
                "formData" => [
                    "registrationList" => $registrationList,
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
                    ],
                    'globals' => [
                        'pmbDevise' => html_entity_decode($pmb_gestion_devise),
                        'opac_rgaa_active' => intval($opac_rgaa_active)
                    ]
                ]
            ]);

            print $animView->render();
        }
    }

    public function addRegistrationAction(int $id_animation, int $id_empr = 0, string $numDaughtersAnimation = "")
    {
        global $msg;
        if (!AnimationOrm::exist($id_animation)) {
            http_response_code(404);
            print $msg['not_allowed'];
            print "<script>window.location='./index.php?lvl=animation_see'</script>";
            return;
        }
        $animationModel = new AnimationModel($id_animation);
        // Inscription multiple mais aucune animations selectionne, on refuse
        if ($animationModel->checkChildrens() && empty($numDaughtersAnimation)) {
            print $msg['not_allowed'];
            print "<script>window.location='./index.php?lvl=animation_see&id=".intval($animationModel->id)."'</script>";
            return;
        }

        $formData = RegistrationModel::getFormData($id_animation, $numDaughtersAnimation);
        $formData['idEmpr'] = $id_empr;

        if (!empty($id_empr) && !empty($formData['listDaughters'])) {
            foreach ($formData['listDaughters'] as $animation) {
                $animation->emprAlreadyRegistred($id_empr);
            }
        }

        $animView = new AnimationsView("animations/registration", [
            "registration" => RegistrationModel::getNewRegistration($id_animation, $id_empr),
            "formData" => $formData,
            "action" => "add"
        ]);
        $animView->use_captcha();

        RGAABuilder::$title = \common::format_hidden_title(
            sprintf(
                $msg['rgaa_registration_animation'],
                $animationModel->name
            )
        );
        print $animView->render();
    }

    public function listRegistrationAction(int $emprId)
    {
        $view = new AnimationsView('animations/empr', [
            'registrations' => RegistrationModel::getEmprRegistrationsList($emprId),
            'action' => 'list'
        ]);
        print $view->render();
    }

    public function deleteRegistrationAction(int $idRegistration, string $hash, int $idPerson = 0)
    {
        global $opac_rgaa_active, $msg;
        if (!RegistrationOrm::exist($idRegistration)) {
            http_response_code(404);
            return $this->notAllowedAction();
        }
        try {
            $registrationModel = new RegistrationModel($idRegistration);
        } catch (\Exception $e) {
            // On n'as réussi à récupérer l'inscription
            return $this->notAllowedAction();
        }

        $param = $registrationModel->idRegistration.$registrationModel->date.$registrationModel->numAnimation;

        $hashModel = new HashModel();

        // Le hash est valide et correspond bien à l'inscription
        if (false === $hashModel->verifeHash($hash, $param) && $registrationModel->hash !== $hash) {
            return $this->notAllowedAction();
        }

        try {
            $registredPersonModel = new RegistredPersonModel($idPerson);
        } catch (\Exception $e) {
            // On n'as réussi à récupérer la personne inscrite
            return $this->notAllowedAction();
        }

        $isContact = false;
        // La personne de contact n'est pas forcement inscripte pour l'animation
        if (empty($idPerson) || ($registrationModel->numEmpr === $registredPersonModel->numEmpr)) {
            // si c'est la personne de contact qui ce désinscrit on supprime tout
            $isContact = true;
        }

        if (empty($registrationModel->idRegistration)) {
            return $this->notAllowedAction();
        }

        $registrationModel->delete($isContact, $idPerson);

        $animationModel = new AnimationModel($registrationModel->numAnimation);
        RGAABuilder::$title = \common::format_hidden_title(
            sprintf(
                $msg['rgaa_unsubscribre_animation'],
                $animationModel->name
            )
        );
        $view = new AnimationsView('animations/registration', [
            'action' => 'delete',
            "formData" => array(
                'animation' => $animationModel,
                'globals' => [
                    'opac_rgaa_active' => intval($opac_rgaa_active)
                ]
            )
        ]);
        print $view->render();
    }

    protected function notAllowedAction()
    {
        global $msg, $opac_rgaa_active;

        if ($opac_rgaa_active) {
            print "<h1>" . $msg['not_allowed'] . "</h1>";
        } else {
            print $msg['not_allowed'];
        }

        print "<script>window.location='./index.php'</script>";
        return false;
    }
}
