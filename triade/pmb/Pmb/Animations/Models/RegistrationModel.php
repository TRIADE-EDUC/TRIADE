<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RegistrationModel.php,v 1.75.2.5 2025/01/13 15:33:50 jparis Exp $
namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\RegistrationOrm;
use Pmb\Animations\Orm\RegistredPersonOrm;
use Pmb\Animations\Orm\PriceTypeCustomFieldValueOrm;
use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\EmprModel;
use Pmb\Animations\Controller\MailingController;
use Pmb\Animations\Library\ICalendar\AnimationIcalendar;
use Pmb\Common\Helper\HashModel;
use Pmb\Animations\Orm\MailingTypeOrm;
use Pmb\Common\Models\MailtplModel;
use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Orm\PriceOrm;
use Pmb\Common\Models\ComptesModel;
use Pmb\Common\Helper\GlobalContext;
use Pmb\Common\Library\ICalendar\ICalendar;

class RegistrationModel extends Model
{

    public const QUOTA_GLOBAL = 0;

    public const QUOTA_INTERNET = 1;

    public const PENDING_VALIDATION = 1;

    public const VALIDATED = 2;

    public const WAITING_LIST = 3;

    protected $ormName = "\Pmb\Animations\Orm\RegistrationOrm";

    public $name;

    public $barcode;

    public $email;

    public $phoneNumber;

    public $numAnimation;

    public $numOrigin;

    public $numRegistrationStatus;

    public $registrationListPerson;

    public $registrationStatus;

    public $idRegistration;

    public $nbRegisteredPersons;

    public $numEmpr;

    public $date;

    public $hash;

    public $animation;

    public $validated;

    public $rawDate;

    public $registredPersons;

    public $unsubscribeLink;

    public static function getRegistrationsWaitingList(int $num_animation = 0)
    {
        $registrationsWaitingList = [];
        $registrations = self::getRegistrations($num_animation, false);
        foreach ($registrations as $registration) {
            if ($registration->numRegistrationStatus == self::WAITING_LIST) {
                $registrationsWaitingList[] = $registration;
            }
        }
        return $registrationsWaitingList;
    }

    public static function getOthersRegistrations(int $num_animation = 0)
    {
        $othersRegistrations = [];
        $registrations = self::getRegistrations($num_animation, false);
        foreach ($registrations as $registration) {
            if ($registration->numRegistrationStatus != self::WAITING_LIST) {
                $othersRegistrations[] = $registration;
            }
        }
        return $othersRegistrations;
    }

    public static function getRegistrations(int $num_animation = 0, bool $toArray = true)
    {
        if ($num_animation) {
            $registrationsList = RegistrationOrm::find('num_animation', $num_animation, 'num_registration_status, id_registration');
        } else {
            $registrationsList = RegistrationOrm::findAll();
        }
        foreach ($registrationsList as $key => $registration) {
            $registration = new RegistrationModel(intval($registration->id_registration));
            $registration->fetchRegistrationStatus();
            $registration->fetchRegistrationListPerson();
            $registration->fetchAnimation();
            $registration->fetchValidated();
            $registration->getFormatDate();

            $registrationsList[$key] = $registration;
        }
        return $toArray ? self::toArray($registrationsList) : $registrationsList;
    }

    public static function getRegistration(int $id)
    {
        $registration = new RegistrationOrm($id);
        return $registration->toArray();
    }

    public static function deleteRegistration(int $id)
    {
        global $pmb_gestion_animation, $pmb_gestion_financiere;

        if (!RegistrationOrm::exist($id)) {
            return false;
        }

        $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_ANNULATION);

        $registration = new RegistrationOrm($id);
        $registrationModel = new RegistrationModel($id);

        $attachments = RegistrationModel::generateICal($registrationModel, true);

        $registrationModel->fetchRegistrationListPerson();
        $registrationList = $registrationModel->registrationListPerson;

        static::reviewRegistration($registration, intval($registration->nb_registered_persons));

        $registration->delete();

        $registredPersonList = RegistredPersonOrm::find("num_registration", $id);
        foreach ($registredPersonList as $person) {
            PriceTypeCustomFieldValueOrm::deleteWhere("anim_price_type_custom_origine", $person->id_person);
            $person->delete();
        }

