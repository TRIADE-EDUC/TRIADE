<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions.class.php,v 1.53 2023/09/28 09:03:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/suggestions_map.class.php');
require_once($class_path.'/z3950_notice.class.php');

class suggestions{
	
	public $id_suggestion = 0;						//Identifiant de suggestion	
	public $titre  = '';							//Titre ouvrage
	public $editeur = '';							//Editeur ou diffuseur
	public $auteur = '';							//Auteur ouvrage
	public $code = '';								//ISBN, ISSN, ...				
	public $prix = '0.00';							//Prix indicatif
	public $nb = 1;								//Quantité à commander
	public $commentaires = '';						//Commentaires sur la suggestion
	public $commentaires_gestion = '';				//Commentaires de gestion sur la suggestion
	public $date_creation = '0000-00-00';			
	public $date_decision = '0000-00-00';			//Date de la décision
	public $statut = '1';							//Statut de la suggestion 
	public $num_produit = 0;						//Identifiant du type de produit 
	public $num_entite = 0;						//Identifiant de l'entité sur laquelle est affectée la suggestion
	public $num_rubrique = 0;						//Identifiant de la rubrique budgetaire d'affectation
	public $num_fournisseur = 0;					//Identifiant du fournisseur associé
	public $num_notice = 0;						//Identifiant de notice si cataloguée			
	public $index_suggestion = '';					//Champ de recherche fulltext
	public $url_suggestion = '';					//URL
	public $num_categ = '1';						//Categorie associee a la suggestion
	public $sugg_location = 0;					//localisation
	public $date_publi='0000-00-00';			//date de publication
	public $sugg_src=0;						//source de la suggestion
	public $sugg_explnum=0;						//explnum attaché
	public $sugg_noti_unimarc='';				//notice unimarc
	
	//Constructeur.	 
	public function __construct($id_suggestion=0) {
		$this->id_suggestion = intval($id_suggestion);
		if ($this->id_suggestion) {
			$this->load();	
		}
	}
	
	
	// charge une suggestion à partir de la base.
	public function load(){
		$q = "select * from suggestions left join explnum_doc_sugg on num_suggestion=id_suggestion where id_suggestion = '".$this->id_suggestion."' ";
		$r = pmb_mysql_query($q) ;
		if(pmb_mysql_num_rows($r)) {
    		$obj = pmb_mysql_fetch_object($r);
    		$this->titre = $obj->titre;
    		$this->editeur = $obj->editeur;
    		$this->auteur = $obj->auteur;
    		$this->code = $obj->code;
    		$this->prix = $obj->prix;
    		$this->nb = $obj->nb;
    		$this->commentaires = $obj->commentaires;
    		$this->commentaires_gestion = $obj->commentaires_gestion;
    		$this->date_creation = $obj->date_creation;
    		$this->date_decision = $obj->date_decision;
    		$this->statut = $obj->statut;
    		$this->num_produit = $obj->num_produit;
    		$this->num_entite = $obj->num_entite;
    		$this->num_rubrique  = $obj->num_rubrique ;
    		$this->num_fournisseur = $obj->num_fournisseur;
    		$this->num_notice = $obj->num_notice;
    		$this->index_suggestion = $obj->index_suggestion;
    		$this->url_suggestion = $obj->url_suggestion;
    		$this->num_categ = $obj->num_categ;
    		$this->sugg_location = $obj->sugg_location;
    		$this->date_publi = $obj->date_publication;
    		$this->sugg_src = $obj->sugg_source;
    		$this->sugg_explnum = $obj->num_explnum_doc;
    		$this->sugg_noti_unimarc = $obj->notice_unimarc;
		}
	}
	
