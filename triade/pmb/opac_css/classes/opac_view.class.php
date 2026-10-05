<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_view.class.php,v 1.27 2022/03/15 09:50:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des vues Opac
global $class_path;
require_once($class_path."/XMLlist.class.php");
require_once($class_path."/param_subst.class.php");
require_once($class_path."/opac_filters.class.php");

/*
 * A décommenter s'il y a un problème de contexte
 * Chargé plus tard pour que les templates soient chargés avec le contexte de la vue
 */
// require_once("$class_path/search.class.php");
// require_once("$class_path/quotas.class.php");

class opac_view {

	public $id=0; 							//Identifiant de la vue courante
	public $id_empr=0; 						//Identifiant de l'emprunteur
	public $name=''; 						//Nom de la vue
	public $requete=''; 					//Requete associee a la vue
	public $comment=''; 					//Commentaire de la vue
	public $param_subst; 					//Classe d'écrasement des paramètres substitués pour cette vue
	public $opac_filters; 					//Filtres des éléments de l'OPAC
	public $view_list_empr_default=0; 		//Identifiant de la vue par défaut pour l'utilisateur courant
	public $opac_views_list=array(); 		//Tableau des vues visibles en OPAC (opac_view_visible=1)
	public $selector; 						//Liste de sélection des vues visibles en OPAC (opac_view_visible=1)
	public $search_class;
	public $opac_view_wo_query = 0;			//pas de recherche mc associée
	protected $loaded_classes=false;

	// constructeur
	public function __construct($id=0,$id_empr=0) {
		// si id, allez chercher les infos dans la base
		$this->id_empr = intval($id_empr);
		if($id === "default_opac"){
		    $this->id = $id;
			if (!$this->check_right()){
				$this->build_env();
			}
		}else{
		    if ($id === "default") {
		        $id = 0;
		    }
			$this->id = intval($id);
			$this->build_env();
		}
	}

	/*
	 * génère l'environnement pour l'emprunteur
	 */
	public function build_env(){
		if(!count($this->opac_views_list)){
			$this->list_views();
		}
		
		if ($this->id === "default_opac") {
		    // Vue OPAC Classique
		    $this->id = 0;		    
		} elseif ($this->id === 0) {
		    // Vue activé par défaut
		    $this->id = $this->view_list_empr_default ?? 0;
		}
		
		if (!$this->check_right()) {
			$this->id = $this->view_list_empr_default;
		}
		
		if($this->id > 0 && $this->check_right()){
			$myQuery_defaut = pmb_mysql_query("SELECT * FROM opac_views WHERE opac_view_id=".$this->id);
			if(pmb_mysql_num_rows($myQuery_defaut)){
				$r_defaut= pmb_mysql_fetch_object($myQuery_defaut);
				$this->id=	$r_defaut->opac_view_id;
				$this->name=$r_defaut->opac_view_name;
				$this->requete=$r_defaut->opac_view_query;
				$this->comment=$r_defaut->opac_view_comment;
				$this->param_subst=new param_subst("opac", "opac_view",$this->id);
				//on récupère les messages de la vue OPAC si la langue est différente de celle par défaut
				/*if($this->get_parameter_value("opac", "default_lang") && $lang != $this->get_parameter_value("opac", "default_lang")) {
					if(function_exists('set_language')) {
						set_language($this->get_parameter_value("opac", "default_lang"));
					}
				}*/
				$this->set_parameters();
				$this->load_classes();
				$this->load_search_other_function();
				$this->opac_filters=new opac_filters($this->id);
				if (!$this->requete) {
					$this->opac_view_wo_query=1;
				}
				$this->regen();
			}
		}
	}
	
	/*
	 * Chargement
	 */
	protected function load_classes() {
		global $base_path;
		global $class_path;
		global $include_path;
		global $javascript_path;
		global $styles_path;
		global $msg,$charset;
		global $current_module;
		
		if(!$this->loaded_classes) {
			require_once($class_path."/search.class.php");
			require_once($class_path."/quotas.class.php");
			$this->loaded_classes = true;
		}
	}
	
	/*
	 * Chargement
	 */
	protected function load_search_other_function() {
		global $include_path, $opac_search_other_function;
		if($opac_search_other_function){
			require_once($include_path."/".$opac_search_other_function);
		}
	}
	
