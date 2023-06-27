<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thesaurus.class.php,v 1.29 2018-10-18 06:45:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/noeuds.class.php");
require_once($class_path."/categories.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once($class_path."/translation.class.php");

class thesaurus {

	public $id_thesaurus = 0;					//Identifiant du thesaurus
	public $libelle_thesaurus = '';
	public $active	= '1';
	public $opac_active = '1';
	public $langue_defaut = 'fr_FR';
	public	$num_noeud_racine = 0; 				//Index du noeud racine du thesaurus
	public $num_noeud_orphelins = 0; 			// Index Noeud orphelins du thesaurus
	public $num_noeud_nonclasses = 0; 			// Index Noeud nonclasses du thesaurus 
	 
	private static $instances =array();
	
	// Constructeur.
	public function __construct($id=0) {
		$this->id_thesaurus = $id+0;
		if ($this->id_thesaurus > 0) {
			$this->load();
		}
	}
	
	static public function get_instance($id){
		if(!isset(self::$instances[$id])){
			self::$instances[$id] = new thesaurus($id);
		}
		return self::$instances[$id];
	}
	
	// charge le thesaurus à partir de la base.
	public function load() {
		global $dbh;

		$q = "select * from thesaurus where id_thesaurus = '".$this->id_thesaurus."' ";
		$r = pmb_mysql_query($q, $dbh) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->id_thesaurus = $obj->id_thesaurus;
		$this->libelle_thesaurus = $obj->libelle_thesaurus;
		$this->active = $obj->active;
		$this->opac_active = $obj->opac_active;
		$this->langue_defaut = $obj->langue_defaut;
		$this->num_noeud_racine = $obj->num_noeud_racine;
		
		$q = "select id_noeud from noeuds where num_thesaurus = '".$this->id_thesaurus."' and autorite = 'ORPHELINS' ";
		$r = pmb_mysql_query($q, $dbh);
		if(pmb_mysql_num_rows($r))	$this->num_noeud_orphelins = pmb_mysql_result($r, 0, 0);
		else $this->num_noeud_orphelins=0;
		 
		$q = "select id_noeud from noeuds where num_thesaurus = '".$this->id_thesaurus."' and autorite = 'NONCLASSES' ";
		$r = pmb_mysql_query($q, $dbh);
		if(pmb_mysql_num_rows($r))	$this->num_noeud_nonclasses= pmb_mysql_result($r, 0, 0);
		else $this->num_noeud_nonclasses=0;
	}

	public function get_form() {
		global $msg, $charset;
		global $thes_form;
		global $include_path;
		global $lang;
		
		//Récuperation de la liste des langues définies pour l'interface
		$langages = new XMLlist("$include_path/messages/languages.xml", 1);
		$langages->analyser();
		$lg = $langages->table;
		
		//Récuperation de la liste des langues définies pour les thésaurus
		$thes_liste_trad = static::getTranslationsList();
		$lg1 = array();
		foreach($thes_liste_trad as $dummykey=>$item) {
			if ($lg[$item]!= '') $lg1[$item] = $lg[$item];
		}
		
		if($this->id_thesaurus) {	//modification
			$title = $msg['thes_modification'];
			$delete_button = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\">";
				
			$identifiant_thesaurus = "<div class='row'><label class='etiquette' >".$msg[38]."</label></div>";
			$identifiant_thesaurus.= "<div class='row'><input type='text' class='saisie-5emd' id='numero_thesaurus' name='numero_thesaurus' readonly='readonly' value='".$this->id_thesaurus."' /></div>";
		
			$langue_defaut = htmlentities(addslashes($lg[$this->langue_defaut]),ENT_QUOTES, $charset);
		} else {	//creation
			$title = $msg['thes_creation'];
			$delete_button = '';
		
			$identifiant_thesaurus = '';
		
			$langue_defaut = "<select class='saisie-30em' id='langue_defaut' name='langue_defaut' >";
			foreach($lg1 as $key=>$value){
				$langue_defaut.= "<option value='".$key."' ";
				if($key == $lang)$langue_defaut.= " selected ";
				$langue_defaut.= " >".htmlentities(addslashes($value),ENT_QUOTES, $charset)."</option>";
			}
			$langue_defaut.= "</select>";
		
		}
		
		$form = $thes_form;
		if(($this->id_thesaurus) && static::hasCateg($this->id_thesaurus)){
			$form = str_replace('!!thesaurus_as_categ!!', "oui", $form);
		}else{
			$form = str_replace('!!thesaurus_as_categ!!', "non", $form);
		}
		
		$form = str_replace('!!id_thes!!', $this->id_thesaurus, $form);
		$form = str_replace('!!form_title!!', $title, $form);
		$form = str_replace('!!identifiant_thesaurus!!', $identifiant_thesaurus, $form);
		$form = str_replace('!!libelle_thesaurus!!', $this->libelle_thesaurus, $form);
		$form = str_replace('!!langue_defaut!!', $langue_defaut, $form);
		$form = str_replace('!!update_url!!', "./autorites.php?categ=categories&sub=thes_update&id_thes=".$this->id_thesaurus, $form);
		$form = str_replace('!!delete_url!!', "./autorites.php?categ=categories&sub=thes_delete&id_thes=".$this->id_thesaurus, $form);
		$form = str_replace('!!cancel_url!!', "./autorites.php?categ=categories&sub=thes", $form);
		$form = str_replace('!!delete_button!!', $delete_button, $form);
		
		$translation = new translation($this->id_thesaurus, 'thesaurus');
		$form .= $translation->connect('thes_form');
		
		return $form;
	}
	
	// enregistre le thesaurus en base.
	public function save() {
		global $dbh;
		global $msg;
		global $pmb_synchro_rdf;
		
		if($this->libelle_thesaurus == '') die("Erreur de création thésaurus");
		if($this->langue_defaut == '') $this->langue_defaut='fr_FR';
		if($this->id_thesaurus) {	//mise à jour thesaurus
			$q = "update thesaurus set libelle_thesaurus = '".$this->libelle_thesaurus."' ";
			$q.= ", active = '".$this->active."' ";
			$q.= ", opac_active = '".$this->opac_active."' ";
			$q.= ", langue_defaut = '".$this->langue_defaut."' ";
			$q.= "where id_thesaurus = '".$this->id_thesaurus."' ";
			$r = pmb_mysql_query($q, $dbh);
			
			//Traductions
			$translation = new translation($this->id_thesaurus, 'thesaurus');
			$translation->update("libelle_thesaurus");
		} else {	//création thesaurus
			$q = "insert into thesaurus set libelle_thesaurus = '".$this->libelle_thesaurus."', active = '1', opac_active = '1', langue_defaut = '".$this->langue_defaut."' ";
			$r = pmb_mysql_query($q, $dbh);
			$this->id_thesaurus = pmb_mysql_insert_id($dbh);

			//Traductions
			$translation = new translation($this->id_thesaurus, 'thesaurus');
			$translation->update("libelle_thesaurus");
			
			//creation noeud racine
			$noeud = new noeuds(); 
			$noeud->autorite = 'TOP';
			$noeud->num_parent = 0;
			$noeud->num_renvoi_voir = 0;
			$noeud->visible = '0';
			$noeud->num_thesaurus = $this->id_thesaurus;
			$noeud->save();
			
			$this->num_noeud_racine = $noeud->id_noeud;
			
			//rattachement noeud racine au thesaurus
			$q = "update thesaurus set num_noeud_racine = '".$this->num_noeud_racine."' ";
			$q.= "where id_thesaurus = '".$this->id_thesaurus."' ";
			$r = pmb_mysql_query($q, $dbh);
			
			//creation noeud orphelins
			$noeud = new noeuds();
			$noeud->autorite = 'ORPHELINS';
			$noeud->num_parent = $this->num_noeud_racine;
			$noeud->num_renvoi_voir = 0;
			$noeud->visible = '0';
			$noeud->num_thesaurus = $this->id_thesaurus;
			$noeud->save();
			$this->num_noeud_orphelins = $noeud->id_noeud;

			//Creation catégorie orphelins langue par défaut
			$categ = new categories($this->num_noeud_orphelins, $this->langue_defaut);
			$categ->libelle_categorie = $msg["thes_orphelins"];
			$categ->save();						
			
			//creation noeud non classes;		 
			$noeud = new noeuds();
			$noeud->autorite = 'NONCLASSES';
			$noeud->num_parent = $this->num_noeud_racine;
			$noeud->num_renvoi_voir = 0;
			$noeud->visible = '0';
			$noeud->num_thesaurus = $this->id_thesaurus;
			$noeud->save();
			$this->num_noeud_nonclasses = $noeud->id_noeud;
			
			//Creation catégorie non classes langue par défaut
			$categ = new categories($this->num_noeud_nonclasses, $this->langue_defaut);
			$categ->libelle_categorie = $msg["thes_non_classes"];
			$categ->save();						

		}  
		//mise à jour dans la base rdf
		if($pmb_synchro_rdf){
			$synchro_rdf = new synchro_rdf();
			$synchro_rdf->updateAuthority($this->id_thesaurus,'thesaurus');
		}
	}
	

	// supprime le thesaurus.
	public static function delete($id_thes=0) {
		global $dbh;
		global $msg;
		global $pmb_synchro_rdf;
		
		if (!$id_thes) {
			return false;
		}
		if($pmb_synchro_rdf){
			$synchro_rdf = new synchro_rdf();
		}
  		$q = "select id_noeud from noeuds where num_thesaurus = '".$id_thes."' ";
  		$r = pmb_mysql_query($q, $dbh);
  		while ($row = pmb_mysql_fetch_row($r)){
  			noeuds::delete($row[0]);
  			/*$q1 = "delete from categories where num_noeud = '".$row[0]."' ";
  			$r1 = pmb_mysql_query($q1, $dbh);
  			$q2 = "delete from noeuds where id_noeud = '".$row[0]."' ";
  			$r2 = pmb_mysql_query($q2, $dbh);
  			*/
  			if($pmb_synchro_rdf){
  				$synchro_rdf->delConcept($row[0]);
  			}
  		}
  		$q = "delete from thesaurus where id_thesaurus = '".$id_thes."' ";
  		$r = pmb_mysql_query($q, $dbh);
  		
  		translation::delete($id_thes, "thesaurus", "libelle_thesaurus");
  		
  		if($pmb_synchro_rdf){
  			$synchro_rdf->delThesaurusDefinition($id_thes);
  		}
	}	

	//Retourne un objet thesaurus à partir de l'ID d'un de ses noeuds
	public static function getByEltId($id_noeud) {
		$id_noeud += 0;
		$q = "select num_thesaurus from noeuds where id_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r) == 0) return NULL;
		return thesaurus::get_instance(pmb_mysql_result($r, 0, 0));
	}

	//Indique si un thesaurus possede des categories autres que les categories de base (TOP, ORPHELINS, NONCLASSES)
	public static function hasCateg($id_thes=0) {
		$id_thes += 0;
		$q = "select count(1) from noeuds where num_thesaurus = '".$id_thes."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) > 3) return TRUE; 
		else return FALSE;		
		
	}

	//Indique si un thesaurus est utilise pour les notices
	public static function hasNotices($id_thes=0) {
 		$id_thes += 0;
		$q = "select count(1) from notices_categories, noeuds where noeuds.num_thesaurus = '".$id_thes."' ";
		$q.= "and noeuds.id_noeud = notices_categories.num_noeud "; 
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) != 0) return TRUE; 
		else return FALSE;		
		
	}

	//Retourne un tableau des langues affichées dans les thésaurus
	public static function getTranslationsList() {
		$q = "select valeur_param from parametres where type_param = 'thesaurus' and sstype_param = 'liste_trad' ";
		$r = pmb_mysql_query($q);
		$a = explode(',',pmb_mysql_result($r, 0, 0));
		return $a;
	}

	//recuperation du thesaurus session
	public static function getSessionThesaurusId() {
		global $thesaurus_defaut;
		global $thesaurus_mode_pmb;
		global $deflt_thesaurus;
		
		if (!isset($_SESSION["id_thesau"])) { 
			
			//choix du thesaurus à afficher si l'on est pas déjà dans un thesaurus 
			//thesaurus par défaut de l'application en mode monothesaurus
			//thesaurus par defaut de l'utilisateur en mode multithesaurus
			
			switch ($thesaurus_mode_pmb) {	// gestion niveau thesaurus
				case "0" :					// Mono thesaurus
					$id_thes = $thesaurus_defaut;
					$_SESSION["id_thesau"] = $id_thes;
					break;
				case "1" :					// Multi thesaurus
					if (!$deflt_thesaurus) $id_thes = $thesaurus_defaut; 
					else $id_thes = $deflt_thesaurus; 
					$_SESSION["id_thesau"] = $id_thes;
					break;
		
				default :					//mal défini -> Mono thesaurus
					$id_thes = $thesaurus_defaut;
					$_SESSION["id_thesau"] = $id_thes;
				break;
			}
		}
		return $_SESSION["id_thesau"];
	}
	
	//définition du thesaurus session
	public static function setSessionThesaurusId($id_thes) {
		$_SESSION["id_thesau"] = $id_thes;
	}


	//recuperation du thesaurus session pour les notices
	public static function getNoticeSessionThesaurusId() {
		global $thesaurus_defaut;
		global $thesaurus_mode_pmb;
		global $deflt_thesaurus;
		
		if (!isset($_SESSION["notice_id_thes"]) || !$_SESSION["notice_id_thes"]) { 
			
			//choix du thesaurus à afficher si l'on est pas déjà dans un thesaurus 
			//thesaurus par défaut de l'application en mode monothesaurus
			//thesaurus par defaut de l'utilisateur en mode multithesaurus
			
			switch ($thesaurus_mode_pmb) {	// gestion niveau thesaurus
				case "0" :					// Mono thesaurus
					$id_thes = $thesaurus_defaut;
					$_SESSION["notice_id_thes"] = $id_thes;
					break;
				case "1" :					// Multi thesaurus
					if (!$deflt_thesaurus) $id_thes = $thesaurus_defaut; 
					else $id_thes = $deflt_thesaurus; 
					$_SESSION["notice_id_thes"] = $id_thes;
					break;
				default :					//mal défini -> Mono thesaurus
					$id_thes = $thesaurus_defaut;
					$_SESSION["notice_id_thes"] = $id_thes;
				break;
			}
		}
		return $_SESSION["notice_id_thes"];
	}
	
	
	//définition du thesaurus session pour les notices
	public static function setNoticeSessionThesaurusId($id_thes) {
		$_SESSION["notice_id_thes"] = $id_thes;
	}

	
	//retourne le libelle du thesaurus
	public function getLibelle($id_thes=0) {
		if (!$id_thes) {
			return $this->libelle_thesaurus;
		} else {
			$q = "select libelle_thesaurus from thesaurus where id_thesaurus = '".$id_thes."' limit 1";
			$r = pmb_mysql_query($q);
			return pmb_mysql_result($r, 0, 0); 
		} 
	}
	
	
	//retourne la liste des thesaurus dans un tableau associé id_thesaurus=>libelle_thesaurus
	public static function getThesaurusList(){
		$list_thes = array();
		$q = "select id_thesaurus, libelle_thesaurus from thesaurus where 1 ORDER BY 2";
		$r = pmb_mysql_query($q);
		while ($row = @pmb_mysql_fetch_object($r)){
			$list_thes[$row->id_thesaurus] = $row->libelle_thesaurus;
		}
		return $list_thes;
	}
	
	//retourne du selecteur de thesaurus
	public static function getSelector($selected, $base_url='') {
		global $msg, $charset;
		global $thesaurus_mode_pmb;
		
		$liste_thesaurus = static::getThesaurusList();
		$selector = '';
		
		if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
			$selector = "<select class='saisie-30em' id='id_thes' name='id_thes' ";
			$selector.= "onchange = \"document.location = '".$base_url."&id_thes='+document.getElementById('id_thes').value; \">" ;
			foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$selector.= "<option value='".$id_thesaurus."' "; ;
				if ($id_thesaurus == $selected) $selector.= " selected";
				$selector.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
			}
			$selector.= "<option value=-1 ";
			if ($selected == -1) $selector.= "selected ";
			$selector.= ">".htmlentities($msg['thes_all'],ENT_QUOTES,$charset)."</option>";
			$selector.= "</select>&nbsp;";
		}
		return $selector;
	}
}
?>
