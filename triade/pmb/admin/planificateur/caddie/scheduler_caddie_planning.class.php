<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_caddie_planning.class.php,v 1.17.2.1.2.1 2025/03/13 11:36:35 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/authorities_caddie.class.php");
require_once($class_path."/caddie.class.php");
require_once($class_path."/empr_caddie.class.php");
		
class scheduler_caddie_planning extends scheduler_planning {
	
	protected static $types;
	
	protected static function get_types() {
		if(!isset(static::$types)) {
			static::$types['caddie'] = caddie::get_types();
			static::$types['empr_caddie'] = array('EMPR');
			static::$types['authorities_caddie'] = authorities_caddie::get_types();
		}
		return static::$types;
	}
	
	protected static function get_model_class_name_from_object_type($object_type='') {
		$types = static::get_types();
		foreach($types as $model_name=>$model_types)
		if(in_array($object_type, $model_types)) {
			return $model_name;
		}
	}
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $msg;
			
		$types = static::get_types();
		if(isset($param['scheduler_caddie_type']) && $param['scheduler_caddie_type']) {
			$scheduler_caddie_type = $param['scheduler_caddie_type'];
		} else {
			$scheduler_caddie_type = $types['caddie'][0];
		}
		if(isset($param['scheduler_caddie_action']) && $param['scheduler_caddie_action']) {
			$action = $param['scheduler_caddie_action'];
		} else {
			$action = '';
		}
		if(isset($param['scheduler_caddie_list']) && $param['scheduler_caddie_list']) {
			$list = $param['scheduler_caddie_list'];
		} else {
			$list = array();
		}
		
