<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thesaurus.class.php,v 1.21.2.1 2024/12/31 08:58:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/noeuds.class.php");
require_once($class_path."/categories.class.php");
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

	// enregistre le thesaurus en base.
	public function save() {
		global $msg;

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
	}

	// supprime le thesaurus.
	public function delete($id_thes=0) {
		if (!$id_thes) $id_thes = $this->id_thesaurus;
  		$q = "select id_noeud from noeuds where num_thesaurus = '".$id_thes."' ";
  		$r = pmb_mysql_query($q);
  		while ($row = pmb_mysql_fetch_row($r)){
  			$q1 = "delete from categories where num_noeud = '".$row[0]."' ";
  			pmb_mysql_query($q1);
  			$q2 = "delete from noeuds where id_noeud = '".$row[0]."' ";
  			pmb_mysql_query($q2);
  		}
  		$q = "delete from thesaurus where id_thesaurus = '".$id_thes."' ";
  		$r = pmb_mysql_query($q);

  		translation::delete($id_thes, "thesaurus", "libelle_thesaurus");
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
	    global $opac_thesaurus_defaut;
	    
	    $opac_thesaurus_defaut = intval($opac_thesaurus_defaut);
	    if($opac_thesaurus_defaut) {
	        $query = "select * from thesaurus where id_thesaurus = '".$opac_thesaurus_defaut."' ";
	        $result = pmb_mysql_query($query);
	        if (!pmb_mysql_num_rows($result)) {
	            $opac_thesaurus_defaut = 0;
	        }
	    }
	    return $opac_thesaurus_defaut;
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
			return $this->get_translated_libelle_thesaurus();
		} else {
			$thesaurus = new thesaurus($id_thes);
			return $thesaurus->get_translated_libelle_thesaurus();
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
	 * Retourne une liste simplifiee des thesaurus actif triee par libelle
	 *
	 * @param string $restrict : liste d'id de thesaurus separes par une virgule
	 *
	 * @return [
	 *     id_thesaurus => 'libelle_thesaurus'
	 * ]
	 */
	public static function getThesaurusList($restrict="")
	{
        if( is_null(static::$thesaurusList) ) {
            static::getFullThesaurusList();
            // Limitation aux thesaurus actifs en OPAC
            foreach(static::$fullThesaurusList as $k => $v) {
                if( 1 == $v['opac_active'] ) {
                    static::$thesaurusList[$k] = $v['libelle_thesaurus'];
                }
            }
        }

        $restrict = preg_replace("/[^0-9|,]/", "", $restrict);
        $restrict = !empty($restrict) ? explode(',', $restrict) : [];

        // Restriction aux thesaurus definis en parametres
        $thesaurusList = [];
        if( empty($restrict) ) {
            $thesaurusList = static::$thesaurusList;
        } else {
            foreach( $restrict as $v ) {
                if( !empty(static::$thesaurusList[$v]) ) {
                    $thesaurusList[$v] = static::$thesaurusList[$v];
                }
            }
        }

        // Traduction
        foreach ($thesaurusList as $k => $v) {
            $thesaurusList[$k] = translation::get_text($k, 'thesaurus', 'libelle_thesaurus', $v);
        }

        // Tri alpha
        asort($thesaurusList, SORT_STRING);

        return $thesaurusList;
	}

	public function get_translated_libelle_thesaurus() {
		return translation::get_translated_text($this->id_thesaurus, 'thesaurus', 'libelle_thesaurus',  $this->libelle_thesaurus);
	}
}