	/*
	 * regenere la recherche de restriction de la vue si necessaire
	 */
	public function regen() {
		if ($this->id && !$this->opac_view_wo_query) {
			$q = "select if((unix_timestamp(now()) - ifnull(unix_timestamp(opac_view_last_gen),0) - opac_view_ttl)>0,1,0) as opac_view_valid from opac_views where opac_view_id=".$this->id." ";
			$r = pmb_mysql_query($q);
			if (pmb_mysql_result($r,0,0)==1) {

				$q="update opac_views set opac_view_last_gen=now() where opac_view_id=".$this->id." ";
				pmb_mysql_query($q);

				$q="truncate table opac_view_notices_".$this->id;
				pmb_mysql_query($q);
				
				$this->search_class = new search("search_fields_gestion");
				$this->search_class->push();
				$this->search_class->unserialize_search($this->requete);
				$table=$this->search_class->make_search();
				$this->search_class->destroy_global_env();
				$this->search_class->pull();
				$q="INSERT ignore INTO opac_view_notices_".$this->id." (opac_view_num_notice) select notice_id from $table ";
				pmb_mysql_query($q);
				pmb_mysql_query("drop table $table");

			}
		}
	}

	/*
	 * Liste les vues disponibles
	 */
	public function list_views(){
		global $pmb_opac_view_activate;
		global $include_path;
		global $lang;

		$this->opac_views_list=array();
		$this->view_list_empr_default=0;
		//on reprend...
		if ($this->id_empr){
			$req="SELECT * FROM opac_views, opac_views_empr  where opac_view_visible!=0 and emprview_view_num=opac_view_id and emprview_empr_num=".$this->id_empr;
			$myQuery = pmb_mysql_query($req);
			if(pmb_mysql_num_rows($myQuery)){
				while($r = pmb_mysql_fetch_object($myQuery)){
					if($r->emprview_default) $this->view_list_empr_default=$r->opac_view_id;
					$this->opac_views_list[] = $r->opac_view_id;
				}
			}
			//on regarde l'OPAC classique
			$query = "select emprview_view_num,emprview_default from opac_views_empr where emprview_view_num = 0 and emprview_empr_num = ".$this->id_empr;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$r = pmb_mysql_fetch_object($result);
				$this->opac_views_list[] = 0;
				if($r->emprview_default) $this->view_list_empr_default=0;
			}

		}
		if(count($this->opac_views_list) == 0){
			if($pmb_opac_view_activate == 2){
				$this->load_classes();
				$qt = new quota("OPAC_VIEW",$include_path."/quotas/own/".$lang."/opac_views.xml");
				$struct = array(
					'READER' => ($this->id_empr ? $this->id_empr : 0)
				);
				if($this->id_empr){
					$struct["READER"] = ($this->id_empr ? $this->id_empr : 0) ;
					$val = $qt->get_quota_value($struct);
				}else{
					$tmp = $qt->apply_conflict(array(""));
					$val = $tmp['VALUE'];
				}
				if ($val != '-1') {
    				$values = unserialize($val);
    				$this->opac_views_list = $values['allowed'];
    				$this->view_list_empr_default = $values['default'];
				}
			}else if(!$this->id_empr){
				$this->opac_views_list[] = 0;
				$req="SELECT * FROM opac_views where opac_view_visible=1";
				$myQuery = pmb_mysql_query($req);
				if(pmb_mysql_num_rows($myQuery)){
					while($r = pmb_mysql_fetch_object($myQuery)){
						//if($r->emprview_default) $this->view_list_empr_default=$r->opac_view_id;
						/*else if(!$this->id_empr && !$this->view_list_empr_default){
							//si pas d'emprunteur, on met la première vue trouvée par défaut
							$this->view_list_empr_default = $r->opac_view_id;
						}*/
						$this->opac_views_list[]=$r->opac_view_id;
					}
				}
			}
		}
	}

	/*
	 * Vérifie la disponibilité de la vue
	 */
	public function check_right(){
		if(!count($this->opac_views_list))
			$this->list_views();
		if(in_array($this->id,$this->opac_views_list))
			return true;
		else return false;
	}

	public function set_parameters(){
	    if($this->id && isset($this->param_subst)) {
			$this->param_subst->set_parameters();
		}
	}

	public function get_parameter_value($type_param, $sstype_param){
	    if($this->id && isset($this->param_subst)) {
			return $this->param_subst->get_parameter_value($type_param, $sstype_param);
		}
		return '';
	}

	public function get_list($name='', $value_selected=0) {
		global $charset;
		if ($this->id_empr) $myQuery = pmb_mysql_query("SELECT * FROM opac_views left join opac_views_empr on (emprview_view_num=opac_view_id and emprview_empr_num=$this->id_empr) where opac_view_visible!=0 order by opac_view_name ");
		else $myQuery = pmb_mysql_query("SELECT * FROM opac_views where opac_view_visible=1 order by opac_view_name ");

		$selector = "<select name='$name' id='$name'>";
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$selector .= "<option value='".$r->opac_view_id."'";
				$r->opac_view_id == $value_selected ? $selector .= " selected='selected'>" : $selector .= ">";
		 		$selector .= htmlentities($r->opac_view_name,ENT_QUOTES, $charset)."</option>";
			}
		}
		$selector .= "</select>";
		$this->selector=$selector;

		return $selector;
	}
} // fin définition classe