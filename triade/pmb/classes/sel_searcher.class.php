<?php
use Pmb\Common\Library\Navbar\Navbar;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_searcher.class.php,v 1.18.8.2 2025/02/27 09:51:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de recherche pour selecteurs
global $base_path, $class_path, $msg;
require_once("$class_path/analyse_query.class.php");
require_once("$class_path/sel_display.class.php");
require_once("$base_path/selectors/templates/sel_searcher_templates.tpl.php");

//Classe générique de recherche
if(!defined('AUT_LIST')) define("AUT_LIST",1);
if(!defined('ELT_LIST'))define("ELT_LIST",2);
if(!defined('AUT_SEARCH'))define("AUT_SEARCH",3);

$tab_query=array();
$tab_query['notice']=$msg['selector_lib_noti'];
$tab_query['bulletin']=$msg['selector_lib_bull'];
$tab_query['article']=$msg['selector_lib_art'];
$tab_query['abt']=$msg['selector_lib_abt'];
$tab_query['frais']=$msg['selector_lib_frais'];
$tab_query['panier']=$msg['selector_lib_caddie'];
$tab_query['sug']=$msg['selector_lib_sug'];

$tab_autorun=array();
$tab_autorun['notice']=false;
$tab_autorun['bulletin']=false;
$tab_autorun['article']=false;
$tab_autorun['abt']=true;
$tab_autorun['frais']=false;
$tab_autorun['panier']=false;
$tab_autorun['sug']=false;

class sel_searcher {

	public $etat;								//Etat de la recherche
	public $page;								//Page courante de la recherche
	public $nbresults;							//Nombre de résultats de la dernière recherche
	public $nbepage;
	public $aut_id;							//Numéro d'autorité pour la recherche
	public $aut_type;							//Type d'autorité pour la recherche
	public $store_form;						//Formulaire contenant les infos de navigation plus des champs pour la recherche
	public $first_search_result;
	public $direct = 0;

	//Elements obligatoires
	public $base_url = '';						//url de base pour les menus, 	
	public $tab_choice=array();				//Liste des choix a effectuer dans le menu
	
	public $elt_f_list = '';					//Formulaire d'affichage des elements
	public $elt_b_list = '';					//Affichage Debut de liste elements
	public $elt_e_list = '';					//Affichage Fin de liste elements
	public $elt_r_list = '';					//Affichage ligne element
	public $elt_r_list_values = array();		//tableau des elements a afficher dans la liste
	public $action = '';						//Action a transmettre pour retour des parametres
	public $action_values = array();			//tableau des elements à modifier dans l'action
	public $back_script = '';					//Script a executer sur selection d'un element
	public $back_script_show_all = '';			//Script a executer sur bouton "Afficher tous les résultats"
	public $back_script_order = '';			//Script a executer pour trier certains elements
	
	public $aut_b_list = '';					//Affichage Debut de liste autorites
	public $aut_e_list = '';					//Affichage Fin de liste autorites
	public $aut_r_list = '';					//Affichage ligne autorite
	public $aut_r_list_values = array();		//tableau des autorites a afficher dans la liste
	
