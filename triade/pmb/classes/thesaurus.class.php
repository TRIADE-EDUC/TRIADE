<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thesaurus.class.php,v 1.37.2.2 2024/12/31 08:58:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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

	protected static $fullThesaurusList = null;
    protected static $thesaurusList = null;

	private static $instances =array();

	// Constructeur.
	public function __construct($id=0) {
		$this->id_thesaurus = intval($id);
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
	    $query = "select * from thesaurus where id_thesaurus = '".$this->id_thesaurus."' ";
	    $result = pmb_mysql_query($query);
	    if (!pmb_mysql_num_rows($result)) {
	        $this->id_thesaurus = 0;
	        return;
	    }
	    $obj = pmb_mysql_fetch_object($result);
		$this->id_thesaurus = $obj->id_thesaurus;
		$this->libelle_thesaurus = $obj->libelle_thesaurus;
		$this->active = $obj->active;
		$this->opac_active = $obj->opac_active;
		$this->langue_defaut = $obj->langue_defaut;
		$this->num_noeud_racine = $obj->num_noeud_racine;

		$query = "select id_noeud from noeuds where num_thesaurus = '".$this->id_thesaurus."' and autorite = 'ORPHELINS' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result))	{
		    $this->num_noeud_orphelins = pmb_mysql_result($result, 0, 0);
		} else {
		    $this->num_noeud_orphelins=0;
		}

		$query = "select id_noeud from noeuds where num_thesaurus = '".$this->id_thesaurus."' and autorite = 'NONCLASSES' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result))	{
		    $this->num_noeud_nonclasses= pmb_mysql_result($result, 0, 0);
		} else {
		    $this->num_noeud_nonclasses=0;
		}
	}

	protected function get_js_form() {
		global $thes_js_form;

		$js_form = $thes_js_form;
		if(($this->id_thesaurus) && static::hasCateg($this->id_thesaurus)){
			$js_form = str_replace('!!thesaurus_as_categ!!', "oui", $js_form);
		}else{
			$js_form = str_replace('!!thesaurus_as_categ!!', "non", $js_form);
		}
		return $js_form;
	}

	public function get_content_form() {
		global $charset;
		global $include_path;
		global $lang;

		$interface_content_form = new interface_content_form(static::class);

		if($this->id_thesaurus) {	//modification
			$interface_content_form->add_element('numero_thesaurus', '38')
			->add_html_node($this->id_thesaurus);
		}
		$interface_content_form->add_element('libelle_thesaurus', '103')
		->add_input_node('text', $this->libelle_thesaurus)
		->set_class('saisie-80em')
		->set_attributes(array('data-translation-fieldname' => 'libelle_thesaurus'));

		//Récuperation de la liste des langues définies pour l'interface
		$langages = new XMLlist("$include_path/messages/languages.xml", 1);
		$langages->analyser();
		$lg = $langages->table;

		//Récuperation de la liste des langues définies pour les thésaurus
		$thes_liste_trad = static::getTranslationsList();
		$lg1 = array();
		foreach($thes_liste_trad as $item) {
			if ($lg[$item]!= '') $lg1[$item] = $lg[$item];
		}

		if($this->id_thesaurus) {	//modification
			$langue_defaut = htmlentities(addslashes($lg[$this->langue_defaut]),ENT_QUOTES, $charset);
		} else {	//creation
			$langue_defaut = "<select class='saisie-30em' id='langue_defaut' name='langue_defaut' >";
			foreach($lg1 as $key=>$value){
				$langue_defaut.= "<option value='".$key."' ";
				if($key == $lang)$langue_defaut.= " selected ";
				$langue_defaut.= " >".htmlentities(addslashes($value),ENT_QUOTES, $charset)."</option>";
			}
			$langue_defaut.= "</select>";
		}
		$interface_content_form->add_element('langue_defaut', 'thes_langue_defaut')
		->add_html_node($langue_defaut);

		return $interface_content_form->get_display();
	}

	public function get_form() {
		global $msg;

		$form = $this->get_js_form();

		$interface_form = new interface_autorites_form('thes_form');
		if(!$this->id_thesaurus){
			$interface_form->set_label($msg['thes_creation']);
		}else{
			$interface_form->set_label($msg['thes_modification']);
		}
		$interface_form->set_object_id($this->id_thesaurus)
		->set_confirm_delete_msg($msg['confirm_suppr'])
		->set_content_form($this->get_content_form())
		->set_table_name('thesaurus')
		->set_field_focus('libelle_thesaurus');
		$form .= $interface_form->get_display();
		return $form;
	}

	// enregistre le thesaurus en base.
	public function save() {
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
			pmb_mysql_query($q);

			//Traductions
			$translation = new translation($this->id_thesaurus, 'thesaurus');
			$translation->update("libelle_thesaurus");
		} else {	//création thesaurus
			$q = "insert into thesaurus set libelle_thesaurus = '".$this->libelle_thesaurus."', active = '1', opac_active = '1', langue_defaut = '".$this->langue_defaut."' ";
			pmb_mysql_query($q);
			$this->id_thesaurus = pmb_mysql_insert_id();

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
			pmb_mysql_query($q);

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
		global $pmb_synchro_rdf;

		if (!$id_thes) {
			return false;
		}
		if($pmb_synchro_rdf){
			$synchro_rdf = new synchro_rdf();
		}
  		$q = "select id_noeud from noeuds where num_thesaurus = '".$id_thes."' ";
  		$r = pmb_mysql_query($q);
  		while ($row = pmb_mysql_fetch_row($r)){
  			noeuds::delete($row[0]);
  			/*$q1 = "delete from categories where num_noeud = '".$row[0]."' ";
  			$r1 = pmb_mysql_query($q1);
  			$q2 = "delete from noeuds where id_noeud = '".$row[0]."' ";
  			$r2 = pmb_mysql_query($q2);
  			*/
  			if($pmb_synchro_rdf){
  				$synchro_rdf->delConcept($row[0]);
  			}
  		}
  		$q = "delete from thesaurus where id_thesaurus = '".$id_thes."' ";
  		$r = pmb_mysql_query($q);

  		translation::delete($id_thes, "thesaurus", "libelle_thesaurus");

  		if($pmb_synchro_rdf){
  			$synchro_rdf->delThesaurusDefinition($id_thes);
  		}
	}

	//Retourne un objet thesaurus à partir de l'ID d'un de ses noeuds
	public static function getByEltId($id_noeud) {
		$id_noeud = intval($id_noeud);
		$q = "select num_thesaurus from noeuds where id_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r) == 0) return NULL;
		return thesaurus::get_instance(pmb_mysql_result($r, 0, 0));
	}

	//Indique si un thesaurus possede des categories autres que les categories de base (TOP, ORPHELINS, NONCLASSES)
	public static function hasCateg($id_thes=0) {
		$id_thes = intval($id_thes);
		$q = "select count(1) from noeuds where num_thesaurus = '".$id_thes."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) > 3) return TRUE;
		else return FALSE;

	}

	//Indique si un thesaurus est utilise pour les notices
	public static function hasNotices($id_thes=0) {
		$id_thes = intval($id_thes);
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

	public static function get_thesaurus_defaut() {
	    global $thesaurus_defaut;
	    
	    $thesaurus_defaut = intval($thesaurus_defaut);
	    if($thesaurus_defaut) {
	        $query = "select * from thesaurus where id_thesaurus = '".$thesaurus_defaut."' ";
	        $result = pmb_mysql_query($query);
	        if (!pmb_mysql_num_rows($result)) {
	            $thesaurus_defaut = 0;
	        }
	    }
	    return $thesaurus_defaut;
	}
	
	//recuperation du thesaurus session
	public static function getSessionThesaurusId() {
		global $thesaurus_mode_pmb;
		global $deflt_thesaurus;

		if (!isset($_SESSION["id_thesau"])) {

			//choix du thesaurus à afficher si l'on est pas déjà dans un thesaurus
			//thesaurus par défaut de l'application en mode monothesaurus
			//thesaurus par defaut de l'utilisateur en mode multithesaurus

			switch ($thesaurus_mode_pmb) {	// gestion niveau thesaurus
				case "0" :					// Mono thesaurus
					$id_thes = static::get_thesaurus_defaut();
					$_SESSION["id_thesau"] = $id_thes;
					break;
				case "1" :					// Multi thesaurus
				    if (!$deflt_thesaurus) {
				        $id_thes = static::get_thesaurus_defaut(); 
				    } else {
				        $id_thes = $deflt_thesaurus; 
				    }
					$_SESSION["id_thesau"] = $id_thes;
					break;

				default :					//mal défini -> Mono thesaurus
				    $id_thes = static::get_thesaurus_defaut();
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
		global $thesaurus_mode_pmb;
		global $deflt_thesaurus;

		if (!isset($_SESSION["notice_id_thes"]) || !$_SESSION["notice_id_thes"]) {

			//choix du thesaurus à afficher si l'on est pas déjà dans un thesaurus
			//thesaurus par défaut de l'application en mode monothesaurus
			//thesaurus par defaut de l'utilisateur en mode multithesaurus

			switch ($thesaurus_mode_pmb) {	// gestion niveau thesaurus
				case "0" :					// Mono thesaurus
				    $id_thes = static::get_thesaurus_defaut();
					$_SESSION["notice_id_thes"] = $id_thes;
					break;
				case "1" :					// Multi thesaurus
				    if (!$deflt_thesaurus) {
				        $id_thes = static::get_thesaurus_defaut();
				    } else {
				        $id_thes = $deflt_thesaurus; 
				    }
					$_SESSION["notice_id_thes"] = $id_thes;
					break;
				default :					//mal défini -> Mono thesaurus
				    $id_thes = static::get_thesaurus_defaut();
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


	/**
	 *  Retourne une liste des thesaurus et de leur propriétés
	 *
	 * @return [
	 *     id_thesaurus => [
	 *         id_thesaurus        => 'Identifiant thesaurus',
	 *         libelle_thesaurus   => 'Nom du Thesaurus',
	 *         langue_defaut       => 'Langue par defaut (fr_FR, ...)',
	 *         active              => 'Active (O/1)',
	 *         opac_active         => 'Active en OPAC (O/1)',
	 *         num_noeud_racine    => 'Identifiant noeud racine',
	 *         thesaurus_order     => 'Ordre d'affichage',
	 *     ]
	 * ]
	 */
	public static function getFullThesaurusList()
	{
	    if(!is_null(static::$fullThesaurusList)) {
	        return static::$fullThesaurusList;
	    }
	    static::$fullThesaurusList = [];
	    $q = "select * from thesaurus";
	    $r = pmb_mysql_query($q);
	    if(!pmb_mysql_num_rows($r)) {
	        return static::$fullThesaurusList = [];
	    }
	    while ($row = pmb_mysql_fetch_assoc($r)){
	        static::$fullThesaurusList[$row['id_thesaurus']] = $row;
	    }
	    return static::$fullThesaurusList;
	}


	//
	/**
	 * Retourne une liste simplifiee des thesaurus triee par libelle
	 *
	 * @return [
	 *     id_thesaurus => 'libelle_thesaurus'
	 * ]
	 */
	public static function getThesaurusList()
	{
	    if( !is_null(static::$thesaurusList)) {
	        return static::$thesaurusList;
	    }
        static::$thesaurusList = [];

	    static::getFullThesaurusList();
	    foreach(static::$fullThesaurusList as $k => $v) {
	        static::$thesaurusList[$k] = $v['libelle_thesaurus'];
	    }
	    asort(static::$thesaurusList, SORT_STRING);
	    return static::$thesaurusList;
	}

	public function get_translated_libelle_thesaurus() {
	    return translation::get_translated_text($this->id_thesaurus, 'thesaurus', 'libelle_thesaurus',  $this->libelle_thesaurus);
	}
	
	//retourne du selecteur de thesaurus
	public static function getSelector($selected, $base_url='') {
	    global $msg, $charset;
	    global $thesaurus_mode_pmb;

	    $liste_thesaurus = static::getThesaurusList();
	    $selector = '';

	    if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
	        $selector = "<select class='saisie-30em' name='id_thes' ";
	        $selector.= "onchange = \"document.location = '".$base_url."&id_thes='+this.value; \">" ;
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

	//selecteur de thesaurus dans le formulaire d'une catégorie
	public static function getSelectorCategoryForm($selected, $base_url='', $on_create=true) {
	    global $charset;
	    global $thesaurus_mode_pmb;

	    $liste_thesaurus = static::getThesaurusList();
	    $selector = '';

	    if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
	        $selector = "<select class='saisie-30em' id='category_id_thes' name='id_thes' ";
	        if(!$on_create) {
	            $selector .= "disabled='disabled' style='background-color:#ccc' ";
	        }
	        $selector.= "onchange = \"if(confirm(pmbDojo.messages.getMessage('grid', 'category_change_thesaurus_confirm'))) {unload_off();document.location = '".$base_url."&id_thes='+this.value;} \" backbone='yes'>" ;
	        foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
	            $selector.= "<option value='".$id_thesaurus."' "; ;
	            if ($id_thesaurus == $selected) $selector.= " selected";
	            $selector.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
	        }
	        $selector.= "</select>&nbsp;";
	    }
	    return $selector;
	}
}