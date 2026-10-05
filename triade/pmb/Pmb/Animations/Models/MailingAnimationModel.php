<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingAnimationModel.php,v 1.17.4.1 2025/06/04 12:39:32 qvarin Exp $

namespace Pmb\Animations\Models;

use Pmb\Animations\Orm\MailingAnimationOrm;
use Pmb\Common\Models\MailingModel;
use Pmb\Common\Models\MailtplModel;
use Pmb\Common\Models\EmprModel;
use Pmb\Common\Models\DocsLocationModel;
use Pmb\Common\Helper\Helper;

class MailingAnimationModel extends MailingModel
{

    protected $ormName = "\Pmb\Animations\Orm\MailingAnimationOrm";

    protected static $AnimationModelInstance = [];
    protected static $EmprModelInstance = [];

    public $idMailing;
    public $numAnimation;
    public $numMailingType;
    public $mailingsType;
    public $alreadyMail;
    public $animation;

    public static function computeCartEmpr(int $idAnimation)
    {
        $anim = AnimationModel::getAnimationForMailing($idAnimation);
        $dateStart = date("d-m-Y", strtotime($anim->event->startDate));
        $dateEnd = date("d-m-Y", strtotime($anim->event->endDate));

        // On test si on à un panier d'emprunteur, si oui on le recupère
        if ($anim->numCart) {
            $caddie = new \empr_caddie($anim->numCart);
            // On met à jour le titre
            $caddie->name = $anim->name . ' - ' . $dateStart . (($dateStart != $dateEnd) ? ' - ' . $dateEnd : '');
            $idCaddie = $caddie->get_id();
        } else {
            // sinon on le crée
            $caddie = new \empr_caddie();
            // On ajoute le titre
            $caddie->name = $anim->name . ' - ' . $dateStart . (($dateStart != $dateEnd) ? ' - ' . $dateEnd : '');
            $idCaddie = $caddie->create_cart();

            // On enregistre le num_cart dans les animations
            $data = new \stdClass();
            $data->numCart = $idCaddie;
            $anim->updateAnimation($idAnimation, $data);
        }

        $notEmpr = array();
        // On recupere et on ajoute les contacts des participants
        $registrationContactList = RegistrationModel::getRegistrations($idAnimation);
        foreach ($registrationContactList as $contact) {
            if (! empty($contact->numEmpr)) {
                $caddie->add_item($contact->numEmpr);
            } else {
                $notEmpr[] = $contact;
            }
        }
        $caddie->save_cart();

        return [
            "idCaddie" => $idCaddie,
            "notEmpr" => $notEmpr
        ];
    }

    public static function sendRegistrationSms($type, $level, $registration, $template)
    {
        return self::sendSms($type, $level, $registration->numEmpr, self::fillAnimationTemplate($template, $registration, false));
    }

    private static function sendSms($type, $level, $idEmpr, $template)
    {
        $empr = new EmprModel($idEmpr);
        if (! empty($empr->emprTel1) && ! empty($empr->emprSms) && $template) {
            return send_sms($type, $level, $empr->emprTel1, $template);
        }
        return false;
    }