	// enregistre une suggestion en base.
	public function save($explnum_doc=""){
		if(($this->titre == '') || ((($this->editeur == '') && ($this->auteur == '')) && (!$this->code) && (!$this->sugg_explnum && !$explnum_doc))) {
		    throw new Exception("Erreur de création suggestions");
		}
	
		if ($this->id_suggestion) {
			
			$q = "update suggestions set titre = '".addslashes($this->titre)."', editeur = '".addslashes($this->editeur)."', ";
			$q.= "auteur = '".addslashes($this->auteur)."', code = '".addslashes($this->code)."', prix = '".$this->prix."', nb = '".$this->nb."', commentaires = '".addslashes($this->commentaires)."', ";
			$q.= "commentaires_gestion = '".addslashes($this->commentaires_gestion)."', date_creation = '".$this->date_creation."', date_decision = '".$this->date_decision."', statut = '".$this->statut."', ";
			$q.= "num_produit = '".$this->num_produit."', num_entite = '".$this->num_entite."', num_rubrique = '".$this->num_rubrique."', ";
			$q.= "num_fournisseur = '".$this->num_fournisseur."', num_notice = '".$this->num_notice."', "; 
			$q.= "index_suggestion = ' ".strip_empty_words($this->titre)." ".strip_empty_words($this->editeur)." ".strip_empty_words($this->auteur)." ".$this->code." ".strip_empty_words($this->commentaires)." ".strip_empty_words($this->commentaires_gestion)." ', ";
			$q.= "url_suggestion = '".addslashes($this->url_suggestion)."', "; 
			$q.= "num_categ = '".$this->num_categ."', ";
			$q.= "sugg_location = '".$this->sugg_location."', ";
			$q.= "date_publication = '".$this->date_publi."', ";
			$q.= "sugg_source = '".$this->sugg_src."' ";
			$q.= "where id_suggestion = '".$this->id_suggestion."' ";
			pmb_mysql_query($q);
			
		} else {
			$q = "insert into suggestions set titre = '".addslashes($this->titre)."', editeur = '".addslashes($this->editeur)."', ";
			$q.= "auteur = '".addslashes($this->auteur)."', code = '".addslashes($this->code)."', prix = '".$this->prix."', nb = '".$this->nb."', commentaires = '".addslashes($this->commentaires)."', ";
			$q.= "commentaires_gestion = '".addslashes($this->commentaires_gestion)."', date_creation = '".$this->date_creation."', date_decision = '".$this->date_decision."', statut = '".$this->statut."', ";
			$q.= "num_produit = '".$this->num_produit."', num_entite = '".$this->num_entite."', num_rubrique = '".$this->num_rubrique."', ";
			$q.= "num_fournisseur = '".$this->num_fournisseur."', num_notice = '".$this->num_notice."', "; 
			$q.= "index_suggestion = ' ".addslashes(strip_empty_words($this->titre)." ".strip_empty_words($this->editeur)." ".strip_empty_words($this->auteur)." ".$this->code." ".strip_empty_words($this->commentaires)." ".strip_empty_words($this->commentaires_gestion))." ', ";
			$q.= "url_suggestion = '".addslashes($this->url_suggestion)."', ";
			$q.= "num_categ = '".$this->num_categ."', ";
			$q.= "sugg_location = '".$this->sugg_location."', ";
			$q.= "date_publication = '".$this->date_publi."', ";
			$q.= "sugg_source = '".$this->sugg_src."' "; 			
			pmb_mysql_query($q);
			$this->id_suggestion = pmb_mysql_insert_id();
		}
		
		if(!empty($explnum_doc)){
			$explnum_doc->save();
			$req = "insert into explnum_doc_sugg set 
				num_explnum_doc='".$explnum_doc->explnum_doc_id."',
				num_suggestion='".$this->id_suggestion."'";
			pmb_mysql_query($req);
		}
	}

