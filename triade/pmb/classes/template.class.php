<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: template.class.php,v 1.10.2.1 2024/10/01 08:24:24 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/template.tpl.php");

class template {
	
	protected $id;			// MySQL id in table 'bannette_tpl'
	
	protected $name;		// nom du template
	
	protected $comment;		// description du template
	
	protected $content; 	// Template
	
	public $duplicate_from_id;
	
	public $type;
	
	protected static $table_name = 'templates';
	protected static $field_name = 'id_template';
	
	protected static $base_url;
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected static function get_data_query($id) {
		$id = intval($id);
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
		global $template_content_form;
		global $charset;
	
		$content_form = $template_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_form($this->get_form_name());
		if(!$this->id){
			$interface_form->set_label($msg['template_ajouter']);
		}else{
			$interface_form->set_label($msg['template_modifier']);
		}
		$content_form = str_replace("!!name!!",		htmlentities($this->name ?? "",ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace("!!comment!!",	htmlentities($this->comment ?? "",ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!content_form!!', $this->get_content_form() ?? "", $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_duplicable(true)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($content_form)
		->set_table_name(static::$table_name)
		->set_field_focus('name');
		return $interface_form->get_display();
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
	public static function delete($id) {
		global $msg;
	
		$id = intval($id);
		if(!$id) {
		    pmb_error::get_instance(static::class)->add_message("", $msg[403]);
		    return false;
		}
	
		// effacement dans la table
		$query = "DELETE FROM ".static::$table_name." WHERE ".static::$field_name."='".$id."' ";
		pmb_mysql_query($query);
		return true;
	}
		
	public function get_id() {
		return $this->id;
	}
	
	public function set_id($id=0) {
		$this->id = intval($id);
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_comment() {
		return $this->comment;
	}

	public function get_content() {
		return $this->content;
	}
	
	public static function render($id, $data) {
		global $msg, $charset, $base_path;
		
		$query = static::get_data_query($id);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			$data = encoding_normalize::utf8_normalize($data);
			$template_content = encoding_normalize::utf8_normalize($temp->template_content);
			try{
				$template_path = $base_path.'/temp/'.LOCATION.'_'.static::$table_name.'_content_'.$id;
				if(!file_exists($template_path) || (md5($template_content) != md5_file($template_path))){
					file_put_contents($template_path, $template_content);
				}
				$H2o = H2o_collection::get_instance($template_path);
				$data_to_return = $H2o->render($data);
			}catch(Exception $e){
				$data_to_return = '<!-- '.$e->getMessage().' -->';
				$data_to_return .= '<div class="error_on_template" title="' .htmlspecialchars($e->getMessage(), ENT_QUOTES). '">';
				$data_to_return .= $msg["540"];
				$data_to_return .= '</div>';
			}
			if ($charset !="utf-8") {
				$data_to_return = encoding_normalize::utf8_decode($data_to_return);
			}
			return $data_to_return;
		}
	}
}