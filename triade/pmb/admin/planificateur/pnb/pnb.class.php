<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.class.php,v 1.10 2019-04-03 14:46:00 arenou Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/expl.class.php");
require_once($class_path."/tu_notice.class.php");
require_once($class_path."/titre_uniforme.class.php");

class pnb extends scheduler_task {
	
	function execution() {
		global $msg, $PMBusername;
		if (SESSrights & ADMINISTRATION_AUTH) {
			if (method_exists($this->proxy, "pmbesConvertImport_convert")) {
				$this->import_onix2uni();
			} else {
				//$this->report[] = "<tr><td>".$this->msg['pnb_import_error_pmbesConvertImport_convert']."</td></tr>";
				$this->add_content_report($this->msg['pnb_import_error_pmbesConvertImport_convert']);
			}
		} else {
			$this->add_section_report(sprintf($msg['planificateur_rights_bad_user_rights'], $PMBusername));
		}
		
	}
	
	private function get_xml() {
		global $base_path;
		global $pmb_pnb_param_login, $pmb_pnb_param_password, $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server;
		
		// Connexion ftp pour récupérer le nom du fichier à récupérer
		$conn_id = ftp_connect($pmb_pnb_param_ftp_server) or die("Impossible de se connecter au serveur $pmb_pnb_param_ftp_server");
		// Identification avec un nom d'utilisateur et un mot de passe
		if (!ftp_login($conn_id, $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password)) {
			ftp_close($conn_id);
			$this->add_content_report($this->msg['pnb_import_error_ftp_login']);
			return '';
		}
		ftp_pasv($conn_id, true);
		ftp_chdir($conn_id, '/HUB/O/');
		$file_list = ftp_nlist($conn_id, '.');
		$files_name = array();
		$query = "SELECT id_pnb_order FROM pnb_orders ";
		$res = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($res)== 0) {			
			$files_name = $file_list;
			asort($files_name);
			
		} else {
			$files_name = array($this->get_last_diffusion_file($file_list));			
		}
		if (count($files_name) == 0 || !$files_name[0]) {
			ftp_close($conn_id);
			return '';
		}
		$xml = array();
		foreach ($files_name as $file_name) {
			if (ftp_get($conn_id, $base_path . '/temp/' . $file_name, $file_name, FTP_BINARY)) {
				$xml[] = file_get_contents($base_path . '/temp/' . $file_name);
			}
		}
		ftp_close($conn_id);
		return $xml;
	}
	
	public function import_onix2uni() {
		global $base_path, $charset;		
		global $pnb_import_notice_statut;
		global $deflt_docs_statut, $deflt_docs_location, $deflt_docs_section, $deflt_docs_codestat, $deflt_lenders;
		
		$parameters = $this->unserialize_task_params();

		$nb_notice_imported = 0;
		
		$xml = $this->get_xml();
		if(!count($xml)) {
			$this->add_content_report($this->msg['pnb_import_no_xml']);
			return 0;
		}
		foreach ($xml as $content) {
			// extraction des offres, xml to array
			$offers = json_decode(json_encode(simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA)),TRUE);
			//printr($offers);
			if(!count($offers['offer'])){
				continue;
			}				
			foreach ($offers['offer'] as $offer) {
				$query = "SELECT id_pnb_order FROM pnb_orders WHERE pnb_order_line_id = '" . addslashes($offer['orderLine']['orderLineId']) . "' ";
				$res = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($res)) {
					continue;
				}
				
				$loanTerms = $offer['orderLine']['usage']['loanTerms'];
				$loanMaxDuration = $this->convert_duration_in_days($loanTerms['loanMaxDuration']['value'], $loanTerms['loanMaxDuration']['unit']);
				$collRights = $offer['orderLine']['usage']['collRights'];
				$offerValidity = $this->convert_duration_in_days($collRights['offerValidity']['value'], $collRights['offerValidity']['unit']);
	
				// Dedoublonage
				$notice_id = 0;
				$notice_id_material = 0;
				$notice = json_decode(json_encode(simplexml_load_string($offer['notice'], "SimpleXMLElement", LIBXML_NOCDATA)),TRUE);			
				$isbn = formatISBN($notice['Product']['ProductIdentifier'][0]['IDValue']);
				$query = "SELECT notice_id FROM notices WHERE code = '" . $isbn . "' ";
				$res = pmb_mysql_query($query);
				if ($r = pmb_mysql_fetch_object($res)) {
					$notice_id = $r->notice_id;
				}
				$isbn_material = formatISBN($notice['Product']['RelatedMaterial']['RelatedProduct'][0]['ProductIdentifier']['IDValue']);
				if ($isbn_material) {
					$query = "SELECT notice_id FROM notices WHERE code = '" . $isbn_material . "' ";
					$res = pmb_mysql_query($query);
					if ($r = pmb_mysql_fetch_object($res)) {
						$notice_id_material = $r->notice_id;
					}
				}
				
				$pnb_order_offer_date_end = '0000-00-00 00:00:00';
				if ($offer['orderLine']['orderDate'] && $offerValidity) {
					$query = "SELECT DATE_ADD('" . $offer['orderLine']['orderDate'] . "', INTERVAL " . $offerValidity . " DAY) as offer_date_end";
					$res = pmb_mysql_query($query);
					if ($r = pmb_mysql_fetch_object($res)) {
						$pnb_order_offer_date_end = $r->offer_date_end;
					}
				}				
				
				// Mémorisation de l'offre
				$query = '
				    INSERT INTO pnb_orders SET
					pnb_order_id_order = "'.addslashes($offer['orderLine']['orderId']).'",
					pnb_order_line_id = "'.addslashes($offer['orderLine']['orderLineId']).'",
				    pnb_order_loan_max_duration = "'.addslashes($loanMaxDuration).'",
				    pnb_order_nb_loans = "'.addslashes($loanTerms['nbLoans']).'",
				    pnb_order_nb_simultaneous_loans = "'.addslashes($loanTerms['loanNbSimultaneousUsers']).'",
				    pnb_order_nb_consult_in_situ = "'.addslashes($loanTerms['consultNbSimultaneousUsersInSitu']).'",
				    pnb_order_nb_consult_ex_situ = "'.addslashes($loanTerms['consultNbSimultaneousUsersExSitu']).'",
				    pnb_order_offer_date = "'.addslashes($this->convert_date_time($offer['orderLine']['orderDate'])).'",
				    pnb_order_offer_date_end = "'.addslashes($this->convert_date_time($pnb_order_offer_date_end)).'",
				    pnb_order_offer_duration = "'.addslashes($offerValidity).'"
			    ';
				pmb_mysql_query($query);
				$id_pnb_order = pmb_mysql_insert_id();
							
				if (!$notice_id) {
					// Import de la notice 'numérique'
					$start = strpos($offer['notice'], '<Product>');
					$end = strpos($offer['notice'], '</Product>');
					$Products = substr($offer['notice'], $start, $end - $start) . '</Product>';				
					
					$Products = encoding_normalize::clean_cp1252($Products, 'utf-8');
					
					$result = $this->proxy->pmbesConvertImport_convert_by_path($Products, 'onix2uni', true, 0, 0);				
					$notice_id = $result['import'][1];		
	
					if ($notice_id) {
						$query = 'UPDATE notices SET is_numeric=1 WHERE notice_id=' . $notice_id;
						pmb_mysql_query($query);
						$nb_notice_imported++;
					}
				}
				
				// Creation exemplaire numérique de l'extrait
				foreach ($notice['Product']['CollateralDetail']['SupportingResource'] as $SupportingResource) {
					if ($SupportingResource['ResourceContentType'] == 15) {
						$f_url = $SupportingResource['ResourceVersion']['ResourceLink'];
						$f_nom = $notice['Product']['DescriptiveDetail']['TitleDetail']['TitleElement']['TitleText'];
						break;
					}
				}	
				if($f_url && $f_nom) {
					$this->explnum_add_url($notice_id, $f_nom, $f_url);
				}
				// Creation des exemplaires					
				$expl = new exemplaire('', 0, $notice_id);
				$data['notice'] = $notice_id;
				$data['typdoc'] = 1;
				$data['cote'] = '-';
				$data['section'] = $deflt_docs_section;
				$data['statut'] = $deflt_docs_statut;
				$data['location'] = $deflt_docs_location;
				$data['codestat'] = $deflt_docs_codestat;
				$data['expl_owner'] = $deflt_lenders;
				
				for ($i = 0; $i < $loanTerms['loanNbSimultaneousUsers']; $i++) {
					$data['cb'] = $expl->gen_cb();
					$expl_id = $expl->import($data);
					if ($expl_id) {
						$query = '
						    INSERT INTO pnb_orders_expl SET
							pnb_order_num = "'.$id_pnb_order.'",
							pnb_order_expl_num = "'.$expl_id.'"
						';
						pmb_mysql_query($query);
					}
					if ($loanTerms['loanNbSimultaneousUsers'] == 999999) {
						// prêtable à l'infini... TBD
						
						break;
					}
				}
				// Associer une oeuvre
				$this->gestion_tu($notice_id, $notice_id_material, $notice['Product']);			
	
				$query = 'UPDATE pnb_orders SET pnb_order_num_notice=' . $notice_id . ' WHERE id_pnb_order=' . $id_pnb_order;
				pmb_mysql_query($query);				

				// Mise à jour des index de la notice
				notice::majNotices($notice_id);
				// Mise à jour de la table notices_global_index
				notice::majNoticesGlobalIndex($notice_id);
				// Mise à jour de la table notices_mots_global_index
				notice::majNoticesMotsGlobalIndex($notice_id);
				
				$this->listen_commande(array(&$this,"traite_commande"));
				if($this->statut == WAITING) {
					$this->send_command(RUNNING);
				}
				if ($this->statut == RUNNING) {
					continue;
				}			
			}
		}
		
		if ($nb_notice_imported > 1) {
			$this->report[] = "<tr><td>".htmlentities(sprintf($this->msg['pnb_import_nb_notice_imported'], $nb_notice_imported), ENT_QUOTES, $charset)."</td></tr>";
		} elseif ($nb_notice_imported==1) {
			$this->report[] = "<tr><td>".htmlentities($this->msg['pnb_import_one_notice_imported'], ENT_QUOTES, $charset)."</td></tr>";
		} else {
			$this->report[] = "<tr><td>".htmlentities($this->msg['pnb_import_no_notice_imported'], ENT_QUOTES, $charset)."</td></tr>";
		}
		$this->update_progression(100);
		return $nb_notice_imported;
	}
	
	private function explnum_add_url($notice_id, $f_nom, $f_url, $f_statut=0) {	
		global $base_path, $charset;
		
		$f_nom = ($charset != 'utf-8' ? utf8_decode($f_nom) : $f_nom);
		
		$query = "DELETE FROM explnum WHERE explnum_notice = ".$notice_id." AND explnum_nom = '".addslashes($f_nom)."'";
		pmb_mysql_query($query);	
	
		$extension = substr($f_url, strripos($f_url,'.')*1+1);	
		create_tableau_mimetype();
		$mimetype = trouve_mimetype('', $extension);	
		$vignette = construire_vignette('', $base_path."/images/mimetype/".icone_mimetype($mimetype, $extension));
		
		$query = "INSERT INTO explnum SET
				explnum_notice = " . $notice_id .",
				explnum_bulletin = 0,
				explnum_nom = '".addslashes($f_nom)."', 
				explnum_url = '".addslashes($f_url)."', 
				explnum_mimetype = 'URL',  
				explnum_vignette = '".addslashes($vignette)."', 
				explnum_extfichier = '".addslashes($extension)."', 
				explnum_docnum_statut = '".(($f_statut)?$f_statut:1)."'				
				";
		pmb_mysql_query($query);
	}
	
	private function get_last_diffusion_file($file_list) {
		if (is_array($file_list)) {
			sort($file_list);
			foreach ($file_list as $file_name) {
				if (strpos($file_name, 'diffusion') !== false) {
					return $file_name;
				}
			}
		}
		return '';
	}
	
	private function gestion_tu($notice_id, $notice_id_material, $data_notice) {
		global $charset;	
		
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
		$value['name'] = ($charset != 'utf-8' ? utf8_decode($titre) : $titre);
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