    private static function fillAnimationTemplate($template, $registration, $mail = true)
    {
        global $msg;

        if (!isset(self::$AnimationModelInstance[$registration->numAnimation])) {
        	$anim = new AnimationModel($registration->numAnimation);
            $anim->getViewData();
            self::$AnimationModelInstance[$registration->numAnimation] = $anim;
        } else {
        	$anim = self::$AnimationModelInstance[$registration->numAnimation];
        }

        if (!isset(self::$EmprModelInstance[$registration->numEmpr])) {
        	$empr = new EmprModel($registration->numEmpr);
        	self::$EmprModelInstance[$registration->numEmpr] = $empr;
        } else {
        	$empr = self::$EmprModelInstance[$registration->numEmpr];
        }


        $listRegistredPersons = RegistredPersonModel::getListRegistredPersons($registration->id);

        $unsubscribe_link = "";
        $registrationPerson = RegistredPersonModel::getRegistredPersonByEmprAndRegistration($registration->numEmpr, $registration->id);
        if (!empty($registrationPerson->idPerson) && $registrationPerson->idPerson != 0) {
            $unsubscribe_link = $registrationPerson->getUnsubscribeLink();
        }
        if (empty($unsubscribe_link)) {
            $unsubscribe_link = "";
        }

        $location = '';
        if (! count($anim->location)) {
            $location .= $msg["no_animation_location"];
        } elseif (! empty($mail)) {
            $location = DocsLocationModel::getInfosLocs($anim);
        } else {
            foreach ($anim->location as $index => $loc) {
                if (! empty($index)) {
                    $location .= ' - ';
                }
                $location .= $loc->location_libelle;
            }
        }

        $template = parent::getReplacePattern($empr, $template);

        $search = [
            '!!animation_name!!',
            '!!animation_empr_name!!',
            '!!animation_empr_firstname!!',
            '!!animation_start_date!!',
            '!!animation_end_date!!',
            '!!animation_start_hour!!',
            '!!animation_end_hour!!',
            '!!animation_registered_list!!',
            '!!animation_location!!',
            '!!animation_registration_unsubscribe_link!!'
        ];

        $empr->emprNom = ! empty($empr->emprNom) ? $empr->emprNom : $registration->name;
        $empr->emprPrenom = ! empty($empr->emprPrenom) ? $empr->emprPrenom : "";

        $eventEndDate = $anim->event->startDate;
        $eventStartHour = "00:00" == $anim->event->startHour ? "" : $anim->event->startHour;
        $eventEndHour = "00:00" == $anim->event->startHour ? "" : $anim->event->startHour;
        if (!$anim->event->duringDay) {
            $eventEndDate = $anim->event->endDate;
            $eventEndHour = "00:00" == $anim->event->endHour ? "" : $anim->event->endHour;
        }
        $replace = [
            $anim->name,
            $empr->emprNom,
            $empr->emprPrenom,
            $anim->event->startDate,
            $eventEndDate,
            $eventStartHour,
            $eventEndHour,
            $listRegistredPersons,
            $location,
            $unsubscribe_link
        ];

        return str_replace($search, $replace, $template);
    }

    public static function getMailings(int $id_animation, bool $duplicate = false)
    {
        $mailings_tab = [];
        $mailing_ORM = new MailingAnimationOrm();
        $mailings = $mailing_ORM->find('num_animation', $id_animation);

        foreach ($mailings as $mailing) {
            $m = new MailingAnimationModel($mailing->id_mailing);
            $m->fetchMailingType();
            if ($duplicate) {
                $m->id = 0;
                $m->idMailing = 0;
                $m->numAnimation = 0;
                $m->numMailingType = 0;
            }
            $mailings_tab[] = $m;
        }
        return $mailings_tab;
    }

    public function fetchMailingType()
    {
        if (! empty($this->mailingsType)) {
            return $this->mailingsType;
        }
        $this->mailingsType = MailingTypeModel::getMailingType($this->numMailingType);
        return $this->mailingsType;
    }

    public static function update(int $id, object $data)
    {
        $mailingOrm = new MailingAnimationOrm($id);

        foreach ($data as $nameProps => $valueProps) {
            $mailingOrm->{$nameProps} = $valueProps;
        }

        return $mailingOrm->save();
    }