	//Constructeur
	public function __construct($base_url) {
		global $etat,$aut_type,$aut_id,$page;
			
		$this->base_url = $base_url;
		$this->etat = $etat;
		$this->aut_type = $aut_type;
		$this->aut_id = $aut_id;
		$this->page = intval($page);
		
		//$this->run();
	}

	
	public function run() {
		$this->set_menu();
		if (!$this->etat) {
			$this->show_form();
		} else {
			switch ($this->etat) {
				case "first_search":
					$r=$this->make_first_search();
					$this->first_search_result=$r;
					switch ($r) {
						case AUT_SEARCH:
							$this->etat="aut_search"; 
							$this->direct=1;
							$this->make_aut_search();
							$this->make_store_form();
							$this->aut_store_search();
							$this->aut_elt_list();
							$this->pager();
							break;
						case AUT_LIST:
							$this->make_store_form();
							$this->store_search();
							$this->aut_list();
							$this->pager();
							break;
						case ELT_LIST:
							$this->make_store_form();
							$this->store_search();
							$this->elt_list();
							$this->pager();
							break;
					}
					break;
				case "aut_search":
					$this->make_aut_search();
					$this->make_store_form();
					$this->aut_store_search();
					$this->aut_elt_list();
					$this->pager();
					break;
			}
		}
	}

	
	public function set_menu() {
		global $charset;
		global $form_query, $nav_bar, $other_query;
		global $tab_query,$tab_autorun;

		$menu_query = $nav_bar;
		foreach($this->tab_choice as $typ_query) {
			if (array_key_exists($typ_query, $tab_query)) {
				$menu_query = str_replace('<!-- other_query -->', $other_query.'<!-- other_query -->', $menu_query);
				$menu_query = str_replace('!!typ_query!!', $typ_query.($tab_autorun[$typ_query]==true?"&autorun=1":""), $menu_query);
				$menu_query = str_replace('!!lib!!', htmlentities($tab_query[$typ_query], ENT_QUOTES, $charset),  $menu_query);
				if ($typ_query==$this->cur_typ_query) {
					$menu_query = str_replace('!!class!!', "class='sel_navbar_current'",  $menu_query);
				} else {
					$menu_query = str_replace('!!class!!', '',  $menu_query);
				}
			}
		}
		$form_query = str_replace('!!menu_query!!', $menu_query, $form_query);
	}	

	
	public function show_form() {

		global $charset;
		global $form_query, $elt_query, $extended_query;
		
		$form_query = str_replace("!!elt_query!!", htmlentities(stripslashes($elt_query),ENT_QUOTES, $charset), $form_query);
		$form_query = str_replace("<!-- extended_query -->", $extended_query, $form_query );
		$form_query = str_replace("!!action_url!!", $this->base_url."&typ_query=".$this->cur_typ_query, $form_query);
		print $form_query;
	}
	
	public function get_nb_per_page() {
	    global $nb_per_page, $nb_per_page_select;
	    
	    if (!$nb_per_page) {
	        $nb_per_page=$nb_per_page_select;
	    }
	    return $nb_per_page;
	}
	
	public function get_start_page() {
	    if(empty($this->page)) {
	        $this->page = 1;
	    }
	    return ($this->page-1)*$this->get_nb_per_page();
	}
	
	public function get_query_limit() {
	    return " limit ".$this->get_start_page().", ".$this->get_nb_per_page()." ";
	}
	
	public function pager() {
		global $msg;
		
		if (!$this->nbresults) {
		    return;
		}
		if (!$this->page) {
		    $current_page = 1;
		} else {
		    $current_page = $this->page;
		}
		$navbar = new Navbar($current_page, $this->nbresults, $this->get_nb_per_page());
	    $navbar->setHiddenFormName('store_search');
		print "<div id='results_pager' class='center'>".$navbar->render()."</div>";
	}

	protected function get_button_show_all() {
	    global $msg, $charset;
	    global $results_show_all;
	    
	    if(!$results_show_all && ($this->nbepage!=1)){
	        return "&nbsp;&nbsp;&nbsp;<input type='button' class='bouton_small' onclick='results_show_all();' name='searcher_results_show_all' value='".htmlentities($msg['searcher_results_show_all'], ENT_QUOTES, $charset)."'>";
	    }
	}
	
	protected function get_button_check_uncheck() {
	    global $msg, $charset;
	    
	    return "
        <div class='row'>
			<input type='button' class='bouton_small' onclick='check_uncheck(1);' id='searcher_results_check_all' name='searcher_results_check_all' value='".htmlentities($msg['searcher_results_check_all'],ENT_QUOTES,$charset)."'>
			&nbsp;&nbsp;
			<input type='button' class='bouton_small' onclick='add_selection();' id='searcher_results_add_selection' name='searcher_results_add_selection' value='".htmlentities($msg['searcher_results_add_selection'],ENT_QUOTES,$charset)."'>
		</div>";
	}
	
	public function make_store_form() {
		$this->store_form="<form name='store_search' action='".$this->base_url."&typ_query=".$this->cur_typ_query."' method='post' style='display:none'>
		<input type='hidden' name='aut_type' value='".$this->aut_type."'/>
		<input type='hidden' name='aut_id' value='".$this->aut_id."'/>
		<input type='hidden' name='etat' value='".$this->etat."'/>
		<input type='hidden' name='page' value='".$this->page."'/>
		!!first_search_variables!!
		</form>";
	}

