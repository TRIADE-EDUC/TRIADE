<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.class.php,v 1.19.10.1 2025/05/09 09:02:48 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $base_path, $include_path, $class_path, $msg;
global $allow_pnb;
global $empr_pnb_devices_list, $pnb_password, $pnb_password_hint;
global $empr_pnb_device, $notice_id;
global $opac_pnb_param_webservice_url, $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password;

require_once $class_path.'/pnb/dilicom.class.php' ;
require_once $class_path.'/emprunteur.class.php' ;
require_once $include_path.'/h2o/pmb_h2o.inc.php';
require_once $include_path."/notice_authors.inc.php";
require_once $class_path.'/jsonRPCClient.php';

class pnb {

	public function get_devices()
    {
        global $base_path;
        if (! (file_exists($base_path . '/temp/pnb_devices_list.temp') && ((time() - 86400) > filemtime($base_path . '/temp/pnb_devices_list.temp')))) {
            $dilicom = new dilicom();
            $data = encoding_normalize::json_decode($dilicom->query('getUserAgent'), true);

            $to_sort_devices = array();
            if (! empty($data)) {
                foreach ($data['listUserAgent'] as $device) {
                    $to_sort_devices[$device['appName']] = $device;
                }
                ksort($to_sort_devices);
                $data['listUserAgent'] = array();
                foreach ($to_sort_devices as $device) {
                    $data['listUserAgent'][] = $device;
                }
            }
            file_put_contents($base_path . '/temp/pnb_devices_list.temp', encoding_normalize::json_encode($data));
        }
        $devices = encoding_normalize::json_decode(file_get_contents($base_path . '/temp/pnb_devices_list.temp'), true);
        return $devices['listUserAgent'];
    }

    public function get_devices_list($empr_id)
    {
        global $include_path;
        $empr = new emprunteur($empr_id);

        $configWs = dilicom::is_pnb_active();
        $empr_devices = $empr->get_devices();
        $pnb_devices = $this->get_devices();
        if (! empty($pnb_devices)) {
            foreach ($pnb_devices as $key => $device) {
                $pnb_devices[$key]['selected'] = false;
                if (in_array($device['userAgentId'], $empr_devices)) {
                    $pnb_devices[$key]['selected'] = true;
                }
            }
        }
        $h2o = H2o_collection::get_instance($include_path . '/templates/pnb/pnb_devices.tpl.html');
        return $h2o->render(array(
            'devices' => $pnb_devices,
            'configWs' => $configWs
        ));
    }

    public function get_empr_devices_list($notice_id)
    {
        global $include_path, $base_path;
        $empr = new emprunteur($_SESSION['id_empr_session']);
        $empr_devices = $empr->get_devices();
        $pnb_devices = $this->get_devices();
        $configWs = dilicom::is_pnb_active();
        $empr_devices_list = array();

        foreach ($pnb_devices as $device) {
            if (in_array($device['userAgentId'], $empr_devices)) {
                $empr_devices_list[] = $device;
            }
        }
        $userHasDefaultPwd = $empr->get_pnb_password() ? true : false;

        $h2o = H2o_collection::get_instance($include_path . '/templates/pnb/pnb_devices_modal.tpl.html');
        $vue = $h2o->render(array(
            'devices' => $empr_devices_list,
            'notice_id' => $notice_id,
            'configWs' => $configWs,
            'userHasDefaultPwd' => $userHasDefaultPwd
        ));
        $vue = str_replace("!!link_param_device!!", $base_path . '/empr.php?tab=pnb&lvl=pnb_devices', $vue);
        $vue = str_replace("!!link_param_password!!", $base_path . '/empr.php?tab=pnb&lvl=pnb_parameters', $vue);

        return [
            'raw' => $empr_devices_list,
            'userHasDefaultPwd' => $userHasDefaultPwd,
            'vue' => $vue
        ];
    }

    public function save_devices_list($empr_id)
    {
        global $empr_pnb_devices_list;
        $empr = new emprunteur($empr_id);
        $empr->set_devices($empr_pnb_devices_list);
        $empr->save_devices();
    }

    public function get_parameters($empr_id)
    {
        global $include_path;
        $empr = new emprunteur($empr_id);

        $empr->init_pnb_parameters();
        $empr_pnb_password_hint = $empr->get_pnb_password_hint();
        $empr_pnb_password = $empr->get_pnb_password();
        $h2o = H2o_collection::get_instance($include_path . '/templates/pnb/pnb_parameters.tpl.html');
        return $h2o->render(array(
            'parameters' => array(
                'pnb_password' => $empr_pnb_password,
                'pnb_password_hint' => $empr_pnb_password_hint
            )
        ));
    }

    public function save_parameters($empr_id)
    {
        global $pnb_password;
        global $pnb_password_hint;
        $empr = new emprunteur($empr_id);
        $empr->set_pnb_password($pnb_password);
        $empr->set_pnb_password_hint($pnb_password_hint);
        $empr->save_pnb_password();
        $empr->save_pnb_password_hint();
    }