    public static function computeMail($idAnim, $mailing_list_choice = [])
    {
        $response = [];
        $anim = AnimationModel::getAnimationForMailing($idAnim);
        // si mailing défini parcourir les mailing
        foreach ($anim->mailings as $mailing) {

            // La communication est utilisé pour une inscription à l'animation
            if ($mailing->mailingsType->periodicity == MailingTypeModel::MAILING_REGISTRATION || $mailing->mailingsType->periodicity == MailingTypeModel::MAILING_CONFIRMATION || $mailing->mailingsType->periodicity == MailingTypeModel::MAILING_ANNULATION) {
                continue;
            }

            // La communication à déjà été faite pour cette animation et sur ce type de mailing
            // Si le mailing auto n'est pas coché on sort
            if ($mailing->alreadyMail || ! $mailing->mailingsType->autoSend ||  ! in_array($mailing->numMailingType, $mailing_list_choice)) {
                continue;
            }

            // On récupere les delay et on compare avec la startDate/EndDate de l'animation
            $delay = $mailing->mailingsType->delay;
            if (intval($delay) != 0)
                $delay = intval($delay) * 3600;

            if ($mailing->mailingsType->periodicity == MailingTypeModel::MAILING_BEFORE ) {
                // dans le cas d'une com avant la date
                $dateAComparer = strtotime($anim->event->startDate) - $delay;
                $canMailing = time() >= $dateAComparer && time() <= strtotime($anim->event->startDate);
            } else if ($mailing->mailingsType->periodicity == MailingTypeModel::MAILING_AFTER ){
                // dans le cas d'une com après la date
                $dateAComparer = strtotime($anim->event->endDate) + $delay;
                $canMailing = time() >= $dateAComparer && time() >= strtotime($anim->event->endDate);
            }
            if ($canMailing) {
                if ($mailing->mailingsType->campaign){
                    $campaignDatas["associated_campaign"] = $mailing->mailingsType->campaign;
                }
                // Si on arrive ici, on communique
                // Recuperer les contacts pour l'animation courante
                $registrationContact = RegistrationModel::getRegistrations($anim->id, false);

                // appele de la methode sendMail
                $template = MailtplModel::getMailtpl($mailing->mailingsType->numTemplate);
                $response[$idAnim . "-" . $anim->name] = self::sendMail($registrationContact, $anim, $template, $campaignDatas, $mailing->mailingsType->numSender);

                // On met à jour le mailing
                $props = new \stdClass();
                $props->already_mail = 1;
                self::update($mailing->id, $props);

                // Sauvegarde du mailing
                MailingListModel::saveMailing($idAnim, MailingListModel::AUTOMATIC_SEND, $template, $response, $campaignDatas, $mailing->mailingsType->numSender);
            }
        }
        return $response;
    }

    public static function computeManualMail($data)
    {
        $response = [];
        $anim = AnimationModel::getAnimationForMailing($data->idAnimation);

        $campaignDatas = array();
        if ($data->mailingAssociatedCampaign){
            $campaignDatas = [
                "associated_campaign" => $data->mailingAssociatedCampaign
            ];
        }

        // PJ du mail
        if (!is_array($data->attachmentFile) && "undefined" == $data->attachmentFile) {
            $attachments = array();
        } else {
            $attachments[] = array(
                'contenu' => file_get_contents($data->attachmentFile["tmp_name"]),
                'nomfichier' => $data->attachmentFile["name"]
            );
        }

        //Recuperer les contacts pour l'animation courante
        $registrationContact = RegistrationModel::getRegistrations($anim->id, false);

        //appele de la methode sendMail
        $response[$data->idAnimation . "-" . $anim->name] = self::sendMail($registrationContact, $anim, $data->template, $campaignDatas, $data->numSender, $attachments);

        //Sauvegarde du mailing
        MailingListModel::saveMailing($data->idAnimation, MailingListModel::MANUAL_SEND, $data->template, $response, $campaignDatas, $data->numSender);
        return $data->idAnimation;
    }