	public function show_elt() {
	}

	
	public function make_first_search() {
		//A surcharger par la fonction qui fait la première recherche après la soumission du formulaire de recherche
		//La fonction renvoie AUT_LIST (le résultat de la recherche est une liste d'autorité)
		//ou ELT_LIST (le résultat de la recherche est une liste d'élements)
		//La fonction doit mettre à jour le nombre de résultats dans $this->nbresults
	}

	
	public function make_aut_search() {
		//A surcharger par la fonction qui fait la recherche des éléments à partir d'un numéro d'autorité (stoqué dans $this->aut_id)
		//La fonction doit mettre à jour le nombre de résultats dans $this->nbresults
	}

	
	public function store_search() {
		//A surcharger par la fonction qui écrit les variables du formulaire "store_search" pour stoquer les champs de recherche
		//En liste de résultat de la première recherche. Il faut remplacer la chaine "!!first_search_variables!!" dans $this->store_form
		global $elt_query;
		global $charset;
		$champs="<input type='hidden' name='elt_query' value='".htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	
	public function aut_store_search() {
		//A surcharger par la fonction qui écrit les variables du formulaire "store_search" pour stoquer les champs de recherche
		//En liste de résultat de la première recherche. Il faut remplacer la chaine "!!first_search_variables!!" dans $this->store_form
		global $elt_query;
		global $charset;
		$champs="<input type='hidden' name='elt_query' value='".htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	
	public function aut_list() {
		//A surcharger par la fonction qui affiche la liste des autorités issues de la première recherche
	}

	
	public function elt_list() {
		//A surcharger par la fonction qui affiche la liste des éléments issues de la première recherche
	}

	
	public function aut_elt_list() {
		//A surcharger par la fonction qui affiche la liste des éléments sous l'autorité $this->aut_id
	}

	
	public function rec_env() {
		//A surcharger par la fonction qui enregistre
	}
}


class sel_searcher_notice_mono extends sel_searcher {
	
	public $t_query;
	public $cur_typ_query='notice';
	
	
	public function make_first_search() {
		global $msg;
		global $elt_query;		
		global $notice_statut_query, $doctype_query;
		global $results_show_all;
		
		$restrict = "niveau_biblio='m' ";				
		if ($notice_statut_query !='-1') {
			$restrict.= "and statut='".$notice_statut_query."' ";
		}
		
		if ($doctype_query !='-1') {
			$restrict.= "and typdoc='".$doctype_query."' ";
		}
		$suite_rqt="or code='".$elt_query."' "; 
		$isbn_verif=traite_code_isbn(stripslashes($elt_query));
		if (isISBN($isbn_verif)) {
			$suite_rqt.="or code='".formatISBN($isbn_verif,13)."' ";
			$suite_rqt.="or code='".formatISBN($isbn_verif,10)."' ";
			
			$q_count = "select count(*) from notices where ".$restrict." and (0 ".$suite_rqt.")";
			$r_count = pmb_mysql_query($q_count);
			$n_count = pmb_mysql_result($r_count,0,0);
			$this->nbresults = $n_count;
			
			$q_list = "select notice_id from notices where ".$restrict." and (0 ".$suite_rqt.")";
			if(!$results_show_all){
				$q_list.= $this->get_query_limit(); 
			}
			$r_list = pmb_mysql_query($q_list);
			$this->t_query=$r_list;
			if(!$results_show_all){
			    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
			}else{
				$this->nbepage=1;
			}
			
		}else{
			$aq=new analyse_query(stripslashes($elt_query));
			if ($aq->error) {
				$this->show_form();
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				return ;
			}else{
				$q_members = $aq->get_query_members("notices","index_wew","index_sew","notice_id");
					
				$q_count = "select count(*) from notices where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.")";
				$r_count = pmb_mysql_query($q_count);
				$n_count = pmb_mysql_result($r_count,0,0);
				$this->nbresults = $n_count;
				
				$q_list = "select notice_id, ".$q_members['select']." as pert from notices where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.") ".$q_members['post'];
				if(!$results_show_all){
				    $q_list.= $this->get_query_limit(); 
				}
				$r_list = pmb_mysql_query($q_list);
				$this->t_query=$r_list;
				if(!$results_show_all){
				    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
				}else{
					$this->nbepage=1;
				}
			}
		}
		return ELT_LIST;
	}

	
	public function store_search() {

		global $elt_query;
		global $notice_statut_query, $doctype_query;
		global $charset;
		
		$champs="<input type='hidden' name='elt_query' value='".htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='notice_statut_query' value='".htmlentities(stripslashes($notice_statut_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='doctype_query' value='".htmlentities(stripslashes($doctype_query),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}
	
	public function elt_list() {

		global $msg, $charset;
		global $elt_query;
		
		$research = '<b>'.htmlentities($msg['selector_lib_noti'],ENT_QUOTES,$charset).'</b>&nbsp;'.htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset);
	
		$this->show_form();
		if ($this->nbresults) {
			$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
			$research.= $this->get_button_show_all();
			$this->elt_b_list = str_replace('!!research!!', $research, $this->elt_b_list);
			print $this->elt_b_list;
			//Boutons check/uncheck/add selection
			print $this->get_button_check_uncheck();
			print "<form name='searcher_results_check_form'>";
			// on lance la requête
			$list = '';
			while ($nz = pmb_mysql_fetch_object($this->t_query)) {
				// notice de monographie
				$mono = new sel_mono_display($nz->notice_id,$this->base_url,'sel_searcher_select_');
				$mono->action=$this->action;
				$mono->action_values=$this->action_values;
				$mono->doForm();
				$list.= $this->elt_r_list;
				if (count($this->elt_r_list_values) ) {
					foreach($this->elt_r_list_values as $v) {
						$list = str_replace("!!$v!!", $mono->$v, $list);
					}
				}
			}
			print $list;
			// fin de liste
			print "</form>";
			print $this->elt_e_list;
			print $this->back_script;
			print $this->back_script_show_all;
		} else {
			error_message_history($msg[357], $msg[1915],1);
		}
	}
}

