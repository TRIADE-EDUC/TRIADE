<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RegistrationModel.php,v 1.28.2.1 2024/06/07 08:31:40 qvarin Exp $
namespace Pmb\Animations\Opac\Models;

use Pmb\Animations\Models\RegistrationModel as Registration;
use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Models\RegistredPersonModel;
use Pmb\Animations\Orm\RegistredPersonOrm;
use Pmb\Animations\Orm\RegistrationOrm;
use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\EmprModel;
use Pmb\Common\Helper\HashModel;
use Pmb\Animations\Models\MailingTypeModel;
use Pmb\Animations\Models\MailingAnimationModel;
use Pmb\Common\Models\MailtplModel;
use Pmb\Animations\Orm\MailingTypeOrm;
use Pmb\Common\Models\ComptesModel;

class RegistrationModel extends Registration
{

    public static function getNewRegistration(int $numAnimation, int $id_empr = 0)
    {
        $registration = new RegistrationModel();

        $registration->numAnimation = $numAnimation;
        $registration->barcode = '';
        $registration->numOrigin = 1;
        if (!empty($id_empr) && $id_empr > 0) {
            $emprModel = new EmprModel($id_empr);
            $registration->barcode = $emprModel->emprCb;
            $registration->name = $emprModel->emprNom;
            $registration->email = $emprModel->emprMail;
            $registration->phoneNumber =  "";
            if (!empty($emprModel->emprTel1)) {
                $registration->phoneNumber =  $emprModel->emprTel1;
            } elseif (!empty($emprModel->emprTel2)) {
                $registration->phoneNumber =  $emprModel->emprTel2;
            }
        }
        if($registration->numRegistrationStatus == 0){
            $registration->numRegistrationStatus = self::PENDING_VALIDATION;
        }
        $registration->registrationListPerson = array();

        return $registration;
    }

    public static function getRegistrationByList(array $registrationList)
    {
        $result = array();
        foreach ($registrationList as $registrationId) {
            $registration = new RegistrationModel($registrationId);
            $registration->fetchRegistrationListPerson();
            $registration->fetchAnimation();
            $registration->animation->fetchEvent();
            $registration->animation->fetchPrices();
            $registration->fetchRegistrationStatus();
            $registration->barcode = "";
            if (!empty($registration->numEmpr)) {
                $emprModel = new EmprModel($registration->numEmpr);
                $registration->barcode = $emprModel->emprCb ?? "";
            }
            if (! empty($registration->animation->event)) {
                $registration->animation->event = $registration->animation->getFormatDate($registration->animation->event);
            }
            $result[] = $registration;
        }
        return $result;
    }

    public function fetchAnimation()
    {
        if (! empty($this->animation)) {
            return $this->animation;
        }
        $this->animation = null;
        if (! empty($this->numAnimation)) {
            $this->animation = new AnimationModel($this->numAnimation);
            $this->animation->fetchQuotas();
            $this->animation->description = nl2br($this->animation->description);
        }
        return $this->animation;
    }

    public static function addRegistration(object $data)
    {
        global $msg, $opac_animations_only_empr;
        global $pmb_gestion_animation, $pmb_gestion_financiere;

        if (! self::registrationAllowed()) {
            return array(
                "success" => false,
                "animations" => array(),
                "message" => $msg['animation_registration_unauthorized']
            );
        }

        if (($opac_animations_only_empr || !empty($data->barcode)) && !EmprModel::ValidBarcode($data->barcode)) {
            return array(
                "success" => false,
                "animations" => array(),
                "message" => $msg['animation_registration_error_contact_barcode']
            );
        }

        if (empty($data->name) || empty($data->numAnimation) || empty($data->email)) {
            return array(
                "success" => false,
                "animations" => array()
            );
        }

        if (empty($data->animationsSelected) && empty(AnimationModel::getDaughterList($data->numAnimation))) {
            $data->animationsSelected[] = $data->numAnimation;
        }

        if (! empty($data->registrationListPerson)) {
            foreach ($data->registrationListPerson as $index => $person) {

                if (($opac_animations_only_empr || !empty($person->barcode)) && !EmprModel::ValidBarcode($person->barcode)) {
                    return array(
                        "success" => false,
                        "animations" => array(),
                        "message" => sprintf($msg['animation_registration_error_barcode'], $person->name)
                    );
                }

                if (empty($person->name)) {
                    return array(
                        "success" => false,
                        "animations" => array(),
                        "message" => sprintf($msg['animation_registration_error_name'], $index)
                    );
                }
            }
        }

        $registrationList = array();
        $animationList = array();

        // Dans le cas de base (simple) on dit que l'on doit passer par une reservation, puis une confirmation en Gestion ICI on gére la reservation
        $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_REGISTRATION);

        if (empty($data->registrationListPerson)) {
            $personContact = new \stdClass();
            $personContact->numRegistration = $data->numRegistration ?? 0;
            $personContact->barcode = $data->barcode;
            $personContact->name = $data->name;
            $personContact->numPrice = $data->numPrice ?? 1;
            $data->registrationListPerson[] = $personContact;
        }

        foreach ($data->animationsSelected as $idAnimation) {
            $anim = new AnimationModel($idAnimation);
            $anim->fetchQuotas();
            if (
                ($anim->internetQuota >= 0) &&
                ($anim->allQuotas['availableQuotas']['internet'] || $anim->internetQuota == 0)
            ) {
                // On peut s'inscrire
                if ($anim->autoRegistration) {
                    // Validation automatique
                    $data->numRegistrationStatus = self::VALIDATED;
                    $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_CONFIRMATION);
                } else {
                    $data->numRegistrationStatus = self::PENDING_VALIDATION;
                }
            } elseif ($anim->allowWaitingList) {
                // Dans le cas d'une inscription local (et qu'il n'y à plus de place), on inscrit sur liste d'attente
                if ($anim->allQuotas['availableQuotas']["internet"] < count($data->registrationListPerson)) {
                    $data->numRegistrationStatus = self::WAITING_LIST;
                }
            } else {
                continue;
            }

