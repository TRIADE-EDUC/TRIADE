<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_tpl.class.php,v 1.13 2021/12/21 15:01:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/template.class.php");
require_once($include_path."/templates/serialcirc_tpl.tpl.php");
require_once($class_path."/serialcirc_tpl_print_fields.class.php");

class serialcirc_tpl extends template {
	
	protected static $table_name = 'serialcirc_tpl';
	protected static $field_name = 'serialcirctpl_id';
	
	protected $piedpage; // pied de page
		
	// ---------------------------------------------------------------
	//		fetch_data() : récupération infos 
	// ---------------------------------------------------------------
	public function fetch_data() {
		$this->name	="";
		$this->comment ="";
		$this->content ="";
		$this->piedpage ="";
		if($this->id) {
			$query = "SELECT * FROM serialcirc_tpl WHERE serialcirctpl_id='".$this->id."' LIMIT 1 ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);				
				$this->name	= $temp->serialcirctpl_name;
				$this->comment	= $temp->serialcirctpl_comment;
 				$this->content = $temp->serialcirctpl_tpl;
 				$this->piedpage = $temp->serialcirctpl_piedpage;
			} else {
				// pas trouvé avec cette clé
				$this->id = 0;								
			}
		}
	}
	
	public function get_fields_options() {
		global $msg;
	
		$fields_options="<option value='{{last_empr.nom}}'>".$msg['serialcirc_fiche_circu_last_empr_first_name']."</option>";
		$fields_options.="<option value='{{last_empr.prenom}}'>".$msg['serialcirc_fiche_circu_last_empr_last_name']."</option>";
		$fields_options.="<option value='{{last_empr.empr_libelle}}'>".$msg['serialcirc_fiche_circu_last_empr_lib']."</option>";
		$fields_options.="<option value='{{last_empr.mail}}'>".$msg['serialcirc_fiche_circu_last_empr_mail']."</option>";
		$fields_options.="<option value='{{last_empr.cb}}'>".$msg['serialcirc_fiche_circu_last_empr_cb']."</option>";
		$fields_options.="<option value='{{expl.cb}}'>".$msg['serialcirc_fiche_circu_bull_cb']."</option>";
		$fields_options.="<option value='{{expl.numero}}'>".$msg['serialcirc_fiche_circu_bull_num']."</option>";
		$fields_options.="<option value='{{expl.bulletine_date}}'>".$msg['serialcirc_fiche_circu_bull_date']."</option>";
		$fields_options.="<option value='{{expl.serial_title}}'>".$msg['serialcirc_fiche_circu_bull_serialname']."</option>";
		$fields_options.="<option value='{{expl.expl_location_name}}'>".$msg['serialcirc_fiche_circu_bull_location']."</option>";
		$fields_options.="<option value='{{expl.expl_cote}}'>".$msg['serialcirc_fiche_circu_bull_cote']."</option>";
		$fields_options.="<option value='{{expl.expl_owner}}'>".$msg['serialcirc_fiche_circu_bull_owner']."</option>";
		return $fields_options;
	}
	
	protected function get_form_name() {
		return "serialcirc_tpl_form";
	}
	
	protected function get_content_form() {
		global $serialcirc_tpl_content_form;
		global $action;
	
		$content_form=$serialcirc_tpl_content_form;
		
		if ($this->duplicate_from_id) $fields =new serialcirc_tpl_print_fields($this->duplicate_from_id);
		else $fields =new serialcirc_tpl_print_fields($this->id);
		switch ($action) {
			case "add_field" :
				$fields->add_field();
				break;
			case "del_field" :
				$fields->del_field();
				break;
			default :
				break;
		}
		$format_serialcirc = $fields->get_select_form("select_field",0,"serialcirc_tpl_print_add_button();");
		$content_form = str_replace("!!format_serialcirc!!", $format_serialcirc, $content_form);
		
		$fields_options="<select id='fields_options' name='fields_options'>";
		$fields_options.= $this->get_fields_options();
		$fields_options.="</select>";
		$content_form=str_replace('!!fields_options!!', $fields_options, $content_form);
		
		$content_form=str_replace('!!pied_page!!', $this->piedpage, $content_form);
		$content_form=str_replace('!!id_tpl!!', $this->id, $content_form);
		$content_form = str_replace("!!order_tpl!!", implode(",",array_keys($fields->circ_tpl)), $content_form);
		return $content_form;
	}
	
	public function get_form() {
		global $action;
		
		switch ($action) {
			case "add_field" :
				$this->set_properties_from_form();
				break;
			case "del_field" :
				$this->set_properties_from_form();
				break;
			default :
				break;
		}
		return parent::get_form();
	}
		
	public function set_properties_from_form() {
		global $piedpage;
	
		$this->piedpage = stripslashes($piedpage);
		parent::set_properties_from_form();
	}
	
	// ---------------------------------------------------------------
	//		save : mise à jour
	// ---------------------------------------------------------------
	public function save() {
		global $msg;
		global $include_path;
			
		if(!$this->name) return false;
	
		$query  = "SET  ";
		$query .= "serialcirctpl_name='".addslashes($this->name)."', ";
		$query .= "serialcirctpl_comment='".addslashes($this->comment)."', ";
		$query .= "serialcirctpl_piedpage='".addslashes($this->piedpage)."' ";
	
		if($this->id) {
			// update
			$query = "UPDATE serialcirc_tpl $query WHERE serialcirctpl_id=".$this->id." ";
			if(!pmb_mysql_query($query)) {
				require_once("$include_path/user_error.inc.php");
				warning($msg["template_modifier"], $msg["template_modifier_erreur"]);
				return false;
			} else {
				// on enregistre les champs
				$fields =new serialcirc_tpl_print_fields($this->id);
				$fields->save_form();
			}
		} else {
			// creation
			$query = "INSERT INTO serialcirc_tpl ".$query;
			if(pmb_mysql_query($query)) {
				$this->id=pmb_mysql_insert_id();
				// on enregistre les champs
				$fields =new serialcirc_tpl_print_fields($this->id);
				$fields->save_form();
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg["template_ajouter"], $msg["template_ajouter_erreur"]);
				return false;
			}
		}
		return true;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression 
	// ---------------------------------------------------------------
	public static function delete($id) {
		global $msg;
		
		$id = intval($id);
		if(!$id) {
		    pmb_error::get_instance(static::class)->add_message("", $msg[403]);
		    return false;
		}

		$total = 0;
		$total = pmb_mysql_result(pmb_mysql_query("select count(1) from serialcirc where serialcirc_tpl ='".$id."' "), 0, 0);
		if ($total==0) {
			// effacement dans la table
			$query = "DELETE FROM serialcirc_tpl WHERE serialcirctpl_id='".$id."' ";
			pmb_mysql_query($query);
		} else {
			pmb_error::get_instance(static::class)->add_message($msg["edit_tpl_serialcirc_delete"], $msg["edit_tpl_serialcirc_delete_forbidden"]);
			return false;
		}
		return true;
	}
	
	public static function get_base_url() {
		global $base_path;
		if(!isset(static::$base_url)) {
			static::$base_url = $base_path.'/edit.php?categ=tpl&sub=serialcirc';
		}
		return static::$base_url;
	}
	
	public static function gen_tpl_select($select_name="form_serialcirc_tpl", $selected_id=0, $onchange="") {		
		global $msg;
		
		$query = "SELECT serialcirctpl_id, concat(serialcirctpl_name,'. ',serialcirctpl_comment) as nom  FROM serialcirc_tpl ORDER BY serialcirctpl_name ";
		return gen_liste ($query, "serialcirctpl_id", "nom", $select_name, $onchange, $selected_id, 0, $msg["serialcirc_tpl_list_default"], 0,$msg["serialcirc_tpl_list_default"], 0) ;
	}

} // fin class 
