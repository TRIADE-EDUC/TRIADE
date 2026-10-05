<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: infopage.class.php,v 1.2.8.1 2024/05/30 10:01:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $msg;
global $form_title_infopage, $form_content_infopage, $form_valid_infopage;
global $form_restrict_infopage, $classementGen_infopages;

class infopage {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $title='';
	public $content='';
	public $valid=0;
	public $restrict=0;
	public $classement='';

	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}

	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
	
		$requete = 'SELECT * FROM infopages WHERE id_infopage='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->title = $data->title_infopage;
		$this->content = $data->content_infopage;
		$this->valid = $data->valid_infopage;
		$this->restrict = $data->restrict_infopage;
		$this->classement = $data->infopage_classement;
	}

	public function get_content_form() {
	    global $PMBuserid;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('form_title_infopage', 'infopage_title_infopage')
	    ->add_input_node('text', $this->title);
	    $interface_content_form->add_element('form_valid_infopage', 'infopage_valid_infopage')
	    ->add_input_node('boolean', $this->valid);
	    $interface_content_form->add_element('form_restrict_infopage', 'infopage_restrict_infopage')
	    ->add_input_node('boolean', $this->restrict);
	    $interface_content_form->add_element('form_content_infopage', 'infopages_content_infopage')
	    ->add_textarea_node($this->content, 120, 40);
	    
	    $classementGen = new classementGen('infopages', $this->id);
	    $html_node = "<select data-dojo-type='dijit/form/ComboBox' id='classementGen_".$classementGen->object_type."' name='classementGen_".$classementGen->object_type."'>
    		".$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle)."
    	</select>";
	    $interface_content_form->add_element('classementGen_'.$classementGen->object_type, 'infopages_classement_list')
	    ->add_html_node($html_node);
	    
	    return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;

		$interface_form = new interface_admin_form('infopagesform');
		if(!$this->id){
			$interface_form->set_label($msg['infopages_creer']);
		}else{
			$interface_form->set_label($msg['infopages_modifier']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->title." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('infopages')
		->set_field_focus('form_title_infopage')
		->set_duplicable(true);
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_title_infopage, $form_content_infopage, $form_valid_infopage;
		global $form_restrict_infopage, $classementGen_infopages;
		
		$this->title = stripslashes($form_title_infopage);
		$this->content = stripslashes($form_content_infopage);
		$this->valid = intval($form_valid_infopage);
		$this->restrict = intval($form_restrict_infopage);
		$this->classement = stripslashes($classementGen_infopages);
	}
	
	public function save() {
		$set_values = "SET title_infopage='".addslashes($this->title)."', 
			content_infopage='".addslashes($this->content)."', 
			valid_infopage='".$this->valid."', 
			restrict_infopage='".$this->restrict."', 
			infopage_classement='".addslashes($this->classement)."' " ;
		if($this->id) {
			$requete = "UPDATE infopages $set_values WHERE id_infopage='".$this->id."' ";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO infopages $set_values ";
			pmb_mysql_query($requete);
		}
	}

	public static function check_data_from_form() {
		global $form_title_infopage;
		
		if(empty($form_title_infopage)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id) {
			$requete = "DELETE from infopages WHERE id_infopage='$id' ";
			pmb_mysql_query($requete);
			return true;
		}
		return true;
	}
}


