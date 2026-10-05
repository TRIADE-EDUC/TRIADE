<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.class.php,v 1.41 2024/03/22 15:31:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) 
	die("no access");

global $base_path, $class_path, $include_path, $msg;
global $empr_pnb_devices_list;
global $pmb_pnb_param_login, $pmb_pnb_typedoc_id;
global $opac_url_base, $pmb_quotas_avances;
global $pmb_pnb_loan_counter;

require_once $class_path.'/pnb/dilicom.class.php';
require_once $class_path.'/emprunteur.class.php';
require_once $class_path.'/ajax_pret.class.php';
require_once $class_path.'/pret.class.php';
require_once $include_path.'/h2o/pmb_h2o.inc.php';
require_once $include_path.'/notice_authors.inc.php';

class pnb {
    
    public function __construct(){}
    
    public function get_devices(){
        global $base_path;
        if(!(file_exists($base_path.'/temp/pnb_devices_list.temp') && ((time()-86400) > filemtime($base_path.'/temp/pnb_devices_list.temp')))){
            $dilicom = new dilicom();
            $data = encoding_normalize::json_decode($dilicom->query('getUserAgent'), true);
            
            $to_sort_devices = array();
            foreach($data['listUserAgent'] as $device) {
                $to_sort_devices[$device['appName']] = $device;
            }
            ksort($to_sort_devices);
            $data['listUserAgent'] = array();
            foreach($to_sort_devices as $device) {
                $data['listUserAgent'][] = $device;
            }
            file_put_contents($base_path.'/temp/pnb_devices_list.temp', encoding_normalize::json_encode($data));
        }
        $devices = encoding_normalize::json_decode(file_get_contents($base_path.'/temp/pnb_devices_list.temp'), true);
        return $devices['listUserAgent'];
    }
    
    public function get_devices_list($empr_id) {
        global $include_path;
        $empr = new emprunteur($empr_id);
        
        $empr_devices = $empr->get_devices();
        $pnb_devices = $this->get_devices();
        foreach($pnb_devices as $key => $device){
            $pnb_devices[$key]['selected'] = false;
            if(in_array($device['userAgentId'], $empr_devices)){
                $pnb_devices[$key]['selected'] = true;
            }
        }
        $h2o = H2o_collection::get_instance($include_path .'/templates/pnb/pnb_devices.tpl.html');
        return $h2o->render(array('devices' => $pnb_devices));
    }
    
    public function save_devices_list($empr_id){
        global $empr_pnb_devices_list;
        $empr = new emprunteur($empr_id);
        $empr->set_devices($empr_pnb_devices_list);
        $empr->save_devices();
    }
    
