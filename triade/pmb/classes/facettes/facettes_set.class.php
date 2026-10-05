<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_set.class.php,v 1.5 2024/03/21 11:06:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class facettes_set {
	protected $id;
	protected $type = 'notices';
	protected $name = '';
	protected $num_user = 0;
	protected $users_groups = [];
	protected $ranking = 0;
	
	
	public function __construct($id=0){
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
	    global $PMBuserid;
	    
	    $this->num_user = $PMBuserid;
		if($this->id) {
			$query = "SELECT * FROM facettes_sets WHERE id_set=".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
    			$row = pmb_mysql_fetch_object($result);
    			$this->type = $row->type;
    			$this->name = $row->name;
    			$this->num_user = $row->num_user;
    			$this->users_groups = encoding_normalize::json_decode($row->users_groups, true);
    			$this->ranking = facettes_sets_users::get_ranking($this->id);
			} else {
			    $this->id = 0;
			}
		}
	}
	
	public function get_content_form() {
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('name', 'facettes_set_name')
	    ->add_input_node('text', $this->name)
	    ->set_attributes(array('data-translation-fieldname' => 'name'));
	    
	    $interface_content_form->add_element('users_groups', 'facettes_set_users_groups')
	    ->add_query_node('select', 'SELECT grp_id, grp_name FROM users_groups ORDER BY grp_name', $this->users_groups, true);
	    
	    if($this->id) {
    	    $is_external = false;
    	    $facettes_model = new facette_search_opac($this->type, $is_external);
    	    list_configuration_gestion_facettes_ui::set_facettes_model($facettes_model);
    	    list_configuration_gestion_facettes_ui::set_num_facettes_set($this->id);
    	    $interface_content_form->add_element('facettes_list', 'facettes_set_facets')
    	    ->add_html_node(list_configuration_gestion_facettes_ui::get_instance(array('num_facettes_set' => $this->id))->get_display_list());
	    }
	    return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_form('facettes_set_form');
		$interface_form->set_url_base($interface_form->get_url_base().'&type='.$this->type);
		if(!$this->id){
		    $interface_form->set_label($msg['facettes_set_add']);
		}else{
		    $interface_form->set_label($msg['facettes_set_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_duplicable(true)
		->set_confirm_delete_msg(sprintf($msg['facettes_set_delete_confirm']))
		->set_content_form($this->get_content_form())
		->set_table_name('facettes_sets')
		->set_field_focus('name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $name, $users_groups;
		
		$this->name = stripslashes($name);
		$this->users_groups = stripslashes_array($users_groups);
	}
	
	public function save() {
		if($this->id) {
			$query = "UPDATE facettes_sets SET ";
			$clause = " WHERE id_set=".$this->id;
		} else {
			$query = "INSERT INTO facettes_sets SET ";
			$clause = "";
			$this->ranking = pmb_mysql_result(pmb_mysql_query("select if(ISNULL(ranking), 1, max(ranking)+1) from facettes_sets where type LIKE '".addslashes($this->type)."%'"),0,0);
		}
		$query .= "
			type='".addslashes($this->type)."',
			name='".addslashes($this->name)."',
            num_user='".$this->num_user."',
            users_groups='".encoding_normalize::json_encode($this->users_groups)."'
			".$clause;
		pmb_mysql_query($query);
		if(!$this->id) {
			$this->id = pmb_mysql_insert_id();
		}
		$translation = new translation($this->id, 'facettes_sets');
		$translation->update("name");
	}
	
	public static function delete($id=0) {
	    $id = intval($id);
		if($id) {
		    facettes_set_user::delete($id);
		    translation::delete($id, 'facettes_sets');
		    $query = "DELETE FROM facettes WHERE num_facettes_set=".$id;
		    pmb_mysql_query($query);
		    $query = "DELETE FROM facettes_external WHERE num_facettes_set=".$id;
		    pmb_mysql_query($query);
			$query = "DELETE FROM facettes_sets WHERE id_set=".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function get_type(){
	    return $this->type;
	}
	
	public function get_name(){
	    return $this->name;
	}
	
	public function get_num_user(){
	    return $this->num_user;
	}
	
	public function get_users_groups(){
	    return $this->users_groups;
	}
	
	public function set_id($id) {
	    $this->id = intval($id);
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
	
	public function get_translated_name() {
	    return translation::get_translated_text($this->id, 'facettes_sets', 'name', $this->name);
	}
}

