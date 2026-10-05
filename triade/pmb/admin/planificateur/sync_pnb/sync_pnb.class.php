<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sync_pnb.class.php,v 1.34.2.1 2024/07/26 10:32:15 dbellamy Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/expl.class.php");
require_once($class_path."/tu_notice.class.php");
require_once($class_path."/titre_uniforme.class.php");


class sync_pnb extends scheduler_task {

	private $orders = array();
	private $ws_loans = array();
	private $loans = array();
	private $onix_files =[];
	private $offer_loan_list = array();
	private $error = [
			'ftp' => false,
			'no_files' => false,
	];
	private $loans_to_delete = [];


	public function execution() {

		global $msg, $PMBusername;

		if (! (SESSrights & ADMINISTRATION_AUTH)) {
			$this->add_section_report ( sprintf ( $msg ['planificateur_rights_bad_user_rights'], $PMBusername ) );
			return;
		}

		if (!method_exists ( $this->proxy, "pmbesConvertImport_convert" )) {
			$this->add_content_report ( $this->msg ['pnb_import_error_pmbesConvertImport_convert'] );
			return;
		}
		$this->prior_checks();

		$this->add_section_report($this->msg['pnb_get_db_data']);
		$this->get_orders();
		$this->get_loans();

		$this->add_section_report($this->msg['pnb_get_loan_status']);
		$this->get_loan_status();
		$this->update_progression(20);

		if($this->params['pnb_check_only_ended_loans']) {

			$this->add_section_report($this->msg['pnb_del_ended_loans']);
			$this->check_loans_from_ws();
			$this->get_overdue_loans();
			$this->clean_loans();

			$this->update_progression(100);
			return;

		}

		$this->add_section_report($this->msg['pnb_get_offer_files']);
		$this->get_offer_files();

		if($this->error['ftp']) {
			return;
		}

		if(!$this->error['no_files']) {

			$this->add_section_report($this->msg['pnb_import_onix_files']);
			$this->import_onix2uni();

			$this->add_section_report($this->msg['pnb_check_items']);
			$this->check_exemplaires();

			$this->update_progression(80);
		}

		$this->add_section_report($this->msg['pnb_del_ended_loans']);
		$this->check_loans_from_import();
		$this->check_loans_from_ws();
		$this->get_overdue_loans();
		$this->clean_loans();

		$this->add_section_report($this->msg['pnb_del_ended_orders']);
		$this->clean_orders();

		$this->update_progression(100);
	}

