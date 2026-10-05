<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_docnum.class.php,v 1.10 2024/04/11 08:26:23 dbellamy Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");

class scan_docnum extends scheduler_task {
	
	/**
	 * Liste le contenu du repertoire $upload_folder
	 * Ne tiens compte que des fichiers
	 * 
	 * @param string $upload_folder
	 * @return array
	 */
	public function list_docnum($upload_folder){
		$list=array();
		$tmp_list=scandir($upload_folder);
		
		foreach ($tmp_list as $item){
			if(!is_dir($upload_folder.$item) && file_exists($upload_folder.$item)&& preg_match('/^(a|b|n)([0-9]+)(\.|\-).+$/', $item)){
				$list[]=$item;
			}
		}
		return $list;
	}
	
	public function execution() {
		global $charset, $msg, $PMBusername;
		
		$reussi=0;
		$error_count=0;
		$errors=array();
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			
			if (method_exists($this->proxy, "pmbesScanDocnum_get_doc_num")) {
				
				if ($parameters["upload_folder"] && $parameters["upload_repertoire"]) {
					//on liste les documents dans le fichier upload_folder	
					$list_docnum=$this->list_docnum($parameters["upload_folder"]);
					if (!empty($list_docnum)) {
						//il y en a
						$percent = 0;
						$p_value = (int) 100/count($list_docnum);
						
						$this->report[] = "<tr><th colspan=3>".$this->msg["planificateur_scan_docnum_doc_a_traiter"]."</th><th>".count($list_docnum)."</th></tr>";
						
						
						foreach ($list_docnum as $docnum){
							$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
				
							if($this->statut == scheduler_task::WAITING) {
							    $this->send_command(scheduler_task::RUNNING);
							}
							if($this->statut == scheduler_task::RUNNING) {
								$explnum=array();
								$explnum['explnum_nomfichier']=$docnum;
								$explnum['explnum_repertoire']=$parameters["upload_repertoire"];
								
								$match=array();
								if(preg_match('/^b([0-9]+)(\.|\-).+$/', $docnum, $match)){
									$explnum['explnum_bulletin']=$match[1];
								}elseif(preg_match('/^a([0-9]+)(\.|\-).+$/', $docnum, $match)){
									$explnum['explnum_notice']=$match[1];
								}elseif(preg_match('/^n([0-9]+)(\.|\-).+$/', $docnum, $match)){
									$explnum['explnum_notice']=$match[1];
								}
								
								$report = $this->proxy->pmbesScanDocnum_get_doc_num($explnum, $parameters["upload_folder"]);
								foreach ($report as $msg_type=>$values){
									switch ($msg_type){
										case 'error':
											foreach($values as $error_msg){
												if($errors[$error_msg]){
													$errors[$error_msg]++;
												}else{
													$errors[$error_msg]=1;
												}
												$error_count++;
											}
											break;
										case 'info':
											$reussi=$reussi+$values;
											break;
									}	
								}
								$percent+=$p_value;
								$this->update_progression($percent);
							}
						}
					}else {
						$this->update_progression(100);
						$this->report[] = "<tr><td colspan=4>".$this->msg["planificateur_scan_docnum_no_docnum"]."</td></tr>";
					}
				} else {
					$this->report[] = "<tr><td colspan=4>".$this->msg["planificateur_scan_docnum_bad_params"]."</td></tr>";
				}
			} else {
				$this->report[] = "<tr><td colspan=4>".sprintf($msg["planificateur_function_rights"],"get_doc_num","pmbesScanDocnum",$PMBusername)."</td></tr>";
			}
		} else {
			$this->add_rights_bad_user_report();
		}
		
		if($reussi){
			$this->report[] = "<tr><td colspan=3>".$this->msg['planificateur_scan_docnum_doc_traites']."</td><td>$reussi</td></tr>";
		}
		if($errors){
			$this->report[] = "<tr><th colspan=3>".$this->msg['planificateur_scan_docnum_doc_non_traites']."</th><th>$error_count</th></tr>";
			foreach($errors as $error_msg=>$error_nb){
				$this->report[] = "<tr><td colspan=3>$error_msg</td><td>$error_nb</td></tr>";
			}
		}
	}
}