class sel_searcher_notice_article extends sel_searcher {
	
	public $t_query;
	public $cur_typ_query='article';
	
	public function make_first_search() {
		global $msg;
		global $elt_query;		
		global $notice_statut_query, $doctype_query;
		global $results_show_all;
		
		$suite_rqt = "";
		$restrict = "niveau_biblio='a' ";				
		if ($notice_statut_query !='-1') {
			$restrict.= "and statut='".$notice_statut_query."' ";
		}
		
		if ($doctype_query !='-1') {
			$restrict.= "and typdoc='".$doctype_query."' ";
		}
		
		$aq=new analyse_query(stripslashes($elt_query));
		if ($aq->error) {
			$this->show_form();
			error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
		} else {
			$q_members = $aq->get_query_members("notices","index_wew","index_sew","notice_id");			
			$q_count = "select count(*) from notices where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.")";
			$r_count = pmb_mysql_query($q_count);
			$n_count = pmb_mysql_result($r_count,0,0);
			$this->nbresults = $n_count;
			
			$q_list = "select notice_id, ".$q_members['select']." as pert from notices where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.") ".$q_members['post'];
			if(!$results_show_all){
			    $q_list.= $this->get_query_limit(); 
			}
			$r_list = pmb_mysql_query($q_list);
			$this->t_query=$r_list;
			if(!$results_show_all){
			    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
			}else{
				$this->nbepage=1;
			}
			return ELT_LIST;
		}
	}
	
	public function store_search() {
		global $elt_query;
		global $notice_statut_query, $doctype_query;
		global $charset;
		
		$champs="<input type='hidden' name='elt_query' value='".htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='notice_statut_query' value='".htmlentities(stripslashes($notice_statut_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='doctype_query' value='".htmlentities(stripslashes($doctype_query),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}
	
	
	public function elt_list() {

		global $msg, $charset;
		global $elt_query;
		
		$research = '<b>'.htmlentities($msg['selector_lib_noti'],ENT_QUOTES,$charset).'</b>&nbsp;'.htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset);
	
		$this->show_form();
		if ($this->nbresults) {
			$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
			$research .= $this->get_button_show_all();
			$this->elt_b_list = str_replace('!!research!!', $research, $this->elt_b_list);
			print $this->elt_b_list;
			//Boutons check/uncheck/add selection
			print $this->get_button_check_uncheck();
			print "<form name='searcher_results_check_form'>";
			// on lance la requête
			while(($nz=pmb_mysql_fetch_object($this->t_query))) {
				// notice d'article
				$art = new sel_article_display($nz->notice_id,$this->base_url,'sel_searcher_select_');
				$art->action=$this->action;
				$art->action_values=$this->action_values;
				$art->doForm();
				$list.= $this->elt_r_list;
				if (count($this->elt_r_list_values) ) {
					foreach($this->elt_r_list_values as $v) {
						$list = str_replace("!!$v!!", $art->$v, $list);
					}
				}
			}
			print $list;
			// fin de liste
			print "</form>";
			print $this->elt_e_list;
			print $this->back_script;
			print $this->back_script_show_all;
		} else {
			error_message_history($msg[357], $msg[1915],1);
		}
	}
}