    public function get_empr_loans($empr_id)
    {
        global $msg;
        $loans = array();

        $sql = "SELECT pnb_loans.pnb_loan_link as epub_link, pnb_loans.pnb_loan_drm as drm, notices_m.notice_id as num_notice_mono, bulletin_id, IF(pret_retour>sysdate(),0,1) as retard, expl_id,";
        $sql .= "date_format(pret_retour, '" . $msg["format_date_sql"] . "') as aff_pret_retour, pret_retour, ";
        $sql .= "date_format(pret_date, '" . $msg["format_date_sql"] . "') as aff_pret_date, ";
        $sql .= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '" . $msg["format_date_sql"] . "'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id, tdoc_libelle, empr_location, location_libelle ";
        $sql .= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
        $sql .= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
        $sql .= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
        $sql .= "        docs_type, docs_location , pret join pnb_orders_expl on pnb_order_expl_num = pret_idexpl join pnb_loans on pnb_loan_num_expl = pret_idexpl and pnb_loan_num_loaner=" . $empr_id . ", empr ";
        $sql .= "WHERE expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr and expl_location = idlocation ";
        $sql .= " order by pret_retour";

        $result = pmb_mysql_query($sql);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $responsab = array(
                    "responsabilites" => array(),
                    "auteurs" => array()
                ); // les auteurs
                $responsab = get_notice_authors($row['num_notice_mono']);

                $as = array_search("0", $responsab["responsabilites"]);
                if ($as !== FALSE && $as !== NULL) {
                    $auteur_0 = $responsab["auteurs"][$as];
                    $auteur = new auteur($auteur_0["id"]);
                    $mention_resp = $auteur->get_isbd();
                } else {
                    $as = array_keys($responsab["responsabilites"], "1");
                    $aut1_libelle = array();
                    for ($i = 0; $i < count($as); $i ++) {
                        $indice = $as[$i];
                        $auteur_1 = $responsab["auteurs"][$indice];
                        $auteur = new auteur($auteur_1["id"]);
                        $aut1_libelle[] = $auteur->get_isbd();
                    }
                    $mention_resp = implode(", ", $aut1_libelle);
                }

                $mention_resp ? $auteur = $mention_resp : $auteur = "";
                $loans[] = $row;
                $loans[count($loans) - 1]['author'] = $auteur;
            }
        }
        return $loans;
    }

    public function get_empr_loans_list($empr_id)
    {
        global $include_path, $allow_pnb, $opac_pnb_loan_display_mode;
        if ($allow_pnb) {
            $empr_loans = $this->get_empr_loans($empr_id);
            $h2o = H2o_collection::get_instance($include_path . '/templates/pnb/pnb_empr_loans.tpl.html');
            return $h2o->render(array(
                'loans' => $empr_loans,
                'pnb_loan_display_mode' => $opac_pnb_loan_display_mode
            ));
        }
        return '';
    }

    /**
     * Effectue un pret numerique
     *
     * @param string $empr_pnb_device
     * @param int $notice_id : identifiant notice
     * @param string $pass
     * @param string $hint_pass
     */
    public function loan_book($empr_pnb_device, $notice_id, $pass = '', $hint_pass = '')
    {
        global $opac_pnb_param_webservice_url;
        global $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password;
        global $allow_pnb, $msg;

        if (! $allow_pnb) {
            print encoding_normalize::json_encode(array(
                "status" => false,
                "message" => $msg['pnb_not_allowed'],
                'infos' => 'pnb_not_allowed'
            ));
            return;
        }

        $result = [];
        try {

            $jsonRPC = new jsonRPCClient(stripslashes($opac_pnb_param_webservice_url));
            $jsonRPC->setUser(stripslashes($pmb_pnb_param_ws_user_name));
            $jsonRPC->setPwd(stripslashes($pmb_pnb_param_ws_user_password));

            $result = $jsonRPC->pmbesPNB_loanBook($_SESSION['id_empr_session'], $notice_id, $empr_pnb_device, $pass, $hint_pass);

        } catch (Exception $e) {}

        print json_encode($result);
    }

    /**
     * Effectue un retour de pret numerique
     *
     * @param int $empr_id
     * @param int $expl_id
     * @param number $fromPortal
     * @param string $drm
     * @return string|boolean
     */
    public function return_book($empr_id, $expl_id, $fromPortal = 0, $drm = '')
    {
        global $opac_pnb_param_webservice_url;
        global $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password;

        $result = [];
        try {

            $jsonRPC = new jsonRPCClient(stripslashes($opac_pnb_param_webservice_url));
            $jsonRPC->setUser(stripslashes($pmb_pnb_param_ws_user_name));
            $jsonRPC->setPwd(stripslashes($pmb_pnb_param_ws_user_password));
            $result = $jsonRPC->pmbesPNB_returnBook($empr_id, $expl_id, $fromPortal, $drm);

        } catch (Exception $e) {}
        return json_encode($result);
    }

    /**
     * Effectue une prolongation de pret numerique
     *
     * @param int $empr_id
     * @param int $expl_id
     * @param number $fromPortal
     * @param string $drm
     * @return string|boolean
     */
    public function extend_loan($empr_id, $expl_id, $fromPortal = 0, $drm = '')
    {
        global $opac_pnb_param_webservice_url;
        global $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password;

        $result = [];
        try {

            $jsonRPC = new jsonRPCClient(stripslashes($opac_pnb_param_webservice_url));
            $jsonRPC->setUser(stripslashes($pmb_pnb_param_ws_user_name));
            $jsonRPC->setPwd(stripslashes($pmb_pnb_param_ws_user_password));
            $result = $jsonRPC->pmbesPNB_extendLoan($empr_id, $expl_id, $fromPortal, $drm);

        } catch (Exception $e) {}
        return json_encode($result);
    }

    /**
     * Affiche le formulaire de pret numerique
     *
     * @param int $notice_id
     */
    public function get_loan_form($notice_id)
    {
        global $include_path;
        $devices = $this->get_empr_devices_list($notice_id);
        $h2o = H2o_collection::get_instance($include_path . '/templates/pnb/pnb_loan_form.tpl.html');
        print $h2o->render(array(
            'devices' => $devices,
            'record_id' => $notice_id
        ));
    }

}