        if ($pmb_gestion_animation && $pmb_gestion_financiere && self::VALIDATED == $registrationModel->numRegistrationStatus) {
            self::registrationAccount($registrationModel, $registrationList, $registrationModel->numRegistrationStatus, ComptesModel::ACCOUNT_SENS_CREDIT);
        }

        // Generation du mail pour l'animation et la personne de contact
        if (! empty($maillingTypeOrm) && ! empty($maillingTypeOrm[0])) {
            $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);
            $temp = array();

            MailingAnimationModel::sendMail(
                [ $registrationModel ],
                $registrationModel->numAnimation,
                $template,
                $temp,
                $maillingTypeOrm[0]->num_sender,
                $attachments,
            );
        }
    }

    public static function addRegistration(object $data)
    {
        global $pmb_gestion_animation, $pmb_gestion_financiere;
        if (empty($data->name) || empty($data->numAnimation) || empty($data->email)) {
            return false;
        }

        if (empty($data->animationsSelected) && empty(AnimationModel::getDaughterList($data->numAnimation))) {
            $data->animationsSelected[] = $data->numAnimation;
        }

        // Dans le cas de base (simple) on dit que l'on doit passer par une reservation, puis une confirmation en Gestion ICI on gére la reservation
        $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_REGISTRATION);

        foreach ($data->animationsSelected as $idAnimation) {
            // a reprendre quand on gerera les statuts "en attente de validaton"
            $data->numRegistrationStatus = self::VALIDATED;

            $anim = new AnimationModel($idAnimation);
            if ($anim->checkChildrens()) {
                continue;
            }
            if ($anim->allowWaitingList) {
                $quotas = AnimationModel::getAllQuotas($idAnimation);
                // Dans le cas d'une inscription local (et qu'il n'y à plus de place), on inscrit sur liste d'attente
                if ($quotas["animationQuotas"]["global"] != 0 && $quotas["availableQuotas"]["global"] < count($data->registrationListPerson)) {
                    $data->numRegistrationStatus = self::WAITING_LIST;
                }
            }

            $registration = new RegistrationOrm();
            $registration->nb_registered_persons = count($data->registrationListPerson);
            $registration->name = $data->name;
            $registration->num_animation = $idAnimation;
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
            if (! empty($data->numEmpr)) {
                $registration->num_empr = $data->numEmpr;
            }
            if (! empty($data->numOrigin)) {
                $registration->num_origin = $data->numOrigin;
            }

            $registration->save();

            if (! empty($data->registrationListPerson)) {
                foreach ($data->registrationListPerson as $person) {
                    $person->numRegistration = $registration->id_registration;
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
                self::registrationAccount($registrationModel, $data->registrationListPerson, $data->numRegistrationStatus, ComptesModel::ACCOUNT_SENS_DEBIT);
            }

            // Generation du mail pour l'animation et la personne de contact
            if (! empty($maillingTypeOrm) && ! empty($maillingTypeOrm[0])) {
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
        }

        return true;
    }

    public static function updateRegistration(int $id, object $data)
    {
        $registration = new RegistrationOrm($id);

        if (! empty($data->nbRegisteredPersons)) {
            $registration->nb_registered_persons = $data->nbRegisteredPersons;
        }
        if (! empty($data->name)) {
            $registration->name = $data->name;
        }
        if (! empty($data->email) && Helper::isValidMail($data->email)) {
            $registration->email = $data->email;
        }
        if (empty($data->phoneNumber) || (! empty($data->phoneNumber) && Helper::isValidPhone($data->phoneNumber))) {
            $registration->phone_number = $data->phoneNumber ?? "";
        }
        if (! empty($data->numAnimation)) {
            $registration->num_animation = $data->numAnimation;
        }
        if (! empty($data->numRegistrationStatus)) {
            $registration->num_registration_status = $data->numRegistrationStatus;
        }
        if (! empty($data->numEmpr)) {
            $registration->num_empr = $data->numEmpr;
        }
        if (! empty($data->numOrigin)) {
            $registration->num_origin = $data->numOrigin;
        }

        $registration->save();

        $registrationModel = new RegistrationModel($registration->id_registration);
        $registrationModel->fetchRegistrationListPerson();

        foreach ($registrationModel->registrationListPerson as $registredPerson) {
            RegistredPersonModel::deleteRegistredPerson($registredPerson->idPerson);
        }

        if (! empty($data->registrationListPerson)) {
            foreach ($data->registrationListPerson as $person) {
                $person->numRegistration = $registration->id_registration;
                RegistredPersonModel::addRegistredPerson($person);
            }
        }

        return $registration->id_registration;
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
            $this->animation->fetchLocation(true);
        }
        return $this->animation;
    }

    public function fetchRegistrationStatus()
    {
        if (! empty($this->registrationStatus)) {
            return $this->registrationStatus;
        }
        $this->registrationStatus = null;
        if (! empty($this->numRegistrationStatus)) {
            $this->registrationStatus = new RegistrationStatusModel($this->numRegistrationStatus);
        }
        return $this->registrationStatus;
    }

    public function fetchEmpr()
    {
        if (! empty($this->empr)) {
            return $this->empr;
        }
        $this->empr = null;
        if (! empty($this->numEmpr)) {
            $this->empr = new EmprModel($this->numEmpr);
        }
        return $this->empr;
    }

    public static function getFormData(int $numAnimation, string $numDaughtersAnimation = '')
    {
        global $opac_animations_only_empr, $opac_rgaa_active;

        $animationModel = new AnimationModel($numAnimation);
        $animationModel->fetchPrices();

        foreach ($animationModel->prices as $price) {
            $price->fetchPriceType();
        }
        $event = $animationModel->fetchEvent();
        $animationModel->event = $animationModel->getFormatDate($event);
        $animationModel->fetchLocation();
        $animationModel->fetchQuotas();
        $animationModel->checkChildrens();

        $formdata = [
            "animation" => $animationModel,
            "listDaughters" => AnimationModel::getDaughterList($numAnimation),
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
            "params" => [
                "animations_only_empr" => intval($opac_animations_only_empr),
                "opac_rgaa_active" => intval($opac_rgaa_active)
            ]
        ];

        $formdata["animationsSelected"] = [];
        if (! empty($numDaughtersAnimation)) {
            $formdata["animationsSelected"] = explode(',', $numDaughtersAnimation);
        }

        return $formdata;
    }

    public static function updateAnimationRegistration(int $id)
    {
        $registrationsList = RegistrationOrm::find("num_animation", $id);
        foreach ($registrationsList as $registrations) {
            $registrations = new RegistrationOrm($registrations->id_registration);
            $registrations->num_animation = 0;
            $registrations->save();
        }
    }

    public static function deleteAnimationRegistration(int $id)
    {
        $registrationsList = RegistrationOrm::find("num_animation", $id);
        foreach ($registrationsList as $registration) {
            RegistredPersonModel::deleteRegistrationRegistredPerson($registration->id_registration);
            $registration->delete();
        }
    }

    public function fetchRegistrationListPerson()
    {
        $this->registrationListPerson = RegistredPersonModel::getListPersonFromRegistration($this->idRegistration);
        return $this->registrationListPerson;
    }

    public static function getRegistrationPlaceForAnimation($idAnimation)
    {
        $registrationOrm = new RegistrationOrm();
        $registrations = [];
        // A reprende lors de la prise en compte de la modération des inscriptions
        $registrations['global'] = $registrationOrm->finds([
            "num_animation" => $idAnimation,
            "num_origin" => self::QUOTA_GLOBAL,
            "num_registration_status" => [
                "operator" => "!=",
                "value" => self::WAITING_LIST
            ]
        ]);
        $registrations['internet'] = $registrationOrm->finds([
            "num_animation" => $idAnimation,
            "num_origin" => self::QUOTA_INTERNET,
            "num_registration_status" => [
                "operator" => "!=",
                "value" => self::WAITING_LIST
            ]
        ]);

        return $registrations;
    }

    public static function getRegistrationWaitingList($idAnimation)
    {
        $registrationOrm = new RegistrationOrm();
        $registrations = [];

        // A reprende lors de la prise en compte de la modération des inscriptions
        $registrations['global'] = $registrationOrm->finds([
            "num_animation" => $idAnimation,
            "num_origin" => self::QUOTA_GLOBAL,
            "num_registration_status" => self::WAITING_LIST
        ]);
        $registrations['internet'] = $registrationOrm->finds([
            "num_animation" => $idAnimation,
            "num_origin" => self::QUOTA_INTERNET,
            "num_registration_status" => self::WAITING_LIST
        ]);

        return $registrations;
    }

    public static function getRegistrationList($idRegistration = 0, $numAnimation = 0)
    {
        $registration = new RegistrationModel($idRegistration);

        if (empty($idRegistration)) {
            $registration->numAnimation = intval($numAnimation);
            $registration->barcode = '';
            $registration->registrationListPerson = array();
        } else {
            $registration->fetchRegistrationListPerson();
            $empr = new EmprModel($registration->numEmpr);
            $registration->barcode = $empr->emprCb;
        }

        return $registration;
    }

    public static function validateRegistration(int $id)
    {
        $registrationOrm = new RegistrationOrm($id);
        if ($registrationOrm->num_registration_status === self::VALIDATED) {
            return;
        }
        $registrationOrm->num_registration_status = self::VALIDATED;
        $registrationOrm->save();

        $registration = new RegistrationModel($id);
        $registration->fetchRegistrationListPerson();

        if ($pmb_gestion_animation && $pmb_gestion_financiere) {
            self::registrationAccount($registration, $registration->registrationListPerson, self::VALIDATED, ComptesModel::ACCOUNT_SENS_DEBIT);
        }

        // Dans le cas de base (simple) on dit que l'on doit passer par une reservation, puis une confirmation en Gestion, ICI on gére la confirmation
        $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_CONFIRMATION);
        // Generation du mail pour l'animation et la personne de contact
        if (! empty($maillingTypeOrm) && ! empty($maillingTypeOrm[0])) {
            $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);
            $temp = array();
            MailingAnimationModel::sendMail(
                [ $registration ],
                $registration->numAnimation,
                $template,
                $temp,
                $maillingTypeOrm[0]->num_sender,
                RegistrationModel::generateICal($registration)
            );
        }

        MailingController::proceedSms('registration', 0, $registration);
    }

    public function fetchValidated()
    {
        $this->validated = boolval($this->numRegistrationStatus == self::VALIDATED);
        return $this->validated;
    }

    public function getFormatDate()
    {
        $this->rawDate = $this->date;
        $date = new \DateTime($this->date);
        $this->date = $date->format("d/m/Y") . " " . $date->format("H:i");
        return $this->date;
    }

    public static function getEmprRegistrationsList($emprId)
    {
        $registredPersonsList = RegistredPersonModel::getRegistredPersonsByEmpr($emprId);
        $registrationsByNumEmpr = self::getRegistredPersonsByNumEmpr($emprId);
        
        $registrations = [];
        $registrationsId = [];
        $registrationsList = [];
        
        foreach ($registredPersonsList as $registredPerson) {
            if(!in_array($registredPerson['num_registration'], $registrationsId)){
                $registrations[] = new RegistrationModel($registredPerson['num_registration']);
                $registrationsId[] = $registredPerson['num_registration'];
            }
        }
        
        foreach($registrationsByNumEmpr as $registrationORM){
            if(!in_array($registrationORM->id_registration, $registrationsId)){
                $registrations[] = new RegistrationModel($registrationORM->id_registration);
                $registrationsId[] = $registrationORM->id_registration;
            }
            
        }
        
        foreach ($registrations as $registration) {
            
            $registration->fetchAnimation();
            $registration->fetchRegistrationStatus();
            $registration->fetchRegistrationListPerson();
            $registration->animation->getViewData();
            
            if (empty($registration->animation->event->dateExpired) && ! $registration->animation->event->duringDay) {
                $registration->animation->event->dateExpired = false;
            }
            
            if (! empty($registration->animation->event) && ! $registration->animation->event->dateExpired) {
                $registrationsList[] = $registration;
            }
        }
        
        return $registrationsList;
    }
    
    public static function getRegistredPersonsByNumEmpr(int $emprId)
    {
        $registration = RegistrationOrm::find('num_empr', $emprId);
        return $registration;
    }

    public static function getIdRegistrationFromEmprAndAnimation($idEmpr, $idAnimation)
    {
        if (! empty($idEmpr)) {
            $instances = RegistrationOrm::finds([
                "num_animation" => $idAnimation,
                "num_empr" => $idEmpr
            ]);

            if (! empty($instances)) {
                $registration = $instances[0];
            } else {
                $instances = RegistredPersonOrm::find("num_empr", intval($idEmpr));
                foreach ($instances as $instance) {
                    if (RegistrationOrm::exist($instance->num_registration)) {
                        $registrationOrm = new RegistrationOrm($instance->num_registration);
                        if ($idAnimation == $registrationOrm->num_animation) {
                            $registration = $registrationOrm;
                            break;
                        }
                    }
                }
            }

            if (! empty($registration)) {
                return intval($registration->id_registration) ?? 0;
            }
        }

        return 0;
    }

    public function getViewData(int $emprId = 0)
    {
        $this->registredPersons = RegistredPersonModel::getListPersonFromRegistration($this->id);

        // Lien de désinscription pour la personne présente
        $this->unsubscribeLink = "";

        if (! empty($this->idRegistration) && ! empty($emprId)) {

            $this->is_contact = false;
            if ($this->numEmpr == $emprId) {
                $this->is_contact = true;
            }

            foreach ($this->registredPersons as $registredPerson) {
                if ($registredPerson->numEmpr == $emprId) {
                    $this->unsubscribeLink = $registredPerson->getUnsubscribeLink();
                    break;
                }
            }

            if (empty($this->unsubscribeLink)) {
                $this->unsubscribeLink = $this->getContactUnsubscribeLink();
            }
        }

        return $this;
    }

    public function getContactUnsubscribeLink()
    {
        global $opac_url_base;

        if (! empty($this->unsubscribeLink)) {
            return $this->unsubscribeLink;
        }

        $this->unsubscribeLink = $opac_url_base . "index.php?lvl=registration&action=delete&id_registration=" . intval($this->idRegistration);
        if (empty($this->hash)) {
            $this->generateHash();
        }
        $this->unsubscribeLink .= "&hash=" . $this->hash;

        return $this->unsubscribeLink;
    }

    public function generateHash()
    {
        $param = $this->idRegistration . $this->date . $this->numAnimation;
        $hashModel = new HashModel();
        $this->hash = $hashModel->generateHash($param);

        $registrationOrm = new RegistrationOrm($this->idRegistration);
        $registrationOrm->hash = $this->hash;
        $registrationOrm->save();

        return $this->hash;
    }

    public static function deleteFromCirculation($idEmpr)
    {
        $registrationOrm = new RegistrationOrm();
        $instances = $registrationOrm->find("num_empr", $idEmpr);
        foreach ($instances as $registration) {
            $registration->num_empr = 0;
            $registration->save();
        }

        $registredPersonOrm = new RegistredPersonOrm();
        $instances = $registredPersonOrm->find("num_empr", $idEmpr);
        foreach ($instances as $registration) {
            $registration->num_empr = 0;
            $registration->save();
        }
    }

    /**
     * Passe une inscription en valide/attente de validation en fonction du nombre de place libere
     *
     * @param RegistrationOrm $registration
     *            Inscription modifiee
     * @param int $freeQuotas
     *            Nombre de place liberee
     * @return boolean false si erreur
     */
    public static function reviewRegistration(RegistrationOrm $registration, int $freeQuotas)
    {
        if (
            RegistrationModel::WAITING_LIST == $registration->num_registration_status ||
            ! AnimationOrm::exist($registration->num_animation) ||
            (empty($freeQuotas) || 0 >= $freeQuotas)
        ) {
            // Une place en liste d'attente qui vien de ce liberer, on faire rien
            // Ou l'animation n'existe pas ou plus
            // Ou le nombre de place disponible n'est pas correcte
            return false;
        }

        $quotas = AnimationModel::getAllQuotas($registration->num_animation);
        $availableQuotas = $quotas['availableQuotas']['internet'];
        $countReservedQuotas = $quotas['reserved']['internet'];
        $countWaitingListQuotas = $quotas['waitingList']['internet'];

        if (0 >= $availableQuotas && 0 >= $countReservedQuotas && 0 >= $countWaitingListQuotas) {
            // On a aucune place de disponible
            // Ou a aucune personne en attente de validation / liste d'attente
            return false;
        }

        if ($registration->num_registration_status == RegistrationModel::VALIDATED) {
            // Une inscription valide vien d'etre liberer
            $foundRegistrationStatus = [
                RegistrationModel::PENDING_VALIDATION,
                RegistrationModel::WAITING_LIST
            ];
        } else {
            // Une inscription en attente de validation vien d'etre liberer
            if ($registration->animation[0]->auto_registration) {
                $foundRegistrationStatus = [
                    RegistrationModel::WAITING_LIST,
                    RegistrationModel::PENDING_VALIDATION
                ];
            } else {
                $foundRegistrationStatus = [
                    RegistrationModel::WAITING_LIST
                ];
            }
        }

        // On priorise les statuts en attente de validation puis les liste d'attente
        $finds = RegistrationOrm::finds([
            "num_animation" => [
                "operator" => "=",
                "value" => $registration->num_animation
            ],
            "nb_registered_persons" => [
                "operator" => "<=",
                "value" => intval($freeQuotas)
            ],
            "num_registration_status" => [
                "operator" => "in",
                "value" => $foundRegistrationStatus
            ],
            RegistrationOrm::$idTableName => [
                "operator" => "!=",
                "value" => $registration->{RegistrationOrm::$idTableName}
            ]
        ], "num_registration_status ASC, " . RegistrationOrm::$idTableName . " ASC", "AND", 1);

        if (empty($finds)) {
            // On ne peut pas donner la place a quelqu'un d'autre
            return true;
        }

        // On inscript les premiers qu'on trouve
        $registrationOrm = $finds[0];

        if ($registration->animation[0]->auto_registration) {
            $registrationOrm->num_registration_status = RegistrationModel::VALIDATED;
            $mailingType = MailingTypeModel::MAILING_CONFIRMATION;
        } else {
            $registrationOrm->num_registration_status = RegistrationModel::PENDING_VALIDATION;
            $mailingType = MailingTypeModel::MAILING_REGISTRATION;
        }

        $registrationOrm->save();

        // Envoie du mail
        $maillingTypeOrm = MailingTypeOrm::find("periodicity", $mailingType);
        if (! empty($maillingTypeOrm) && ! empty($maillingTypeOrm[0])) {
            $campaignDatas = [];
            $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);

            $registrationModel = new RegistrationModel($registrationOrm->{RegistrationOrm::$idTableName});
            MailingAnimationModel::sendMail(
                [ $registrationModel ],
                $registration->num_animation,
                $template,
                $campaignDatas,
                $maillingTypeOrm[0]->num_sender,
                RegistrationModel::generateICal($registrationModel)
            );
        }

        // On recalcule ne nombre de place libre
        $freeQuotas -= $registrationOrm->nb_registered_persons;
        if (empty($freeQuotas) || 1 > $freeQuotas) {
            return true;
        }

        // Et c'est reparti pour un tour
        return static::reviewRegistration($registration, $freeQuotas);
    }

    /**
     * Passe une inscription en valide/attente de validation en fonction du nombre de place libere
     *
     * @param RegistrationOrm $registration
     *            Inscription modifiee
     * @param int $freeQuotas
     *            Nombre de place liberee
     * @return boolean false si erreur
     */
    public static function reviewAnimationRegistration(RegistrationOrm $registration, int $freeQuotas)
    {
        if (! AnimationOrm::exist($registration->num_animation) || (empty($freeQuotas) || 0 >= $freeQuotas)) {
            // Ou le nombre de place disponible n'est pas correcte
            return false;
        }

        $quotas = AnimationModel::getAllQuotas($registration->num_animation);
        $availableQuotas = $quotas['availableQuotas']['internet'];

        if (0 >= $availableQuotas) {
            // On a aucune place de disponible
            return false;
        }

        // On priorise les statuts en attente de validation puis les liste d'attente
        $finds = RegistrationOrm::finds([
            "num_animation" => [
                "operator" => "=",
                "value" => $registration->num_animation
            ],
            "nb_registered_persons" => [
                "operator" => "<=",
                "value" => intval($freeQuotas)
            ],
            "num_registration_status" => [
                "operator" => "=",
                "value" => RegistrationModel::WAITING_LIST
            ]
        ], "num_registration_status ASC, " . RegistrationOrm::$idTableName . " ASC", "AND", 1);

        if (empty($finds)) {
            // On ne peut pas donner la place a quelqu'un d'autre
            return true;
        }

        // On inscript les premiers qu'on trouve
        $registrationOrm = $finds[0];

        if ($registration->animation[0]->auto_registration) {
            $registrationOrm->num_registration_status = RegistrationModel::VALIDATED;
            $mailingType = MailingTypeModel::MAILING_CONFIRMATION;
        } else {
            $registrationOrm->num_registration_status = RegistrationModel::PENDING_VALIDATION;
            $mailingType = MailingTypeModel::MAILING_REGISTRATION;
        }

        $registrationOrm->save();

        // Envoie du mail
        $maillingTypeOrm = MailingTypeOrm::find("periodicity", $mailingType);
        if (! empty($maillingTypeOrm) && ! empty($maillingTypeOrm[0])) {
            $campaignDatas = [];
            $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);

            $registrationModel = new RegistrationModel($registrationOrm->{RegistrationOrm::$idTableName});
            MailingAnimationModel::sendMail(
                [ $registrationModel ],
                $registration->num_animation,
                $template,
                $campaignDatas,
                $maillingTypeOrm[0]->num_sender,
                RegistrationModel::generateICal($registrationModel)
            );
        }

        // On recalcule ne nombre de place libre
        $freeQuotas -= $registrationOrm->nb_registered_persons;
        if (empty($freeQuotas) || 1 > $freeQuotas) {
            return false;
        }

        return $freeQuotas;
    }

    public static function getTotalPay(array $registrationListPerson)
    {
        $total = 0;
        foreach ($registrationListPerson as $person) {
            $price = new PriceOrm($person->numPrice);
            $total += $price->value;
        }
        return $total;
    }

    /**
     *
     * @param object $registration
     * @param int $statusRegistration
     * @param int $sens
     * @return boolean
     */
    public static function registrationAccount(object $registration, $registrationList, int $statusRegistration, int $sens)
    {
        if (empty($registrationList)) {
            return;
        }

        $totalToPay = self::getTotalPay($registrationList);

        if (0 != $totalToPay && ! empty($registration->numEmpr) && self::VALIDATED == $registration->numRegistrationStatus) {
            $animation = new AnimationModel($registration->numAnimation);
            $animation->fetchEvent();
            $date = new \DateTime($animation->event->startDate);
            $titre = ComptesModel::ACCOUNT_SENS_DEBIT == $sens ? GlobalContext::msg("animation_comment_cmpte_subscribe_animation") : GlobalContext::msg("animation_comment_cmpte_unsubscribe_animation");
            $comment = $titre . " " . GlobalContext::msg("animation_comment_cmpte_animation_of") . " " . $date->format("d/m/Y") . " <a href='../animations.php?categ=animations&action=view&id=" . $animation->id . "' target='_blank'>" . $animation->name . " </a>";
            ComptesModel::accountDebit($registration->numEmpr, $totalToPay, ComptesModel::ACCOUNT_ANIMATION, $comment, $sens);
        }
    }

    /**
     * Generation du fichier ICal
     *
     * @param RegistrationModel $registrationModel
     * @param boolean $isCancelled
     * @return array{array{nomfichier:string, contenu:string}}
     */
    public static function generateICal(RegistrationModel $registrationModel, bool $isCancelled = false)
    {
        $iCalendar = AnimationIcalendar::getInstance($registrationModel->fetchAnimation());
        $iCalendar->setRegistration($registrationModel, $isCancelled);

        global $base_path;
        switch (true) {
            case $isCancelled:
                $attachmentName = $base_path . '/temp/animation-registration-cancelled.ics';
                break;

            case $registrationModel->numRegistrationStatus == RegistrationModel::VALIDATED:
                $attachmentName = $base_path . '/temp/animation-registration-validated.ics';
                break;

            case $registrationModel->numRegistrationStatus == RegistrationModel::PENDING_VALIDATION:
                $attachmentName = $base_path . '/temp/animation-registration-pending-validation.ics';
                break;

            case $registrationModel->numRegistrationStatus == RegistrationModel::WAITING_LIST:
                $attachmentName = $base_path . '/temp/animation-registration-waiting-list.ics';
                break;

            default:
                $attachmentName = $base_path . '/temp/animation-registration-unknown.ics';
                break;
        }

        return [
            [
                'nomfichier' => $attachmentName,
                'contenu' => $iCalendar->output(ICalendar::OUTPUT_DEST_S)
            ]
        ];
    }
}