class sel_searcher_bulletin extends sel_searcher {
	
	public $t_query;
	public $cur_typ_query='bulletin';
	
	public function make_first_search() {
		global $msg;
		global $elt_query;
		global $results_show_all;
		
		$restrict = "niveau_biblio='s' ";				
		$restrict.= "and bulletin_notice=notice_id ";
		
		$suite_rqt="or code='".$elt_query."' ";
		
		$issn_verif=traite_code_ISSN(stripslashes($elt_query));
		if (isISSN(stripslashes($elt_query))) {
			$suite_rqt.=" or code='".$issn_verif."' ";
			$q_count = "select count(distinct notice_id) from notices, bulletins where ".$restrict." and (0 ".$suite_rqt.")";
			$r_count = pmb_mysql_query($q_count);
			$n_count = pmb_mysql_result($r_count,0,0);
			$this->nbresults = $n_count;
			
			$q_list = "select distinct(notice_id) from notices, bulletins where ".$restrict." and (0 ".$suite_rqt.")";
			if(!$results_show_all){
			    $q_list .= $this->get_query_limit(); 
			}
			$r_list = pmb_mysql_query($q_list);
			$this->t_query=$r_list;
			if(!$results_show_all){
			    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
			}else{
				$this->nbepage=1;
			}
		}else{
			$aq=new analyse_query(stripslashes($elt_query));
			if ($aq->error) {
				$this->show_form();
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				return ;
			} else {
				$q_members = $aq->get_query_members("notices","index_wew","index_sew","notice_id");	
				$q_count = "select count(distinct notice_id) from notices, bulletins where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.")";
				$r_count = pmb_mysql_query($q_count);
				$n_count = pmb_mysql_result($r_count,0,0);
				$this->nbresults = $n_count;
				
				$q_list = "select distinct(notice_id), ".$q_members['select']." as pert from notices, bulletins where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.") ".$q_members['post'];
				if(!$results_show_all){
				    $q_list.= $this->get_query_limit(); 
				}
				$r_list = pmb_mysql_query($q_list);
				$this->t_query=$r_list;
				if(!$results_show_all){
				    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
				}else{
					$this->nbepage=1;
				}
			}
		}
		return AUT_LIST;
	}
	
	public function make_aut_search() {
		global $results_show_all;
			
		switch ($this->aut_type) {
			case 'perio':
				$q_count="select count(*) from bulletins where bulletin_notice='".$this->aut_id."' ";
				$q_list="select bulletin_id from bulletins where bulletin_notice='".$this->aut_id."' order by date_date desc, bulletin_numero desc";
				if(!$results_show_all){
				    $q_list.= $this->get_query_limit();
				}
				break;
		}
		$r_count = pmb_mysql_query($q_count);
		$n_count = pmb_mysql_result($r_count,0,0);
		$this->nbresults=$n_count;
		
		$r_list = pmb_mysql_query($q_list);
		$this->t_query=$r_list;
		if(!$results_show_all){
		    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
		}else{
			$this->nbepage=1;
		}
	}
	
	public function aut_list() {

		global $msg, $charset;
		global $elt_query;
		
		$research = '<b>'.htmlentities($msg['771'],ENT_QUOTES,$charset).'</b>&nbsp;'.htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset);
	
		$this->show_form();
		if ($this->nbresults) {
			$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
			$research .= $this->get_button_show_all();
			$this->aut_b_list = str_replace('!!research!!', $research, $this->aut_b_list);
			print $this->aut_b_list;
			$list = '';
			// on lance la requete
			while(($nz=pmb_mysql_fetch_object($this->t_query))) {
				// notice de perio
				$perio = new sel_serial_display($nz->notice_id, $this->base_url);
				$perio->action="<a href='".$this->base_url."&typ_query=".$this->cur_typ_query."&etat=aut_search&aut_type=perio&aut_id=!!aut_id!!' >!!display!!</a>";
				$perio->doForm();
				$list.= $this->aut_r_list;
				if (count($this->aut_r_list_values)) {
					foreach($this->aut_r_list_values as $v) {
						$list=str_replace("!!$v!!", $perio->$v, $list);
					}
				}
			}
			print $list;
			print $this->back_script_show_all;
			// fin de liste
			print $this->aut_e_list;
		} else {
			error_message_history($msg[357], $msg[1915], 1);
		}
	}
	
	public function aut_elt_list() {
			
		global $msg, $charset;
		global $elt_query;

		$research .= '<b>'.htmlentities($msg['selector_lib_bull'],ENT_QUOTES,$charset).'</b>&nbsp;'.htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset);
	
		$this->show_form();
		if ($this->nbresults) {
			$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
			$research .= $this->get_button_show_all();
			$this->elt_b_list = str_replace('!!research!!', $research, $this->elt_b_list);
			print $this->elt_b_list;
			//Boutons check/uncheck/add selection
			print $this->get_button_check_uncheck();
			print "<form name='searcher_results_check_form'>";
			// on lance la requête
			while(($nz=pmb_mysql_fetch_object($this->t_query))) {
				// bulletin
				$bull = new sel_bulletin_display($nz->bulletin_id, $this->base_url,'sel_searcher_select_');
				$bull->action=$this->action;
				$bull->action_values=$this->action_values;
				$bull->doForm();
				$list.= $this->elt_r_list;
				if (count($this->elt_r_list_values)) {
					foreach($this->elt_r_list_values as $v) {
						$list = str_replace("!!$v!!", $bull->$v, $list);
					}
				}
			}
			print $list;
			// fin de liste
			print "</form>";
			print $this->elt_e_list;
			print $this->back_script;
			$this->back_script_show_all = str_replace('!!base_url!!',$this->base_url,$this->back_script_show_all);
			$this->back_script_show_all = str_replace('!!cur_typ_query!!',$this->cur_typ_query,$this->back_script_show_all);
			$this->back_script_show_all = str_replace('!!aut_id!!',$this->aut_id,$this->back_script_show_all);
			print $this->back_script_show_all;
		} else {
			error_message_history($msg[357], $msg[1915], 1);
		}
	}
}