	//Vérifie si une suggestion existe déjà en base
	public static function exists($origine, $titre, $auteur, $editeur, $isbn) {
		$q = "select count(1) from suggestions_origine, suggestions where origine = '".addslashes($origine)."' and titre = '".$titre."' and id_suggestion = num_suggestion and auteur='".$auteur."' and editeur = '".$editeur."' and code = '".$isbn."' ";
		$q.= "and statut in (1,2,8) ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//supprime une suggestion de la base
	public static function delete($id_suggestion= 0) {
		$id_suggestion = intval($id_suggestion);
		if($id_suggestion) {
    		$q = "delete from suggestions where id_suggestion = '".$id_suggestion."' ";
    		pmb_mysql_query($q);
    		
    		$q = "delete ed,eds from explnum_doc ed join explnum_doc_sugg eds on ed.id_explnum_doc=eds.num_explnum_doc where eds.num_suggestion=$id_suggestion";
    		pmb_mysql_query($q);
		}
	}

	//Compte le nb de suggestion par statut pour une bibliothèque
	public static function getNbSuggestions($id_bibli=0, $statut='-1', $num_categ='-1', $mask='', $aq=0, $location=0, $user_input='',$source=0, $user_id=array(), $user_statut=array(), $date_inf='', $date_sup='') {
		if($source) 
			$filtre_src = " sugg_source = '".$source."' ";
		else $filtre_src=" 1 ";
		
		if (!$statut) $statut='-1';
		if ($statut == '-1') { 
			$filtre1 = '1';
		} elseif ($statut == $mask) {
			$filtre1 = "(statut & '".$mask."') = '".$mask."' ";
		} else {
			$filtre1 = "(statut & '".$mask."') = 0 and (statut & '".$statut."') = '".$statut."' ";
		}
		
		if ($num_categ == '-1') {
			$filtre2 = '1';
		} else {
			$filtre2 = "num_categ = '".$num_categ."' ";
		}
			
		if (!$id_bibli) $filtre3 = '1';
			else $filtre3.= "num_entite = '".$id_bibli."' ";
		if ($location == 0) {
			$filtre4 = '1';
		} else {
			$filtre4 = "sugg_location = '".$location."' ";
		}
		
		if (!trim($date_inf) && !trim($date_sup)) {
			$filtre5 = '1';
		} else {
			if (trim($date_inf) && trim($date_sup)) {
				$filtre5 = "(date_creation BETWEEN '".$date_inf."' AND '".$date_sup."')";
			} elseif (trim($date_inf)) {
				$filtre5 = "date_creation >= '".$date_inf."'";
			} else {
				$filtre5 = "date_creation <= '".$date_sup."'";
			}
		}
		
		$filtre_empr='';
		$tab_empr=array();
		$filtre_user='';
		$tab_user=array();
		$filtre_visitor='';
		$tab_visitor=array();
		if (is_array($user_id) && count($user_id) && is_array($user_statut) && count($user_statut)) {
			foreach ($user_id as $k=>$id) {
				if ($user_statut[$k] == "0") {
					$tab_user[] = $id;
				}
				if ($user_statut[$k] == "1") {
					$tab_empr[] = $id;
				}
				if ($user_statut[$k] == "2") {
					$tab_visitor[] = $id;
				}
			}
		}
		if (is_array($tab_empr) && count($tab_empr)) {
			$filtre_empr = "suggestions_origine.origine in ('".implode("','",$tab_empr)."') and type_origine='1' ";
		}
		if (is_array($tab_user) && count($tab_user)) {		
			$filtre_user = "suggestions_origine.origine in ('".implode("','",$tab_user)."') and type_origine='0' ";
		}
		if (is_array($tab_visitor) && count($tab_visitor)) {		
			$filtre_visitor = "suggestions_origine.origine in ('".implode("','",$tab_visitor)."') and type_origine='2' ";
		}
		if ($filtre_empr!="" || $filtre_user!="" || $filtre_visitor!="") {
			$table_origine = ", suggestions_origine ";
			$join_origine = "  id_suggestion=num_suggestion  ";
			
			$deja_filtre = false;
			$clause_origine = " and (";
			if ($filtre_empr) {
				$clause_origine.=" (".$filtre_empr.") ";
				$deja_filtre = true;
			}
			if ($filtre_user) {
				if ($deja_filtre) {
					$clause_origine.=" or ";
				}
				$clause_origine.=" (".$filtre_user.") ";
				$deja_filtre = true;
			}
			if ($filtre_visitor) {
				if ($deja_filtre) {
					$clause_origine.=" or ";
				}
				$clause_origine.=" (".$filtre_visitor.") ";
				$deja_filtre = true;
			}
			$clause_origine.= " ) and ";
		} else {
			$table_origine = "";
			$join_origine = "";
			$clause_origine = "";
		}
		
		if (!$aq) {
			$q = "select count(1) from suggestions $table_origine";
			$q.= "where $join_origine $clause_origine ".$filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4 ." and ".$filtre5 ." and ".$filtre_src;
		} else {
			
			$isbn = '';
			$t_codes = array();
			
			if ($user_input!=='') {
				if (isEAN($user_input)) {
					// la saisie est un EAN -> on tente de le formater en ISBN
					$isbn = EANtoISBN($user_input);
					if($isbn) {
						$t_codes[] = $isbn;
						$t_codes[] = formatISBN($isbn,10);
					}
				} elseif (isISBN($user_input)) {
					// si la saisie est un ISBN
					$isbn = formatISBN($user_input);
					if($isbn) { 
						$t_codes[] = $isbn ;
						$t_codes[] = formatISBN($isbn,13);
					}
				}
			}
			
			if (count($t_codes)) {
				
				$q = "select count(1) from suggestions $table_origine";
				$q.= "where $join_origine $clause_origine (".$filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4." and ".$filtre5 ." and ".$filtre_src;
				$q.= ") "; 
				$q.= "and ('0' ";
				foreach ($t_codes as $v) {
					$q.= "or code like '%".$v."%' ";
				}
				$q.=") ";
				
			} else {
						
				$members = $aq->get_query_members("suggestions","concat(titre,' ',editeur,' ',auteur,' ',commentaires)","index_suggestion", "id_suggestion");
								
				$q = $q = "select count(1) from suggestions $table_origine ";
				$q.= "where $join_origine $clause_origine (".$filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4." and ".$filtre5 ." and ".$filtre_src;
				$q.= ") ";  
				$q.= "and (".$members["where"]." )";
			}
		}

		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0); 
	}
	
