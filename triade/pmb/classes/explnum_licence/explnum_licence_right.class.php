<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_right.class.php,v 1.5 2018-04-20 15:26:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/explnum_licence/explnum_licence_right.tpl.php');

/**
 * Classe de gestion des profils de régimes de licence
 * @author apetithomme, vtouchard
 *
 */
class explnum_licence_right {
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;
	
	/**
	 * Libellé du profil de régime de licence
	 * @var string
	 */
	protected $label;

	/**
	 * Type (autorisation / interdiction) 
	 * @var integer
	 */
	protected $type;
	
	/**
	 * Identifiant du régime de licence
	 * @var int $explnum_licence_num
	 */
	protected $explnum_licence_num;
	
	/**
	 * URL du logo
	 * @var string
	 */
	protected $logo_url;
	
	/**
	 * Phrase d'explication
	 * @var string
	 */
	protected $explanation;
	
	public function __construct($id = 0) {
		$this->id = $id*1;
	}
	
	public function get_form() {
		global $admin_explnum_licence_right_form, $msg, $charset;
		
		$form = $admin_explnum_licence_right_form;
		$form = str_replace('!!id!!', $this->id*1, $form);
		$form = str_replace('!!explnum_licence_id!!', $this->explnum_licence_num, $form);
		if(!$this->id){
			$form = str_replace('!!form_title!!', $msg['explnum_licence_right_new'], $form);
			$form = str_replace('!!explnum_licence_right_type_0!!', '', $form);
			$form = str_replace('!!explnum_licence_right_type_1!!', 'checked="checked"', $form);
			$form = str_replace('!!bouton_supprimer!!', '', $form);
		}else{
			$form = str_replace('!!form_title!!', $msg['explnum_licence_right_edit'], $form);
			$form = str_replace('!!explnum_licence_right_type!!', htmlentities($this->type, ENT_QUOTES, $charset), $form);
			$form = str_replace('!!explnum_licence_right_type_0!!', ($this->type ? '' : 'checked="checked"'), $form);
			$form = str_replace('!!explnum_licence_right_type_1!!', ($this->type ? 'checked="checked"' : ''), $form);
			$form = str_replace('!!bouton_supprimer!!', '<input type="button" class="bouton" value="'.$msg['63'].'" onclick="if (confirm(\''.addslashes($msg['explnum_licence_right_confirm_delete']).'\')) {document.location=\'./admin.php?categ=docnum&sub=licence&action=settings&id='.$this->explnum_licence_num.'&what=rights&rightaction=delete&rightid='.$this->id.'\'}" />', $form);
		}
		$form = str_replace('!!explnum_licence_right_label!!', $this->get_label(), $form);
		$form = str_replace('!!explnum_licence_right_logo_url!!', $this->get_logo_url(), $form);
		$form = str_replace('!!explnum_licence_right_explanation!!', $this->get_explanation(), $form);
		
		$translation = new translation($this->id, 'explnum_licence_rights');
		$form .= $translation->connect('explnumlicencerightform');
		
		return $form;
	}
	
	public function get_values_from_form(){
		global $explnum_licence_right_label, $explnum_licence_right_logo_url;
		global $explnum_licence_right_explanation, $explnum_licence_right_type;
		
		$this->label = stripslashes($explnum_licence_right_label);
		$this->logo_url = stripslashes($explnum_licence_right_logo_url);
		$this->explanation = stripslashes($explnum_licence_right_explanation);
		$this->type = stripslashes($explnum_licence_right_type);
	}
	
	public function save(){
		$query = '';
		$clause = '';
		if($this->id){
			$query.= 'update ';
			$clause = ' where id_explnum_licence_right = '.$this->id;
		}else{
			$query.= 'insert into '; 
		}
		
		$query.= 'explnum_licence_rights set
				explnum_licence_right_explnum_licence_num = "'.addslashes($this->explnum_licence_num).'",
				explnum_licence_right_label = "'.addslashes($this->label).'",
				explnum_licence_right_logo_url = "'.addslashes($this->logo_url).'",
				explnum_licence_right_explanation = "'.addslashes($this->explanation).'",
				explnum_licence_right_type = "'.addslashes($this->type).'"';
		$query.= $clause;
		
		pmb_mysql_query($query);
		if(!$this->id) {
			$this->id = pmb_mysql_insert_id();
		}
		$translation = new translation($this->id, 'explnum_licence_rights');
		$translation->update_small_text('explnum_licence_right_label');
		$translation->update_small_text('explnum_licence_right_logo_url');
		$translation->update_text('explnum_licence_right_explanation');
	}
	
	public function fetch_data() {
		if (!$this->id) {
			return false;
		}
		$query = 'select explnum_licence_right_explnum_licence_num, explnum_licence_right_label, explnum_licence_right_logo_url, explnum_licence_right_explanation, explnum_licence_right_type 
				from explnum_licence_rights where id_explnum_licence_right = '.$this->id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_assoc($result);
		if (count($row)) {
			$this->explnum_licence_num = $row['explnum_licence_right_explnum_licence_num'];
			$this->label = $row['explnum_licence_right_label'];
			$this->logo_url = $row['explnum_licence_right_logo_url'];
			$this->explanation = $row['explnum_licence_right_explanation'];
			$this->type = $row['explnum_licence_right_type'];
		}
	}
	
	public function delete() {
		if (!$this->id) {
			return false;
		}
		pmb_mysql_query('delete from explnum_licence_profile_rights where explnum_licence_right_num = '.$this->id);
		
		pmb_mysql_query('delete from explnum_licence_rights where id_explnum_licence_right = '.$this->id);
		translation::delete($this->id, 'explnum_licence_rights');
	}
	
	public function set_explnum_licence_num($explnum_licence_num) {
		$this->explnum_licence_num = $explnum_licence_num*1;
		return $this;
	}
	
	public function get_label(){
		if(!isset($this->label)){
			$this->fetch_data();
		}
		return $this->label;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function get_logo_url() {
		if (!isset($this->logo_url)) {
			$this->fetch_data();
		}
		return $this->logo_url;
	}
	
	public function get_explanation() {
		if (!isset($this->explanation)) {
			$this->fetch_data();
		}
		return $this->explanation;
	}
	
	public function get_type() {
		if (!isset($this->type)) {
			$this->fetch_data();
		}
		return $this->type;
	}
}