<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_indexint_controller.class.php,v 1.1 2018-10-08 13:59:39 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/entities/entities_authorities_controller.class.php");

// on a besoin des templates indexation interne
include($include_path.'/templates/indexint.tpl.php');

// la classe de gestion des indexation interne
require_once($class_path.'/indexint.class.php');
require_once($class_path.'/pclassement.class.php');

class entities_indexint_controller extends entities_authorities_controller {
	
	protected $model_class_name = 'indexint';
	
	protected $id_pclass;
	
	public function get_object_instance() {
		$model_class_name = $this->get_model_class_name();
		$object_instance = new $model_class_name($this->id, $this->id_pclass);
		if(method_exists($model_class_name, 'set_controller')) {
			$model_class_name::set_controller($this);
		}
		return $object_instance;
	}
	
	public function set_id_pclass($id_pclass=0) {
		$this->id_pclass = $id_pclass+0;
	}
	
	public function proceed() {
		global $sub;
		global $id_pclass;
		
		switch($sub) {
			case 'pclass':
				print pclassement::get_display_list();
				break;
			case 'pclass_form':
				$pclassement = new pclassement($id_pclass);
				print $pclassement->get_form();
				break;
			case 'pclass_update':
				$pclassement = new pclassement($id_pclass);
				$pclassement->set_properties_from_form();
				$pclassement->save();
				print pclassement::get_display_list();
				break;
			case 'pclass_delete' :
				$pclassement = new pclassement($id_pclass);
				$pclassement->delete();
				print pclassement::get_display_list();
				break;
			default:
				parent::proceed();
				break;
		}
	}
	
	public function proceed_replace() {
		global $msg;
		global $n_indexint_id, $aut_link_save;
	
		$object_instance = $this->get_object_instance();
		if(!$n_indexint_id) {
			$object_instance->replace_form();
		}else {
			// routine de remplacement
			$rep_result = $object_instance->replace($n_indexint_id,$aut_link_save);
			if(!$rep_result) {
				print $this->get_display_list();
			}else {
				error_message($msg[132], $rep_result, 1, $this->get_edit_link());
			}
		}
	}
	
	public function proceed_update() {
		global $msg;
		global $indexint_nom, $indexint_comment, $indexint_pclassement;
		global $authority_statut, $authority_thumbnail_url;
	
		// mettre à jour
		$object_instance = $this->get_object_instance();
		$object_instance->update($indexint_nom, $indexint_comment, $indexint_pclassement, $authority_statut, $authority_thumbnail_url);
		if($object_instance->get_cp_error_message()){
			error_message($msg['indexint_create'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
			return 0;
		}else{
			return $object_instance->indexint_id;
		}
	}
	
	public function get_searcher_instance() {
		global $exact;
		
		$exact += 0;
		$indexint_searcher = searcher_factory::get_searcher('indexint', '', $this->user_input);
		$fields_restrict = array();
		if (!$exact) {
			$fields_restrict[]= array(
					'field' => "code_champ",
					'values' => array(8002),
					'op' => "and",
					'not' => false
			);
		} else {
			$fields_restrict[]= array(
					'field' => "code_champ",
					'values' => array(8001),
					'op' => "and",
					'not' => false
			);
		}
		$indexint_searcher->add_fields_restrict($fields_restrict);
		return $indexint_searcher;
	}
	
	protected function search_form() {
		global $id_pclass;
		
		$id_pclass += 0;
		$model_class_name = $this->get_model_class_name();
		$model_class_name::search_form($id_pclass);
	}
	
	protected function get_pagination_link() {
		global $thesaurus_classement_mode_pmb;
		global $thesaurus_classement_defaut;
		global $id_pclass;
		global $exact;
		
		$link = parent::get_pagination_link();
		if ($thesaurus_classement_mode_pmb != 0) {
			if($id_pclass!=0) {
				$link .= "&id_pclass=$id_pclass";
			}
		} else {
			$link .= "&id_pclass=$thesaurus_classement_defaut";
		}
		$link .= "&exact=".$exact;
		return $link;
	}
	
	protected function get_display_header_list() {
		global $msg;
		
		$display = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			<th>".$msg[707]."</th>
			<th>".$msg["count_notices_assoc"]."</th>
			<th></th>
		</tr>";
		return $display;
	}
	
	protected function get_display_columns() {
		global $thesaurus_classement_mode_pmb;
		global $charset;
		
		$object_instance = $this->authority->get_object_instance();
		
		if($thesaurus_classement_mode_pmb!=0){
			$pclass_name="[".$object_instance->name_pclass."] ";
		} else {
			$pclass_name="";
		}
		$display = $this->get_display_label_column($pclass_name.htmlentities($object_instance->name,ENT_QUOTES, $charset));
		$display .= "
			<td style='vertical-align:top' onmousedown=\"document.location='".$this->get_edit_link($this->authority->get_num_object())."&user_input=".rawurlencode($this->user_input)."&nbr_lignes=".$this->nbr_lignes."&page=".$this->page."';\">
				".htmlentities($object_instance->comment,ENT_QUOTES, $charset)."
			</td>";
		return $display;
	}
	protected function get_query_notice_count() {
		return "SELECT count(*) FROM notices WHERE indexint = ".$this->authority->get_num_object();
	}
	
	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=indexint&id=".$id;
	}
	
	protected function get_edit_link($id=0) {
		global $thesaurus_classement_mode_pmb;
		global $thesaurus_classement_defaut;
		global $id_pclass;
		global $exact;
		
		if(!$id) $id = $this->id;
		$link = '';
		if ($thesaurus_classement_mode_pmb != 0) {
			if($id_pclass!=0) {
				$link .= "&id_pclass=$id_pclass";
			}
		} else {
			$link .= "&id_pclass=$thesaurus_classement_defaut";
		}
		return $this->url_base."&sub=indexint_form&id=".$id."&exact=".$exact.$link;
	}
	
	protected function get_results_title() {
		global $msg;
		global $exact;
		
		if ($this->user_input) {
			if ($exact)
				$c_user_input= $msg["rech_exacte"];
			else
				$c_user_input=$msg["rech_commentaire"];
		} else {
			$c_user_input = '';
		}
		return $msg['indexint_found']." ".$c_user_input;
	}
	
	protected function display_no_results() {
		global $msg;
		
		error_message($msg['indexint_search'], str_replace('!!titre_cle!!', $this->user_input, $msg['indexint_noresult']), 0, $this->url_base.'&sub=&id=');
	}
	
	protected function get_search_mode() {
		return 1;
	}
	
	protected function get_aut_type() {
		return "indexint";
	}
	
	protected function get_last_order() {
		return 'order by indexint_id desc ';
	}
	
	public function get_back_url() {
		global $exact;
	
		$this->back_url = parent::get_back_url();
		if($this->id_pclass) $this->back_url .= "&id_pclass=".$this->id_pclass;
		if($exact) $this->back_url .= "&exact=".$exact;
		return $this->back_url;
	}
	
	public function get_delete_url() {
		global $exact;
		
		$this->delete_url = parent::get_delete_url();
		if($this->id_pclass) $this->delete_url .= "&id_pclass=".$this->id_pclass;
		if($exact) $this->delete_url .= "&exact=".$exact;
		return $this->delete_url;
	}
	
	protected function get_aut_const(){
	    return TYPE_INDEXINT;
	}
}
