<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingListModel.php,v 1.10 2023/03/02 10:24:54 qvarin Exp $

namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\MailingListOrm;
use Pmb\Common\Models\CustomFieldModel;
use Pmb\Common\Helper\Helper;

class MailingListModel extends Model
{
    protected $ormName = "\Pmb\Animations\Orm\MailingListOrm";
    Const MANUAL_SEND = 0;
    Const AUTOMATIC_SEND = 1;

    public static function getMailingList(int $id)
    {
        $returnList = array();
        $mailList = static::toArray(MailingListOrm::find("num_animation", $id, "id_mailing_list DESC"));

        foreach ($mailList as $key => $mail) {

            $mail['mailing_content'] = json_decode(stripslashes($mail['mailing_content']));
            $mail['response_content'] = json_decode(stripslashes($mail['response_content']));

            $date = new \DateTime($mail['send_at']);
            $mail['send_at'] = $date->format('d/m/Y H:i');

            if (! empty($mail['num_user'])) {
                $user = Helper::getUser($mail['num_user']);
                if (! empty($user)) {
                    $mail['user_name'] = $user['nom'] . ' ' . $user['prenom'];
                }
            }

            $returnList[$key] = $mail;
        }
        return $returnList;
    }
    
    public static function saveMailing($num_animation, $auto_send, $mailing_content, $response_content, $campaignDatas, $numSender){
        //on compte le nombre de mail ayant aboutit et en erreur
        $nb_success_mails = 0;
        $nb_error_mails = 0;
        foreach ($response_content as $animation){
            foreach ($animation as $contact){
                if ($contact['SUCCESS']){
                    $nb_success_mails++;
                } else {
                    $nb_error_mails++;
                }
            }
        }
        
        $mailingList = new MailingListOrm();
        $mailingList->num_animation = $num_animation;
        $mailingList->send_at = date('Y-m-d H:i:s');
        $mailingList->auto_send = $auto_send;
        $mailingList->mailing_content = addslashes(\encoding_normalize::json_encode($mailing_content));
        $mailingList->response_content = addslashes(\encoding_normalize::json_encode($response_content));
        $mailingList->nb_error_mails = $nb_error_mails;
        $mailingList->nb_success_mails = $nb_success_mails;
        $mailingList->num_user = $numSender;
        $mailingList->num_campaign = (!empty($campaignDatas["associated_num_campaign"]) ? $campaignDatas["associated_num_campaign"] : 0 );
        
        $mailingList->save();
    }
}