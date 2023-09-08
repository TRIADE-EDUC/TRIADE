<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_caddie.class.php,v 1.5 2019-06-10 08:57:12 btafforeau Exp $

require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/parameters.class.php");
require_once($base_path."/admin/planificateur/caddie/scheduler_caddie_planning.class.php");

class scheduler_caddie extends scheduler_task {
	
	protected function execution_proc($myCart, $idproc=0, $proc_class_name, $method_name) {
		$this->add_section_report($proc_class_name::get_name($idproc), 'scheduler_report_section_proc');
		if ($proc_class_name::check_rights($idproc)) {
			$hp = new parameters ($idproc, $proc_class_name);
			$hp->get_final_query();
			$this->add_content_report($hp->final_query);
			$response = $myCart->{$method_name}($hp->final_query);
			if($response) {
				$this->add_content_report($response);
			}
		} else {
			$this->add_content_report($this->msg['scheduler_caddie_proc_no_rights']);
		}
		$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
	}
	
	public function execution() {
		global $msg, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			//progression
			$p_value = (int) 100/count($parameters["scheduler_caddie_list"]);
			$exploded_action = explode('|||', $parameters["scheduler_caddie_action"]);
			$model_class_name = $exploded_action[0];
			$action_type = $exploded_action[1];
			$action_what = $exploded_action[2];
			
			//flag
			$elt_flag = (isset($parameters['scheduler_caddie_action_elt_flag']) ? $parameters['scheduler_caddie_action_elt_flag'] : '');
			$elt_no_flag = (isset($parameters['scheduler_caddie_action_elt_no_flag']) ? $parameters['scheduler_caddie_action_elt_no_flag'] : '');
			$elt_flag_inconnu = (isset($parameters['scheduler_caddie_action_elt_flag_inconnu']) ? $parameters['scheduler_caddie_action_elt_flag_inconnu'] : '');
			$elt_no_flag_inconnu = (isset($parameters['scheduler_caddie_action_elt_no_flag_inconnu']) ? $parameters['scheduler_caddie_action_elt_no_flag_inconnu'] : '');
			//par panier
			$by_caddie = (isset($parameters['scheduler_caddie_action_by_caddie']) ? $parameters['scheduler_caddie_action_by_caddie'] : 0);
			
			$scheduler_actions = scheduler_caddie_planning::get_actions();
			if(isset($scheduler_actions[$model_class_name][$action_type][$action_what]) && $scheduler_actions[$model_class_name][$action_type][$action_what]) {
				$this->add_section_report('['.$msg['caddie_de_'.$parameters['scheduler_caddie_type']].'] '.$msg['caddie_menu_'.$action_type]." &gt; ".$scheduler_actions[$model_class_name][$action_type][$action_what]);
				foreach ($parameters["scheduler_caddie_list"] as $idcaddie) {
					$this->listen_commande(array(&$this,"traite_commande"));
					if($this->statut == WAITING) {
						$this->send_command(RUNNING);
					}
					if ($this->statut == RUNNING) {
						$myCart = new $model_class_name($idcaddie);
						$this->add_section_report($myCart->name, 'scheduler_report_section_caddie');
						switch ($action_type) {
							case 'collecte':
								switch ($action_what) {
									case 'selection':
										$this->execution_proc($myCart, $parameters["scheduler_proc"], $model_class_name.'_procs', 'add_items_by_collecte_selection');
										break;
								}
								break;
							case 'pointage':
								switch ($action_what) {
									case 'selection':
										$this->execution_proc($myCart, $parameters["scheduler_proc"], $model_class_name.'_procs', 'pointe_items_from_query');
										break;
									case 'panier':
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										if($by_caddie) {
											$liste = array();
											$myCart_selected = new $model_class_name($by_caddie);
											$liste_0=$liste_1= array();
											if ($elt_flag) {
												$liste_0 = $myCart_selected->get_cart("FLAG", $elt_flag_inconnu) ;
											}
											if ($elt_no_flag) {
												$liste_1= $myCart_selected->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
											}
											$liste = array_merge($liste_0,$liste_1);
											if(count($liste)) {
											    foreach ($liste as $cle => $object) {
													$myCart->pointe_item($object,$myCart_selected->type);
												}
											}
										} else {
											$this->add_content_report($msg['scheduler_caddie_action_no_caddie']);
										}
										$this->add_content_report($msg['caddie_menu_pointage_apres_pointage']);
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										break;
									case 'raz':
										if ($model_class_name::check_rights($idcaddie)) $myCart->depointe_items();
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										break;
								}
								break;
							case 'action':
								switch ($action_what) {
									case 'supprpanier':
										$this->add_content_report($msg['caddie_situation_before_suppr']);
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										if ($elt_flag) {
											$myCart->del_item_flag($elt_flag_inconnu);
										}
										if ($elt_no_flag) {
											$myCart->del_item_no_flag($elt_no_flag_inconnu);
										}
										$this->add_content_report($msg['caddie_situation_after_suppr']);
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										break;
									case 'selection':
										$this->execution_proc($myCart, $parameters["scheduler_proc"], $model_class_name.'_procs', 'update_items_by_action_selection');
										break;
									case 'supprbase':
										$this->add_content_report($msg['caddie_situation_before_suppr']);
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										$liste_0=$liste_1= array();
										if ($elt_flag) {
											$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
										}
										if ($elt_no_flag) {
											$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
										}
										$liste= array_merge($liste_0,$liste_1);
										$res_aff_suppr_base = $myCart->del_items_base_from_list($liste);
										if ($res_aff_suppr_base) {
											$this->add_content_report($msg['caddie_supprbase_elt_used']);
											// inclusion du javascript de gestion des listes dépliables
											// début de liste
	// 										print $begin_result_liste;
	// 										print $res_aff_suppr_base ;
	// 										print $end_result_liste;
										}
										$this->add_content_report($msg['caddie_situation_after_suppr']);
										$myCart->compte_items();
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										break;
									case 'reindex':
										@set_time_limit(0);
										$nb_elements_flag=$nb_elements_no_flag=0;
										$liste_0=$liste_1= array();
										if ($elt_flag) {
											$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
											$nb_elements_flag=count($liste_0);
										}
										if ($elt_no_flag) {
											$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
											$nb_elements_no_flag=count($liste_1);
										}
										$liste= array_merge($liste_0,$liste_1);
										$nb_elements_total=count($liste);
											
										if($nb_elements_total){
										    foreach ($liste as $cle => $object) {
												$myCart->reindex_object($object);
											}
										}
										$this->add_content_report(sprintf($msg["caddie_action_flag_processed"],$nb_elements_flag));
										$this->add_content_report(sprintf($msg["caddie_action_no_flag_processed"],$nb_elements_no_flag));
										$this->add_content_report(sprintf($msg["caddie_action_total_processed"],$nb_elements_total));
										$this->add_content_report($myCart->aff_cart_nb_items(), 'scheduler_report_section_caddie_nb_items');
										break;
								}
								break;
						}
						$percent += $p_value;
						$this->update_progression($percent);
					}
				}
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}


