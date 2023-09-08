<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: template.class.php,v 1.4 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/template.tpl.php");

class template {
	
	protected $id;			// MySQL id in table 'bannette_tpl'
	
	protected $name;		// nom du template
	
	protected $comment;		// description du template
	
	protected $content; 	// Template
	
	public $duplicate_from_id;
	
	protected static $table_name = 'templates';
	protected static $field_name = 'id_template';
	
	protected static $base_url;
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id = $id+0;
		$this->fetch_data();
	}
	
	protected static function get_data_query($id) {
		$id += 0;
		return "SELECT * FROM templates WHERE id_template='".$id."'";
	}
	
	protected function fetch_data() {
		if($this->id) {
			$query = static::get_data_query($this->id);
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$this->type	= $temp->template_type;
				$this->name	= $temp->template_name;
				$this->comment	= $temp->template_comment;
				$this->content = $temp->template_content;
			}
		}
	}
	
	protected function get_form_name() {
		return "template_form";
	}
	
	// ---------------------------------------------------------------
	//		get_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function get_form() {
		global $msg;
		global $template_form;
		global $charset;
	
		$form=$template_form;
		if($this->id) {
			$libelle = $msg["template_modifier"];
			$button_delete = "<input type='button' class='bouton' value='".$msg[63]."' onClick=\"confirm_delete();\">";
			$action_delete = static::get_base_url()."&action=delete&id=".$this->id;
			$button_duplicate = "<input type='button' class='bouton' value='".$msg["edit_tpl_duplicate_button"]."' onClick=\"document.location='".static::get_base_url()."&action=duplicate&id=".$this->id."';\" />";
		} else {
			$libelle = $msg["template_ajouter"];
			$button_delete = "";
			$button_duplicate = "";
			$action_delete= "";
		}
		$form = str_replace("!!libelle!!",	$libelle, $form);
		$form = str_replace("!!name!!",		htmlentities($this->name,ENT_QUOTES, $charset), $form);
		$form = str_replace("!!comment!!",	htmlentities($this->comment,ENT_QUOTES, $charset), $form);
		
		$form=str_replace('!!content_form!!', $this->get_content_form(), $form);
	
		$form = str_replace("!!action!!",	static::get_base_url()."&action=update&id=".$this->id, $form);
		$form = str_replace("!!duplicate!!", $button_duplicate, $form);
		$form = str_replace("!!delete!!",	$button_delete,	$form);
		$form = str_replace("!!action_delete!!",$action_delete,	$form);
		$form = str_replace("!!id!!",		$this->id, $form);
		$form = str_replace("!!form_name!!", $this->get_form_name(), $form);
		return $form;
	}
	
	public function set_properties_from_form() {
		global $name, $comment,$content;
	
		$this->name = clean_string(stripslashes($name));
		$this->comment = stripslashes($comment);
		$this->content = stripslashes($content);
	}
	
	public function save() {
		global $msg;
		global $include_path;
			
		if(!$this->name) return false;
	
		$query  = "SET  ";
		$query .= "template_name='".addslashes($this->name)."', ";
		$query .= "template_comment='".addslashes($this->comment)."', ";
		$query .= "template_content='".addslashes($this->content)."' ";
	
		if($this->id) {
			// update
			$query = "UPDATE ".static::$table_name." $query WHERE ".static::$field_name."=".$this->id." ";
			if(!pmb_mysql_query($query)) {
				require_once("$include_path/user_error.inc.php");
				warning($msg["template_modifier"], $msg["template_modifier_erreur"]);
				return false;
			}
		} else {
			// creation
			$query = "INSERT INTO ".static::$table_name." ".$query;
			if(pmb_mysql_query($query)) {
				$this->id=pmb_mysql_insert_id();
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
	public function delete() {
		global $msg;
	
		if(!$this->id)	return $msg[403];
	
		// effacement dans la table
		$query = "DELETE FROM ".static::$table_name." WHERE ".static::$field_name."='".$this->id."' ";
		pmb_mysql_query($query);
		return false;
	}
		
	public static function render($id, $data) {
		global $charset;
		
		$query = static::get_data_query($id);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			$data = encoding_normalize::utf8_normalize($data);
			$data_to_return = H2o::parseString(encoding_normalize::utf8_normalize($temp->template_content))->render($data);
			if ($charset !="utf-8") {
				$data_to_return = utf8_decode($data_to_return);
			}
			return $data_to_return;
		}
	}
	
	public static function proceed($id) {
		global $action;
		
		$id = intval($id);
		$class_name = static::class;
		$template_instance = static::get_template_instance($id);
		
		switch ($action) {
			case "edit":
				print $template_instance->get_form();
				break;
			case "update":
				$template_instance->set_properties_from_form();
				$template_instance->save();
				print $class_name::get_display_list();
				break;
			case "delete":
				$template_instance->delete();
				print $class_name::get_display_list();
				break;
			case 'duplicate':
				$template_instance->id = 0;
				$template_instance->duplicate_from_id = $id;
				print $template_instance->get_form();
				break;
			default:
				print $class_name::get_display_list();
				break;
		}
	}
	
	public static function get_template_instance($id) {
		return new template($id);
	}
	
	public static function get_list_query() {
		return "SELECT id_template FROM templates ORDER BY template_name ";
	}
	
	// ---------------------------------------------------------------
	//		get_list : affichage de la liste des éléments
	// ---------------------------------------------------------------
	public static function get_display_list() {
		global $charset,$msg;
		global $template_liste, $template_liste_ligne;
	
		$tableau = "";
		$query = static::get_list_query();
		$result = @pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$pair="odd";
			while($row = pmb_mysql_fetch_object($result)){
				$template = static::get_template_instance($row->id_template);
					
				if($pair=="even") $pair ="odd";	else $pair ="even";
				// contruction de la ligne
				$ligne=$template_liste_ligne;
	
				$ligne = str_replace("!!name!!",	htmlentities($template->name,ENT_QUOTES, $charset), $ligne);
				$ligne = str_replace("!!comment!!",	htmlentities($template->comment,ENT_QUOTES, $charset), $ligne);
				$ligne = str_replace("!!pair!!",	$pair, $ligne);
				$ligne = str_replace("!!link_edit!!",	static::get_base_url()."&action=edit&id=".$template->id, $ligne);
				$ligne = str_replace("!!id!!",		$template->id, $ligne);
				$tableau.=$ligne;
			}
		}
		$liste = str_replace("!!template_liste!!",$tableau, $template_liste);
		$liste = str_replace("!!link_ajouter!!",	static::get_base_url()."&action=edit", $liste);
		return $liste;
	}
}