    public static function sendMail($registrationContact, $anim, $template, &$campaignDatas = array(), $idSender = 0, $attachment = array())
    {
        global $charset;


        $contactList = [];
        // On commence a preparer le mail
        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: multipart/form-data; charset=" . $charset . "\n";

        // on recupere les infos du mail depuis le template
        $mailObject = $template->mailtplObjet;
        $content = $template->mailtplTpl;

        // Pour chaque contact
        foreach ($registrationContact as $contact) {
            if (!$contact->email) {
                $contactList[] = [
                	"NAME" => $contact->name,
                	"EMAIL" => $contact->email,
                    "SUCCESS" => false
                ];
                continue;
            }

            // On remplace les variables avec fillAnimationTemplate
            $sujet = MailingAnimationModel::fillAnimationTemplate($mailObject, $contact);
            $body = MailingAnimationModel::fillAnimationTemplate($content, $contact);

            // On récupere les données du sender
            $sender = self::getSender($idSender);

            // On ajoute les campagne de mails
            if (!empty($campaignDatas)) {
                if(!empty($campaignDatas['associated_campaign'])) {
                    if(!empty($campaignDatas['associated_num_campaign'])) {
                        $campaign = new \campaign($campaignDatas['associated_num_campaign']);
                    } else {
                        $campaign = new \campaign();
                        $campaign->set_type('animation');
                        $campaign->set_label($sujet);
                        $saved = $campaign->save();
                        //On conserve l'identifiant de la nouvelle campagne pour les autres paquets
                        if($saved) {
                            $campaignDatas['associated_num_campaign'] = $campaign->get_id();
                        }
                    }
                }
            }
            if(!empty($campaignDatas) && !empty($campaignDatas['associated_campaign'])) {
                $idEmpr = (!empty($contact->numEmpr) ? $contact->numEmpr : 0);
                $r = $campaign->send_mail($idEmpr, $contact->name, $contact->email, $sujet, $body, $sender->name, $sender->email, $headers, "", "", 0, $attachment) ;
            } else {
                //on envoi le mail
            	$r = mailpmb($contact->name, $contact->email, $sujet, $body, $sender->name, $sender->email, $headers, "", "", 1, $attachment);
            }
            $contactList[] = [
            	"NAME" => $contact->name,
                "EMAIL" => $contact->email,
                "SUCCESS" => $r
            ];
        }
        return $contactList;
    }

    protected static function getSender($idSender)
    {
        global $PMBuserid;

        if ($idSender == 0){
            $idSender = $PMBuserid;
        }

        $user = Helper::getUser($idSender);
        $sender = new \stdClass();
        $sender->name = $user["nom"] . " " . $user["prenom"];
        $sender->email = $user["user_email"];

        return $sender;
    }

    public static function getSenders()
    {
        global $PMBuserid, $msg;
        $users = Helper::getUsers();
        $sender = array();
        foreach ($users as $key=>$user){
            $sender[$key]["id"] = $user["userid"];
            $email = (!empty($user["user_email"]) ? $user["user_email"] : $msg["animation_mailing_no_mail"]);
            $sender[$key]["name"] = $user["nom"] .' '. $user["prenom"] .' (' . $email. ')';
            $sender[$key]["selected"] = ($PMBuserid == $user["userid"] ? "selected" : "") ;
            $sender[$key]["mail"] = $user["user_email"];
        }

        return $sender;
    }

    public function delete()
    {
        $orm = new MailingAnimationOrm($this->id);
        if (!empty($orm)) {
            $orm->delete();
        }
    }

    public static function sendMailToBibli($registration, $user, $template, $idSender)
    {
        global $charset;
        // On commence a preparer le mail
        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: multipart/form-data; charset=" . $charset . "\n";

        // on recupere les infos du mail depuis le template
        $mailObject = $template->mailtplObjet;
        $content = $template->mailtplTpl;

        // On remplace les variables avec fillAnimationTemplate
        $sujet = MailingAnimationModel::fillAnimationTemplate($mailObject, $registration);
        $body = MailingAnimationModel::fillAnimationTemplate($content, $registration);

        // On récupere les données du sender
        $sender = self::getSender($idSender);

        //on envoi le mail
        $mail = $user["user_email"];
        if (!empty($user["user_email_recipient"])) {
            $mail = $user["user_email_recipient"];
        }
        $r = mailpmb($user["nom"], $mail, $sujet, $body, $sender->name, $sender->email, $headers, "", "", 1, array());
    }
}