<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: divercities.class.php,v 1.2.10.1 2025/03/25 10:21:10 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path."/connecteurs_out.class.php";
require_once $class_path."/connecteurs_out_sets.class.php";
require_once $class_path."/external_services_converters.class.php";
require_once $class_path."/encoding_normalize.class.php";

class divercities extends connecteur_out {


    public function get_config_form() {
        $result = '';
        return $result;
    }


    public function update_config_from_form() {
        return;
    }


    public function instantiate_source_class($source_id) {
        return new divercities_source($this, $source_id, $this->msg);
    }


    //On chargera nous même les messages si on en a besoin
    public function need_global_messages() {
        return false;
    }


    public function process($source_id, $pmb_user_id) {

        global $uid, $token;

        $userinfos = [];
        $msg = '';
        header('Content-Type: application/json');

        if(!isset($uid) || !is_numeric($uid) || !$uid) {
            $msg = encoding_normalize::utf8_normalize($this->msg['divercities_no_uid']);
            echo encoding_normalize::json_encode(array('state'=>'KO', 'msg'=>$msg, 'userinfos'=>$userinfos));
            return;
        }
        if(!isset($token) || !is_string($token) || !$token) {
            $msg = encoding_normalize::utf8_normalize($this->msg['divercities_no_token']);
            echo encoding_normalize::json_encode(array('state'=>'KO', 'msg'=>$msg, 'userinfos'=>$userinfos));
            return;
        }

        $q = "select empr_nom, empr_prenom,empr_mail from empr where id_empr={$uid} and empr_date_expiration > date(now())";
        $r = pmb_mysql_query($q);
        $n = pmb_mysql_num_rows($r);
        if(!$n) {
            $msg = encoding_normalize::utf8_normalize($this->msg['divercities_invalid_uid']);
            echo encoding_normalize::json_encode(array('state'=>'KO', 'msg'=>$msg, 'userinfos'=>$userinfos));
            return;
        }

        $source = $this->instantiate_source_class($source_id);
        $param = $source->config;
        $verified_token = md5($uid.$param['shared_key'].$param['station_id']);
        if($token != $verified_token) {
            $msg = encoding_normalize::utf8_normalize($this->msg['divercities_invalid_token']);
            echo encoding_normalize::json_encode(array('state'=>'KO', 'msg'=>$msg, 'userinfos'=>$userinfos));
            return;
        }

        $res = pmb_mysql_fetch_assoc($r);
        $userinfos['lastname'] = $res['empr_nom'];
        $userinfos['firstname'] = $res['empr_prenom'];
        $userinfos['mail'] = $res['empr_mail'];

        echo encoding_normalize::json_encode(array('state'=>'OK', 'msg'=>$msg, 'userinfos'=>$userinfos));

    }
}


class divercities_source extends connecteur_out_source {


    public function get_config_form() {
        global $charset;
        $result = parent::get_config_form();

        //init parametres a la creation
        if(!$this->id) {
            $this->config['station_id'] = '';
            $this->config['shared_key'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
        //Adresse du Web service
        $result .= '<div class=row><label class="etiquette">'.htmlentities($this->msg["divercities_service_endpoint"], ENT_QUOTES, $charset).'</label><br />';
        if ($this->id) {
            $result .= '<a target="_blank" href="ws/connector_out.php?source_id='.$this->id.'">ws/connector_out.php?source_id='.$this->id.'</a>';
        } else {
            $result .= htmlentities($this->msg["divercities_service_endpoint_unrecorded"], ENT_QUOTES, $charset);
        }
        $result .= "</div>";

        //
        $result.= "
        <div class='row'>&nbsp;</div>
        <div class='row'>
            <label class='etiquette' for='station_id'>".htmlentities($this->msg['divercities_station_id'],ENT_QUOTES,$charset)."</label><br />
            <input type='text' class='saisie-20em' id='station_id' name='station_id' value='".$this->config['station_id']."' />
        </div>
        <div class='row'>
            <label class='etiquette' for='shared_key'>".htmlentities($this->msg['divercities_shared_key'],ENT_QUOTES,$charset)."</label><br />
            <input type='text' class='saisie-80emr' name='shared_key' id='shared_key' value='".htmlentities($this->config['shared_key'],ENT_QUOTES,$charset)."' />
        </div>";

        return $result;
    }


    public function update_config_from_form() {

        //donnees postees
        global $station_id, $shared_key;

        parent::update_config_from_form();
        $this->config['station_id'] = $station_id;
        $this->config['shared_key'] = $shared_key;
    }
}
