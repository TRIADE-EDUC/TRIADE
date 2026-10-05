<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: term_search.class.php,v 1.39.4.2 2025/04/16 10:29:12 dgoron Exp $
//
// Gestion de la recherche des termes dans le thésaurus

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/classes/category.class.php");
require_once($class_path."/analyse_query.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categorie.class.php");

class term_search {
	public $id_thes = 0;				//Etendue de la recherche (identifiant thesaurus ou multi-thesaurus si 0)
	public $thes;
	public $search_term_name;			//Nom de la variable contenant le terme recherché dans les catégories
	public $search_term_origin_name;	//Nom de la variable contenant le terme recherché saisi par l'utilisateur
    public $search_term;				//Terme recherché dans les catégories
	public $search_term_origin;			//Terme recherché saisi par l'utilisateur
    public $n_per_page;				//Nombre de résultats par page
    public $base_query;				//Paramètres supplémentaires à passer dans l'url
    public $url_for_term_show;			//Page à appeller pour l'affichage de la fiche du terme
	public $url_for_term_search;		//Page à appeller pour l'affichage de la liste des termes correspondants à la recherche
	public $offset;					//offset en fonction de la page courante
	public $page;    					//Page courante (récupérée du formulaire)
	public $n_total;					//Nombre de termes total correspondants à la recherche
    public $keep_tilde;				//Affichage ou non des catégories cachées
    public $order;						//Stockage de la clause select de calcul de pertinence
    public $error_message;				//Erreur renvoyée par l'analyse de la chaine
    public $where;						//Clause where après analyse de la chaine
    public $aq;
	
    //Constructeur
    public function __construct($search_term_name, $search_term_origin_name, $n_per_page=200, $base_query = '', $url_for_term_show = '', $url_for_term_search = '', $keep_tilde = 0, $id_thes = 0) {

    	global $page;
    	
		//recuperation du thesaurus session
		if(!$id_thes) {
			$id_thes = thesaurus::getSessionThesaurusId();
		} else {
			thesaurus::setSessionThesaurusId($id_thes);
		}
		
    	$this->search_term_name=$search_term_name;
    	$this->search_term_origin_name=$search_term_origin_name;
    	
    	global ${$search_term_name};
    	global ${$search_term_origin_name};
    	
    	$this->search_term=stripslashes(${$search_term_name} ?? '');
    	$this->search_term_origin=stripslashes(${$search_term_origin_name} ?? '');
		$this->n_per_page=$n_per_page;
    	$this->base_query=$base_query;
    	$this->url_for_term_show=$url_for_term_show;
    	$this->url_for_term_search=$url_for_term_search;
    	$this->keep_tilde=$keep_tilde;

		$this->id_thes = $id_thes;		
   		if ($id_thes != -1) $this->thes= new thesaurus($id_thes);
    	
    	if ($page=="") $page=0;
    	$this->page=$page;
    	$this->offset=$page*$this->n_per_page;
    	//$this->get_term_count();
    }
    
    //Affichage du navigateur de pages
    public function page_navigator() {
    	global $msg;
    	
    	$navig = "<span class='navbar'>";
    	$last_page=ceil($this->n_total/$this->n_per_page)-1;
    	$url_page=$this->url_for_term_search."?".$this->search_term_name."=".rawurlencode($this->search_term)."&".$this->search_term_origin_name."=".rawurlencode($this->search_term_origin);
    	if ($this->offset!=0) {
    	    $navig.="<a href=\"$url_page&page=0"."&nbresultterme=".$this->n_total." title='".$msg["first_page"]."'\"><img src='".get_url_icon('first.png')."' alt='".$msg["first_page"]."'  style='border:0px'/></a>";
    	} else {
    	    $navig.="<img src='".get_url_icon('first-grey.png')."' alt='' style='border:0px'/>";
    	}
    	if ($this->offset!=0) {
    	    $navig.="<a href=\"$url_page&page=".($this->page-1)."&nbresultterme=".$this->n_total."&".$this->base_query." title='".$msg["prec_page"]."'\"><img src='".get_url_icon('prev.png')."' alt='".$msg["prec_page"]."' style='border:0px'/></a>";
    	} else {
    	    $navig.="<img src='".get_url_icon('prev-grey.png')."' alt='' style='border:0px'/>";
    	}
		$navig.=" (".($this->offset+1)."-".min($this->offset+$this->n_per_page,$this->n_total).")/".$this->n_total." ";
		if (($this->offset+$this->n_per_page+1)<$this->n_total) {
		    $navig.="<a href=\"$url_page&page=".($this->page+1)."&nbresultterme=".$this->n_total."&".$this->base_query." title='".$msg["next_page"]."'\"><img src='".get_url_icon('next.png')."' alt='".$msg["next_page"]."' style='border:0px'/></a>";
		} else {
		    $navig.="<img src='".get_url_icon('next-grey.png')."' alt='' style='border:0px'/>";
		}
		if (($this->offset+$this->n_per_page+1)<$this->n_total) {
		    $navig.="<a href=\"$url_page&page=$last_page"."&nbresultterme=".$this->n_total." title='".$msg["last_page"]."'\"><img src='".get_url_icon('last.png')."' alt='".$msg["last_page"]."' style='border:0px'/></a>";
		} else {
		    $navig.="<img src='".get_url_icon('last-grey.png')."' alt='' style='border:0px'/>";
		}
		$navig.="</span>";
		return $navig;	
    }
    