class sel_searcher_frais extends sel_searcher {
	
	public $t_query;
	public $cur_typ_query='frais';
	
	public function make_first_search() {

		global $msg;
		global $elt_query;
		global $results_show_all;
		
		$aq=new analyse_query(stripslashes($elt_query));
		if ($aq->error) {
			$this->show_form();
			error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
		} else {
			$q_count=$aq->get_query_count('frais','libelle','index_libelle','id_frais');
			$r_count = pmb_mysql_query($q_count);
			$n_count = pmb_mysql_result($r_count,0,0);
			$this->nbresults = $n_count;
			
			if(!$results_show_all){
				$q_list = $aq->get_query('frais','libelle','index_libelle','id_frais', $this->get_start_page() , $this->get_nb_per_page()); 
			}else{
				$q_list = $aq->get_query('frais','libelle','index_libelle','id_frais');
			}
			$r_list = pmb_mysql_query($q_list);
			$this->t_query=$r_list;
			if(!$results_show_all){
			    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
			}else{
				$this->nbepage=1;
			}
			return ELT_LIST;
		}
	}

	public function elt_list() {
		global $msg, $charset;
		global $elt_query;
		
		$research = '<b>'.htmlentities($msg['selector_lib_frais'],ENT_QUOTES,$charset).'</b>&nbsp;'.htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset);
	
		$this->show_form();
		if ($this->nbresults) {
			$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
			$research .= $this->get_button_show_all();
			$this->elt_b_list = str_replace('!!research!!', $research, $this->elt_b_list);
			print $this->elt_b_list;
			//Boutons check/uncheck/add selection
			print $this->get_button_check_uncheck();
			print "<form name='searcher_results_check_form'>";
			// on lance la requête
			$list = '';
			while(($nz=pmb_mysql_fetch_object($this->t_query))) {
				
				// frais annexes
				$frais = new sel_frais_display($nz->id_frais, $this->base_url,'sel_searcher_select_');
				$frais->action=$this->action;
				$frais->action_values=$this->action_values;
				$frais->doForm();
				$list.= $this->elt_r_list;
				if (count($this->elt_r_list_values) ) {
					foreach($this->elt_r_list_values as $v) {
						$list = str_replace("!!$v!!", $frais->$v, $list);
					}
				}
			}
			print $list;
			// fin de liste
			print "</form>";
			print $this->elt_e_list;
			print $this->back_script;
			print $this->back_script_show_all;
		} else {
			error_message_history($msg[357], $msg[1915], 1);
		}
	}
}

