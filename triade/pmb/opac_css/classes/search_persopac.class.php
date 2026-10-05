<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_persopac.class.php,v 1.29 2024/04/22 14:29:39 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des recherches personnalisées

// inclusions principales
global $class_path, $include_path;
require_once("$include_path/templates/search_persopac.tpl.php");
require_once("$class_path/search.class.php");
require_once("$class_path/translation.class.php");

class search_persopac {
	public $id=0;
	public $name="";
	public $shortname="";
	public $buttonlabel="";
	public $query="";
	public $human="";
	public $directlink="";
	public $limitsearch="";
	public $order;
	public $empr_categ_restrict = array();
	public $url_base='./index.php?';
	public $search_persopac_list = array();
	public $directlink_user;
	public $directlink_user_form;


	// constructeur
	public function __construct($id=0) {
		$this->id = intval($id);
		if ($this->id) {
			$this->fetch_data();
		} else {
			$this->get_link();
		}
	}

	// récupération des infos en base
	public function fetch_data() {
		$result = pmb_mysql_query("SELECT * FROM search_persopac WHERE search_id='".$this->id."'");
		$row= pmb_mysql_fetch_object($result);
		$this->name=translation::get_text($this->id,"search_persopac","search_name",$row->search_name);
		$this->shortname=translation::get_text($this->id,"search_persopac","search_shortname",$row->search_shortname);
		$this->buttonlabel = $row->search_button_label ?? "";
		$this->query=$row->search_query;
		$this->human=$row->search_human;
		$this->directlink=$row->search_directlink;
		$this->limitsearch=$row->search_limitsearch;

		$this->empr_categ_restrict = array();
		$query  = "select id_categ_empr from search_persopac_empr_categ where id_search_persopac = ".$this->id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)){
			while ($row = pmb_mysql_fetch_object($result)) {
				$this->empr_categ_restrict[]=$row->id_categ_empr;
			}
		}
	}

	public function get_tab() {
		global $base_path;
		global $onglet_persopac;

		if ($this->directlink == 2) {
			$js_launch_search= "document.forms['search_form".$this->id."'].action = '".$base_path."/index.php?lvl=more_results&mode=extended&search_type_asked=extended_search';";
		} else {
			$js_launch_search= "";
		}

		$tab = "<li ".($onglet_persopac==$this->id ? " id='current' aria-current='page' " : "")." >
			<a href=\"javascript:".$js_launch_search."document.forms['search_form".$this->id."'].submit();\" data-search-perso-id='".$this->id."'>".($this->shortname ? $this->shortname : $this->name)."</a>";
		
		$my_search=new search();
		$backup_search=$my_search->serialize_search();
		$my_search->unserialize_search($this->query);
		$tab .= $my_search->make_hidden_search_form($this->url_base."search_type_asked=extended_search&onglet_persopac=".$this->id."&limitsearch=".$this->limitsearch,"search_form".$this->id);
		$my_search->destroy_global_env();
		$my_search->unserialize_search($backup_search);
		$tab .= "</li>";
		return $tab;
	}
	
	public function get_link() {
		global $opac_view_filter_class;
		$myQuery = pmb_mysql_query("SELECT search_persopac.*, group_concat(id_categ_empr) as categ_restrict FROM search_persopac left join search_persopac_empr_categ on id_search_persopac = search_id group by search_id order by search_order, search_name ");
		
		$this->search_persopac_list=array();
		$link="";
		$forms_search="";
		if(pmb_mysql_num_rows($myQuery)){
			$i=0;
			//on récupère la catégorie du lecteur...
			if($_SESSION['id_empr_session']){
				$req = "select empr_categ from empr where id_empr = ".$_SESSION['id_empr_session'];
				$res =pmb_mysql_query($req);
				if(pmb_mysql_num_rows($res)){
					$empr_categ = pmb_mysql_result($res,0,0);
				}else $empr_categ = 0;
			}else $empr_categ = 0;
			while(($r=pmb_mysql_fetch_object($myQuery))) {	
				if($opac_view_filter_class){
					if(!$opac_view_filter_class->is_selected("search_perso", $r->search_id))  continue; 
				}
				$empr_categ_restrict = ($r->categ_restrict != '' ? explode(",",$r->categ_restrict) : array());
				if(count($empr_categ_restrict) == 0 || in_array($empr_categ,$empr_categ_restrict)){
					if($r->search_directlink) {					
						$search_persopac = new search_persopac($r->search_id);
						$link.= $search_persopac->get_tab();
					}
					$this->search_persopac_list[$i] = new stdClass();		
					$this->search_persopac_list[$i]->id=$r->search_id;
					$this->search_persopac_list[$i]->name = translation::get_text($r->search_id,"search_persopac","search_name",$r->search_name);
					$this->search_persopac_list[$i]->shortname = translation::get_text($r->search_id,"search_persopac","search_shortname",$r->search_shortname);
					$this->search_persopac_list[$i]->query=$r->search_query;
					$this->search_persopac_list[$i]->human=$r->search_human;
					$this->search_persopac_list[$i]->directlink=$r->search_directlink;	
					$this->search_persopac_list[$i]->limitsearch=$r->search_limitsearch;				
					$i++;
				}			
			}	
		}
		$this->directlink_user=$link;
		$this->directlink_user_form=$forms_search;
		return true;
	}

	// fonction générant le form de saisie 
	public function do_list() {
		global $tpl_search_persopac_liste_tableau,$tpl_search_persopac_liste_tableau_ligne;
		$forms_search = '';
		// liste des lien de recherche directe
		$liste="";
		// pour toute les recherche de l'utilisateur
		$my_search=new search();
		for($i=0;$i<count($this->search_persopac_list);$i++) {
			if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";      
			//composer le formulaire de la recherche
			$my_search->unserialize_search($this->search_persopac_list[$i]->query);
			$forms_search.= $my_search->make_hidden_search_form($this->url_base."search_type_asked=extended_search&limitsearch=".$this->search_persopac_list[$i]->limitsearch,"search_form".$this->search_persopac_list[$i]->id);
			
	        $td_javascript="  onmousedown=\"javascript:document.forms['search_form".$this->search_persopac_list[$i]->id."'].submit();\" ";	
	        $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
	
	        $line = str_replace('!!td_javascript!!',$td_javascript , $tpl_search_persopac_liste_tableau_ligne);
	        $line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
	        $line = str_replace('!!pair_impair!!',$pair_impair , $line);
	
			$line =str_replace('!!id!!', $this->search_persopac_list[$i]->id, $line);
			$line = str_replace('!!name!!', $this->search_persopac_list[$i]->name, $line);
			$line = str_replace('!!human!!', $this->search_persopac_list[$i]->human, $line);		
			$line = str_replace('!!shortname!!', $this->search_persopac_list[$i]->shortname, $line);
			
			$liste.=$line;
		}
		 
		$tpl_search_persopac_liste_tableau = str_replace('!!lignes_tableau!!',$liste , $tpl_search_persopac_liste_tableau);
		return $forms_search.$tpl_search_persopac_liste_tableau;	
	}

	public function get_forms_list() {
		global $search_type_asked;
		
		if ((isset($search_type_asked) && $search_type_asked == 'external_search')) {
			return '';
		} 
		$my_search=new search();
		$forms_search='';
		$links='';
		for($i=0;$i<count($this->search_persopac_list);$i++) {
			//composer le formulaire de la recherche
		    $my_search->push();
			$my_search->unserialize_search($this->search_persopac_list[$i]->query);
			$forms_search.= $my_search->make_hidden_search_form($this->url_base."search_type_asked=extended_search&limitsearch=".$this->search_persopac_list[$i]->limitsearch,"search_form".$this->search_persopac_list[$i]->id);
			$libelle= $this->search_persopac_list[$i]->name;
			$links.="
				<span>
					<a href=\"javascript:document.forms['search_form".$this->search_persopac_list[$i]->id."'].submit();\" data-search-perso-id='".$this->search_persopac_list[$i]->id."'>$libelle</a>
					</span><br/>";
			$my_search->pull();
		}
		return $forms_search.$links;
	}

} // fin définition classe
