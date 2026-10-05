<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: coordonnees.class.php,v 1.12 2021/12/22 11:22:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class coordonnees{
	
	public $id_contact = 0;			//Identifiant du contact	
	public $num_entite = 0;			//Identifiant de l'entité à laquelle est rattaché le contact
	public $type_coord = 0;			//type de coordonnées (0=non précisé, 1=principale/facturation, 2=livraison)
	public $libelle = '';				//Libellé adresse si <> de raison sociale entité
	public $contact = '';				//Genre, Nom, Prenom du contact
	public $adr1 = '';					//Ligne 1 adresse
	public $adr2 = '';					//Ligne 2 adresse
	public $cp = '';					//Code postal
	public $ville = '';				//Ville
	public $etat = '';					//Etat
	public $pays = '';					//Pays
	public $tel1 = '';					//Numéro de tél 1
	public $tel2 = '';					//Numéro de tél 2
	public $fax = '';					//Numéro de fax
	public $email = '';				//Email
	public $commentaires = '';			//Commentaires sur le contact			
	 
	//Constructeur.	 
	public function __construct($id_contact= 0) {
		$this->id_contact = intval($id_contact);
		if ($this->id_contact) {
			$this->load();	
		} 
	}	
	
	// charge un contact à partir de la base.
	public function load(){
		$q = "select * from coordonnees where id_contact = '".$this->id_contact."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->num_entite = $obj->num_entite;
		$this->type_coord = $obj->type_coord;
		$this->libelle = $obj->libelle;
		$this->contact = $obj->contact;
		$this->adr1 = $obj->adr1;
		$this->adr2 = $obj->adr2;
		$this->cp = $obj->cp;
		$this->ville = $obj->ville;
		$this->etat = $obj->etat;
		$this->pays = $obj->pays;
		$this->tel1 = $obj->tel1;
		$this->tel2 = $obj->tel2;
		$this->fax = $obj->fax;
		$this->email = $obj->email;
		$this->commentaires = $obj->commentaires;
	}
	
	public function set_properties_from_form($i) {
		global $lib_, $cta_, $ad1_, $ad2_, $cpo_, $vil_, $eta_, $pay_, $te1_, $te2_, $fax_, $ema_;
		$this->libelle = $lib_[$i];
		$this->contact = $cta_[$i];
		$this->adr1 = $ad1_[$i];
		$this->adr2 = $ad2_[$i];
		$this->cp = $cpo_[$i];
		$this->ville = $vil_[$i];
		$this->etat = $eta_[$i];
		$this->pays = $pay_[$i];
		$this->tel1 = $te1_[$i];
		$this->tel2 = $te2_[$i];
		$this->fax = $fax_[$i];
		$this->email = $ema_[$i];
	}
	
	// enregistre un contact en base.
	public function save(){
		if( !$this->num_entite ) die ("Erreur de création coordonnées");
		
		if ($this->id_contact) {
		
			$q = "update coordonnees set num_entite = '".$this->num_entite."', type_coord = '".$this->type_coord."', libelle = '".$this->libelle."', contact = '".$this->contact."', ";
			$q.= "adr1 = '".$this->adr1."', adr2 = '".$this->adr2."', cp = '".$this->cp."', ville = '".$this->ville."', ";
			$q.= "etat = '".$this->etat."', pays = '".$this->pays."', tel1 = '".$this->tel1."', tel2 = '".$this->tel2."', ";
			$q.= "fax = '".$this->fax."', email = '".$this->email."', commentaires = '".$this->commentaires."' ";
			$q.= "where id_contact = '".$this->id_contact."' ";
			pmb_mysql_query($q);

		} else {
			
			$q = "insert into coordonnees set num_entite = '".$this->num_entite."', type_coord = '".$this->type_coord."', libelle = '".$this->libelle."', contact = '".$this->contact."', ";
			$q.= "adr1 = '".$this->adr1."', adr2 = '".$this->adr2."', cp = '".$this->cp."', ville = '".$this->ville."', ";
			$q.= "etat = '".$this->etat."', pays = '".$this->pays."', tel1 = '".$this->tel1."', tel2 = '".$this->tel2."', ";
			$q.= "fax = '".$this->fax."', email = '".$this->email."', commentaires = '".$this->commentaires."' "; 
			pmb_mysql_query($q);
			$this->id_contact = pmb_mysql_insert_id();
		}
	}

	//supprime un contact de la base
	public function delete($id_contact= 0) {
		$id_contact = intval($id_contact);
		if(!$id_contact) $id_contact = $this->id_contact; 	

		$q = "delete from coordonnees where id_contact = '".$id_contact."' ";
		pmb_mysql_query($q);
	}

	//Recherche si un contact existe déjà dans la base à partir de son identifiant
	public function exists($id_contact=0) {
		$id_contact = intval($id_contact);
		if (!$id_contact) $id_contact = $this->id_contact;
		$q = "select count(1) from coordonnees where id_contact = '".$id_contact."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	//optimization de la table coordonnees
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE coordonnees');
		return $opt;
	}
	
	public function get_formatted_address() {
		global $charset;
		
		$address = '';
		if($this->libelle != '') {
			$address .= htmlentities($this->libelle, ENT_QUOTES, $charset)."\n";
		}
		if($this->contact != '') {
			$address .= htmlentities($this->contact, ENT_QUOTES, $charset)."\n";
		}
		if($this->adr1 != '') {
			$address .= htmlentities($this->adr1, ENT_QUOTES, $charset)."\n";
		}
		if($this->adr2 != '') {
			$address .= htmlentities($this->adr2, ENT_QUOTES, $charset)."\n";
		}
		if($this->cp !='') {
			$address .= htmlentities($this->cp, ENT_QUOTES, $charset).' ';
		}
		if($this->ville != '') {
			$address .= htmlentities($this->ville, ENT_QUOTES, $charset);
		}
		return $address;
	}
	
	public static function get_formatted_address_form_coord($coord) {
		global $charset;
		
		$address = '';
		if($coord->libelle != '') {
			$address .= htmlentities($coord->libelle, ENT_QUOTES, $charset)."\n";
		}
		if($coord->contact != '') {
			$address .= htmlentities($coord->contact, ENT_QUOTES, $charset)."\n";
		}
		if($coord->adr1 != '') {
			$address .= htmlentities($coord->adr1, ENT_QUOTES, $charset)."\n";
		}
		if($coord->adr2 != '') {
			$address .= htmlentities($coord->adr2, ENT_QUOTES, $charset)."\n";
		}
		if($coord->cp !='') {
			$address .= htmlentities($coord->cp, ENT_QUOTES, $charset).' ';
		}
		if($coord->ville != '') {
			$address .= htmlentities($coord->ville, ENT_QUOTES, $charset);
		}
		return $address;
	}
}
?>