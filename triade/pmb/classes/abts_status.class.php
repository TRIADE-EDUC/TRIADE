<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_status.class.php,v 1.6 2024/03/22 15:31:03 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once ($include_path . '/templates/abts_abonnements.tpl.php');

class abts_status{
	/* ---------------------------------------------------------------
	 propriétés de la classe
	 --------------------------------------------------------------- */
	
	public $id=0;
	public $gestion_libelle='';
	public $opac_libelle='';
	public $class_html='statutnot1';
	public $bulletinage_active=0;
	
	protected static $status = array();
	private static $status_fetched = false;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
	 getData() : récupération des propriétés
	 --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
		
		$query = 'SELECT * FROM abts_status WHERE abts_status_id='.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		
		$data = pmb_mysql_fetch_object($result);
		$this->gestion_libelle = $data->abts_status_gestion_libelle;
		$this->opac_libelle = $data->abts_status_opac_libelle;
		$this->class_html = $data->abts_status_class_html;
		$this->bulletinage_active = $data->abts_status_bulletinage_active;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_gestion_libelle', 'docnum_statut_libelle')
		->add_input_node('text', $this->gestion_libelle);
		$interface_content_form->add_inherited_element('display_colors', 'form_class_html', 'docnum_statut_class_html')
		->init_nodes([$this->class_html]);
		$interface_content_form->add_element('form_bulletinage_active', 'docnum_statut_bulletinage_active', 'flat')
		->add_input_node('boolean', $this->bulletinage_active);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('statusform');
		if($this->id){
			$interface_form->set_label($msg['118']);
		}else{
			$interface_form->set_label($msg['115']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->gestion_libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('abts_status')
		->set_field_focus('form_gestion_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_gestion_libelle,$form_class_html, $form_bulletinage_active;
		
		$this->gestion_libelle = stripslashes($form_gestion_libelle);
		$this->opac_libelle = stripslashes($form_gestion_libelle);
		$this->class_html = stripslashes($form_class_html);
		$this->bulletinage_active = intval($form_bulletinage_active);
	}
	
	public function save() {
		if($this->gestion_libelle){
			if($this->id){
				$query = " update abts_status set ";
				$where = "where abts_status_id = ".$this->id;
			}else{
				$query = " insert into abts_status set ";
				$where = "";
			}
			$query.="
				abts_status_gestion_libelle = '".addslashes($this->gestion_libelle)."',
				abts_status_opac_libelle = '".addslashes($this->opac_libelle)."',
				abts_status_class_html = '".addslashes($this->class_html)."',
				abts_status_bulletinage_active = '".addslashes($this->bulletinage_active)."'
			";
			$result = pmb_mysql_query($query.$where);
			if(!$result){
				return false;
			}
		}
		return true;
	}
	
	public static function check_data_from_form() {
		global $form_gestion_libelle;
		
		if(empty($form_gestion_libelle)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		global $msg;
		
		$id=intval($id);
		if($id==1) return true;
		
		$used = static::check_used($id);
		if(!count($used)){
			$query = "delete from abts_status where abts_status_id = ".$id;
			pmb_mysql_query($query);
			return true;
		} else {
			$msg_suppr_err= $msg['abts_status_used'].'<br/>';
			foreach($used as $auth){
				$msg_suppr_err.=$auth['link'].'<br/>';
			}
			pmb_error::get_instance(static::class)->add_message('abts_status_used', $msg_suppr_err);
			return false;
		}
	}
	
	public static function get_list(){
		if(!static::$status_fetched){
			static::$status = array();
			$query = "select abts_status_id, abts_status_gestion_libelle,abts_status_class_html,abts_status_bulletinage_active					
					from abts_status order by abts_status_gestion_libelle";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					static::$status[$row->abts_status_id] = array(
						'label' => $row->abts_status_gestion_libelle,
						'class_html' => $row->abts_status_class_html,
						'bulletinage_active' => $row->abts_status_bulletinage_active,					
					);
				}
			}
			static::$status_fetched = true;
		}
	}

	public static function get_ids_bulletinage_active(){
		static::get_list();
		
		$ids = array();
		foreach(static::$status as $id_statut => $statut){
			if($statut['bulletinage_active']) {
				$ids[] = $id_statut;
			}
		}
		return $ids;
	}
	
	/**
	 * Fonction qui controle si le status est utilisé
	 * @param integer $id du statut
	 * @return array: ids des abonnemets
	 */
	public static function check_used($id){
		$id = intval($id);
		$used = array();
		$query="select abt_id from abts_abts where abt_status=".$id;
		$res = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($res)) {
			while($r = pmb_mysql_fetch_object($res)) {
				$used[] = $r->abt_id;
			}			
		}
		return $used;
	}
	
	
	/**
	 * Fonction permettant de générer le selecteur des statuts
	 * @param integer $id du statut sélectionné  
	 * @param boolean $selector_search Sélécteur affiché dans la page de recherche
	 * @return string
	 */
	public static function get_form_for($id, $search=false){
	    global $msg;
	    
	    $id=intval($id);
	    static::get_list();
	    
        $on_change='';
        if($search){
        	$on_change='onchange="if(this.form) this.form.submit();"';        
        }
        $selector = '<select name="abts_status" '.$on_change.' >';
        if($search){
            $selector.='<option value="0">'.$msg['abts_status_selector_all'].'</option>';
        }
        foreach(static::$status as $id_statut => $statut){
            $selector.='<option '.(($id_statut == $id)?'selected="selected"':'').' value="'.$id_statut.'">'.$statut['label'].'</option>';
        }
        $selector.= '</select>';
        return $selector;
	}
	
	/**
	 * Fonction qui construit l'affichage du statut
	 * @param integer $id du statut
	 * @return string
	 */
	public static function get_display($id){
		global $charset;
		 
		$id=intval($id);
		static::get_list();
		$statut = static::$status[$id];
		$display = "<small><span class='".$statut['class_html']."' style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></a></span></small>";
		$display .= "<div id='zoom_statut".$id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($statut['label'],ENT_QUOTES, $charset))."</b></div>";
		return $display;
	}
}