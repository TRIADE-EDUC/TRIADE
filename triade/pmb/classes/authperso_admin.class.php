<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_admin.class.php,v 1.21.4.1 2025/01/31 09:18:09 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/authperso_admin.tpl.php");
require_once($include_path."/templates/parametres_perso.tpl.php");
require_once($class_path."/custom_parametres_perso.class.php");
require_once("$class_path/interface/admin/interface_admin_authorities_authperso_form.class.php");

class authperso_admin {
	public $id=0;
	public $info=array();


	public function __construct($id=0) {
		$this->id=intval($id);
		$this->fetch_data();
	}

	public function fetch_data() {
		$this->info=array();
		$this->info['fields']=array();
		if(!$this->id) {
			$this->info['name']= '';
			$this->info['onglet_num']= 0;
			$this->info['isbd_script']= '';
			$this->info['view_script']= '';
			$this->info['opac_search']= 0;
			$this->info['opac_multi_search']= 0;
			$this->info['gestion_search']= 0;
			$this->info['gestion_multi_search']= 0;
			$this->info['oeuvre_event']= 0;
			$this->info['comment']= '';
			$this->info['responsability_authperso']= 0;
			return;
		}

		$req="select * from authperso where id_authperso=". $this->id;
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			$this->info['id']= $r->id_authperso;
			$this->info['name']= $r->authperso_name;
			$this->info['onglet_num']= $r->authperso_notice_onglet_num;
			$this->info['isbd_script']= $r->authperso_isbd_script;
			$this->info['view_script']= $r->authperso_view_script;
			$this->info['opac_search']= $r->authperso_opac_search;
			$this->info['opac_multi_search']= $r->authperso_opac_multi_search;
			$this->info['gestion_search']= $r->authperso_gestion_search;
			$this->info['gestion_multi_search']= $r->authperso_gestion_multi_search;
			$this->info['oeuvre_event']= $r->authperso_oeuvre_event;
			$this->info['comment']= $r->authperso_comment;
			$this->info['responsability_authperso']= $r->authperso_responsability_authperso;
			$this->info['onglet_name']="";
			$req="SELECT * FROM notice_onglet where id_onglet=".$r->authperso_notice_onglet_num;
			$resultat=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($resultat)) {
				$r_onglet=pmb_mysql_fetch_object($resultat);
				$this->info['onglet_name']= $r_onglet->onglet_name;
			}
		} else {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
		}
	}

	protected function get_js_form() {
		global $authperso_js_form_tpl;

		return $authperso_js_form_tpl;
	}

	public function get_content_form() {
	    global $msg;

	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('name', 'admin_authperso_form_name')
	    ->add_input_node('text', $this->info['name'])
	    ->set_attributes(array('data-translation-fieldname' => 'authperso_name'));

	    $notice_onglet_list=gen_liste ("SELECT * FROM notice_onglet",
	        "id_onglet", "onglet_name", "notice_onglet", "", $this->info['onglet_num'], 0, $msg["admin_authperso_notice_onglet_no"],0,$msg["admin_authperso_notice_onglet_sel"]);
	    $interface_content_form->add_element('notice_onglet', 'admin_authperso_notice_onglet')
	    ->add_html_node($notice_onglet_list." <a href='./admin.php?categ=notices&sub=onglet' target='_blank'>".$msg['admin_authperso_notice_onglet_see']."</a>");

	    $fields_options="<select id='fields_options' name='fields_options'>";
	    $fields_options.=$this->get_fields_options();
	    $fields_options.="</select>";
	    $button_isbd_script = "<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options'), document.getElementById('isbd_script')); return false; \" value=' ".$msg['admin_authperso_insert_field']." ' />";
	    $element = $interface_content_form->add_element('isbd_script', 'admin_authperso_form_isbd_script');
	    $element->add_html_node($fields_options.$button_isbd_script);
		$element->add_textarea_node($this->info['isbd_script'], 50, 4);

	    $fields_options_view="<select id='fields_options_view' name='fields'>";
	    $fields_options_view.=$this->get_fields_options();
	    $fields_options_view.="</select>";
	    $button_view_script = "<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options_view'), document.getElementById('view_script')); return false; \" value=' ".$msg['admin_authperso_insert_field']." ' />";
	    $element = $interface_content_form->add_element('view_script', 'admin_authperso_form_view_script');
	    $element->add_html_node($fields_options_view.$button_view_script);
		$element->add_textarea_node($this->info['view_script'], 50, 4);

	    $interface_content_form->add_element('responsability_authperso')
	    ->add_input_node('boolean', $this->info['responsability_authperso'])
	    ->set_label_code('admin_responsability_authperso_yes');

	    $search_simple_checked=array();
	    $search_simple_checked[$this->info['opac_search']+0]= " checked='checked' ";
	    $search_tpl="
			<input type='radio' ".(isset($search_simple_checked[0]) ? $search_simple_checked[0] : '')." name='search_simple' value='0' >".$msg["admin_authperso_opac_search_no"]."
			<input type='radio' ".(isset($search_simple_checked[1]) ? $search_simple_checked[1] : '')." name='search_simple' value='1' >".$msg["admin_authperso_opac_search_yes"]."
			<input type='radio' ".(isset($search_simple_checked[2]) ? $search_simple_checked[2] : '')." name='search_simple' value='2' >".$msg["admin_authperso_opac_search_yes_active"]."
		";
	    $element = $interface_content_form->add_element('search_multi', 'admin_authperso_opac_search');
	    $element->add_html_node($search_tpl);
	    $element->add_input_node('boolean', $this->info['opac_multi_search'])
	    ->set_label_code('admin_authperso_opac_search_multi_critere');

	    $search_simple_checked_gestion=array();
	    $search_simple_checked_gestion[$this->info['gestion_search']+0]= " checked='checked' ";
	    $search_tpl_gestion="
			<input type='radio' ".(isset($search_simple_checked_gestion[0]) ? $search_simple_checked_gestion[0] : '')." name='gestion_search_simple' value='0' >".$msg["admin_authperso_gestion_search_no"]."
			<input type='radio' ".(isset($search_simple_checked_gestion[1]) ? $search_simple_checked_gestion[1] : '')." name='gestion_search_simple' value='1' >".$msg["admin_authperso_gestion_search_yes"]."
			<input type='radio' ".(isset($search_simple_checked_gestion[2]) ? $search_simple_checked_gestion[2] : '')." name='gestion_search_simple' value='2' >".$msg["admin_authperso_gestion_search_yes_active"]."
		";
	    $element = $interface_content_form->add_element('gestion_search_multi', 'admin_authperso_gestion_search');
	    $element->add_html_node($search_tpl_gestion);
	    $element->add_input_node('boolean', $this->info['gestion_multi_search'])
	    ->set_label_code('admin_authperso_gestion_search_multi');

	    $interface_content_form->add_element('oeuvre_event', 'admin_authperso_form_oeuvre_event')
	    ->add_input_node('boolean', $this->info['oeuvre_event'])
	    ->set_label_code('admin_authperso_form_oeuvre_event_yes');

	    $interface_content_form->add_element('comment', 'admin_authperso_form_comment')
	    ->add_textarea_node($this->info['comment'], 50, 4);

	    return $interface_content_form->get_display();
	}

	public function get_form() {
		global $msg;

		$interface_form = new interface_admin_authorities_authperso_form('authperso');
		if($this->id){
			$interface_form->set_label($msg['admin_authperso_form_edit']);
		} else {
			$interface_form->set_label($msg['admin_authperso_form_add']);
		}

		if($this->id){
			//bouton supprimer
			$req="select * from authperso_authorities where authperso_authority_authperso_num=". $this->id;
			$res = pmb_mysql_query($req);
			if((pmb_mysql_num_rows($res))) {
				$interface_form->set_no_deletable(true);
			}
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->info['name']." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('authperso')
		->set_field_focus('name');
		$display = $this->get_js_form();
		$display .= $interface_form->get_display();
		return $display;
	}

	public function set_properties_from_form() {
		global $name, $notice_onglet, $isbd_script, $view_script, $comment;
		global $search_simple, $search_multi, $gestion_search_simple, $gestion_search_multi;
		global $oeuvre_event, $responsability_authperso;

		$this->info['name']= stripslashes($name);
		$this->info['onglet_num']= intval($notice_onglet);
		$this->info['isbd_script']= stripslashes($isbd_script);
		$this->info['view_script']= stripslashes($view_script);
		$this->info['opac_search']= intval($search_simple);
		$this->info['opac_multi_search']= intval($search_multi);
		$this->info['gestion_search']= intval($gestion_search_simple);
		$this->info['gestion_multi_search']= intval($gestion_search_multi);
		$this->info['oeuvre_event']= intval($oeuvre_event);
		$this->info['comment']= stripslashes($comment);
		$this->info['responsability_authperso']= intval($responsability_authperso);
		$this->info['onglet_name']="";
	}

	public function save() {
		global $base_path;

		$fields="
			authperso_name='".addslashes($this->info['name'])."',
			authperso_notice_onglet_num='".addslashes($this->info['onglet_num'])."',
			authperso_isbd_script='".addslashes($this->info['isbd_script'])."' ,
			authperso_view_script='".addslashes($this->info['view_script'])."' ,
			authperso_opac_search='".$this->info['opac_search']."',
			authperso_opac_multi_search='".$this->info['opac_multi_search']."',
			authperso_gestion_search='".$this->info['gestion_search']."',
			authperso_gestion_multi_search='".$this->info['gestion_multi_search']."',
			authperso_oeuvre_event='".$this->info['oeuvre_event']."',
			authperso_comment='".addslashes($this->info['comment'])."',
			authperso_responsability_authperso='".$this->info['responsability_authperso']."'
		";
		if(!$this->id){ // Ajout
			$req="INSERT INTO authperso SET $fields ";
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req="UPDATE authperso SET $fields where id_authperso=".$this->id;
			pmb_mysql_query($req);
			$isbd_template_path = $base_path.'/temp/'.LOCATION.'_authperso_isbd_'.$this->id;
			if(file_exists($isbd_template_path)){
				unlink($isbd_template_path);
			}
			$view_template_path = $base_path.'/temp/'.LOCATION.'_authperso_view_'.$this->id;
			if(file_exists($view_template_path)){
				unlink($view_template_path);
			}
		}
		$translation = new translation($this->id, "authperso");
		$translation->update("authperso_name", "name");
		$this->fetch_data();
	}

	public static function delete($id) {
		global $option_navigation,$option_visibilite;

		$id = intval($id);
		if($id) {
			$p_perso=new custom_parametres_perso("authperso","authperso",$id,"./admin.php?categ=authorities&sub=authperso&auth_action=edition&id_authperso=".$id,$option_navigation,$option_visibilite);
			if(count($p_perso->t_fields) == 0) {
				$p_perso->delete_all();

				$query = "delete from authperso_authorities where  authperso_authority_authperso_num = '".$id."' ";
				pmb_mysql_query($query);

				translation::delete($id, "authperso");

				$req="DELETE from authperso WHERE id_authperso=".$id;
				pmb_mysql_query($req);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('', 'authperso_used_custom_fields');
				return false;
			}
		}
		return true;
	}

	public function fields_edition() {
		global $msg;

		$option_visibilite = array();
		$option_visibilite["multiple"] = "block";
		$option_visibilite["obligatoire"] = "block";
		$option_visibilite["search"] = "block";
		$option_visibilite["export"] = "none";
		$option_visibilite["filters"]="none";
		$option_visibilite["exclusion"] = "none";
		$option_visibilite["opac_sort"] = "block";

		$option_navigation = array();
		$option_navigation['msg_title'] = $msg["admin_menu_docs_perso_authperso"]." : ".$this->info['name'];
		$option_navigation['url_return_list'] = "./admin.php?categ=authorities&sub=authperso&auth_action=";
		$option_navigation['msg_return_list'] = $msg["admin_authperso_return_list"];

		$option_navigation['url_update_global_index'] = "./admin.php?categ=authorities&sub=authperso&auth_action=update_global_index&id_authperso=".$this->id;
		$option_navigation['msg_update_global_index'] = $msg["admin_authperso_update_global_index"];

		$p_perso = new custom_parametres_perso("authperso", "authperso", $this->id, "./admin.php?categ=authorities&sub=authperso&auth_action=edition&id_authperso=".$this->id, $option_navigation, $option_visibilite);

		$p_perso->proceed();
	}

	public function get_fields_options(){
		global $msg;

		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);
		$options = $p_perso->get_selector_options_1();
		$options .= "<option value='{% for index_concept in index_concepts %}
		{{index_concept.label}}
	 {% endfor %}'>index_concepts</option>";
		if($this->info["responsability_authperso"]) {
			$options .= "<option value='{% for responsability in responsability_authperso %}
			{{responsability.isbd}}, {{responsability.fonction_name}} {%if responsability.qualification %}({{responsability.qualification}}){% endif %}
		 {% endfor %}'>".$msg["aut_responsability_form_responsability_authperso"]."</option>";
		}
		return $options;
	}
} //authperso class end