class sel_searcher_abt extends sel_searcher {
	
	public $t_query;
	public $cur_typ_query='abt';
	
	
	public function make_first_search() {

		global $msg;
		global $elt_query;
		global $location_query, $date_ech_query;
		global $results_show_all;
		global $specific_order;
		
		$restrict = "1 ";
		if ($location_query!='-1') {
			$restrict.= "and location_id='".$location_query."' ";
		}
		$restrict.= "and notice_id=num_notice ";
		
		if ($date_ech_query!='-1') {
			$restrict.= "and date_fin < '".$date_ech_query."' ";	
		}
		
		$suite_rqt="or code='".$elt_query."' ";
		
		$issn_verif=traite_code_ISSN(stripslashes($elt_query));
		if (isISSN(stripslashes($elt_query))) {
			$suite_rqt.=" or code='".$issn_verif."' ";			
			$q_count = "select count(abt_id) from notices, abts_abts where ".$restrict." and (0 ".$suite_rqt.")";
			$r_count = pmb_mysql_query($q_count);
			$n_count = pmb_mysql_result($r_count,0,0);
			$this->nbresults = $n_count;
			
			if ((!$specific_order) || ($specific_order==1)) {
				$order_by = " ORDER BY 1,3";
			} elseif($specific_order==2){
				$order_by = " ORDER BY 1 DESC,3 DESC";
			} elseif ($specific_order == 3) {
				$order_by = " ORDER BY date_fin";
			} elseif ($specific_order == 4) {
				$order_by = " ORDER BY date_fin DESC";
			}
			
			$q_list = "select tit1, abt_id, abt_name from notices, abts_abts where ".$restrict." and (0 ".$suite_rqt.")".$order_by;
			if(!$results_show_all){
			    $q_list .= $this->get_query_limit();
			} 
			$r_list = pmb_mysql_query($q_list);
			$this->t_query=$r_list;
			if(!$results_show_all){
			    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
			}else{
				$this->nbepage=1;
			}
			
		}else{
			$aq=new analyse_query(stripslashes($elt_query));
			if ($aq->error) {
				$this->show_form();
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				return ;
			} else {
				$q_members = $aq->get_query_members("notices","index_wew","index_sew","abt_id");			
				$q_count = "select count(abt_id) from notices, abts_abts where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.")";
				$r_count = pmb_mysql_query($q_count);
				$n_count = pmb_mysql_result($r_count,0,0);
				$this->nbresults = $n_count;
				
				$q_list = "select abt_id, ".$q_members['select']." as pert from notices, abts_abts where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.") ".$q_members['post'];
				//Comportement d'origine
				if (!$specific_order) {
					//On filtre sur la pertinence
					if(!$results_show_all){
					    $q_list.= $this->get_query_limit(); 
					}
					//on surcharge la requête d'origine pour ajouter trier sur titre de périodique
					$new_q_list="
							SELECT tit1, a.abt_id, a.abt_name, pert 
							FROM notices n, abts_abts a, (".$q_list.") as q 
							WHERE q.abt_id=a.abt_id AND a.num_notice=n.notice_id 
							ORDER BY 1 ASC,3 DESC";
				} elseif ($specific_order==1) { //Tri notice/abo croissant sans pertinence
					$q_list = "select abt_id, ".$q_members['select']." as pert from notices, abts_abts where ".$restrict." and (".$q_members["where"]." ".$suite_rqt.") ".$q_members['post'];
					//on surcharge la requête d'origine pour ajouter trier sur titre de périodique
					$new_q_list="
							SELECT tit1, a.abt_id, a.abt_name, pert 
							FROM notices n, abts_abts a, (".$q_list.") as q 
							WHERE q.abt_id=a.abt_id AND a.num_notice=n.notice_id 
							ORDER BY 1 ASC,3 DESC";
					if(!$results_show_all){
					    $new_q_list.= $this->get_query_limit();
					}
				} elseif ($specific_order==2) { //Tri notice/abo décroissant sans pertinence
					$new_q_list="
							SELECT tit1, a.abt_id, a.abt_name, pert
							FROM notices n, abts_abts a, (".$q_list.") as q
							WHERE q.abt_id=a.abt_id AND a.num_notice=n.notice_id
							ORDER BY 1 DESC,3 ASC";
					if(!$results_show_all){
					    $new_q_list.= $this->get_query_limit();
					}
				} elseif ($specific_order == 3) { //Date échéance croissante
					$new_q_list="
							SELECT tit1, a.abt_id, a.abt_name, pert 
							FROM notices n, abts_abts a, (".$q_list.") as q 
							WHERE q.abt_id=a.abt_id AND a.num_notice=n.notice_id 
							ORDER BY a.date_fin";
					if(!$results_show_all){
					    $new_q_list.= $this->get_query_limit();
					}
				} elseif ($specific_order == 4) { //Date échéance décroissante
					$new_q_list="
							SELECT tit1, a.abt_id, a.abt_name, pert 
							FROM notices n, abts_abts a, (".$q_list.") as q 
							WHERE q.abt_id=a.abt_id AND a.num_notice=n.notice_id 
							ORDER BY a.date_fin DESC";
					if(!$results_show_all){
					    $new_q_list.= $this->get_query_limit();
					}
				}
				$r_list = pmb_mysql_query($new_q_list);
				$this->t_query=$r_list;
				if(!$results_show_all){
				    $this->nbepage=ceil($this->nbresults/$this->get_nb_per_page());
				}else{
					$this->nbepage=1;
				}
			}
		}
		return ELT_LIST;
	}

	public function store_search() {

		global $elt_query;
		global $location_query, $date_ech_query;
		global $charset;
		global $specific_order;
		
		$champs="<input type='hidden' name='elt_query' value='".htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='location_query' value='".htmlentities(stripslashes($location_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='date_ech_query' value='".htmlentities(stripslashes($date_ech_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='specific_order' value='".htmlentities(stripslashes($specific_order),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}
	
	public function elt_list() {

		global $msg, $charset;
		global $elt_query;
		global $specific_order;
		global $pmb_serialcirc_active;

		$research = '<b>'.htmlentities($msg['selector_lib_abt'],ENT_QUOTES,$charset).'</b>&nbsp;'.htmlentities(stripslashes($elt_query),ENT_QUOTES,$charset);
	
		$this->show_form();
		if ($this->nbresults) {
			$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
			$research .= $this->get_button_show_all();
			$this->elt_b_list = str_replace('!!research!!', $research, $this->elt_b_list);
			print $this->elt_b_list;
			//Boutons check/uncheck/add selection
			print $this->get_button_check_uncheck();
			print "<form name='searcher_results_check_form'>";
			//entete pour trier
			print "<div class='row' style='margin-left:5px;'>";
			if ($specific_order==1) {
				$specific_order_new = 2;
			} else {
				$specific_order_new = 1;
			}
			print "<div class='colonne80'>
						<div class='notice-parent'>
							<b><a href='javascript:specific_order(".$specific_order_new.")'>".htmlentities($msg['selector_lib_abt'],ENT_QUOTES,$charset)."</a></b>
						</div>
				   </div>";
			if ($specific_order==3) {
				$specific_order_new = 4;
			} else {
				$specific_order_new = 3;
			}
			print "<div class='colonne10'>
						<div class='notice-parent'>
							<b><a href='javascript:specific_order(".$specific_order_new.")'>".htmlentities($msg['acquisition_abt_ech'],ENT_QUOTES,$charset)."</a></b>
						</div>
					</div>";
			if($pmb_serialcirc_active) {
    			print "<div class='colonne10'>
    						<div class='notice-parent'>
    							<b>".htmlentities($msg['acquisition_nb_recipients'],ENT_QUOTES,$charset)."</b>
    						</div>
    					</div>";
			} else {
			    print "<div class='colonne10'>&nbsp;</div>";
			}
			print "</div>";
			print $this->back_script_order;
			$list = '';
			// on lance la requête 
			while(($nz=pmb_mysql_fetch_object($this->t_query))) {
				// abonnement
				$abt = new sel_abt_display($nz->abt_id, $this->base_url,'sel_searcher_select_');
				$abt->action=$this->action;
				$abt->action_values=$this->action_values;
				$abt->doForm();
				$list.= $this->elt_r_list;
				if (count($this->elt_r_list_values)) {
					foreach($this->elt_r_list_values as $v) {
						$list = str_replace("!!$v!!", $abt->$v, $list);
					}
				}
			}
			print $list;
			// fin de liste
			print "</form>";
			print $this->elt_e_list;
			print $this->back_script;
			print $this->back_script_show_all;
		} else {
			error_message_history($msg[357], $msg[1915],1);
		}
	}	
}