	/**
	 * Vérifications et corrections préalables (erreurs d'import, ...)
	 */
	private function prior_checks() {


		$this->add_section_report($this->msg['pnb_prior_checks']);
		//Suppression des exemplaires attachés à une commande inexistante
		$q = "select pnb_order_expl_num from pnb_orders_expl where pnb_order_num not in (select id_pnb_order from pnb_orders)";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			while($row = pmb_mysql_fetch_row($r)) {
				exemplaire::del_expl($row[0]);
			}
		}
	}

	/**
	 * Recuperation de la liste des commandes en base
	 */
	private function get_orders() {

		$q = "select * from pnb_orders";
		$r = pmb_mysql_query($q);

		if(pmb_mysql_num_rows($r)) {
			while($row = pmb_mysql_fetch_assoc($r)) {
				$this->orders[$row['pnb_order_line_id']]['id_pnb_order'] = $row['id_pnb_order'];
				$this->orders[$row['pnb_order_line_id']]['pnb_order_id_order'] = $row['pnb_order_id_order'];
				$this->orders[$row['pnb_order_line_id']]['pnb_order_num_notice'] = $row['pnb_order_num_notice'];
				$this->orders[$row['pnb_order_line_id']]['pnb_order_nb_simultaneous_loans'] = $row['pnb_order_nb_simultaneous_loans'];
				$this->orders[$row['pnb_order_line_id']]['pnb_current_nta'] = $row['pnb_current_nta'];
				$this->orders[$row['pnb_order_line_id']]['pnb_order_data'] = !empty($row['pnb_order_data']) ? encoding_normalize::json_decode($row['pnb_order_data'], true) : [];
			}
		}
	}


	/**
	 * Recuperation des prets enregistres en base
	 */
	private function get_loans() {

		$q = "SELECT * FROM pnb_loans";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			while($row = pmb_mysql_fetch_assoc($r)) {
				$this->loans[$row['pnb_loan_loanid']]['id_pnb_loan'] = $row['id_pnb_loan'];
				$this->loans[$row['pnb_loan_loanid']]['pnb_loan_loanid'] = $row['pnb_loan_loanid'];
				$this->loans[$row['pnb_loan_loanid']]['pnb_loan_order_line_id'] = $row['pnb_loan_order_line_id'];
				$this->loans[$row['pnb_loan_loanid']]['pnb_loan_num_expl'] = $row['pnb_loan_num_expl'];
				$this->loans[$row['pnb_loan_loanid']]['pnb_loan_num_loaner'] = $row['pnb_loan_num_loaner'];
				$this->loans[$row['pnb_loan_loanid']]['statut'] = '';
			}
		}
	}


	/**
	 * Interrogation des statuts de pret / chaque commande
	 * et recuperation de la liste des prets enregistres au niveau du WS dilicom
	 */
	private function get_loan_status() {
	    if(empty($this->orders)) {
	        return;
	    }
	    
	    $line_ids = array();
	    foreach($this->orders as $pnb_order_line_id => $order) {
	        $line_ids[] = $pnb_order_line_id;
	        if(count($line_ids) === 10) {
	            $this->call_dilicom($line_ids);
	            $line_ids = [];
	        }
	    }
	    
	    if(count($line_ids)) {
	        $this->call_dilicom($line_ids);
	    }
	}
	
	protected function call_dilicom($line_ids) {
	    $returnEndedLoan = 1;
	    $response = dilicom::get_instance()->get_loan_status($line_ids, $returnEndedLoan);
	    if(is_array($response) && count($response["loanResponseLine"])) {
	        foreach ($response["loanResponseLine"] as $response_line) {
	            //Recuperation du nb de jetons restants / commande
	            if(isset($response_line['nta'])) {
	                $this->orders[$response_line["orderLineId"]]['pnb_current_nta'] = $response_line['nta'];
	            }
	            //Recuperation du nb de prets simultanes possibles / commande
	            if(isset($response_line['nus1'])) {
	                $this->orders[$response_line["orderLineId"]]['pnb_current_nus1'] = $response_line['nus1'];
	            }
	            //Recuperation de la liste des prets
	            if(!empty($response_line['pnbLoanList'])) {
	                foreach($response_line['pnbLoanList'] as $ws_loan) {
	                    $this->ws_loans[$ws_loan['loanId']] = $ws_loan;
	                }
	            }
	            
	            $this->listen_commande(array(&$this,"traite_commande"));
	            if($this->statut == scheduler_task::WAITING) {
	                $this->send_command(scheduler_task::RUNNING);
	            }
	            if ($this->statut == scheduler_task::RUNNING) {
	                continue;
	            }
	        }
	    }
	}


	/**
	 * Lecture des fichiers full et diffusion sur le FTP Dilicom
	 * @return array[]
	 */
	private function get_offer_files() {

		global $base_path;
		global $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server;

		// Connexion ftp pour récupérer le nom du fichier à récupérer
		$conn_id = ftp_connect($pmb_pnb_param_ftp_server);
		if(!$conn_id) {
			$this->error['ftp'] = true;
			$this->add_content_report($this->msg['pnb_ftp_error']);
			return ;
		}

		// Identification avec un nom d'utilisateur et un mot de passe
		if (!ftp_login($conn_id, $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password)) {
			$this->error['ftp'] = true;
			$this->add_content_report($this->msg['pnb_ftp_error']);
			ftp_close($conn_id);
			return ;
		}
		ftp_pasv($conn_id, true);
		ftp_chdir($conn_id, '/HUB/O/');

		$files_to_process = [];
		$this->add_content_report($this->msg['pnb_files_downloaded']);

		$full_file_list = ftp_nlist($conn_id, 'full_pnb*');
		$last_full_file = '';
		$last_full_file_date = '';
		if(is_array($full_file_list) && count($full_file_list)) {
			rsort($full_file_list);
			$last_full_file = $full_file_list[0];
			$last_full_file_date = substr(explode('_', $last_full_file)[3], 0, 8);
		}

		$diff_file_list = ftp_nlist($conn_id, 'diffusion_pnb*');
		$last_diff_file = '';
		$last_diff_file_date = '';
		if(is_array($diff_file_list) && count($diff_file_list)) {
			rsort($diff_file_list);
			$last_diff_file = $diff_file_list[0];
			$diff_file_list = array_reverse($diff_file_list);
			$last_diff_file_date = substr(explode('_', $last_diff_file)[3], 0, 8);
		}

		$pnb_do_full_sync = 0;
		if(!empty($this->params['pnb_do_full_sync'])) {
			$pnb_do_full_sync = intval($this->params['pnb_do_full_sync']);
		}

		//on force une synchro complete s'il n'y a pas de commandes
		$query = "SELECT id_pnb_order FROM pnb_orders ";
		$res = pmb_mysql_query($query);
		if (!pmb_mysql_num_rows($res)) {
			$pnb_do_full_sync = 1;
		}

		switch (true) {

			//synchronisation complete avec full et diff
			case ( $pnb_do_full_sync && $last_full_file && $last_diff_file) :
				$files_to_process[] = $last_full_file;
				foreach($diff_file_list as $diff_file) {
					$diff_file_date = substr(explode('_', $diff_file)[3], 0, 8);
					if ($diff_file_date > $last_full_file_date) {
						$files_to_process[] = $diff_file;
					}
				}
				break;

				//synchronisation complete avec full sans diff
			case ( $pnb_do_full_sync && $last_full_file && !$last_diff_file) :
				$files_to_process[] = $last_full_file;
				break;

				//synchronisation complete sans full avec diff
			case ( $pnb_do_full_sync && !$last_full_file && $last_diff_file) :
				$files_to_process = $diff_file_list;
				break;

				//synchronisation differentielle avec full et diff
			case ( !$pnb_do_full_sync && $last_full_file && $last_diff_file) :
				if ($last_full_file_date >= $last_diff_file_date ) {
					$files_to_process[] = $last_full_file;
				} else {
					$files_to_process[] = $last_diff_file;
				}
				break;

				//synchronisation differentielle avec full sans diff
			case ( !$pnb_do_full_sync && $last_full_file && !$last_diff_file) :
				$files_to_process[] = $last_full_file;
				break;

				//synchronisation differentielle sans full avec diff
			case ( !$pnb_do_full_sync && !$last_full_file && $last_diff_file) :
				$files_to_process[] = $last_diff_file;
				break;
		}

		$this->onix_files = [];
		if (!count($files_to_process)) {
			$this->error['no_files'] = true;
			$this->add_content_report($this->msg['pnb_no_file']);
			ftp_close($conn_id);
			return;
		}

		foreach($files_to_process as $file_name) {
			$this->add_content_report($file_name);
		}

		foreach ($files_to_process as $file_name) {

			$fg = ftp_get($conn_id, $base_path . '/temp/' . $file_name, $file_name, FTP_BINARY);
			if ($fg) {
				$this->onix_files[] = file_get_contents($base_path . '/temp/' . $file_name);
				@unlink($base_path . '/temp/' . $file_name);
			}
		}
		if(!count($this->onix_files)) {
			$this->error['no_files'] = true;
		}
		ftp_close($conn_id);

		return;
	}


	/**
	 * Recuperation en base des prets dont la date de retour est depassee
	 */
	private function get_overdue_loans() {

		$query = "select pnb_loan_num_expl, pnb_loan_num_loaner, pnb_loan_loanid FROM pret join pnb_loans on pret_idexpl=pnb_loan_num_expl AND pret_pnb_flag = 1 WHERE pret_retour < CURDATE() ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$this->loans_to_delete[] = [
						'pnb_loan_loanid' => $row['pnb_loan_loanid'],
						'pnb_loan_num_loaner' => $row['pnb_loan_num_loaner'],
						'pnb_loan_num_expl' => $row['pnb_loan_num_expl'],
				];
			}
		}

	}


	/**
	 * Nettoyage des prets
	 */
	private function clean_loans() {

	    //Suppression des prets qui ne sont plus references dans la table pnb_loans
	    $q = "delete ignore from pret where pret_pnb_flag=1 and pret_idexpl not in (select pnb_loan_num_expl from pnb_loans)";
	    pmb_mysql_query($q);

		if(empty($this->loans_to_delete)) {
			return;
		}

		foreach($this->loans_to_delete as $loan) {

			// Suppression dans la table pnb_loans
			$q = "DELETE ignore FROM pnb_loans WHERE pnb_loan_num_loaner = '".$loan['pnb_loan_num_loaner']."' AND pnb_loan_num_expl = '".$loan['pnb_loan_num_expl']."'";
			pmb_mysql_query($q);

			// Suppression dans la table pret
			$q = "DELETE ignore FROM pret WHERE pret_pnb_flag = 1 AND pret_idexpl = '".$loan['pnb_loan_num_expl']."' AND pret_idempr = '".$loan['pnb_loan_num_loaner']."'";
			pmb_mysql_query($q);

			//Creation du shorturl_context extendCallback
			$shorturl_extend_context = serialize(array('empr_id' => $loan['pnb_loan_num_loaner'], 'expl_id' => $loan['pnb_loan_num_expl']));
			$shorturl_extend_hash = '';
			$shorturl_return_context = '';
			//Recuperation du shorturl_hash extendCallback
			$q1 = "select shorturl_hash from shorturls where shorturl_action = 'extendCallback' AND shorturl_type = 'pnb' AND shorturl_context = '".addslashes($shorturl_extend_context)."'";
			$r1 = pmb_mysql_query($q1);
			if(pmb_mysql_num_rows($r1)) {
				//Creation du shorturl_context returnCallback
				$shorturl_extend_hash = pmb_mysql_result($r1,0,0);
				$shorturl_return_context = serialize(array('empr_id' => $loan['pnb_loan_num_loaner'], 'expl_id' => $loan['pnb_loan_num_expl'], 'extend_hash' => $shorturl_extend_hash));
			}
			//Suppression du shorturl extendCallback
			$q2 = "DELETE ignore FROM shorturls WHERE shorturl_action = 'extendCallback' AND shorturl_type = 'pnb' AND shorturl_context = '".addslashes($shorturl_extend_context)."'";
			pmb_mysql_query($q2);

			//Suppression du shorturl returnCallback
			$q3 = "DELETE ignore FROM shorturls WHERE shorturl_action = 'returnCallback' AND shorturl_type = 'pnb' AND shorturl_context = '".addslashes($shorturl_return_context)."'";
			pmb_mysql_query($q3);

			$this->add_content_report(sprintf($this->msg['pnb_loan_deleted'], $loan['pnb_loan_loanid']));
		}

	}


	/**
	 * Verification des prets depuis les imports
	 */
	private function check_loans_from_import() {

		if(empty($this->offer_loan_list)) {
			return;
		}
		//statut de pret = ok si le pret est present dans la liste
		foreach($this->offer_loan_list as $offerLoanList) {

			foreach ($offerLoanList['offerLine'] as $loan) {

				if (array_key_exists($loan['line']['loanIdList']['loanId'], $this->loans)) {
					$this->loans[$loan['line']['loanIdList']['loanId']]['statut'] = 'ok';
					continue;
				}
			}
		}

		//Suppression des prets dont le statut est != ok
		foreach ($this->loans as $loan) {

			//A supprimer à terme, le séparateur précédent était "_"
			$loan['pnb_loan_loanid'] = str_replace('_', 'x', $loan['pnb_loan_loanid']);
			//$loanid = explode('_', $loan['pnb_loan_loanid']);
			$loanid = explode('x', $loan['pnb_loan_loanid']);

			$date = $loanid[2];

			// Ne pas supprimer le prêt créé le jour même
			if ($date == date('Ymd')) {
				continue;
			}

			//Enregistrement des prets a supprimer
			if ($loan['statut'] != 'ok') {
				$this->loans_to_delete[] = [
						'pnb_loan_loanid' => $loan['pnb_loan_loanid'],
						'pnb_loan_num_loaner' => $loan['pnb_loan_num_loaner'],
						'pnb_loan_num_expl' => $loan['pnb_loan_num_expl'],
				];
			}
		}
	}


	/**
	 * Verification des prets depuis le WS
	 */
	private function check_loans_from_ws() {

		if(empty($this->ws_loans)) {
			return;
		}
		foreach ($this->ws_loans as $loan_id=>$loan) {

			if (array_key_exists($loan['loanId'], $this->loans)) {

				$endloandate = str_replace('-','', substr($loan['endLoanDate'],0,10));

				//Enregistrement des prets a supprimer
				if($endloandate < date('Ymd')) {
					$this->loans_to_delete[] = [
							'pnb_loan_loanid' => $loan['loanId'],
							'pnb_loan_num_loaner' => $loan['pnb_loan_num_loaner'],
							'pnb_loan_num_expl' => $loan['pnb_loan_num_expl'],
					];
				}
			}
		}
	}

	/**
	 * Nettoyage des commandes
	 */
	private function clean_orders(){

		foreach ($this->orders as $pnb_order_line_id => $order) {

			if ( isset($order['id_pnb_order']) &&  $order['pnb_current_nta'] == 0 ) {

				$pnb_order = new pnb_order($order['id_pnb_order']);
				$exemplaires = $pnb_order->get_exemplaires();

				$all_exemplaires_deleted = 1;
				if (count($exemplaires)) {
					foreach ($exemplaires as $exemplaire){
						$exemplaire_deleted = exemplaire::del_expl($exemplaire) ;
						$all_exemplaires_deleted = $all_exemplaires_deleted & $exemplaire_deleted;
					}
				}

				if ($all_exemplaires_deleted == 1) {
					// on peut supprimer la notice
					notice::del_notice($order['pnb_order_num_notice']);
					//et la commande
					pnb_order::delete_pnb_order($pnb_order_line_id);

				}

				$this->add_content_report(sprintf($this->msg['pnb_order_deleted'], $pnb_order_line_id));
			}
		}
	}


	/**
	 * Import des fichiers onix
	 * @return number : nb de notices importees
	 */
	private function import_onix2uni() {

		$nb_notice_imported = 0;

		if(!count($this->onix_files)) {
			return 0;
		}

		foreach ($this->onix_files as $content) {

			//extraction des offres, xml to array
			$offers = json_decode(json_encode(simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)),TRUE);

			//Recuperation des prets en cours
			//A priori inutile car non fourni dans le xml
			//A faire avec le WS
			if (isset($offers['offerLoanList'])) {
				$this->offer_loan_list[] = $offers['offerLoanList'];
			}

			if(!isset($offers['offer']) || !count($offers['offer'])){
				continue;
			}

			if(empty($offers['offer'][0])) {
				$offers['offer'] = [$offers['offer']];
			}


			foreach ($offers['offer'] as $offer) {

				$orderlines = [];
				if(!isset($offer['orderLine'][0])) {
					$orderlines[] = $offer['orderLine'];
				} else {
					$orderlines = $offer['orderLine'];
				}
				if(empty($orderlines)) {
					continue;
				}

				$notice_id = 0;
				$notice_id_material = 0;

				$jsonNotice = json_encode(simplexml_load_string($offer['notice'], "SimpleXMLElement", LIBXML_NOCDATA));
				$notice = json_decode($jsonNotice,TRUE);

				foreach($orderlines as $orderline) {


					if(!isset($orderline['orderId'])) {
						continue;
					}

					// La commande existe t-elle deja ?
					if(array_key_exists($orderline['orderLineId'], $this->orders )) {
						$this->add_content_report(sprintf($this->msg['pnb_order_still_imported'], $orderline['orderLineId']));

						//On met à jour la order_data pour les clients ayant déjà importé leurs orders avant que cette colonne n'existe
						if(empty($this->orders[$orderline['orderLineId']]['pnb_order_data'])) {
						    $query = 'UPDATE pnb_orders SET pnb_order_data = "'.addslashes($jsonNotice).'" WHERE id_pnb_order = '.$this->orders[$orderline['orderLineId']]['id_pnb_order'];
						    pmb_mysql_query($query);
						}
						continue;
					}


					//La commande a-t-elle encore des jetons disponibles ?
					$loan_status = dilicom::get_instance()->get_loan_status([$orderline['orderLineId']]);

					if($loan_status['loanResponseLine'][0]['nta']==0) {
						$this->add_content_report(sprintf($this->msg['pnb_expired_order'], $orderline['orderLineId']));
						continue;
					}


					$this->add_content_report(sprintf($this->msg['pnb_add_order'], $orderline['orderLineId']));


					$loanTerms = $orderline['usage']['loanTerms'];
					$loanMaxDuration = $this->convert_duration_in_days($loanTerms['loanMaxDuration']['value'], $loanTerms['loanMaxDuration']['unit']);
					$collRights = $orderline['usage']['collRights'];
					$offerValidity = $this->convert_duration_in_days($collRights['offerValidity']['value'], $collRights['offerValidity']['unit']);


					$pnb_order_offer_date_end = '0000-00-00 00:00:00';
					if ($orderline['orderDate'] && $offerValidity) {
						$query = "SELECT DATE_ADD('" . $orderline['orderDate'] . "', INTERVAL " . $offerValidity . " DAY) as offer_date_end";
						$res = pmb_mysql_query($query);
						if ($r = pmb_mysql_fetch_object($res)) {
							$pnb_order_offer_date_end = $r->offer_date_end;
						}
					}


					// Mémorisation de l'offre
					$query = 'INSERT INTO pnb_orders SET
						pnb_order_id_order = "'.addslashes($orderline['orderId']).'",
						pnb_order_line_id = "'.addslashes($orderline['orderLineId']).'",
						pnb_order_loan_max_duration = "'.addslashes($loanMaxDuration).'",
						pnb_order_nb_loans = "'.addslashes($loanTerms['nbLoans']).'",
						pnb_order_nb_simultaneous_loans = "'.addslashes($loanTerms['loanNbSimultaneousUsers']).'",
						pnb_order_nb_consult_in_situ = "'.addslashes($loanTerms['consultNbSimultaneousUsersInSitu']).'",
						pnb_order_nb_consult_ex_situ = "'.addslashes($loanTerms['consultNbSimultaneousUsersExSitu']).'",
						pnb_order_offer_date = "'.addslashes($this->convert_date_time($orderline['orderDate'])).'",
						pnb_order_offer_date_end = "'.addslashes($this->convert_date_time($pnb_order_offer_date_end)).'",
						pnb_order_offer_duration = "'.addslashes($offerValidity).'",
						pnb_current_nta = '.$loan_status['loanResponseLine'][0]['nta'].',
						pnb_order_data = "'.addslashes($jsonNotice).'"';
					pmb_mysql_query($query);
					$id_pnb_order = pmb_mysql_insert_id();

					if(!$id_pnb_order) {
						$this->add_content_report(sprintf($this->msg['pnb_add_order_error'], $orderline['orderLineId']));
						continue;
					}

					if(!$notice_id) {

						$isbn = '';
						$product_identifier_ean = '';
						$product_identifier_isbn = '';

						//formatage donnees
						if( !empty($notice['Product']['ProductIdentifier']['ProductIDType']) ) {
							$tmp = $notice['Product']['ProductIdentifier'];
							unset($notice['Product']['ProductIdentifier']);
							$notice['Product']['ProductIdentifier'][0] = $tmp;
							unset($tmp);
						}

						if( !empty($notice['Product']['ProductIdentifier']) && is_array($notice['Product']['ProductIdentifier']) ) {
							//Recherche EAN13 (GTIN13)  normalement obligatoire => ProductIDType=03
							//ou a defaut ISBN13 => ProductIDType=15
							foreach($notice['Product']['ProductIdentifier'] as $product_identifier) {

								if( isset($product_identifier['ProductIDType']) && isset($product_identifier['IDValue']) ) {
									if( $product_identifier['ProductIDType']=='03' ) {
										$product_identifier_ean = $product_identifier['IDValue'];
										continue; //on sort ici
									}
									if( $product_identifier['ProductIDType']=='15' ) {
										$product_identifier_isbn = $product_identifier['IDValue'];
										//on continue
									}
								}
							}
						}

						$product_identifier = '';
						if($product_identifier_ean) {
							$product_identifier = $product_identifier_ean;
						} elseif($product_identifier_isbn) {
							$product_identifier = $product_identifier_isbn;
						}

						if( empty($product_identifier)) {
							$this->add_content_report(sprintf($this->msg['pnb_ignore_order_without_product_number'], $orderline['orderLineId']));
							continue;
						}

						if(isISBN($product_identifier))  {
							$isbn = formatISBN($product_identifier);
						} else {
							$isbn = $product_identifier;
						}
						$query = "SELECT notice_id FROM notices WHERE code = '" . $isbn . "' ";
						$res = pmb_mysql_query($query);
						if ($r = pmb_mysql_fetch_object($res)) {
							$notice_id = $r->notice_id;
						}

						//On cherche l'oeuvre originale
						$isbn_material = '';
						$related_product_identifier = '';
						$related_product_identifier_ean = '';
						$related_product_identifier_isbn = '';

						//formatage donnees
						if( !empty($notice['Product']['RelatedMaterial']['RelatedProduct']['ProductIdentifier']) ) {
							$tmp = $notice['Product']['RelatedMaterial']['RelatedProduct'];
							unset($notice['Product']['RelatedMaterial']['RelatedProduct']);
							$notice['Product']['RelatedMaterial']['RelatedProduct'][]=$tmp;
							unset($tmp);
						}
						if( !empty($notice['Product']['RelatedMaterial']['RelatedProduct']) ) {
							foreach($notice['Product']['RelatedMaterial']['RelatedProduct'] as $krp=>$related_product) {
								if( !empty($related_product['ProductIdentifier']) && !empty($related_product['ProductIdentifier']['ProductIDType']) ) {
									$tmp = $related_product['ProductIdentifier'];
									unset($notice['Product']['RelatedMaterial']['RelatedProduct'][$krp]['ProductIdentifier']);
									$notice['Product']['RelatedMaterial']['RelatedProduct'][$krp]['ProductIdentifier'][]=$tmp;
									unset($tmp);
								}
							}
						}

						if( !empty($notice['Product']['RelatedMaterial']['RelatedProduct']) && is_array($notice['Product']['RelatedMaterial']['RelatedProduct']) ) {
							//Recherche Oeuvre originale => ProductRelationCode = 13 - Epublication based on (print product)
							//avec EAN13 (GTIN13) => => ProductIDType=13
							foreach($notice['Product']['RelatedMaterial']['RelatedProduct'] as $related_product) {

								if( isset($related_product['ProductRelationCode']) && ($related_product['ProductRelationCode']=='13') ) {

									foreach($related_product['ProductIdentifier'] as $related_identifier) {

										if( isset($related_identifier['ProductIDType']) && isset($related_identifier['IDValue']) ) {
											if( $related_identifier['ProductIDType']=='03' ) {
												$related_product_identifier_ean = $related_identifier['IDValue'];
												continue; //on sort ici
											}
											if( $related_identifier['ProductIDType']=='15' ) {
												$related_product_identifier_isbn = $related_identifier['IDValue'];
												//on continue
											}
										}
									}
								}
							}
						}
						if($related_product_identifier_ean) {
							$related_product_identifier = $related_product_identifier_ean;
						} elseif($related_product_identifier_isbn) {
							$related_product_identifier = $related_product_identifier_isbn;
						}

						if(!empty($related_product_identifier)) {

							if(isISBN($related_product_identifier) ) {
								$isbn_material = formatISBN($related_product_identifier);
							} else {
								$isbn_material = $related_product_identifier;
							}
						}

						if ($isbn_material) {
							$query = "SELECT notice_id FROM notices WHERE code = '" . $isbn_material . "' ";
							$res = pmb_mysql_query($query);
							if ($r = pmb_mysql_fetch_object($res)) {
								$notice_id_material = $r->notice_id;
							}
						}


						if (!$notice_id) {

							// Import de la notice 'numérique'
							$start = strpos($offer['notice'], '<Product>');
							$end = strpos($offer['notice'], '</Product>');
							$Product = substr($offer['notice'], $start, $end - $start) . '</Product>';

							$Product = encoding_normalize::clean_cp1252($Product, 'utf-8');

							$result = $this->proxy->pmbesConvertImport_convert_by_path($Product, 'onix2uni', true, 0, 0);
							$notice_id = $result['import'][1];


							if ($notice_id) {
								$query = 'UPDATE notices SET is_numeric=1 WHERE notice_id=' . $notice_id;
								pmb_mysql_query($query);
								$nb_notice_imported++;
							}
						}

						if($notice_id) {
							// Creation exemplaire numérique de l'extrait
							$f_url = '';
							$f_nom = '';
							if( isset($notice['Product']['CollateralDetail']['SupportingResource'])
									&& is_array($notice['Product']['CollateralDetail']['SupportingResource'])
									&& count($notice['Product']['CollateralDetail']['SupportingResource']) ) {

										foreach ($notice['Product']['CollateralDetail']['SupportingResource'] as $SupportingResource) {
											if ( isset($SupportingResource['ResourceContentType']) && ($SupportingResource['ResourceContentType'] == 15) ) {
												$f_url = $SupportingResource['ResourceVersion']['ResourceLink'];
												$f_nom = $notice['Product']['DescriptiveDetail']['TitleDetail']['TitleElement']['TitleText'];
												break;
											}
										}
									}
									if($f_url && $f_nom) {
										$this->explnum_add_url($notice_id, $f_nom, $f_url);
									}
						}

					}

					$this->create_exemplaires($notice_id, $id_pnb_order, $orderline['orderLineId'], $loanTerms['loanNbSimultaneousUsers']);

					// Associer une oeuvre
					$this->gestion_tu($notice_id, $notice_id_material, $notice['Product']);

					$query = 'UPDATE pnb_orders SET pnb_order_num_notice=' . $notice_id . ' WHERE id_pnb_order=' . $id_pnb_order;
					pmb_mysql_query($query);

					//Mise à jour liste des lignes de commandes
					$this->orders[$orderline['orderLineId']]['id_pnb_order'] = $id_pnb_order;
					$this->orders[$orderline['orderLineId']]['pnb_order_id_order'] = $orderline['orderId'];
					$this->orders[$orderline['orderLineId']]['pnb_order_num_notice'] = $notice_id;
					$this->orders[$orderline['orderLineId']]['pnb_order_nb_simultaneous_loans'] = $loanTerms['loanNbSimultaneousUsers'];
					$this->orders[$orderline['orderLineId']]['pnb_current_nta'] = $loan_status['loanResponseLine'][0]['nta'];

					// Mise à jour des index de la notice
					notice::majNotices($notice_id);
					// Mise à jour de la table notices_global_index
					notice::majNoticesGlobalIndex($notice_id);
					// Mise à jour de la table notices_mots_global_index
					notice::majNoticesMotsGlobalIndex($notice_id);

				}

			}

			$this->listen_commande(array(&$this,"traite_commande"));
			if($this->statut == scheduler_task::WAITING) {
			    $this->send_command(scheduler_task::RUNNING);
			}
			if ($this->statut == scheduler_task::RUNNING) {
				continue;
			}
		}

		if ($nb_notice_imported >= 1) {
			$this->add_content_report(sprintf($this->msg['pnb_nb_imported_records'], $nb_notice_imported));
		} else {
			$this->add_content_report($this->msg['pnb_no_record_imported']);
		}
		return $nb_notice_imported;
	}


	/**
	 * Creation des exemplaires associes a chaque commande
	 * @param int $notice_id
	 * @param int $id_pnb_order
	 * @param string $pnb_order_line_id
	 * @param int $nb_expl
	 */
	private function create_exemplaires($notice_id, $id_pnb_order, $pnb_order_line_id, $nb_expl) {

		global $pmb_pnb_typedoc_id, $pmb_pnb_location_id, $pmb_pnb_section_id, $pmb_pnb_statut_id, $pmb_pnb_codestat_id, $pmb_pnb_owner_id;
		global $deflt_docs_type, $deflt_docs_location, $deflt_docs_section, $deflt_docs_statut, $deflt_docs_codestat, $deflt_lenders;

		$nb_expl = intval($nb_expl);
		$data = array();
		$data['notice'] = $notice_id;
		$data['typdoc'] = ($pmb_pnb_typedoc_id) ? $pmb_pnb_typedoc_id : $deflt_docs_type;
		$data['location'] = ($pmb_pnb_location_id) ? $pmb_pnb_location_id : $deflt_docs_location;
		$data['section'] = ($pmb_pnb_section_id) ? $pmb_pnb_section_id : $deflt_docs_section;
		$data['statut'] = ($pmb_pnb_statut_id) ? $pmb_pnb_statut_id : $deflt_docs_statut;
		$data['codestat'] = ($pmb_pnb_codestat_id) ? $pmb_pnb_codestat_id : $deflt_docs_codestat;
		$data['expl_owner'] = ($pmb_pnb_owner_id) ? $pmb_pnb_owner_id : $deflt_lenders;
		$data['cote'] = '-';
		$data['expl_pnb_flag'] = 1;

		for ($i = 0; $i < $nb_expl; $i++) {

			$data['cb'] = $this->gen_code_exemplaire($pnb_order_line_id);
			$expl_id = exemplaire::import($data);

			if ($expl_id) {

				$this->add_content_report(sprintf($this->msg['pnb_add_item'], $data['cb']));

				$query = 'INSERT INTO pnb_orders_expl SET
					pnb_order_num = "'.$id_pnb_order.'",
					pnb_order_expl_num = "'.$expl_id.'" ';
				pmb_mysql_query($query);
			}
		}

	}


	/**
	 * Vérification des exemplaires associes a chaque commande
	 */
	private function check_exemplaires() {

		foreach($this->orders as $pnb_order_line_id=>$order) {

			if (isset($order['id_pnb_order']) &&
					(isset($order['pnb_current_nta']) && $order['pnb_current_nta'] != 0 )
					) {

						$this->add_content_report(sprintf($this->msg['pnb_check_order_items'], $pnb_order_line_id));
						$nb_expl = $order['pnb_order_nb_simultaneous_loans'];
						$q = "select count(*) from pnb_orders_expl where pnb_order_num='".addslashes($order['id_pnb_order'])."'";
						$r = pmb_mysql_query($q);
						if(pmb_mysql_num_rows($r)) {
							$existing_nb_expl = pmb_mysql_result($r,0,0);
							if($existing_nb_expl < $nb_expl){
								$this->create_exemplaires($order['pnb_order_num_notice'], $order['id_pnb_order'], $pnb_order_line_id, $nb_expl-$existing_nb_expl);
							}
						}
					}
		}

	}


	/*
	 * Generation d'un CB d'exemplaire / une ligne de commande
	 */
	private function gen_code_exemplaire($pnb_order_line_id) {

		$code_exemplaire = $pnb_order_line_id.'-001';
		$query="select max(expl_cb)as cb from exemplaires WHERE expl_cb like '".addslashes($pnb_order_line_id)."-%'" ;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)){
			$code_exemplaire = pmb_mysql_result($result, 0, 0);
			$r = explode('-', $code_exemplaire);
			$n = array_pop($r);
			$n++;
			$code_exemplaire = $pnb_order_line_id."-".str_pad($n, 3, 0, STR_PAD_LEFT);
		}
		return $code_exemplaire;
	}


	private function explnum_add_url($notice_id, $f_nom, $f_url, $f_statut=0) {
		global $base_path, $charset;

		$f_nom = ($charset != 'utf-8' ? encoding_normalize::utf8_decode($f_nom) : $f_nom);

		$query = "DELETE FROM explnum WHERE explnum_notice = ".$notice_id." AND explnum_nom = '".addslashes($f_nom)."'";
		pmb_mysql_query($query);

		$extension = substr($f_url, strripos($f_url,'.')*1+1);
		create_tableau_mimetype();
		$mimetype = trouve_mimetype('', $extension);
		$vignette = construire_vignette('', $base_path."/images/mimetype/".icone_mimetype($mimetype, $extension));

		$query = "INSERT INTO explnum SET explnum_notice = " . $notice_id .",
					explnum_bulletin = 0,
					explnum_nom = '".addslashes($f_nom)."',
					explnum_url = '".addslashes($f_url)."',
					explnum_mimetype = 'URL',
					explnum_vignette = '".addslashes($vignette)."',
					explnum_extfichier = '".addslashes($extension)."',
					explnum_docnum_statut = '".(($f_statut)?$f_statut:1)."'	";
		pmb_mysql_query($query);
	}


	private function gestion_tu($notice_id, $notice_id_material, $data_notice) {

		if (!$notice_id) return;
		if ($notice_id_material) {
			// La notice papier a des titres uniformes ?
			$tu_notice = new tu_notice($notice_id_material);
			if (count($tu_notice->ntu_data)) {
				$query = "DELETE FROM notices_titres_uniformes WHERE ntu_num_notice=" . $notice_id;
				pmb_mysql_query($query);
				$ordre=0;
				foreach ($tu_notice->ntu_data as $ntu) {
					tu_notice::create_tu_notice_link($ntu->num_tu, $notice_id, $ordre++);
				}
			} else {
				// Pas de titre uniforme dans la notice papier, on cree le titre uniforme et insert dans la notice numerique et la notice papier
				$tu_id = $this->create_tu($data_notice);
				if ($tu_id) {
					tu_notice::create_tu_notice_link($tu_id, $notice_id);
					tu_notice::create_tu_notice_link($tu_id, $notice_id_material);
				}
			}
		} else {
			// la notice numérique est seule, on cree le titre uniforme et insert dans la notice numerique
			$tu_id = $this->create_tu($data_notice);
			if ($tu_id) {
				tu_notice::create_tu_notice_link($tu_id, $notice_id);
			}
		}

	}


	private function create_tu($data_notice) {
		global $charset;

		$tu = new titre_uniforme();
		$value ['oeuvre_type'] = 'a'; // Litteraire
		$value ['oeuvre_nature'] = 'b'; // Oeuvre

		$titre = $data_notice['DescriptiveDetail']['TitleDetail']['TitleElement']['TitleText'];
		$value['name'] = ($charset != 'utf-8' ? encoding_normalize::utf8_decode($titre) : $titre);
		$tu_id = $tu->import_tu_exist($value, 1);
		if(!$tu_id) {
			$tu->update($value);
			return  $tu->id;
		} else {
			return $tu_id;
		}
	}

	private function convert_date_time($value) {
		if (!$value) return '';
		return str_replace('T', ' ', substr($value, 0, 19));
	}

	private function convert_duration_in_days($value, $unit) {
		switch ($unit) {
			case 'HOUR':
				return 1; //TBD
				break;
			case 'DAY':
				return $value;
				break;
			case 'MONTH':
				return $value * 30;
				break;
			case 'YEAR':
				return $value * 365;
				break;
			default:
				return $value;
				break;
		}
	}

}
