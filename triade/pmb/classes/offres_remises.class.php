<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: offres_remises.class.php,v 1.12.8.1 2025/05/28 06:20:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class offres_remises{
	
	public $num_fournisseur = 0;				//Identifiant du fournisseur 
	public $num_produit = 0;					//Identifiant du type de produit
	public $remise = '0.00';					//Remise applicable en %
	public $condition_remise = '';
	 
	//Constructeur.	 
	public function __construct($num_fournisseur=0, $num_produit=0) {
		$this->num_fournisseur = intval($num_fournisseur);
		$this->num_produit = intval($num_produit);
		if ($this->num_fournisseur || $this->num_produit) {
			$this->load();			
		}
	}	

	// charge une offre de remise à partir de la base.
	public function load(){
		$q = "select * from offres_remises where num_fournisseur = '".$this->num_fournisseur."' and num_produit = '".$this->num_produit."' ";
		$r = pmb_mysql_query($q);
		if ($obj = pmb_mysql_fetch_object($r)) {
    		$this->remise = $obj->remise;
    		$this->condition_remise = $obj->condition_remise;
		}
	}

	public function get_content_form() {
	    global $msg, $charset;
	    global $rem_content_form;
	    
	    $content_form = $rem_content_form;
	    
	    $bibli_raison_sociale = $msg['acquisition_coord_all'];
	    $fournisseur = new entites($this->num_fournisseur);
	    if($fournisseur->num_bibli) {
	        $bibli = new entites($fournisseur->num_bibli);
	        $bibli_raison_sociale = $bibli->raison_sociale;
	    }
	    $content_form = str_replace('!!raison!!', htmlentities($fournisseur->raison_sociale, ENT_QUOTES, $charset), $content_form);
	    
	    if(!$this->num_produit) {
	        //Produits non remisés pour le selecteur
	        $sel_attr = ['id'=>'sel_prod', 'name'=>'sel_prod'];
	        $sel_prod = entites::get_html_select_types_produits_sans_remise($this->num_fournisseur, $sel_attr);
	        $content_form = str_replace('!!lib_prod!!', $sel_prod, $content_form);
	        
	        $content_form = str_replace('!!rem!!', '0.00', $content_form);
	        $content_form = str_replace('!!commentaires!!', '', $content_form);
	    } else {
	        $typ= new types_produits($this->num_produit);
	        $content_form = str_replace('!!lib_prod!!', htmlentities($typ->libelle, ENT_QUOTES, $charset), $content_form);
	        
	        $content_form = str_replace('!!rem!!', number_format($this->remise, 2,'.','' ), $content_form);
	        $content_form = str_replace('!!commentaires!!', htmlentities($this->condition_remise, ENT_QUOTES, $charset), $content_form);
	    }
	    $content_form = str_replace('!!lib_bibli!!', htmlentities($bibli_raison_sociale, ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!id_prod!!', $this->num_produit, $content_form);
	    
	    return $content_form;
	}
	
	//Affiche le formulaire de remise par type de produits
	public function get_form() {
	    global $msg;
	    
	    if(!$this->num_fournisseur) {
	        return;
	    }
	    $interface_form = new interface_acquisition_fourn_rem_form('remform');
	    if(!$this->num_produit){
	        $interface_form->set_label($msg['acquisition_rem_add']);
	    }else{
	        $interface_form->set_label($msg['acquisition_rem_mod']);
	    }
	    $interface_form->set_object_id($this->num_produit)
	    ->set_num_fournisseur($this->num_fournisseur)
	    ->set_confirm_delete_msg($msg['confirm_suppr'])
	    ->set_content_form($this->get_content_form())
	    ->set_table_name('offres_remises');
	    print $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
	    global $rem, $comment;
	    
	    $this->remise = $rem;
	    $this->condition_remise = stripslashes($comment);
	}
	
	// enregistre une offre de remise en base.
	public function save(){
		if(!$this->num_fournisseur || !$this->num_produit) die("Erreur de création offres_remises");
		
		$q = "select count(1) from offres_remises where num_fournisseur = '".$this->num_fournisseur."' and num_produit = '".$this->num_produit."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) != 0) {

			$q = "update offres_remises set remise = '".$this->remise."', condition_remise ='".addslashes($this->condition_remise)."' ";
			$q.= "where num_fournisseur = '".$this->num_fournisseur."' and num_produit = '".$this->num_produit."' ";
			$r = pmb_mysql_query($q);
			
		} else {

			$q = "insert into offres_remises set num_fournisseur = '".$this->num_fournisseur."', num_produit = '".$this->num_produit."', ";
			$q.= "remise =  '".$this->remise."', condition_remise = '".addslashes($this->condition_remise)."' ";
			$r = pmb_mysql_query($q);

		}
	}

	//supprime un exercice de la base
	public static function delete($num_fournisseur, $num_produit) {
		$num_fournisseur = intval($num_fournisseur);
		$num_produit = intval($num_produit);
		$q = "delete from offres_remises where num_fournisseur = '".$num_fournisseur."' and num_produit = '".$num_produit."' ";
		pmb_mysql_query($q);
	}
	
	//optimization de la table offres_remises
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE offres_remises');
		return $opt;
	}
}