<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingTypeModel.php,v 1.15.4.1 2025/06/04 12:39:32 qvarin Exp $

namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\MailingTypeOrm;
use Pmb\Common\Models\CustomFieldModel;
use Pmb\Animations\Orm\MailingAnimationOrm;

class MailingTypeModel extends Model
{
    Const MAILING_BEFORE= 1;
    Const MAILING_AFTER = 2;
    Const MAILING_REGISTRATION = 3;
    Const MAILING_CONFIRMATION = 4;
    Const MAILING_ANNULATION = 5;
    Const MAILING_SEND_TO_BIBLI = 6;

    public $idMailingType;
    public $name;
    public $delay;
    public $periodicity;
    public $autoSend;
    public $numTemplate;
    public $campaign;
    public $numSender;
    public $mailtpl;
    public $mailings;

    protected $ormName = "\Pmb\Animations\Orm\MailingTypeOrm";

    public static function getMailingsTypeList()
    {
        $mailingsTypesList = MailingTypeOrm::findAll();

        foreach ($mailingsTypesList as $key => $mailingType){
            $mt = new mailingTypeModel($mailingType->id_mailing_type);
            $mailingsTypesList[$key] = $mt;
        }

        return self::toArray($mailingsTypesList);
    }

    public static function getMailingsTypeListForAnimation()
    {
        $mailingsTypesList = MailingTypeOrm::findAll();
        $mailingsTypes = array();

        foreach ($mailingsTypesList as $key => $mailingType){
             if ($mailingType->periodicity == MailingTypeModel::MAILING_BEFORE || $mailingType->periodicity == MailingTypeModel::MAILING_AFTER){
                $mt = new mailingTypeModel($mailingType->id_mailing_type);
                $mailingsTypes[$key] = $mt;
             }
        }
        return self::toArray($mailingsTypes);
    }

    public function getEditAddData()
    {
        return $this;
    }

    public static function addMailingType(object $data)
    {
        $mailingType = new MailingTypeOrm();

        if (empty($data->name) || empty($data->numTemplate)) {
            return false;
        }

        $mailingType->name = $data->name;
        $mailingType->delay = $data->delay ?? 0;
        $mailingType->periodicity = $data->periodicity ?? 0;
        $mailingType->auto_send = $data->autoSend ?? 0;
        $mailingType->num_template = $data->numTemplate;
        $mailingType->campaign = $data->campaign ?? 0;
        $mailingType->num_sender = $data->numSender?? 0;

        $mailingType->save();
        return $mailingType->toArray();
    }

    public static function updateMailingType(int $id, object $data)
    {
        $mailingType = new MailingTypeOrm($id);

        if (!empty($data->name)) {
            $mailingType->name = $data->name;
        }

        $mailingType->delay = $data->delay ?? 0;
        $mailingType->periodicity = $data->periodicity ?? 0;
        $mailingType->auto_send = $data->autoSend ?? 0;
        $mailingType->campaign = $data->campaign ?? 0;
        $mailingType->num_sender = $data->numSender?? 0;

        if (!empty($data->numTemplate)) {
            $mailingType->num_template = $data->numTemplate;
        }

        $mailingType->save();
        return $mailingType->toArray();
    }

    public static function deleteMailing(int $id)
    {
        $mailingType = MailingTypeOrm::findById($id);
        foreach ($mailingType->mailings as $mailing) {
            $mailing->delete();
        }
        $mailingType->delete();
    }


    public static function getMailingType(int $id)
    {
        $mailingType = new MailingTypeModel($id);
        return $mailingType;
    }

    public static function checkTypeMail($data)
    {
        $animMailing = MailingAnimationOrm::find("num_mailing_type" , $data->id);
        if (!empty($animMailing)) {
            return true;
        }
        return false;
    }

    public static function checkMailtplIsUse(int $id)
    {
        $mailingType = MailingTypeOrm::find("num_template", $id);
        if (!empty($mailingType)){
            return true;
        }
        return false;
    }

    public static function getTypeComIsSet()
    {
        $registration = MailingTypeOrm::find("periodicity", self::MAILING_REGISTRATION);
        $confirmation = MailingTypeOrm::find("periodicity", self::MAILING_CONFIRMATION);
        $annulation = MailingTypeOrm::find("periodicity", self::MAILING_ANNULATION);
        $sendToBibli = MailingTypeOrm::find("periodicity", self::MAILING_SEND_TO_BIBLI);

        return [
            "registration" => count($registration),
            "confirmation" => count($confirmation),
            "annulation" => count($annulation),
            "sendtobibli" => count($sendToBibli)
        ];
    }
}