	//Retourne une requete pour liste des suggestions par statut pour une bibliothèque
	public static function listSuggestions($id_bibli=0, $statut='-1', $num_categ='-1', $mask='', $debut=0, $nb_per_page=0, $aq=0, $order='',$location=0, $user_input='',$source=0, $user_id=0, $user_statut = array(), $date_inf='', $date_sup='') {
		
		if($source) 
			$filtre_src = " sugg_source = '".$source."' ";
		else $filtre_src=" 1 ";
		
		if (!$statut) $statut='-1';
		if ($statut == '-1') { 
			$filtre1 = '1';
		} elseif ($statut == $mask) {
			$filtre1 = "(statut & '".$mask."') = '".$mask."' ";
		} else {
			$filtre1 = "(statut & '".$mask."') = 0 and (statut & ".$statut.") = '".$statut."' ";
		}
			
		if ($num_categ == '-1') {
			$filtre2 = '1';
		} else {
			$filtre2 = "num_categ = '".$num_categ."' ";
		}

		if (!$id_bibli) $filtre3 = '1';
			else $filtre3.= "num_entite = '".$id_bibli."' ";

		if ($location == 0) {
			$filtre4 = '1';
		} else {
			$filtre4 = "sugg_location = '".$location."' ";
		}	

		if (!trim($date_inf) && !trim($date_sup)) {
			$filtre5 = '1';
		} else {
			if (trim($date_inf) && trim($date_sup)) {
				$filtre5 = "(date_creation BETWEEN '".$date_inf."' AND '".$date_sup."')";
			} elseif (trim($date_inf)) {
				$filtre5 = "date_creation >= '".$date_inf."'";
			} else {
				$filtre5 = "date_creation <= '".$date_sup."'";
			}
		}
		
		$filtre_empr='';
		$tab_empr=array();
		$filtre_user='';
		$tab_user=array();
		$filtre_visitor='';
		$tab_visitor=array();
		if (is_array($user_id) && count($user_id) && is_array($user_statut) && count($user_statut)) {
			foreach ($user_id as $k=>$id) {
				if ($user_statut[$k] == "0") {
					$tab_user[] = $id;
				}
				if ($user_statut[$k] == "1") {
					$tab_empr[] = $id;
				}
				if ($user_statut[$k] == "2") {
					$tab_visitor[] = $id;
				}
			}
		}
		if (is_array($tab_empr) && count($tab_empr)) {
			$filtre_empr = "suggestions_origine.origine in ('".implode("','",$tab_empr)."') and type_origine='1' ";
		}
		if (is_array($tab_user) && count($tab_user)) {
			$filtre_user = "suggestions_origine.origine in ('".implode("','",$tab_user)."') and type_origine='0' ";
		}
		if (is_array($tab_visitor) && count($tab_visitor)) {
			$filtre_visitor = "suggestions_origine.origine in ('".implode("','",$tab_visitor)."') and type_origine='2' ";
		}
		if ($filtre_empr!="" || $filtre_user!="" || $filtre_visitor!="") {
			$table_origine = ", suggestions_origine ";
			$join_origine = "  id_suggestion=num_suggestion  ";
				
			$deja_filtre = false;
			$clause_origine = " and (";
			if ($filtre_empr) {
				$clause_origine.=" (".$filtre_empr.") ";
				$deja_filtre = true;
			}
			if ($filtre_user) {
				if ($deja_filtre) {
					$clause_origine.=" or ";
				}
				$clause_origine.=" (".$filtre_user.") ";
				$deja_filtre = true;
			}
			if ($filtre_visitor) {
				if ($deja_filtre) {
					$clause_origine.=" or ";
				}
				$clause_origine.=" (".$filtre_visitor.") ";
				$deja_filtre = true;
			}
			$clause_origine.= " ) and ";
		} else {
			$table_origine = "";
			$join_origine = "";
			$clause_origine = "";
		}
		
		if(!$aq) {
			
			$q = "select * from suggestions $table_origine";
			$q.= "where $join_origine $clause_origine ".$filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4 ." and ".$filtre5 ." and ".$filtre_src;
			if(!$order) $q.="order by statut, date_creation desc ";
				else $q.= "order by".$order." ";
			
		} else {
			
			$isbn = '';
			$t_codes = array();
			
			if ($user_input!=='') {
				if (isEAN($user_input)) {
					// la saisie est un EAN -> on tente de le formater en ISBN
					$isbn = EANtoISBN($user_input);
					if($isbn) {
						$t_codes[] = $isbn;
						$t_codes[] = formatISBN($isbn,10);
					}
				} elseif (isISBN($user_input)) {
					// si la saisie est un ISBN
					$isbn = formatISBN($user_input);
					if($isbn) { 
						$t_codes[] = $isbn ;
						$t_codes[] = formatISBN($isbn,13);
					}
				}
			}
			
			if (count($t_codes)) {

				$q = "select * from suggestions $table_origine";
				$q.= "where $join_origine $clause_origine (".$filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4." and ".$filtre5 ." and ".$filtre_src;
				$q.= ") "; 
				$q.= "and ('0' ";
				foreach ($t_codes as $v) {
					$q.= "or code like '%".$v."%' ";
				}
				$q.=") ";
				if(!$order) $q.="order by statut, date_creation desc ";
					else $q.= "order by".$order." ";
				
			} else {
			
				$members=$aq->get_query_members("suggestions","concat(titre,' ',editeur,' ',auteur,' ',commentaires)","index_suggestion","id_suggestion");
				
				$q = $q = "select *, ".$members["select"]." as pert from suggestions $table_origine ";
				$q.= "where $join_origine $clause_origine (".$filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4." and ".$filtre5 ." and ".$filtre_src;
				$q.= ") ";  
				$q.= "and (".$members["where"]." ";
				foreach ($t_codes as $v) {
					$q.= "or index_suggestion like ('%".$v."%') ";
				}
				$q.=") ";
			
				if (!$order) {
					$q.= "order by pert desc ";	
				} else {
					$q.= "order by ".$order.", pert desc ";
				}
			}
		}
		if (!$debut && $nb_per_page) $q.= "limit ".$nb_per_page;
		if ($debut && $nb_per_page) $q.= "limit ".$debut.",".$nb_per_page;
		return $q;				
	}
	
