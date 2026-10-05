<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_categ.class.php,v 1.10 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class suggestions_categ{
	
	public $id_categ = 0;							//Identifiant de categorie de suggestions	
	public $libelle_categ  = '';					//Libelle  de categorie de suggestions
	
	//Constructeur.	 
	public function __construct($id_categ= 0) {
		$this->id_categ = intval($id_categ);
		if ($this->id_categ) {
			$this->load();	
		}
	}	
	
	// charge une categorie de suggestions à partir de la base.
	public function load(){
		$q = "select * from suggestions_categ where id_categ = '".$this->id_categ."' ";
		$r = pmb_mysql_query($q) ;
		if(!pmb_mysql_num_rows($r)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle_categ = $obj->libelle_categ;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('libelle', '103')
		->add_input_node('text', $this->libelle_categ);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('categform');
		if(!$this->id_categ){
			$interface_form->set_label($msg['acquisition_ajout_categ']);
		}else{
			$interface_form->set_label($msg['acquisition_modif_categ']);
		}
		$interface_form->set_object_id($this->id_categ)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle_categ." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('suggestions_categ')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle;
		
		$this->libelle_categ = stripslashes($libelle);
	}
	
	public function get_query_if_exists() {
		$query = "select count(1) from suggestions_categ where libelle_categ = '".addslashes($this->libelle_categ)."' ";
		if($this->id_categ) $query.= "and id_categ != '".$this->id_categ."' ";
		return $query;
	}
	
	// enregistre une categorie de suggestions en base.
	public function save(){
		if( $this->libelle_categ == '' ) die("Erreur de création catégorie de suggestions");
		if ($this->id_categ) {
			$q = "update suggestions_categ set libelle_categ = '".addslashes($this->libelle_categ)."' ";
			$q.= "where id_categ = '".$this->id_categ."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into suggestions_categ set libelle_categ = '".addslashes($this->libelle_categ)."' ";
			pmb_mysql_query($q);
			$this->id_categ = pmb_mysql_insert_id();
		}
	}

	//Retourne une liste des categories de suggestions (tableau id->libelle)
	public static function getCategList() {
		$list_categ = array();

		$q = "select * from suggestions_categ order by libelle_categ ";
		$r = pmb_mysql_query($q);
		while ($row = pmb_mysql_fetch_object($r)){
			$list_categ[$row->id_categ] = $row->libelle_categ;
		}
		return $list_categ;
	}

	//Vérifie si une categorie de suggestions existe			
	public static function exists($id_categ) {
		$q = "select count(1) from suggestions_categ where id_categ = '".$id_categ."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//Vérifie si le libelle d'une categorie de suggestions existe déjà en base
	public static function existsLibelle($libelle, $id_categ=0) {
		$q = "select count(1) from suggestions_categ where libelle_categ = '".$libelle."' ";
		if($id_categ) $q.= "and id_categ != '".$id_categ."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//supprime une categorie de suggestions de la base
	public static function delete($id= 0) {
		global $msg;
		
		$id = intval($id);
		if($id) {
			if ($id == 1) {	//categorie avec id=1 non supprimable
				$msg_suppr_err = $msg['acquisition_categ_used'] ;
				pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
				return false;
			} else {
				$total1 = static::hasSuggestions($id);
				if ($total1==0) {
					$q = "delete from suggestions_categ where id_categ = '".$id."' ";
					pmb_mysql_query($q);
					return true;
				} else {
					$msg_suppr_err = $msg['acquisition_categ_used'] ;
					if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_categ_used_sugg'] ;
					pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
					return false;
				}
			}
		}
		return true;
	}

	//Vérifie si la categorie de suggestions est utilisee dans les suggestions	
	public static function hasSuggestions($id){
		$id = intval($id);
		$q = "select count(1) from suggestions where num_categ = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table suggestions_categ
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE suggestions_categ');
		return $opt;
	}
				
}?>