<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frais.class.php,v 1.19 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frais{
	
    public $id_frais = 0;					//Identifiant du frais annexe
	public $libelle = '';
	public $condition_frais = '';
	public $montant = '000000.00';
	public $num_cp_compta = 0;
	public $num_tva_achat = 0;
	public $add_to_new_order = 0;
	
	//Constructeur.	 
	public function __construct($id_frais= 0) {
		$this->id_frais = intval($id_frais);
		if ($this->id_frais) {
			$this->load();	
		}
	}
		
	// charge un frais annexe à partir de la base.
	public function load(){
		$q = "select * from frais where id_frais = '".$this->id_frais."' ";
		$r = pmb_mysql_query($q) ;
		if(!pmb_mysql_num_rows($r)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->condition_frais = $obj->condition_frais;
		$this->montant = $obj->montant;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->num_tva_achat = $obj->num_tva_achat;
		$this->add_to_new_order = $obj->add_to_new_order;
	}
	
	public function get_content_form() {
		global $pmb_gestion_devise;
		global $acquisition_gestion_tva;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('libelle', '103')
		->add_input_node('text', $this->libelle);
		$interface_content_form->add_element('condition', 'acquisition_frais_cond')
		->add_textarea_node($this->condition_frais)
		->set_cols(62)
		->set_rows(6)
		->set_attributes(array('wrap' => 'virtual'));
		$interface_content_form->add_element('montant', 'acquisition_frais_montant')
		->add_input_node('float', ($this->id_frais ? $this->montant : ''))
		->set_label(' '.$pmb_gestion_devise);
		$interface_content_form->add_element('cp_compta', 'acquisition_num_cp_compta')
		->add_input_node('integer', $this->num_cp_compta)
		->set_class('saisie-20em');
		if ($acquisition_gestion_tva) {
			$interface_content_form->add_element('tva_achat', 'acquisition_num_tva_achat')
			->add_query_node('select', tva_achats::listTva(), $this->num_tva_achat);
		}
		$interface_content_form->add_element('add_to_new_order', 'acquisition_frais_add_to_new_order')
		->add_input_node('boolean', $this->add_to_new_order)
		->set_class('switch')
		->set_label_code('acquisition_frais_add_to_new_order_enable');
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		$interface_form = new interface_admin_form('fraisform');
		if(!$this->id_frais){
			$interface_form->set_label($msg['acquisition_ajout_frais']);
		}else{
			$interface_form->set_label($msg['acquisition_modif_frais']);
		}
		$interface_form->set_object_id($this->id_frais)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('frais')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle, $condition, $montant, $cp_compta, $tva_achat, $add_to_new_order;
		
		$this->libelle = stripslashes($libelle);
		$this->condition_frais = stripslashes($condition);
		$this->montant = stripslashes($montant);
		$this->num_cp_compta = stripslashes($cp_compta);
		$this->num_tva_achat = stripslashes($tva_achat);
		$this->add_to_new_order = intval($add_to_new_order);
	}
	
	public function get_query_if_exists() {
		$query = "select count(1) from frais where libelle = '".addslashes($this->libelle)."' ";
		if ($this->id_frais) $query.= "and id_frais != '".$this->id_frais."' ";
		return $query;
	}
	
	// enregistre le frais annexe en base.
	public function save(){
		if($this->libelle == '') die("Erreur de création frais"); 
		if($this->id_frais) {		
		    
			$q = "update frais set 
                    libelle ='".addslashes($this->libelle)."', 
                    condition_frais = '".addslashes($this->condition_frais)."', 
                    montant = '".addslashes($this->montant)."', 
                    num_cp_compta = '".addslashes($this->num_cp_compta)."', 
                    num_tva_achat = '".addslashes($this->num_tva_achat)."', 
                    index_libelle = ' ".addslashes(strip_empty_words($this->libelle))." ',
                    add_to_new_order = ".intval($this->add_to_new_order)."
                where id_frais = {$this->id_frais} ";
			pmb_mysql_query($q);
	
		} else {
		
			$q = "insert into frais set 
                    libelle = '".addslashes($this->libelle)."', 
                    condition_frais =  '".addslashes($this->condition_frais)."', 
                    montant = '".addslashes($this->montant)."',  
                    num_cp_compta = '".addslashes($this->num_cp_compta)."',  
                    num_tva_achat = '".addslashes($this->num_tva_achat)."', 
                    index_libelle = ' ".addslashes(strip_empty_words($this->libelle))." ',
                    add_to_new_order = ".intval($this->add_to_new_order)." ";
			pmb_mysql_query($q);
			$this->id_frais = pmb_mysql_insert_id();
		}
	
	}

	public static function check_data_from_form() {
		global $msg;
		global $libelle, $montant;
		
		//Vérification du format du montant
		$montant = str_replace(',','.',$montant);
		if (!is_numeric($montant) || $montant >9999999999.99 ) {
			error_form_message($libelle." ".$msg["acquisition_frais_error"]);
			return false;
		}
		return true;
	}
	
	//supprime un frais annexe de la base
	public static function delete($id=0) {
		global $msg;
		
		$id = intval($id);
		if($id) {
			$total1 = static::hasFournisseurs($id);
			if ($total1==0) {
				$q = "delete from frais where id_frais = '".$id."' ";
				pmb_mysql_query($q);
				return true;
			} else {
				$msg_suppr_err = $msg['acquisition_frais_used'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_frais_used_fou'] ;
				pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
				return false;
			}
		}
		return true;
	}
	
	//Vérifie si un frais existe			
	public static function exists($id){
		$id = intval($id);
		$q = "select count(1) from frais where id_frais = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//Vérifie si le libellé d'un frais annexe existe déjà			
	public static function existsLibelle($libelle, $id=0){
		$id = intval($id);
		$q = "select count(1) from frais where libelle = '".$libelle."' ";
		if ($id) $q.= "and id_frais != '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}

	//Vérifie si le frais annexe est utilisé dans les fournisseurs	
	public static function hasFournisseurs($id){
		$id = intval($id);
		if (!$id) return 0;
		$q = "select count(1) from entites where num_frais = '".$id."' and type_entite = '0'";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table frais
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE frais');
		return $opt;
	}
	
	public static function getFraisForNewOrder() {
	    
	    $ret = [];
	    $q = "select 
                frais.id_frais, frais.libelle as libelle_frais, frais.condition_frais, frais.montant as montant_frais, frais.num_cp_compta as num_cp_compta_frais, 
                tva_achats.id_tva, tva_achats.libelle as libelle_tva, tva_achats.taux_tva 
            from frais left join tva_achats on frais.num_tva_achat = tva_achats.id_tva 
            where frais.add_to_new_order=1 order by frais.libelle";
	    $r = pmb_mysql_query($q);
	    if(!pmb_mysql_num_rows($r)) {
	        return [];
	    }
	    while($row=pmb_mysql_fetch_assoc($r)) {
	        $ret[] = $row;
	    }
	    return $ret;
	}
}