		$form_task = "
		<script type='text/javascript'>
				function scheduler_caddie_get_actions(object_type) {
					var request = new http_request();
					request.request('./ajax.php?module=admin&categ=planificateur&sub=caddie&action=get_actions&object_type='+object_type, false,'', false);
					document.getElementById('scheduler_caddie_planning_actions').innerHTML = request.get_text();
                    scheduler_caddie_get_action_form(object_type, document.getElementById('scheduler_caddie_action').value);
				}
				function scheduler_caddie_get_list(object_type) {
					var request = new http_request();
					request.request('./ajax.php?module=admin&categ=planificateur&sub=caddie&action=get_list&object_type='+object_type, false,'', false);
					document.getElementById('scheduler_caddie_planning_caddies_list').innerHTML = request.get_text();
				}
				function scheduler_caddie_get_proc_options(idproc, table) {
					var request = new http_request();
					request.request('./ajax.php?module=admin&categ=planificateur&sub=caddie&action=get_proc_options&id='+idproc+'&table='+table, false,'', false);
					document.getElementById('scheduler_proc_options').innerHTML = request.get_text();
				}
		</script>
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["scheduler_caddie_type"]."</label>
			</div>
			<div class='colonne_suite'>
				<select name='scheduler_caddie_type' onchange='scheduler_caddie_get_actions(this.value);scheduler_caddie_get_list(this.value);'>";
		foreach ($types as $options) {
			foreach($options as $type) {
				$form_task .= "<option value='".$type."' ".($scheduler_caddie_type == $type ? "selected='selected'" : "").">".$msg['caddie_de_'.$type]."</option>";
			}
		}
		$form_task .= "
				</select>
			</div>
		</div>";
		$form_task .= $this->get_display_actions($scheduler_caddie_type, $action);
		$form_task .= "<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["scheduler_caddie_list"]."</label>
			</div>
			<div class='colonne_suite' id='scheduler_caddie_planning_caddies_list'>
				".static::get_display_caddie_list($scheduler_caddie_type, $list)."
			</div>
		</div>";	
		return $form_task;
	}
		
	public static function get_display_caddie_row($caddie_instance, $valeur=array(), $list=array()) {
		$display= "
			<td>
				<input type='checkbox' id='scheduler_caddie_list_".$valeur['idcaddie']."' name='scheduler_caddie_list[".$valeur['idcaddie']."]' value='".$valeur['idcaddie']."' ".(isset($list[$valeur['idcaddie']]) && $list[$valeur['idcaddie']] ? "checked='checked'" : "")." />
			</td>
			<td class='classement60'>";
		$display.= "<strong>".$valeur['name']."</strong>";
		if ($valeur['comment']){
			$display.=  "<br /><small>(".$valeur['comment'].")</small>";
		}
		$display.= "</td>";
		$display.= $caddie_instance->aff_nb_items_reduit();
		return $display;
	}
	
	public static function get_display_caddie_list($object_type='', $list=array()) {
		global $msg;
		global $PMBuserid;
	
		$display = '';
		$model_class_name = static::get_model_class_name_from_object_type($object_type);
		$liste = $model_class_name::get_cart_list($object_type);
		if (!empty($liste)) {
			$print_cart = array();
			$parity = array();
			foreach ($liste as $valeur) {
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $valeur['autorisations_all'] || $PMBuserid==1) {
					$myCart = new $model_class_name();
					$myCart->nb_item=$valeur['nb_item'];
					$myCart->nb_item_pointe=$valeur['nb_item_pointe'];
					$myCart->type=$valeur['type'];
					$print_cart[$myCart->type]["titre"]="<b>".$msg["caddie_de_".$myCart->type]."</b><br />";
					if(!trim($valeur["caddie_classement"])){
						$valeur["caddie_classement"]=classementGen::getDefaultLibelle();
					}
					if(!isset($parity[$myCart->type])) $parity[$myCart->type] = 0;
					$parity[$myCart->type]=1-$parity[$myCart->type];
					if ($parity[$myCart->type]) $pair_impair = "even";
					else $pair_impair = "odd";
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
	
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["titre"] = stripslashes($valeur["caddie_classement"]);
					if(!isset($print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"])) {
						$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] = '';
					}
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] .= "<tr class='$pair_impair' $tr_javascript >".static::get_display_caddie_row($myCart, $valeur, $list)."</tr>";
				}
			}
			//Tri des classements
			foreach($print_cart as $key => $cart_type) {
				ksort($print_cart[$key]["classement_list"]);
			}
			// affichage des paniers par type
			foreach($print_cart as $key => $cart_type) {
				//on remplace les clés à cause des accents
				$cart_type["classement_list"]=array_values($cart_type["classement_list"]);
				foreach($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
					$display.=gen_plus($key.$keyBis,$cart_typeBis["titre"],"<table style='border:0px; border-spacing: 0px; width: 100%' class='classementGen_tableau' role='presentation'>".$cart_typeBis["cart_list"]."</table>",0);
				}
			}
		} else {
			$display .= $msg[398];
		}
		return $display;
	}
	
	public function get_display_caddie_selector_row($caddie_instance, $valeur=array()) {
		$display = $valeur['name'];
		if ($valeur['comment']){
			$display.=  "<small>(".$valeur['comment'].")</small>";
		}
		$display.= " ".strip_tags($caddie_instance->aff_nb_items_reduit());
		return $display;
	}
	
	public function get_display_caddie_selector($object_type='') {
		global $msg;
		global $PMBuserid;
		global $charset;
	
		$display = '';
		$model_class_name = static::get_model_class_name_from_object_type($object_type);
		$liste = $model_class_name::get_cart_list($object_type);
		if (!empty($liste)) {
			$print_cart = array();
		    foreach ($liste as $valeur) {
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $valeur['autorisations_all'] || $PMBuserid==1) {
					$myCart = new $model_class_name();
					$myCart->nb_item=$valeur['nb_item'];
					$myCart->nb_item_pointe=$valeur['nb_item_pointe'];
					$myCart->type=$valeur['type'];
					$print_cart[$myCart->type]["titre"]="<b>".$msg["caddie_de_".$myCart->type]."</b><br />";
					if(!trim($valeur["caddie_classement"])){
						$valeur["caddie_classement"]=classementGen::getDefaultLibelle();
					}
	
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["titre"] = stripslashes($valeur["caddie_classement"]);
					if(!isset($print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"])) {
						$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] = '';
					}
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] .= "<option value='".$valeur['idcaddie']."' ".(isset($this->param['scheduler_caddie_action_by_caddie']) && $this->param['scheduler_caddie_action_by_caddie'] == $valeur['idcaddie'] ? "selected='selected'" : "").">".htmlentities($this->get_display_caddie_selector_row($myCart, $valeur), ENT_QUOTES, $charset)."</option>";
				}
			}
			//Tri des classements
			foreach($print_cart as $key => $cart_type) {
				ksort($print_cart[$key]["classement_list"]);
			}
			$display .= "<select id='scheduler_caddie_action_by_caddie' name='scheduler_caddie_action_by_caddie'>";
			// affichage des paniers par type
			foreach($print_cart as $key => $cart_type) {
				foreach($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
					$display .= "<optgroup label='".htmlentities($keyBis, ENT_QUOTES, $charset)."'>";
					$display.= $cart_typeBis["cart_list"];
					$display.= "</optgroup>";
				}
			}
			$display .= "</select>";
		} else {
			$display .= $msg[398];
		}
		return $display;
	}
	
	public static function get_actions() {
		global $msg;
		
		return array(
				'caddie' => array(
						'collecte' => array(
								'selection' => $msg['caddie_menu_collecte_selection']
						),
						'pointage' => array(
								'selection' => $msg['caddie_menu_pointage_selection'],
								'panier' => $msg['caddie_menu_pointage_panier'],
								'raz' => $msg['caddie_menu_pointage_raz']
						),
						'action' => array(
								'supprpanier' => $msg['caddie_menu_action_suppr_panier'],
								'selection' => $msg['caddie_menu_action_selection'],
								'supprbase' => $msg['caddie_menu_action_suppr_base'],
								'reindex' => $msg['caddie_menu_action_reindex'],
								'signature' => $msg['caddie_menu_action_signature']
						)
				),
				'empr_caddie' => array(
						'collecte' => array(
								'selection' => $msg['empr_caddie_menu_collecte_selection']
						),
						'pointage' => array(
								'selection' => $msg['empr_caddie_menu_pointage_selection'],
								'panier' => $msg['empr_caddie_menu_pointage_panier'],
								'raz' => $msg['empr_caddie_menu_pointage_raz']
						),
				),
				'authorities_caddie' => array(
						'collecte' => array(
								'selection' => $msg['caddie_menu_collecte_selection']
						),
						'pointage' => array(
								'selection' => $msg['caddie_menu_pointage_selection'],
								'panier' => $msg['caddie_menu_pointage_panier'],
								'raz' => $msg['caddie_menu_pointage_raz']
						),
						'action' => array(
								'supprpanier' => $msg['caddie_menu_action_suppr_panier'],
								'selection' => $msg['caddie_menu_action_selection'],
								'supprbase' => $msg['caddie_menu_action_suppr_base'],
								'reindex' => $msg['caddie_menu_action_reindex']
						)
				)
		);
		
		
	}
	
	public static function get_actions_selector($object_type='', $action='') {
		global $msg;
		
		$selector = "<select id='scheduler_caddie_action' name='scheduler_caddie_action' onchange=\"scheduler_caddie_get_action_form('".$object_type."',this.value);\">";
		$model_class_name = static::get_model_class_name_from_object_type($object_type);
		$actions = static::get_actions();
		foreach($actions[$model_class_name] as $menu=>$tabs) {
			foreach($tabs as $key=>$label) {
				$option_value = $model_class_name."|||".$menu."|||".$key;
				$selector .= "<option value='".$option_value."' ".($action == $option_value ? "selected='selected'" : "").">".$msg['caddie_menu_'.$menu]." &gt; ".$label."</option>";
			}
		}
		$selector .= "</select>";
		return $selector; 
	}
	
	public function get_choix_quoi_content($action_what) {
	    global $msg, $pmb_digital_signature_activate;
		
		$html = "";
		if($action_what == "signature") {
		    if(empty($pmb_digital_signature_activate)) {
		        return "<div class='row'>
                           <span class='erreur'>" . $msg["planificateur_signature_need_activate"] . "<span>
        			    </div>";
		    }
		    $signature_id = 0;
		    if (!empty($this->param['scheduler_caddie_action_sign'])) {
		        $signature_id = $this->param['scheduler_caddie_action_sign'];
		    }
		    
		    $caddie_clear = "";
		    if (!empty($this->param['scheduler_caddie_action_clear'])) {
		        $caddie_clear = "checked";
		    }
		    $html = "
                <div class='row'>
        			<div>
        				<label for='scheduler_caddie_action_sign'>" . $msg["planificateur_signature_list_choice"] . "</label>
        				" . gen_liste_multiple("select id, name, num_cert from digital_signature", "id", "name", "", "scheduler_caddie_action_sign","",$signature_id,"", "", "", "", 0) . "
        			</div>
        		</div>
                <div class='row'>
        			<div>
        				<label for='scheduler_caddie_action_clear'>" . $msg["planificateur_signature_clear_caddie"] . "</label>
                        <input type='checkbox' name='scheduler_caddie_action_clear' value='1' " . $caddie_clear . ">
        			</div>
        		</div>
            ";
		}
		$html .= "
			<div class='scheduler_caddie_action_flag'>
				<div class='row'>
					<input type='checkbox' name='scheduler_caddie_action_elt_flag' id='scheduler_caddie_action_elt_flag' ".(isset($this->param['scheduler_caddie_action_elt_flag']) && $this->param['scheduler_caddie_action_elt_flag'] ? "checked='checked'" : "")." value='1'><label for='scheduler_caddie_action_elt_flag'>".$msg['caddie_item_marque']."</label>
					".(($action_what=="supprbase" || $action_what=="supprpanier") ? "&nbsp;<input type='checkbox' name='scheduler_caddie_action_elt_flag_inconnu' id='scheduler_caddie_action_elt_flag_inconnu' value='1'><label for='scheduler_caddie_action_elt_flag_inconnu'>".$msg['caddie_item_blob'] : '')."</label>		
				</div>
				<div class='row'>
					<input type='checkbox' name='scheduler_caddie_action_elt_no_flag' id='scheduler_caddie_action_elt_no_flag' ".(isset($this->param['scheduler_caddie_action_elt_no_flag']) && $this->param['scheduler_caddie_action_elt_no_flag'] ? "checked='checked'" : "")." value='1'><label for='scheduler_caddie_action_elt_no_flag'>".$msg['caddie_item_NonMarque']."</label>
					".(($action_what=="supprbase" || $action_what=="supprpanier") ? "&nbsp;<input type='checkbox' name='scheduler_caddie_action_elt_no_flag_inconnu' id='scheduler_caddie_action_elt_no_flag_inconnu' value='1'><label for='scheduler_caddie_action_elt_no_flag_inconnu'>".$msg['caddie_item_blob'] : '')."</label>
				</div>
			</div>
				";
		return $html;
	}
	
	public function get_action_form($object_type='', $action='') {
		$this->get_property_task_bdd();
		$action_form = '';
		if($action) {
			$exploded_action = explode('|||', $action);
			//$action_model_class_name = $exploded_action[0];
			$action_type = $exploded_action[1];
			$action_what = $exploded_action[2];
			
			switch ($action_type) {
				case 'collecte':
					switch ($action_what) {
						case 'selection':
							$action_form .= $this->get_choix_quoi_content($action_what);
							$action_form .= $this->get_display_procs_list($object_type, 'SELECT');
							break;
					}
					break;
				case 'pointage':
					switch ($action_what) {
						case 'selection':
							$action_form .= $this->get_choix_quoi_content($action_what);
							$action_form .= $this->get_display_procs_list($object_type, 'SELECT');
							break;
						case 'panier':
							$action_form .= $this->get_choix_quoi_content($action_what);
							$action_form .= $this->get_display_caddie_selector($object_type);
							break;
						case 'raz':
							//No sub form
							break;
					}
					break;
				case 'action':
					switch ($action_what) {
						case 'supprpanier':
							$action_form .= $this->get_choix_quoi_content($action_what);
							break;
						case 'selection':
							$action_form .= $this->get_choix_quoi_content($action_what);
							$action_form .= $this->get_display_procs_list($object_type);
							break;
						case 'supprbase':
							$action_form .= $this->get_choix_quoi_content($action_what);
							break;
						case 'reindex':
							$action_form .= $this->get_choix_quoi_content($action_what);
							break;
						case 'signature':
						    $action_form .= $this->get_choix_quoi_content($action_what);
						    break;
					}
					break;
			}
		}
		return $action_form;
	}
	
	protected function get_display_actions($object_type='', $action='') {
		$display = "
		<script type='text/javascript'>
			function scheduler_caddie_get_action_form(object_type, sub_action) {
				var request = new http_request();
				request.request('./ajax.php?module=admin&categ=planificateur&sub=caddie&action=get_action_form&id=".$this->id."&object_type='+object_type+'&sub_action='+sub_action, false,'', false);
				document.getElementById('scheduler_caddie_planning_action_form').innerHTML = request.get_text();
			}
		</script>
		<div class='row'>
			<div class='colonne3'>
				<label for='scheduler_caddie_planning_actions'>".$this->msg["scheduler_caddie_action"]."</label>
			</div>
			<div class='colonne_suite' id='scheduler_caddie_planning_actions'>
				".static::get_actions_selector($object_type, $action)."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				&nbsp;
			</div>
			<div class='colonne_suite' id='scheduler_caddie_planning_action_form'>
				".$this->get_action_form($object_type, $action)."
			</div>
		</div>
		<script type='text/javascript'>
			scheduler_caddie_get_action_form('".$object_type."',document.getElementById('scheduler_caddie_action').value);
		</script>";
		return $display;
	}
	
	public function make_serialized_task_params() {
		global $scheduler_caddie_type;
		global $scheduler_caddie_action;
		global $scheduler_caddie_action_elt_flag;
		global $scheduler_caddie_action_elt_no_flag;
		global $scheduler_caddie_action_elt_flag_inconnu;
		global $scheduler_caddie_action_elt_no_flag_inconnu;
		global $scheduler_caddie_action_by_caddie;
		global $scheduler_caddie_list;
		global $scheduler_proc;
		global $scheduler_caddie_action_clear;
		global $scheduler_caddie_action_sign;
		
		$t = parent::make_serialized_task_params();
		
		$t['scheduler_caddie_type'] = $scheduler_caddie_type;
		$t['scheduler_caddie_action'] = $scheduler_caddie_action;
		$t['scheduler_caddie_action_elt_flag'] = (int) $scheduler_caddie_action_elt_flag;
		$t['scheduler_caddie_action_elt_no_flag'] = (int) $scheduler_caddie_action_elt_no_flag;
		$t['scheduler_caddie_action_elt_flag_inconnu'] = (int) $scheduler_caddie_action_elt_flag_inconnu;
		$t['scheduler_caddie_action_elt_no_flag_inconnu'] = (int) $scheduler_caddie_action_elt_no_flag_inconnu;
		$t['scheduler_caddie_action_by_caddie'] = (int) $scheduler_caddie_action_by_caddie;
		$t['scheduler_caddie_list'] = $scheduler_caddie_list;
		$t['scheduler_caddie_action_clear'] = $scheduler_caddie_action_clear;
		$t['scheduler_caddie_action_sign'] = $scheduler_caddie_action_sign;
		$t['scheduler_proc'] = $scheduler_proc;
		$t['scheduler_proc_options'] = array();
		if($t['scheduler_proc']) {
		    $exploded_action = explode('|||', $scheduler_caddie_action);
		    $table = (!empty($exploded_action[0]) ? $exploded_action[0] : 'caddie_procs');
		    if(strpos($table, 'procs') === false) {
		        $table .= '_procs'; //on rajoute le suffixe pour la table de procédures
		    }
		    $hp = new parameters ($t['scheduler_proc'], $table);
			$t['scheduler_proc_options'] = $hp->make_serialized_parameters_params();
		}
    	return serialize($t);
	}
	
	public static function is_for_cart($object_type, $requete) {
	    $match = [];
		if (preg_match("/CADDIE\(([^\)]*)\)/",$requete,$match)) {
			$m=explode(",",$match[1]);
			$as=array_search($object_type,$m);
			if (($as!==NULL)&&($as!==false)) return true; else return false;
		} else return false;
	}
	
	// affichage du tableau des procédures
	public function get_display_procs_list($object_type, $type='ACTION') {
		global $msg;
		global $PMBuserid;
	
		$model_class_name = static::get_model_class_name_from_object_type($object_type);
		$proc_class_name = $model_class_name.'_procs';
		
		$display = "<hr />".$msg['caddie_select_proc']."<br />";
	
		if ($PMBuserid!=1) $where=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		else $where="";
		$query = "SELECT idproc, type, name, requete, comment, autorisations, parameters FROM ".$proc_class_name::$table." WHERE type='".$type."' $where ORDER BY name ";
		$result = pmb_mysql_query($query);
		$display .= "
		<div class='row'>";
		$n_proc = 0;
		if($result) {
			$display .= "<select name='scheduler_proc' onchange='scheduler_caddie_get_proc_options(this.value, \"".$proc_class_name::$table."\");'>";
			while($row = pmb_mysql_fetch_object($result)) {
				$autorisations=explode(" ",$row->autorisations);
				if ((array_search ($PMBuserid, $autorisations)!==FALSE || $PMBuserid == 1)&&($type != 'ACTION' || static::is_for_cart($object_type, $row->requete))) {
					if(empty($this->param['scheduler_proc'])) {
						$this->param['scheduler_proc'] = $row->idproc;
					}
					$display .= "<option value='".$row->idproc."' ".($this->param['scheduler_proc'] == $row->idproc ? "selected='selected'" : "").">".$row->name."</option>";
					$n_proc++;
				}
			}
			$display .= "</select>
			<div id='scheduler_proc_options' class='row'>";
			if(isset($this->param['scheduler_proc']) && $this->param['scheduler_proc']) {
				$hp = new parameters ($this->param['scheduler_proc']);
				if (isset($this->param['scheduler_proc_options']) && is_array($this->param['scheduler_proc_options'])) {
				    $hp->make_unserialized_parameters_params($this->param['scheduler_proc_options']);
				}
				$display .= $hp->get_content_form();
			}
			$display .= "</div>";
		}
		if ($n_proc==0) {
			switch ($type) {
				case 'ACTION':
					$display .= $msg["caddie_no_action_proc"];
					break;
				case 'SELECT':
					$display .= $msg["caddie_no_select_proc"];
					break;
			}
		}
		$display .= "</div>";
		return $display;
	}
}