            $registration = new RegistrationOrm();
            $registration->nb_registered_persons = count($data->registrationListPerson);
            $registration->name = $data->name;
            $registration->num_animation = $idAnimation;
            $animationList[] = $idAnimation;
            $registration->date = date('Y-m-d H:i:s');

            if (! empty($data->phoneNumber) && Helper::isValidPhone($data->phoneNumber)) {
                $registration->phone_number = $data->phoneNumber;
            }
            if (! empty($data->email) && Helper::isValidMail($data->email)) {
                $registration->email = $data->email;
            }
            if (! empty($data->numRegistrationStatus)) {
                $registration->num_registration_status = $data->numRegistrationStatus;
            }

            if (! empty($data->barcode)) {
                $emprModel = EmprModel::getEmprByCB($data->barcode);
                $registration->num_empr = $emprModel->idEmpr;
            }

            if (! empty($data->numOrigin)) {
                $registration->num_origin = $data->numOrigin;
            }

            $registration->save();

            $param = $registration->{RegistrationOrm::$idTableName} . $registration->date . $registration->nb_registered_persons;
            $hashModel = new HashModel();

            $registration->hash = $hashModel->generateHash($param);
            $registration->save();

            $registrationList[] = $registration->id_registration;

            if (! empty($data->registrationListPerson)) {
                foreach ($data->registrationListPerson as $person) {
                    $person->numRegistration = $registration->id_registration;
                    if (! empty($person->barcode)) {
                        $emprModel = EmprModel::getEmprByCB($person->barcode);
                        $person->numEmpr = $emprModel->idEmpr;
                    }
                    if (! empty($person->animations)) {
                        foreach ($person->animations as $numAnimation => $animations) {
                            if ($numAnimation == $idAnimation) {
                                $person->personCustomsFields = $animations->personCustomsFields;
                                $person->numAnimation = $animations->numAnimation;
                                $person->numPrice = $animations->numPrice;
                            }
                        }
                    }
                    RegistredPersonModel::addRegistredPerson($person);
                }
            }

            $registrationModel = new RegistrationModel($registration->{RegistrationOrm::$idTableName});

            if ($pmb_gestion_animation && $pmb_gestion_financiere && self::VALIDATED == $data->numRegistrationStatus) {
                static::registrationAccount($registrationModel, $data->registrationListPerson, $data->numRegistrationStatus, ComptesModel::ACCOUNT_SENS_DEBIT);
            }

            // Generation du mail pour l'animation et la personne de contact
            if (! empty($maillingTypeOrm) && ! empty($maillingTypeOrm[0])) {
                $registrationModel = new RegistrationModel($registration->{RegistrationOrm::$idTableName});
                $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);
                $temp = array();
                MailingAnimationModel::sendMail(
                    [ $registrationModel ],
                    $idAnimation,
                    $template,
                    $temp,
                    $maillingTypeOrm[0]->num_sender,
                    RegistrationModel::generateICal($registrationModel)
                );
            }

            // Generation d'un email pour les personnes qui on actives : Alerter par mail des nouvelles inscriptions aux animations OPAC
            $params = ["user_email", "user_email_recipient", "nom"];
            $conditions = [
                "user_alert_animation_mail" => "1",
            ];
            $users = Helper::getUsersByFields($params, $conditions);
            if (!empty($users)) {
                $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_SEND_TO_BIBLI);
                if (!empty($maillingTypeOrm)) {
                    $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);
                    foreach ($users as $user) {
                        MailingAnimationModel::sendMailToBibli($registrationModel, $user, $template, $maillingTypeOrm[0]->num_sender);
                    }
                }
            }

        }

        $_SESSION['registrationList'] = $registrationList;

        $success = true;
        // La personne est inscrite a aucune animation
        if (empty($animationList)) {
            $success = false;
        }

        return array(
            "success" => $success,
            "animations" => $animationList
        );
    }

    public static function registrationAllowed()
    {
        global $opac_animations_only_empr, $id_empr;

        if ($opac_animations_only_empr && empty($id_empr)) {
            return false;
        }
        return true;
    }

    public function delete(bool $isContact = false, int $idRegistrationPerson = 0)
    {
        if (!empty($this->idRegistration)) {

            $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_ANNULATION);

            if ($isContact) {
                static::deleteRegistration($this->idRegistration);
            } else {

                $registredPerson = new RegistredPersonOrm($idRegistrationPerson);
                $registration = new RegistrationOrm($registredPerson->num_registration);
                $registration->nb_registered_persons -= 1;
                $registration->save();

                static::reviewRegistration($registration, 1);

                $registredPerson->delete();

                // Generation du mail pour l'animation et la personne de contact
                // si $is_contact == true le mail est envoye dans la methode deleteRegistration
                if (!empty($maillingTypeOrm) && !empty($maillingTypeOrm[0])) {
                    $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);
                    $temp = array();
                    MailingAnimationModel::sendMail(
                        [ $this ],
                        $this->numAnimation,
                        $template,
                        $temp,
                        $maillingTypeOrm[0]->num_sender
                    );
                }
            }
        } else {
            return false;
        }
    }
}
