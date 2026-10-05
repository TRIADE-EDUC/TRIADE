<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tva_achats.class.php,v 1.15 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class tva_achats{
	
	public $id_tva = 0;					//Identifiant de tva_achats 
	public $libelle = '';					//Libelle sur la tva
	public $taux_tva = '0.00';				//taux de tva en %					
	public $num_cp_compta = '';
	 
	//Constructeur.	 
	public function __construct($id_tva= 0) {
		$this->id_tva = intval($id_tva);
		if ($this->id_tva) {
			$this->load();	
		}
	}
		
	// charge le taux de tva à partir de la base.
	public function load(){
		$q = "select * from tva_achats where id_tva = '".$this->id_tva."' ";
		$r = pmb_mysql_query($q) ;
		if(!pmb_mysql_num_rows($r)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->taux_tva = $obj->taux_tva;
		$this->num_cp_compta = $obj->num_cp_compta;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('libelle', '103')
		->add_input_node('text', $this->libelle);
		$interface_content_form->add_element('taux_tva', 'acquisition_tva_taux')
		->add_input_node('float', $this->taux_tva)
		->set_label(' %');
		$interface_content_form->add_element('cp_compta', 'acquisition_num_cp_compta')
		->add_input_node('integer', $this->num_cp_compta)
		->set_class('saisie-20em');
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
			global $msg;
			
			$interface_form = new interface_admin_form('tvaform');
			if(!$this->id_tva){
				$interface_form->set_label($msg['acquisition_ajout_tva']);
			}else{
				$interface_form->set_label($msg['acquisition_modif_tva']);
			}
			$interface_form->set_object_id($this->id_tva)
			->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
			->set_content_form($this->get_content_form())
			->set_table_name('tva_achats')
			->set_field_focus('libelle');
			return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle, $taux_tva, $cp_compta;
		
		$this->libelle = stripslashes($libelle);
		$this->taux_tva = stripslashes($taux_tva);
		$this->num_cp_compta = stripslashes($cp_compta);
	}
	
	public function get_query_if_exists() {
		$query = "select count(1) from tva_achats where libelle = '".addslashes($this->libelle)."' ";
		if ($this->id_tva) $query .= "and id_tva != '".$this->id_tva."' ";
		return $query;
	}
	
	// enregistre le taux de tva en base.
	public function save(){
		if(!$this->libelle) die("Erreur de création tva_achats");
		if($this->id_tva) {
			$q = "update tva_achats set taux_tva ='".addslashes($this->taux_tva)."', libelle = '".addslashes($this->libelle)."', num_cp_compta = '".addslashes($this->num_cp_compta)."' ";
			$q.= "where id_tva = '".$this->id_tva."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into tva_achats set libelle = '".addslashes($this->libelle)."', taux_tva = '".addslashes($this->taux_tva)."', num_cp_compta = '".addslashes($this->num_cp_compta)."' ";
			pmb_mysql_query($q);
			$this->id_tva = pmb_mysql_insert_id();
		}
	}

	public static function check_data_from_form() {
		global $msg;
		global $libelle, $taux_tva;
		
		//Vérification du format du taux de tva
		$taux_tva = str_replace(',','.',$taux_tva);
		if ($taux_tva < 0.00 || $taux_tva >99.99) {
			error_form_message($libelle.$msg["acquisition_tva_error"]);
			return false;
		}
		return true;
	}
	
	//supprime un taux de tva de la base
	public static function delete($id= 0) {
		global $msg;
		
		$id = intval($id);
		if($id) {
			$total1 = static::hasTypesProduits($id);
			$total2 = static::hasFrais($id);
			if (($total1+$total2)==0) {
				$q = "delete from tva_achats where id_tva = '".$id."' ";
				pmb_mysql_query($q);
				return true;
			} else {
				$msg_suppr_err = $msg['acquisition_tva_used'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_tva_used_type'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_tva_used_frais'] ;
				pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
				return false;
			}
		}
		return true;
	}

	//Retourne une requete contenant la liste des taux de tva achats
	public static function listTva() {
		$q = "select id_tva as id, concat(libelle, ' ', taux_tva, ' %') as label, tva_achats.* from tva_achats order by libelle ";
		return $q;
	}

	//Compte les taux de tva achats
	public static function countTva() {
		$q = "select count(1) from tva_achats  ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si un taux de tva achats existe			
	public static function exists($id){
		$id = intval($id);
		$q = "select count(1) from tva_achats where id_tva = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le libellé d'un taux de tva achats existe déjà			
	public static function existsLibelle($libelle, $id=0){
		$id = intval($id);
		$q = "select count(1) from tva_achats where libelle = '".$libelle."' ";
		if ($id) $q.= "and id_tva != '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le taux de tva achats est utilisé dans les types de produits			
	public static function hasTypesProduits($id= 0){
		$id = intval($id);
		if (!$id) return 0;
		$q = "select count(1) from types_produits where num_tva_achat = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le taux de tva achats est utilisé dans les frais		
	public static function hasFrais($id= 0){
		$id = intval($id);
		if (!$id) return 0;
		$q = "select count(1) from frais where num_tva_achat = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}
	
	//optimization de la table taux de tva
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE tva_achats');
		return $opt;
	}
				
}
?>