	//Retourne  une requete pour liste des suggestions par origine 
	//type_origine: 0=utilisateur, 1=lecteur, 2=visiteur
	public static function listSuggestionsByOrigine($origine, $type_origine='1') {
		$q = "select * from suggestions_origine, suggestions where origine = '".addslashes($origine)."' ";
		if ($type_origine != '-1') $q.= "and type_origine = '".$type_origine."' ";
		$q.= "and id_suggestion=num_suggestion order by date_suggestion ";		
		return $q;				
	}

	//Retourne un tableau des origines pour une suggestion
	public function getOrigines($id_suggestion=0) {
		$tab_orig=array();
		$id_suggestion = intval($id_suggestion);
		if (!$id_suggestion) $id_suggestion = $this->id_suggestion;
		$q = "select * from suggestions_origine where num_suggestion=$id_suggestion order by date_suggestion, type_origine ";
		$r = pmb_mysql_query($q);
			
		for($i=0;$i<pmb_mysql_num_rows($r);$i++) {
			$tab_orig[] = pmb_mysql_fetch_array($r,PMB_MYSQL_ASSOC); 
		}
		return $tab_orig;
	}
	
	//optimization de la table suggestions
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE suggestions');
		return $opt;
	}
	
	//Récupération du docnum associé
	public function get_explnum($champ=''){
		$req = "select * from explnum_doc join explnum_doc_sugg on num_explnum_doc=id_explnum_doc where num_suggestion='".$this->id_suggestion."'";
		$res= pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			$tab = pmb_mysql_fetch_array($res);
			switch($champ){				
				case 'id':
					return $tab['id_explnum_doc'];
					break;
				case 'nom':
					return $tab['explnum_doc_nomfichier'];
					break;
				case 'ext';
					return $tab['explnum_doc_extfichier'];
					break;
				case 'mime';
					return $tab['explnum_doc_mimetype'];
					break;	
			}
		}
		return 0;
	}
	
	/*
	 * On catalogue la notice
	 */
	public function catalog_notice(){
		if($this->sugg_noti_unimarc && !$this->num_notice){
			$z=new z3950_notice("unimarc",$this->sugg_noti_unimarc);
			$z->var_to_post();
			$ret=$z->insert_in_database();
			//On attache la notice à la suggestion
			$req = " update suggestions set num_notice='".$ret[1]."' where id_suggestion='".$this->id_suggestion."'";
			pmb_mysql_query($req);
		}
	}
}