    //Récupération du terme where pour la recherche
    public function get_where_term() {
		global $msg;
		global $opac_stemming_active;
    	//Si il y a déjà un terme where calculé alors renvoi tout de suite
    	if ($this->where) return $this->where;
    	
    	//Si il y a un terme saisi alors close where
    	$where_term = '';
		if ($this->search_term) {
			$this->error_message="";
			$aq=new analyse_query($this->search_term,0,0,1,0,$opac_stemming_active);
			if (!$aq->error) {
				$members=$aq->get_query_members("categories","libelle_categorie","index_categorie","num_noeud");
				$where_term = "and ".$members["where"];
				$this->order = $members["select"];
				$this->where = $where_term;
				$this->aq = $aq;
			} else {
				$this->error_message=sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message);
			}
		}
		return $where_term;
    }
    
    //Récupération du nombre de termes correspondants à la recherche
    public function get_term_count() {
    	global $lang;
    	global $opac_thesaurus;
    	global $default_tmp_storage_engine;

		//Comptage du nombre de termes
    	$where_term=$this->get_where_term();
		if ($where_term) {
			$members_catdef = $this->aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
			$members_catlg = $this->aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
		}

		if ($this->id_thes != -1){	//1 seul thesaurus
				
			if ( ($opac_thesaurus!='1') || ($lang==$this->thes->langue_defaut) || (in_array($lang, thesaurus::getTranslationsList())===false) ) { 	//Recherche dans la langue par défaut du thesaurus

				$q = "select count(distinct libelle_categorie) ";
				$q.= "from categories as catdef ";
				$q.= "where 1 ";
				if ($where_term) $q.= "and ".$members_catdef["where"]." ";
				$q.= "and catdef.num_thesaurus = '".$this->id_thes."' ";
				$q.= "and catdef.langue = '".$this->thes->langue_defaut."' "; 
				$q.= "and catdef.libelle_categorie not like '~%' ";
				$r = pmb_mysql_query($q);
				$this->n_total=pmb_mysql_result($r, 0, 0);
			
			} else {		//Recherche dans la langue de l'interface ou dans la langue par défaut du thesaurus

				$q = "drop table if exists cattmp ";
				$r = pmb_mysql_query($q);
	
				$q1 = "create temporary table cattmp engine={$default_tmp_storage_engine} select ";
				$q1.= "if(catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle ";
				$q1.= "from categories as catdef "; 
				$q1.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' "; 
				$q1.= "where 1 ";
				if ($where_term) $q1.= "and if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") ";
				$q1.= "and catdef.num_thesaurus = '".$this->id_thes."' ";
				$q1.= "and catdef.langue = '".$this->thes->langue_defaut."' "; 
				$q1.= "and catdef.libelle_categorie not like '~%' ";
				pmb_mysql_query($q1);
				$q2 = "select count(distinct categ_libelle) from cattmp ";
				$r2 = pmb_mysql_query($q2);
				$this->n_total=pmb_mysql_result($r2, 0, 0);
			}
			
		} else {

			//tous les thesaurus
			//on recherche dans la langue de l'interface ou dans la langue par défaut du thesaurus
			$q = "drop table if exists cattmp ";
			$r = pmb_mysql_query($q);

			$q1 = "create temporary table cattmp engine={$default_tmp_storage_engine} select ";
			$q1.= "id_thesaurus, ";
			$q1.= "if(catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle ";
			$q1.= "from thesaurus ";
			$q1.= "left join categories as catdef on id_thesaurus=catdef.num_thesaurus and catdef.langue=thesaurus.langue_defaut ";
			$q1.= "left join categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."' ";
			$q1.= "where 1 ";
			if ($where_term) $q1.= "and (if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") ) ";
			$q1.= "and catdef.libelle_categorie not like '~%' ";
			pmb_mysql_query($q1);
			
			$q2 = "select count(distinct id_thesaurus,categ_libelle) from cattmp ";					
		  	$r2=pmb_mysql_query($q2);
		 	$this->n_total=pmb_mysql_result($r2,0,0);
		}
}
    
    
    //Affichage de la liste des résultats
    public function show_list_of_terms() {
    	global $charset;
    	global $msg;
    	global $lang;
     	global $nbresultterme;
		
    	//Si il y a eu erreur lors de la première analyse...
    	if ($this->error_message) {
    		return $this->error_message;
    	}
    	
		//Recherche des termes correspondants à la requête
		$where_term=$this->get_where_term();
		if($where_term) {
			$members_catdef = $this->aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
			$members_catlg = $this->aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
		}else{
			echo $msg["term_search_info"];
			return;
		}
		
		if($nbresultterme){
			$this->n_total=$nbresultterme;
			$requete = "select count(catdef.num_noeud) as nb, ";
		}else{
			$requete = "select SQL_CALC_FOUND_ROWS count(catdef.num_noeud) as nb, ";
		}
		
		if ($this->id_thes != -1){		//1 seul thesaurus
			
			if (($lang==$this->thes->langue_defaut) || (in_array($lang, thesaurus::getTranslationsList())===false) ) { 	//Recherche dans la langue par défaut du thesaurus
				$requete.= "num_thesaurus, ";
				$requete.= "num_noeud as categ_id, ";
				$requete.= "libelle_categorie as categ_libelle, ";
				$requete.= "catdef.index_categorie as indexcat ";
				if ($where_term) $requete.= ", ".$members_catdef["select"]." as pert ";
				$requete.= ", catdef.comment_public ";
				$requete.= "from categories as catdef "; 
				$requete.= "where 1 ";
				if ($where_term) $requete.= "and ".$members_catdef["where"]." ";
				$requete.= "and num_thesaurus = '".$this->id_thes."' ";
				$requete.= "and catdef.langue = '".$this->thes->langue_defaut."' ";
				$requete.= "and catdef.libelle_categorie not like '~%' ";
				$requete.= "group by categ_libelle ";
				$requete.= "order by ";
				if ($where_term) $requete.= "pert desc, ";
				$requete.= "indexcat asc ";
				$requete.= "limit ".$this->offset.",".$this->n_per_page;
			
			} else {		//Recherche dans la langue de l'interface ou dans la langue par défaut du thesaurus
				$requete.= "catdef.num_thesaurus, ";
				$requete.= "catdef.num_noeud as categ_id, ";
				$requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as categ_libelle, ";
				$requete.= "if (catlg.num_noeud is null, catdef.index_categorie , catlg.index_categorie ) as indexcat ";
				if ($where_term) $requete.= ", if (catlg.num_noeud is null, ".$members_catdef["select"].", ".$members_catlg["select"].") as pert ";
				$requete.= ", catdef.comment_public ";
				$requete.= "from categories as catdef "; 
				$requete.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
				$requete.= "where 1 ";
				if ($where_term) $requete.= "and (if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") ) ";
				$requete.= "and catdef.num_thesaurus = '".$this->id_thes."' ";
				$requete.= "and catdef.langue = '".$this->thes->langue_defaut."' ";
				$requete.= "and catdef.libelle_categorie not like '~%' ";
				$requete.= "group by categ_libelle ";
				$requete.= "order by ";
				if ($where_term) $requete.= "pert desc, ";
				$requete.= "indexcat asc ";
				$requete.= "limit ".$this->offset.",".$this->n_per_page;
				
			}
			
		} else {
			
			//tous les thesaurus
			//on recherche dans la langue de l'interface ou dans la langue par défaut du thesaurus
			$requete.= "catdef.num_thesaurus, ";
			$requete.= "catdef.num_noeud as categ_id, ";
			$requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie , catlg.libelle_categorie ) as categ_libelle, ";
			$requete.= "if (catlg.num_noeud is null, catdef.index_categorie , catlg.index_categorie ) as indexcat ";
			if ($where_term) $requete.= ", if (catlg.num_noeud is null, ".$members_catdef["select"].", ".$members_catlg["select"].") as pert ";
			$requete.= ", catdef.comment_public ";
			$requete.= "from thesaurus ";
			$requete.= "left join categories as catdef on id_thesaurus=catdef.num_thesaurus and catdef.langue=thesaurus.langue_defaut ";
			$requete.= "left join categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."' ";
			if ($where_term) $requete.= "where if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") ";
			$requete.= "group by categ_libelle, catdef.num_thesaurus ";
			$requete.= "order by ";
			if ($where_term) $requete.= "pert desc, ";
			$requete.= "catdef.num_thesaurus, indexcat asc ";
			$requete.= "limit ".$this->offset.",".$this->n_per_page;
		}
		$resultat=pmb_mysql_query($requete);
		
		//On récupère le nombre de résultat
		if(!$this->n_total){
			$qry = "SELECT FOUND_ROWS() AS NbRows";
			if($resnum = pmb_mysql_query($qry)){
				$this->n_total=pmb_mysql_result($resnum,0,0);
			}
		}
		
		$res="<b>";
		if ($this->search_term!='') {
		    $res.=$msg['term_search_found_term'].'<i>'.htmlentities($this->search_term_origin,ENT_QUOTES,$charset);
		} else {
		    $res.='<i>'.$msg['term_search_all_terms'];
		}
		$res.="</i></b>\n";

		//Navigateur de page
		if($this->n_total) {
		    $res.="<br /><span style='text-align:right;'>".$this->page_navigator()."</span><br /><br />";
		} else {
		    $res.="<br /><br /><span style='text-align:right;'><i>".$msg['term_no_results']."</span><br /><br />";
		}

		//Affichage des termes trouvés
		$class='colonne2';
		while ($r=pmb_mysql_fetch_object($resultat)) {
			$show=1;
			//S'il n'y a qu'un seul résultat, vérification que ce n'est pas un terme masqué
			if (($r->nb == 1) && (!$this->keep_tilde)) {
			    $t_test = new category($r->categ_id);
				if (($t_test->is_under_tilde)&&(!$t_test->voir_id)) $show=0;
			}
			if ($show) {
				// Si il y a présence d'un commentaire affichage du layer
				$result_com = categorie::zoom_categ($r->categ_id, $r->comment_public);
				$res.="<div class='".$class."'>";
				if ($r->nb>1) $nbre_termes ='('.$r->nb.') ';
				else  $nbre_termes ='' ;
				$args = 'term='.rawurlencode($r->categ_libelle).'&id_thes='.$r->num_thesaurus.'&'.$this->base_query;
				$res.= $nbre_termes."<a href=\"".$this->url_for_term_show.'?'.$args."\" data-evt-args=\"".$args."\" target=\"term_show\" ".$result_com['java_com'].">";
				if ($this->id_thes == -1) {	 //le nom du thesaurus n'est pas affiché si 1 seul thesaurus
					$thesaurus = new thesaurus($r->num_thesaurus);
					$res.= '['.htmlentities(addslashes($thesaurus->getLibelle()),ENT_QUOTES, $charset).'] ';
				}
				$res.= htmlentities($r->categ_libelle,ENT_QUOTES,$charset)."</a>".$result_com['zoom']."<br />\n";
				$res.='</div>';
				if ($class=='colonne2') $class='colonne_suite';
				else $class='colonne2';
			}
		}
		if ($class=='colonne_suite') {
		    $res.="<div class=\"colonne_suite\"></div>\n";
		}
		return $res;
    }
}