    public function get_empr_loans($empr_id) {
        global $msg;
        $loans = array();

        $sql = "SELECT notices_m.notice_id as num_notice_mono, bulletin_id, IF(pret_retour>sysdate(),0,1) as retard, expl_id," ;
        $sql.= "date_format(pret_retour, '".$msg["format_date_sql"]."') as aff_pret_retour, pret_retour, ";
        $sql.= "date_format(pret_date, '".$msg["format_date_sql"]."') as aff_pret_date, " ;
        $sql.= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '".$msg["format_date_sql"]."'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id, tdoc_libelle, empr_location, location_libelle ";
        $sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
        $sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
        $sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
        $sql.= "        docs_type, docs_location , pret join pnb_orders_expl on pnb_order_expl_num = pret_idexpl, empr ";
        $sql.= "WHERE expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr and expl_location = idlocation ";
        $sql.= " order by location_libelle, pret_retour";
        
        $result = pmb_mysql_query($sql);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
                $responsab = get_notice_authors($row['num_notice_mono']) ;
                
                $as = array_search ("0", $responsab["responsabilites"]) ;
                if ($as!== FALSE && $as!== NULL) {
                    $auteur_0 = $responsab["auteurs"][$as] ;
                    $auteur = new auteur($auteur_0["id"]);
                    $mention_resp = $auteur->get_isbd();
                } else {
                    $as = array_keys ($responsab["responsabilites"], "1" ) ;
                    $aut1_libelle = array();
                    for ($i = 0 ; $i < count($as) ; $i++) {
                        $indice = $as[$i] ;
                        $auteur_1 = $responsab["auteurs"][$indice] ;
                        $auteur = new auteur($auteur_1["id"]);
                        $aut1_libelle[]= $auteur->get_isbd();
                    }
                    $mention_resp = implode (", ",$aut1_libelle) ;
                }
                
                $mention_resp ? $auteur = $mention_resp : $auteur="";
                $loans[] = $row;
                $loans[count($loans)-1]['author'] = $auteur;
            }
        }
        return $loans;
    }
    
    public function get_empr_loans_list($empr_id) {
        global $include_path;
        
        $empr_loans = $this->get_empr_loans($empr_id);
        $h2o = H2o_collection::get_instance($include_path .'/templates/pnb/pnb_empr_loans.tpl.html');
        
        return $h2o->render(array('loans' => $empr_loans));
    }
    
    public function loan_book($empr_id, $record_id, $user_agent, $pass ='', $hint_pass ='') {
        global $pmb_pnb_param_login, $pmb_pnb_typedoc_id;
        global $opac_url_base;
        global $pmb_quotas_avances, $msg;
        
        $loaner = new emprunteur($empr_id);
        $order = $this->get_order_expl($record_id);
        
        if(count($order)){ 
            $pnb_order_line_id = $order['pnb_order_line_id'];
            if(!$pnb_order_line_id){
                return array("status" => false, "message" => $msg['pnb_unknown_order'], 'infos' => 'get_order_line_id');
            }
            
            if($this->is_already_borrowed($empr_id, $pnb_order_line_id)){
                $loan_link = $this->get_pnb_loan_link($empr_id, $pnb_order_line_id);
                return array("status" => false, "message" => $msg['pnb_loan_already_borrowed'], 'infos' => array( 'link' => array( 'url' => $loan_link )));
            }
            
            $pnb_record_order = new pnb_record_orders();
            $nta = $pnb_record_order->get_loans_completed_number($pnb_order_line_id);
            if (!$nta) {
                return array("status" => false, "message" => $msg['pnb_loan_no_token'], 'infos' => "Plus de jetons restant");
            }
            
            $pnb_loan = new pnb_loan();
            $pnb_loan->check_pieges(0, $empr_id, 0, $order['expl_id'], false);
            if($pnb_loan->status){                
                return array("status" => false, "message" => $pnb_loan->error_message, 'infos' => 'check_pieges');
            }
                      
            $duree_pret = 0;
            if($pmb_quotas_avances) {
                $qt=new quota("PNB_LOAN_TIME_QUOTA");
                $struct = array();
                $struct["READER"]=$empr_id;
                $struct["EXPL"]=$order['expl_id'];
                $struct["NOTI"] = exemplaire::get_expl_notice_from_id($order['expl_id']);
                $struct["BULL"] = exemplaire::get_expl_bulletin_from_id($order['expl_id']);
                $duree_pret=$qt->get_quota_value($struct);
                if ($duree_pret==-1) $duree_pret=0;
            } else {
                $q = "select duree_pret from docs_type where idtyp_doc=".$pmb_pnb_typedoc_id;
                $r = pmb_mysql_query($q);
                if (pmb_mysql_num_rows($r)) {
                    $duree_pret = pmb_mysql_result($r, 0, 0);
                }
            }
            //pour dilicom, la date est celle du prêt +1 / à la date enregistrée dans PMB
            $duree_pret += 1;
            $query = "SELECT date_add(CURDATE(), INTERVAL $duree_pret DAY) as date_retour";
            $result=pmb_mysql_query($query);
            $date_retour = pmb_mysql_fetch_row($result)[0];
            
            // Formatage date_retour pour Dilicom
            $return_date = new DateTime($date_retour);
            $loanEndDate = $return_date->format(DateTime::ISO8601);
            
            //Création de l'URL de retour anticipé
            $callback_context = [
                'empr_id' => $empr_id,
                'expl_id' => $order['expl_id']
            ];
            $extend_shorturl = new shorturl_type_pnb();
            $extend_hash = $extend_shorturl->generate_hash("extendCallback", $callback_context);
            
            $callback_context["extend_hash"] = $extend_hash;
            
            $return_shorturl = new shorturl_type_pnb();
            $return_hash = $return_shorturl->generate_hash("returnCallback", $callback_context);
            
            //info obligatoire pour dilicom mais pas forcement renseignees
            $DRMinfo_readerPass = $loaner->get_pnb_password();
            $DRMinfo_readerHint = $loaner->get_pnb_password_hint();
            if(!empty($pass) && !empty($hint_pass)) {
            	$DRMinfo_readerPass = base64_encode(hash('sha256', $pass, 1));
            	$DRMinfo_readerHint = $hint_pass;
            }
            if($DRMinfo_readerHint == '' ) {
            	$DRMinfo_readerHint = 'hint';
            }
            
            $param_dilicom = [
            	'glnLoaner' => $pmb_pnb_param_login,
                'glnContractor' => $pmb_pnb_param_login,
                'orderLineId' => $pnb_order_line_id,
                'loanId' => $empr_id . 'x' . $order['expl_id'] . 'x' . date("Ymd"),
                'ean13' => $this->get_ean_from_record($record_id),
                'accessMedium' => 'DOWNLOAD',
                'localization' => 'EX_SITU',
                'loanEndDate' => $loanEndDate,
            	'callbackUrl' => $opac_url_base."s.php?h=".$return_hash,
                'DRMinfo.'.'userAgent' => ($user_agent) ? $user_agent : NULL,
            	'DRMinfo.'.'readerPass' => $DRMinfo_readerPass,
            	'DRMinfo.'.'readerHint' => $DRMinfo_readerHint,
                'DRMinfo.'.'readerId' => $loaner->cb,
            	'DRMinfo.'.'extendUrl' => $opac_url_base."s.php?h=".$extend_hash,
                //'userInfo.'.'year' => intval($loaner->birth),
                'userInfo.'.'year' => rand(1950, 2010),
                'userInfo.'.'gender' => ($loaner->sexe == 1 ? 'H' : 'F'),
            ];  
            
            $dilicom = new dilicom();
            $response = $dilicom->query('loanBook', $param_dilicom);
            $response = encoding_normalize::json_decode($response, true);           
            if(!empty($response) && !empty($response['returnStatus'])){
                $response['num_loaner'] = $loaner->id;
                $response['num_expl'] = $order['expl_id'];
                switch($response['returnStatus']) {
                    case 'OK' :
                        $pnb_loan->confirm_pret($empr_id, $order['expl_id'], $date_retour, 'Dilicom');
                        $this->save_pnb_loan_infos($response);
                        $this->increment_loan_counter();
                        return array("status" => true, "message"=> $msg['pnb_loan_succeed'], "infos" => $response);
                        break;
                    case 'RECALL' :
                        $pnb_loan->confirm_pret($empr_id, $order['expl_id'], $date_retour, 'Dilicom');
                        $this->save_pnb_loan_infos($response);
                        return array("status" => true, "message"=> $msg['pnb_loan_already_borrowed'], "infos" => $response);
                        break;
                    default:
                        //Suppression du prêt côté PMB si le prêt n'a pas fonctionné chez Dilicom
                        $query = "delete from pret where pret_idexpl = '".$order['expl_id']."'";
                        pmb_mysql_query($query);
                        return array("status" => false, "message" => $msg['pnb_loan_failed'], 'infos' => $response['returnMessage']);
                        break;
                }
            } else {                
                //Suppression du prêt côté PMB si pas de réponse de Dilicom
                $query = "delete from pret where pret_idexpl = '".$order['expl_id']."'";
                pmb_mysql_query($query);
                return array("status" => false, "message" => $msg['pnb_dilicom_no_response'], 'infos' => "Pas de reponse de Dilicom");
            }
        }
        return array("status" => false, "message" => $msg['pnb_no_order'], 'infos' => "Pas de commande");
    }
    
    public function return_book($empr_id,$expl_id){  
        
        $pret = new expl_to_do('',$expl_id);
        $result = $pret->do_pnb_retour();
        
        //appel de la suppression specifique au PNB
        pnb_loan::del_pnb_loans($empr_id, $expl_id);

        return pnb::pnb_success($result['message'], $result['infos']);
    }
        
    /**
     * Retourne un identifiant d'exemplaire et un identifiant de ligne de commande depuis l'identifiant d'une notice
     * avec un choix pertinent du jeton
     * 
     * @param int $record_id
     * @return boolean|array
     */
    protected function get_order_expl($record_id){
                
        // On vas chercher la commande qui a le plus de pertinence
        $query = "SELECT pnb_order_line_id, pnb_orders.pnb_current_nta/datediff(pnb_orders.pnb_order_offer_date_end, pnb_orders.pnb_order_offer_date) as pert 
                    FROM `pnb_orders` WHERE pnb_order_num_notice = '". $record_id ."' 
                    ORDER BY pert DESC LIMIT 1 ";
        
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            
            $order =  pmb_mysql_fetch_assoc($result);
            $query = "SELECT expl_id, pnb_order_num as order_num, pnb_order_line_id FROM exemplaires
				LEFT JOIN pret ON exemplaires.expl_id = pret.pret_idexpl
				JOIN pnb_orders_expl ON pnb_orders_expl.pnb_order_expl_num = exemplaires.expl_id
				JOIN pnb_orders ON pnb_orders.id_pnb_order = pnb_orders_expl.pnb_order_num
				WHERE expl_notice = '". $record_id ."' AND expl_bulletin = 0 AND pnb_orders.pnb_order_line_id ='". $order['pnb_order_line_id'] ."' AND pret.pret_idexpl is null
				ORDER BY pnb_orders.pnb_order_offer_date_end ASC";
            $result = pmb_mysql_query($query);
            
            if (pmb_mysql_num_rows($result)) {
                return pmb_mysql_fetch_assoc($result);
            }
        }
        return 0;
    }
    
    protected function get_order_line_id($order_id){
        $query = "select pnb_order_line_id as order_line_id from pnb_orders where id_pnb_order = ".$order_id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            return pmb_mysql_fetch_assoc($result);
        }
        return false;
    }
    
    protected function increment_loan_counter(){
        global $pmb_pnb_loan_counter;
        $pmb_pnb_loan_counter++;
        $query = "UPDATE parametres SET valeur_param='".$pmb_pnb_loan_counter."' where type_param= 'pmb' and sstype_param='pnb_loan_counter' ";
        pmb_mysql_query($query);
    }
    
    protected function get_order_line_id_from_order_num($order_num){
        $query = "select pnb_order_line_id as order_line_id from pnb_orders where id_pnb_order= ".$order_num;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $data = pmb_mysql_fetch_assoc($result);
            return $data['order_line_id'];
        }
        return false;
    }
    
    protected function get_ean_from_record($record_id){
        $query = "select code from notices where notice_id= ".$record_id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $data =  pmb_mysql_fetch_assoc($result);
            return str_replace('-', '', $data['code']);
        }
        return false;
    }
    
    protected function get_pnb_loan_link($empr_id, $pnb_order_line_id){
        $loan_link = "";
        $query = "select pnb_loan_link from pnb_loans where pnb_loan_num_loaner = $empr_id and pnb_loan_order_line_id='".addslashes($pnb_order_line_id)."'";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $loan_link =  pmb_mysql_result($result, 0, 0);
        }
        return $loan_link;
    }
    
    protected function save_pnb_loan_infos($infos){
        global $pmb_pnb_loan_counter;
        $query = "INSERT INTO pnb_loans set ";
        $query .= "id_pnb_loan = '".$pmb_pnb_loan_counter."', ";
        $query .= "pnb_loan_order_line_id = '".$infos['orderLineId']."', ";
        $query .= "pnb_loan_link = '".$infos['link']['url']."', ";
        $query .= "pnb_loan_request_id   = '".$infos['requestId']."', ";
        $query .= "pnb_loan_num_expl = '".$infos['num_expl']."', ";
        $query .= "pnb_loan_num_loaner = '".$infos['num_loaner']."', ";
        $query .= "pnb_loan_drm = '".$infos['protection']."', ";
        $query .= "pnb_loan_loanid = '".$infos['loanId']."' ";
        pmb_mysql_query($query);
    }
    
    public function get_mailto_data($commands_ids){
        global $pmb_pnb_param_login;
        $commands_details = array();
        foreach($commands_ids as $command_id){
            $command_id = intval($command_id);
            $command = new pnb_order($command_id);
            $commands_details[] = array(
                'orderId' =>  $command->get_order_id(),
                'orderCreateDate' => $command->get_offer_formated_date(),
                'orderLineId' =>  $command->get_line_id(),
            );
        }
        if(count($commands_details)){
            $commands_details['GLN'] = $pmb_pnb_param_login;
            $commands_details['address'] = 'technique@dilicom.fr';
        }
        return $commands_details;
    }
    
    public function is_already_borrowed($empr_id, $pnb_order_line_id){
        
        $q = "select count(*) from pnb_loans where pnb_loan_num_loaner=$empr_id and pnb_loan_order_line_id ='".addslashes($pnb_order_line_id)."' ";
        $r = pmb_mysql_query($q);
        $n = 0;
        if(pmb_mysql_num_rows($r)){
           $n = pmb_mysql_result($r,0,0);
        }
        if($n) {
            return true;
        } 
        return false;
    }
    
    public static function delete_pnb_record_links($record_id){
    	$record_id = intval($record_id);
        $query = 'select expl_id, expl_cb from exemplaires where expl_notice =' . $record_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                
                pmb_mysql_query("delete from pret where pret_idexpl ='" . $row['expl_id'] . "' ");
                
                pmb_mysql_query("delete from pnb_loans where pnb_loan_num_expl  ='" . $row['expl_id'] . "' ");
                
                pmb_mysql_query("delete from pnb_orders_expl where pnb_order_expl_num  ='" . $row['expl_id'] . "' ");
                
                exemplaire::del_expl($row['expl_id']);
            }
        }
        pmb_mysql_query("delete from pnb_orders where pnb_order_num_notice  ='" . $record_id . "' ");
    }

    protected function get_order_line_id_from_expl_id($id_empr, $expl_id){
        
        $order_line_id = "";
        $query = "select pnb_loan_order_line_id from pnb_loans where pnb_loan_num_loaner = $id_empr and pnb_loan_num_expl= $expl_id";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $order_line_id =  pmb_mysql_result($result, 0, 0);
        }
        
        return $order_line_id;
    }

    protected function get_loan_id_from_expl_id($id_empr, $expl_id){
        
        $loan_id = "";
        $query = "select pnb_loan_loanid from pnb_loans where pnb_loan_num_loaner = $id_empr and pnb_loan_num_expl= $expl_id";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $loan_id =  pmb_mysql_result($result, 0, 0);
        }
        
        return $loan_id;
    }
    
    public function return_book_to_Dilicom($id_empr, $expl_id){
        global $msg;
        
        $orderLineId = $this->get_order_line_id_from_expl_id($id_empr, $expl_id);
        $loanId = $this->get_loan_id_from_expl_id($id_empr, $expl_id);
        if (empty($orderLineId) || empty($loanId)){
            return pnb::pnb_error($msg['pnb_return_loan_fail']);
        }
        
        $dilicom = new dilicom();
        $response = $dilicom->returnLoan($orderLineId,$loanId);

        return $response;
    }

    public function extend_loan_to_dilicom($id_empr, $expl_id){
        global $msg,$pmb_quotas_avances;
        
        $date = $this->get_new_loan_end_date($id_empr, $expl_id);
        if (isset($date["status"]) && $date["status"] == false) {
            return $date;
        }
        
        $orderLineId = $this->get_order_line_id_from_expl_id($id_empr, $expl_id);
        $loanId = $this->get_loan_id_from_expl_id($id_empr, $expl_id);

        if (empty($orderLineId) || empty($loanId)){
            return pnb::pnb_error($msg['pnb_extend_loan_fail']);
        }

        $dilicom = new dilicom();
        $response = $dilicom->extendLoan($orderLineId,$loanId,$date);

        return $response;
        
    }
    
    protected function get_new_loan_end_date($id_empr,$expl_id){
        global $pmb_quotas_avances,$pmb_pret_restriction_prolongation, $msg;
        
        $loanData = $this->get_empr_loan($id_empr, $expl_id);
        if (empty($loanData['pret_retour'])){
            return pnb::pnb_error($msg['pnb_fail_msg']);
            
        }
        //On recupere le parametrage des prolongations
        if ($pmb_quotas_avances){
            $paramNbrProlong = $this->get_quotas("PNB_LOAN_PROLONG_NMBR_QUOTA",$id_empr,$expl_id);
            $paramDureeProlong = $this->get_quotas("PNB_LOAN_PROLONG_TIME_QUOTA",$id_empr,$expl_id);
        } else if ($pmb_pret_restriction_prolongation){
            global $pmb_pret_nombre_prolongation, $opac_pret_duree_prolongation;
            $paramNbrProlong = $pmb_pret_nombre_prolongation;
            $paramDureeProlong = $opac_pret_duree_prolongation;
        }
        
        //Calcule du nombre de prolongations restantes, s'il existe des restrictions
        if ($pmb_pret_restriction_prolongation){
            $prolongIsOk = $this->check_cpt_prolong($id_empr, $expl_id, $paramNbrProlong);
            if (!$prolongIsOk) {
                return pnb::pnb_error($msg['pnb_extend_max_nb_prolong_reached']);
            }
        }
        
        //Calcul de la nouvelle date de fin de prêt
        $dateInitial = new \DateTime($loanData["pret_retour"]);
        $newEndDate = $dateInitial->add(new \DateInterval("P".$paramDureeProlong."D"));
        // Formatage date_retour pour Dilicom
        $newLoanEndDate = $newEndDate->format(DateTime::ISO8601);
        
        return $newLoanEndDate;
    }
    
    public function get_empr_loan($id_empr, $expl_id){
        
        $loans = $this->get_empr_loans($id_empr);
        $loanData = array();

        foreach ($loans as $loan){
            if ($loan["expl_id"] == $expl_id){
                $loanData = $loan;
            }
        }

        return $loanData;
        
    }
    
    protected function check_cpt_prolong($id_empr, $expl_id, $paramNbrProlong) {

        $cpt_prolongation = 0;
        $query = "SELECT cpt_prolongation FROM pret WHERE pret_idempr = $id_empr AND pret_idexpl = $expl_id";
        $r = pmb_mysql_query($query);
        $cpt_prolongation = pmb_mysql_result($r, 0, 0);
        $availableProlong = $paramNbrProlong - $cpt_prolongation;

        if ($availableProlong <= 0){
            return false;
        }
        return true;
    }
    
    protected function get_quotas($nameQuotas, $id_empr, $expl_id){

        $quota = 0;
        
        $qt=new quota($nameQuotas);
        $struct = array();
        $struct["READER"]=$id_empr;
        $struct["EXPL"]=$expl_id;
        $struct["NOTI"] = exemplaire::get_expl_notice_from_id($expl_id);
        $struct["BULL"] = exemplaire::get_expl_bulletin_from_id($expl_id);
        $quota=$qt->get_quota_value($struct);
        if ($quota==-1) $quota=0;
        
        return $quota;
    }
    
    public function extend_loan($id_empr,$expl_id){
        global $msg, $class_path;

        $date = $this->get_new_loan_end_date($id_empr, $expl_id);
        if (isset($date["status"]) && $date["status"] == false) {
            return $date;
        }
        $date = new DateTime($date);
        $pnb_loan = new pnb_loan();
        $response = $pnb_loan->extendLoan($id_empr, $expl_id, $date->format("Y-m-d"));
        if (isset($response["status"]) && $response["status"] == false){
            return  $response;
        }
        return pnb::pnb_success($msg['pnb_extend_loan_success'], $response);
    }
    
    
    public static function pnb_error($errorMsg,$infos=""){
        return ["status" => false, "message" => $errorMsg, "infos" => $infos];
    }

    public static function pnb_success($successMsg,$infos=""){
        return ["status" => true, "message" => $successMsg, "infos" => $infos];
    }
    
    
}


