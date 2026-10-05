<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: types_produits.class.php,v 1.19 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class types_produits{
	
	
	public $id_produit = 0;					//Identifiant du type_produit 
	public $libelle = '';
	public $num_cp_compta = '';
	public $num_tva_achat = 0;

	 
	//Constructeur.	 
	public function __construct($id_produit= 0) {
		$this->id_produit = intval($id_produit);
		if ($this->id_produit) {
			$this->load();	
		}
	}
	
		
	// charge le type de produit à partir de la base.
	public function load(){
		$q = "select * from types_produits where id_produit = '".$this->id_produit."' ";
		$r = pmb_mysql_query($q) ;
		if(!pmb_mysql_num_rows($r)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->num_tva_achat = $obj->num_tva_achat;
	}
	
	public function get_content_form() {
		global $acquisition_gestion_tva;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('libelle', '103')
		->add_input_node('text', $this->libelle);
		$interface_content_form->add_element('cp_compta', 'acquisition_num_cp_compta')
		->add_input_node('integer', $this->num_cp_compta)
		->set_class('saisie-20em');
		if ($acquisition_gestion_tva) {
			$interface_content_form->add_element('tva_achat', 'acquisition_num_tva_achat')
			->add_query_node('select', tva_achats::listTva(), $this->num_tva_achat);
		}
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('typeform');
		if(!$this->id_produit){
			$interface_form->set_label($msg['acquisition_ajout_type']);
		}else{
			$interface_form->set_label($msg['acquisition_modif_type']);
		}
		$interface_form->set_object_id($this->id_produit)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('types_produits')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle, $cp_compta, $tva_achat;
		
		$this->libelle = stripslashes($libelle);
		$this->num_cp_compta = stripslashes($cp_compta);
		$this->num_tva_achat = stripslashes($tva_achat);
	}
	
	public function get_query_if_exists() {
		$query = "select count(1) from types_produits where libelle = '".addslashes($this->libelle)."' ";
		if ($this->id_produit) $query .= "and id_produit != '".$this->id_produit."' ";
		return $query;
	}
	
	// enregistre le type de produit en base.
	public function save(){
		if($this->libelle == '') die("Erreur de création type produit");

		if($this->id_produit) {
			$q = "update types_produits set libelle ='".addslashes($this->libelle)."', num_cp_compta = '".addslashes($this->num_cp_compta)."', ";
			$q.= "num_tva_achat = '".addslashes($this->num_tva_achat)."' ";
			$q.= "where id_produit = '".$this->id_produit."' ";
			pmb_mysql_query($q);		
		} else {
			$q = "insert into types_produits set libelle = '".addslashes($this->libelle)."', num_cp_compta = '".addslashes($this->num_cp_compta)."', ";
			$q.= " num_tva_achat = '".addslashes($this->num_tva_achat)."' ";
			pmb_mysql_query($q);
			$this->id_produit = pmb_mysql_insert_id();
		}
	}

	//supprime un type de produit de la base
	public static function delete($id= 0) {
		global $msg;
		
		$id = intval($id);
		if($id) {
			$total1 = static::hasOffres_remises($id);
			$total2 = static::hasSuggestions($id);
			if (($total1+$total2)==0) {
				$q = "delete from types_produits where id_produit = '".$id."' ";
				pmb_mysql_query($q);
				return true;
			} else {
				$msg_suppr_err = $msg['acquisition_type_used'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_type_used_off'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_type_used_sug'] ;
				pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
				return false;
			}
		}
		return true;
	}

	//Retourne une requete pour liste des types de produits
	public static function listTypes($debut=0, $nb_per_page=0) {
		$q = "select * from types_produits order by libelle ";
		if ($debut) {
			$q.="limit ".$debut ;
			if($nb_per_page) $q.= ",".$nb_per_page;
		} else {
			if($nb_per_page) $q.= "limit ".$nb_per_page;
		}
		return $q;
	}

	//Retourne le nb de types de produits
	public static function countTypes() {
		$q = "select count(1) from types_produits  ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si un type de produit existe			
	public static function exists($id){
		$id = intval($id);
		$q = "select count(1) from types_produits where id_produit = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	//Vérifie si le libellé d'un type de produit existe déjà			
	public static function existsLibelle($libelle, $id=0){
		$id = intval($id);
		$q = "select count(1) from types_produits where libelle = '".$libelle."' ";
		if ($id) $q.= "and id_produit != '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le type de produit est utilisé dans les offres de remises	
	public static function hasOffres_remises($id){
		$id = intval($id);
		if (!$id) return 0;
		$q = "select count(1) from offres_remises where num_produit = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le type de produit est utilisé dans les suggestions	
	public static function hasSuggestions($id){
		$id = intval($id);
		if (!$id) return 0;
		$q = "select count(1) from suggestions where num_produit = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table types_produits
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE types_produits');
		return $opt;
	}
}
?>