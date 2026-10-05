<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_titres_uniformes_controller.class.php,v 1.14.6.1 2024/06/11 08:23:56 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($class_path."/entities/entities_authorities_controller.class.php");
// classe de gestion des titres uniformes
require_once($class_path.'/titre_uniforme.class.php');
require_once($include_path.'/templates/titres_uniformes.tpl.php');

class entities_titres_uniformes_controller extends entities_authorities_controller {

	protected $model_class_name = 'titre_uniforme';

	public function proceed() {
		global $sub;

		switch($sub) {
			case 'titre_uniforme_form':
			    $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
			    $this->proceed_form();
				break;
			case 'titre_uniforme_last':
				$this->proceed_last();
				break;
			default:
				parent::proceed();
				break;
		}
	}

	public function proceed_update() {
		global $msg;
		global $tu_name, $tonalite, $form_tonalite_selector, $comment, $tu_import_denied;
		global $tu_form, $form_form_selector, $date, $subject, $place, $history;
		global $characteristic, $intended_termination, $intended_audience;
		global $context, $coordinates, $equinox, $oeuvre_nature, $oeuvre_type;
		global $authority_statut, $authority_thumbnail_url;

		global $max_oeuvre_expression;
		global $max_oeuvre_expression_from;
		global $max_oeuvre_event;
		global $max_other_link;
		global $tab_other_link_order;
		global $tab_oeuvre_expression_order;
		global $tab_oeuvre_expression_from_order;
		global $max_tu_notices;
		global $max_distrib;
		global $max_ref;
		global $max_subdiv;
		global $forcing_values;
		global $forcing;

		if (empty($forcing_values)) {
    		// mettre à jour
    		$titre_uniforme_val = array(
    				'name' 			=> $tu_name,
    				'tonalite' 		=> $tonalite,
    				'tonalite_selector' => $form_tonalite_selector,
    				'comment'		=> $comment,
    				'import_denied'	=> (isset($tu_import_denied) ? $tu_import_denied : 0),
    				'tu_form' 			=> $tu_form,
    				'form_selector' => $form_form_selector,
    				'date' 			=> $date,
    				'subject' 		=> $subject,
    				'place' 		=> $place,
    				'history' 		=> $history,
    				'characteristic'		=> $characteristic,
    				'intended_termination' 	=> $intended_termination,
    				'intended_audience' 	=> $intended_audience,
    				'context' 		=> $context,
    				'coordinates' 	=> $coordinates,
    				'equinox' 		=> $equinox,
    				'oeuvre_nature' => $oeuvre_nature,
    				'oeuvre_type' 	=> $oeuvre_type,
    				'statut'=> $authority_statut,
    				'thumbnail_url' => $authority_thumbnail_url
    		);

    		$titre_uniforme_val['oeuvre_expression'] = array();
    		$tab_oeuvre_expression_order = explode(",", $tab_oeuvre_expression_order);
    		foreach ($tab_oeuvre_expression_order as $order) {
    			$var_oeuvre_expressioncode = 'f_oeuvre_expression_code'.$order;
    			$var_oeuvre_expressiontype = 'f_oeuvre_expression_type'.$order;
    			global ${$var_oeuvre_expressioncode};
    			global ${$var_oeuvre_expressiontype};
    			if (${$var_oeuvre_expressioncode}) {
    				$titre_uniforme_val['oeuvre_expression'][] = array(
    						'code' => ${$var_oeuvre_expressioncode},
    						'type' => ${$var_oeuvre_expressiontype}
    				);
    			}
    		}
    		$titre_uniforme_val['oeuvre_expression_from'] = array();
    		$tab_oeuvre_expression_from_order = explode(",", $tab_oeuvre_expression_from_order);
    		foreach ($tab_oeuvre_expression_from_order as $order) {
    		    $var_oeuvre_expression_fromcode = 'f_oeuvre_expression_from_code'.$order;
    		    $var_oeuvre_expression_fromtype = 'f_oeuvre_expression_from_type'.$order;
    			global ${$var_oeuvre_expression_fromcode};
    			global ${$var_oeuvre_expression_fromtype};
    			if (${$var_oeuvre_expression_fromcode}) {
    				$titre_uniforme_val['oeuvre_expression_from'][] = array(
    						'code' => ${$var_oeuvre_expression_fromcode},
    						'type' => ${$var_oeuvre_expression_fromtype}
    				);
    			}
    		}

    		$titre_uniforme_val['oeuvre_event'] = array();
    		for ($i=0; $i< $max_oeuvre_event; $i++) {
    			$var_oeuvre_eventcode = 'f_oeuvre_event_code'.$i;
    			$var_oeuvre_eventtype = 'f_oeuvre_event_type'.$i;
    			global ${$var_oeuvre_eventcode};
    			if (${$var_oeuvre_eventcode}) {
    				$titre_uniforme_val['oeuvre_event'][] = array(
    						'code' => ${$var_oeuvre_eventcode}
    				);
    			}
    		}

    		$titre_uniforme_val['other_link'] = array();
    		$count=0;
    		$tab_other_link_order = explode(",", $tab_other_link_order);
    		foreach ($tab_other_link_order as $order) {
    		    $var_other_linkcode = 'f_other_link_code'.$order;
    		    $var_oeuvre_others_link = 'f_oeuvre_other_link'.$order;
    			global ${$var_oeuvre_others_link};
    			global ${$var_other_linkcode};
    			if (${$var_other_linkcode}){
    				$titre_uniforme_val['other_link'][$count]['type'] = ${$var_oeuvre_others_link};
    				$titre_uniforme_val['other_link'][$count]['code'] = ${$var_other_linkcode};
    				$count++;
    			}
    		}

    		$titre_uniforme_val['tu_notices'] = array();
    		$nb_ntu=0;
    		for ($i=0; $i< $max_tu_notices; $i++) {
    			$var_tu_notices_code = 'f_tu_notices_code'.$i;
    			$var_tu_notices_ntu_titre = 'ntu_titre'.$i;
    			$var_tu_notices_ntu_date = 'ntu_date'.$i;
    			$var_tu_notices_ntu_sous_vedette = 'ntu_sous_vedette'.$i;
    			$var_tu_notices_ntu_langue = 'ntu_langue'.$i;
    			$var_tu_notices_ntu_version = 'ntu_version'.$i;
    			$var_tu_notices_ntu_mention = 'ntu_mention'.$i;
    			global ${$var_tu_notices_code};
    			if (${$var_tu_notices_code}) {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_num_notice'] = ${$var_tu_notices_code};
    			}else{
    				continue;
    			}
    			global ${$var_tu_notices_ntu_titre};
    			if (${$var_tu_notices_ntu_titre}) {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_titre'] = ${$var_tu_notices_ntu_titre};
    			} else {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_titre'] = '';
    			}
    			global ${$var_tu_notices_ntu_date};
    			if (${$var_tu_notices_ntu_date}) {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_date'] = ${$var_tu_notices_ntu_date};
    			} else {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_date'] = '';
    			}
    			global ${$var_tu_notices_ntu_sous_vedette};
    			if (${$var_tu_notices_ntu_sous_vedette}) {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_sous_vedette'] = ${$var_tu_notices_ntu_sous_vedette};
    			} else {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_sous_vedette'] = '';
    			}
    			global ${$var_tu_notices_ntu_langue};
    			if (${$var_tu_notices_ntu_langue}) {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_langue'] = ${$var_tu_notices_ntu_langue};
    			} else {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_langue'] = '';
    			}
    			global ${$var_tu_notices_ntu_version};
    			if (${$var_tu_notices_ntu_version}) {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_version'] = ${$var_tu_notices_ntu_version};
    			} else {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_version'] = '';
    			}
    			global ${$var_tu_notices_ntu_mention};
    			if (${$var_tu_notices_ntu_mention}) {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_mention'] = ${$var_tu_notices_ntu_mention};
    			} else {
    				$titre_uniforme_val['tu_notices'][$nb_ntu]['ntu_mention'] = '';
    			}
    			global ${$var_tu_notices_code};
    			if (${$var_tu_notices_code}) {
    				$nb_ntu++;
    			}
    		}

    		// Distribution instrumentale et vocale (pour la musique)
    		$titre_uniforme_val['distrib'] = array();
    		for($i=0; $i<=$max_distrib; $i++) {
    			$f_distrib_value = "f_distrib".$i;
    			global ${$f_distrib_value};
    			if(${$f_distrib_value}) $titre_uniforme_val['distrib'][]= ${$f_distrib_value};
    		}
    		// Référence numérique (pour la musique)
    		$titre_uniforme_val['ref'] = array();
    		for($i=0; $i<=$max_ref; $i++) {
    			$f_ref_value = "f_ref".$i;
    			global ${$f_ref_value};
    			if(${$f_ref_value}) $titre_uniforme_val['ref'][]= ${$f_ref_value};
    		}
    		// Subdivision de forme
    		$titre_uniforme_val['subdiv'] = array();
    		for($i=0; $i<=$max_subdiv; $i++) {
    			$f_subdiv_value = "f_subdiv".$i;
    			global ${$f_subdiv_value};
    			if(${$f_subdiv_value}) $titre_uniforme_val['subdiv'][]= ${$f_subdiv_value};
    		}
		} else {
		    $titre_uniforme_val = encoding_normalize::json_decode(stripslashes($forcing_values), true);
		}

		$object_instance = $this->get_object_instance();
		if (!isset($forcing)) {
		    $forcing = false;
		}
		$updated = $object_instance->update($titre_uniforme_val, $forcing);
		if($object_instance->get_cp_error_message()){
			error_message($msg['aut_titre_uniforme_creation'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
		}elseif($updated) {
			return $object_instance->id;
		}
		return 0;
	}

	public function get_searcher_instance() {
		return searcher_factory::get_searcher('titres_uniformes', '', $this->user_input);
	}

	protected function get_display_header_list() {
		global $msg;

		$this->num_auth_present = searcher_authorities_titres_uniformes::has_authorities_sources('uniform_title');

		$display = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			".($this->num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
			<th>".$msg["count_notices_assoc"]."</th>
            <th></th>
		</tr>";
		return $display;
	}

	protected function get_pagination_link() {
		global $oeuvre_type_selector;
		global $oeuvre_nature_selector;

		$link = parent::get_pagination_link();
		$link .= '&oeuvre_type_selector='.$oeuvre_type_selector;
		$link .= '&oeuvre_nature_selector='.$oeuvre_nature_selector;
		return $link;
	}

	protected function get_display_columns() {
		$object_instance = $this->authority->get_object_instance();
		$object_instance->do_isbd();

		$display = $this->get_display_label_column($object_instance->get_isbd());

 		//Numéros d'autorite
		if($this->num_auth_present){
			$display .= "<td>".searcher_authorities_titres_uniformes::get_display_authorities_sources($object_instance->id, 'uniform_title')."</td>";
		}
		return $display;
	}

	protected function get_query_notice_count() {
		return "SELECT count(*) FROM notices_titres_uniformes WHERE ntu_num_tu = ".$this->authority->get_num_object();
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=titre_uniforme&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=titre_uniforme_form&id=".$id;
	}

	protected function get_results_title() {
		global $msg;

		return $msg['aut_titre_uniforme_result'];
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg[211],
			str_replace('!!author_cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg['aut_titre_uniforme_no_result']),
			0,
			$this->url_base.'&sub=&id='
		);
	}

	protected function get_search_mode() {
		return 9;
	}

	protected function get_aut_type() {
		return "titre_uniforme";
	}

	protected function get_last_order() {
		return 'order by tu_id desc ';
	}

	protected function get_aut_const(){
	    return TYPE_TITRE_UNIFORME;
	}

	public function proceed_delete() {
	    global $msg;

	    $object_instance = $this->get_object_instance();
	    $sup_result = $object_instance->delete();
	    if(!$sup_result) {
	        print $this->get_display_list();
	    }else {
	        box_confirm_message($msg[132], $sup_result, titre_uniforme::format_delete_url("&forcing=1&id=".$this->id, true), $this->get_edit_link(), $msg["autorite_suppr_categ_forcage_button"]);
	    }
	}
}
