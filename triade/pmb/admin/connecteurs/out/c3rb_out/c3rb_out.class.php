<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: c3rb_out.class.php,v 1.2.4.2 2025/03/25 10:21:10 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;
require_once $class_path . "/connecteurs_out.class.php";
require_once $class_path . "/connecteurs_out_sets.class.php";
require_once $class_path . "/external_services_converters.class.php";
require_once $class_path . "/encoding_normalize.class.php";

class c3rb_out extends connecteur_out
{

    public function get_config_form()
    {
        $result = '';
        return $result;
    }

    public function update_config_from_form()
    {
        return;
    }

    public function instantiate_source_class($source_id)
    {
        return new c3rb_out_source($this, $source_id, $this->msg);
    }

    // On chargera nous même les messages si on en a besoin
    public function need_global_messages()
    {
        return false;
    }

    public function process($source_id, $pmb_user_id)
    {
        global $userid, $sessionid, $token;

        // verif parametres requete
        $userid = intval($userid);
        $c3rb_sessionid = is_string($sessionid) ? $sessionid : '';
        $c3rb_token = is_string($token) ? $token : '';

        if (empty($userid) || empty($sessionid) || empty($token)) {
            header('Content-Type: application/xml', null, 400);
            echo '<?xml version="1.0" encoding="utf-8"?><error>bad request</error>';
            return;
        }

        // verif existence userid
        $q = "select empr_nom, empr_prenom, empr_cb, empr_year, empr_cp, empr_ville, empr_date_expiration, empr_mail from empr where id_empr=".$userid." and empr_date_expiration > date(now())";
        $r = pmb_mysql_query($q);
        $n = pmb_mysql_num_rows($r);
        if (! $n) {
            header('Content-Type: application/xml', null, 400);
            echo '<?xml version="1.0" encoding="utf-8"?><error>invalid userid</error>';
            return;
        }

        // verif existence token
        $sessions_tokens = new sessions_tokens('c3rb');
        $sessions_tokens->set_token($c3rb_sessionid);
        $sessions_tokens_sessid = $sessions_tokens->get_SESSID();
        if(empty($sessions_tokens_sessid)) {
            header('Content-Type: application/xml', null, 400);
            echo '<?xml version="1.0" encoding="utf-8"?><error>invalid token</error>';
            return;
        }

        // verif coherence token
        $source = $this->instantiate_source_class($source_id);
        $param = $source->config;
        $verified_token = hash('sha256', $param['c3rb_shared_key'] . $userid . $c3rb_sessionid);
        if ($c3rb_token != $verified_token) {
            header('Content-Type: application/xml', null, 400);
            echo '<?xml version="1.0" encoding="utf-8"?><error>invalid token</error>';
            return;
        }

        $res = pmb_mysql_fetch_assoc($r);
        $xml_response = '<?xml version="1.0" encoding="utf-8"?>';
        $xml_response .= '<datas>';
        $xml_response .= '<userid>' . $userid . '</userid>';
        $xml_response .= '<nom>' . $res['empr_nom'] . '</nom>';
        $xml_response .= '<prenom>' . $res['empr_prenom'] . '</prenom>';
        $xml_response .= '<carte>' . $res['empr_cb'] . '</carte>';
        $empr_year = empty($res['empr_year']) ? '1970' : $res['empr_year'];
        $xml_response .= '<date_naiss>' . '01/01/' . $empr_year . '</date_naiss>';
        $xml_response .= '<cp>' . $res['empr_cp'] . '</cp>';
        $xml_response .= '<ville>' . $res['empr_ville'] . '</ville>';
        $fin_adhes = explode('-', $res['empr_date_expiration']);
        $xml_response .= '<fin_adhes>' . $fin_adhes[2] . '/' . $fin_adhes[1] . '/' . $fin_adhes[0] . '</fin_adhes>';
        $xml_response .= '<mail>' . $res['empr_mail'] . '</mail>';
        $xml_response .= '</datas>';
        $xml_response = encoding_normalize::utf8_normalize($xml_response);
        header('Content-Type: application/xml', null, 200);
        echo $xml_response;
    }
}

class c3rb_out_source extends connecteur_out_source
{
    public function get_config_form()
    {
        global $charset;
        $result = parent::get_config_form();

        // Adresse du Web service sortant PMB
        $result .= '<div class=row><label class="etiquette">' . htmlentities($this->msg["c3rb_service_endpoint"], ENT_QUOTES, $charset) . '</label><br />';
        if ($this->id) {
            $result .= '<a target="_blank" href="ws/connector_out.php?source_id=' . $this->id . '">ws/connector_out.php?source_id=' . $this->id . '</a>';
        } else {
            $result .= htmlentities($this->msg["c3rb_service_endpoint_unrecorded"], ENT_QUOTES, $charset);
        }
        $result .= "</div>";

        //
        $result .= "
        <div class='row'>&nbsp;</div>
        <div class='row'>
            <label class='etiquette' for='c3rb_sso_endpoint'>" . htmlentities($this->msg['c3rb_sso_endpoint'], ENT_QUOTES, $charset) . "</label><br />
            <input type='text' class='saisie-80em' id='c3rb_sso_endpoint' name='c3rb_sso_endpoint' value='" . $this->config['c3rb_sso_endpoint'] . "' />
        </div>
        <div class='row'>
            <label class='etiquette' for='c3rb_shared_key'>" . htmlentities($this->msg['c3rb_shared_key'], ENT_QUOTES, $charset) . "</label><br />
            <input type='password' class='saisie-80em' name='c3rb_shared_key' id='c3rb_shared_key' autocomplete='off' value='" . htmlentities($this->config['c3rb_shared_key'], ENT_QUOTES, $charset) . "' />
            <span class='fa fa-eye' onclick='toggle_password(this, \"c3rb_shared_key\");' ></span>
        </div>";

        return $result;
    }

    public function update_config_from_form()
    {
        // donnees postees
        global $c3rb_sso_endpoint, $c3rb_shared_key;

        parent::update_config_from_form();
        $this->config['c3rb_sso_endpoint'] = $c3rb_sso_endpoint;
        $this->config['c3rb_shared_key'] = $c3rb_shared_key;
    }
}
