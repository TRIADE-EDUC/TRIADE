<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource.class.php,v 1.59.2.3.2.1 2025/05/07 14:49:15 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path.'/acces.class.php';

class cms_module_common_datasource extends cms_module_root{
	protected $cadre_parent;
	protected $num_cadre_content;
	protected $selectors=array();
	protected $sub_datasources=array();
	protected $datasource_data;
	protected $used_external_filters = false;
	protected $external_filters = array();

	public static $sections_tree = array();
	public static $sections_path = array();


	protected static $sections_by_user = array();
	protected static $articles_by_user = array();

	/**
	 * Datasources au même niveau dans le cas d'une datasource multiple, pour ne pas supprimer les supprimer notamment
	 * @var array
	 */
	protected $brothers = array();
	protected $limitable = false;
	protected $once_sub_selector;

	public function __construct($id=0){
		$this->id = intval($id);
		parent::__construct();
	}

	public function get_available_selectors(){
		return array();
	}

	public function set_cadre_parent($id){
		$this->cadre_parent = intval($id);
	}

	public function set_num_cadre_content($id){
		$this->num_cadre_content = intval($id);
	}

	public function set_filter($filter) {
		$this->used_external_filters = true;
		$this->external_filters[] = $filter;
	}

	/*
	 * Récupération des informations en base
	 */
	protected function fetch_datas(){
		if($this->id){
			//on commence par aller chercher ses infos
			$query = " SELECT id_cadre_content, cadre_content_hash, cadre_content_num_cadre, cadre_content_data, cadre_content_num_cadre_content
                        FROM cms_cadre_content
                        WHERE id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = intval($row->id_cadre_content);
				$this->hash = $row->cadre_content_hash;
				$this->cadre_parent = intval($row->cadre_content_num_cadre);
				$this->num_cadre_content = intval($row->cadre_content_num_cadre_content);
				$this->unserialize($row->cadre_content_data);
			}
			//on va chercher les infos des sélecteurs...
			$query = "select id_cadre_content, cadre_content_object from cms_cadre_content where cadre_content_type='selector' and cadre_content_num_cadre != 0 and cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$this->selectors[] = array(
						'id' => intval($row->id_cadre_content),
						'name' => $row->cadre_content_object
					);
				}
			}
			//on va chercher les infos des sélecteurs...
			$query = "select id_cadre_content, cadre_content_object from cms_cadre_content where cadre_content_type='datasource' and cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$this->sub_datasources[] = array(
					    'id' => (int) $row->id_cadre_content,
						'name' => $row->cadre_content_object
					);
				}
			}
		}
	}

	/*
	 * Méthode de génération du formulaire...
	 */
	public function get_form(){
		$selectors = $this->get_available_selectors();

		$form = "
			<div class='row'>";
		$form.=$this->get_hash_form();
		$form.= $this->get_selectors_list_form();

		if(isset($this->parameters['selector']) && $this->parameters['selector']!= "" || (is_countable($selectors) && count($selectors)==1)){
			$selector_id = 0;
			if(is_countable($this->selectors) && $this->parameters['selector']!= ""){
				for($i=0 ; $i<count($this->selectors) ; $i++){
					if($this->selectors[$i]['name'] == $this->parameters['selector']){
						$selector_id = $this->selectors[$i]['id'];
						break;
					}
				}
				$selector_name= $this->parameters['selector'];
			}else if(count($selectors)==1){
				$selector_name= $selectors[0];
			}
			$form.="
			<script>
				cms_module_load_elem_form('".$selector_name."','".$selector_id."','".$this->get_form_value_name('selector_form')."');
			</script>";
		}
		$form.="
				<div id='".$this->get_form_value_name('selector_form')."' dojoType='dojox.layout.ContentPane'>
				</div>
			</div>";
		return $form;
	}

	/*
	 * Formulaire de sélection d'un sélecteur
	 */
	protected function get_selectors_list_form(){
		$selectors = $this->get_available_selectors();
		if(is_countable($selectors) && count($selectors)>1){
			$form= "
				<div class='colonne3'>
					<label for='".$this->get_form_value_name('selector_choice')."'>".$this->format_text($this->msg['cms_module_common_datasource_selector_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='hidden' name='".$this->get_form_value_name('selector_choice_last_value')."' id='".$this->get_form_value_name('selector_choice_last_value')."' value='".(isset($this->parameters['selector']) && $this->parameters['selector'] ? $this->parameters['selector'] : "" )."' />
					<select name='".$this->get_form_value_name('selector_choice')."' id='".$this->get_form_value_name('selector_choice')."' onchange=' _".$this->get_value_from_form('')."load_selector_form(this.value)'>
						<option value=''>".$this->format_text($this->msg['cms_module_common_datasource_selector_choice'])."</option>";
			foreach($selectors as $selector){
				$form.= "
						<option value='".$selector."' ".(isset($this->parameters['selector']) && $selector == $this->parameters['selector'] ? "selected='selected'":"").">".$this->format_text($this->msg[$selector])."</option>";
			}
			$form.="
					</select>
					<script>
						function _".$this->get_value_from_form('')."load_selector_form(selector){
							if(selector != ''){
								//on évite un message d'alerter si le il n'y a encore rien de fait...
								if(document.getElementById('".$this->get_form_value_name('selector_choice_last_value')."').value != ''){
									var confirmed = confirm('".addslashes($this->msg['cms_module_common_selector_confirm_change_selector'])."');
								}else{
									var confirmed = true;
								}
								if(confirmed){
									document.getElementById('".$this->get_form_value_name('selector_choice_last_value')."').value = selector;
									cms_module_load_elem_form(selector,0,'".$this->get_form_value_name('selector_form')."');
								}else{
									var sel = document.getElementById('".$this->get_form_value_name('selector_choice')."');
									for(var i=0 ; i<sel.options.length ; i++){
										if(sel.options[i].value == document.getElementById('".$this->get_form_value_name('selector_choice_last_value')."').value){
											sel.selectedIndex = i;
										}
									}
								}
							}
						}
					</script>
				</div>";
		}else{
			$form = "
				<input type='hidden' name='".$this->get_form_value_name('selector_choice')."' value='".(isset($selectors[0]) ? $selectors[0] : '')."'/>";
		}
		return $form;
	}

	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
		$this->parameters['selector'] = $this->get_value_from_form('selector_choice');

		$this->get_hash();
		if($this->id){
			$query = "update cms_cadre_content set";
			$clause = " where id_cadre_content='".$this->id."'";
		}else{
			$query = "insert into cms_cadre_content set";
			$clause = "";
		}
		$query.= "
			cadre_content_hash = '".$this->hash."',
			cadre_content_type = 'datasource',
			cadre_content_object = '".$this->class_name."',".
			($this->cadre_parent ? "cadre_content_num_cadre = '".$this->cadre_parent."'," : "").
			($this->num_cadre_content? "cadre_content_num_cadre_content = '".$this->num_cadre_content."'," : "")."
			cadre_content_data = '".addslashes($this->serialize())."'
			".$clause;
		$result = pmb_mysql_query($query);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			if (!empty($this->cadre_parent)) {
				//on supprime les anciennes sources de données...
				$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where id_cadre_content not in ('".implode("','", $this->get_brothers())."') and cadre_content_type='datasource' and cadre_content_num_cadre = '".$this->cadre_parent."'";
    			$query.= " and cadre_content_num_cadre_content=". (!empty($this->num_cadre_content) ? $this->num_cadre_content : "0");
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
			    	while($row= pmb_mysql_fetch_object($result)){
			        	$obj = new $row->cadre_content_object($row->id_cadre_content);
			        	$obj->delete();
			    	}
				}
			}
			//sub datasource
			$sub_datasource_id = 0;
			if (is_countable($this->sub_datasources)) {
    			for($i=0 ; $i<count($this->sub_datasources) ; $i++){
    			    if($this->parameters['sub_datasource'] == $this->sub_datasources[$i]['name']){
    			        $sub_datasource_id = $this->sub_datasources[$i]['id'];
    					break;
    				}
    			}
			}
			if(!empty($this->parameters['sub_datasource'])){
			    $sub_datasource = new $this->parameters['sub_datasource']($sub_datasource_id);
			    $sub_datasource->set_num_cadre_content($this->id);
			    $sub_datasource->set_cadre_parent($this->cadre_parent);
			    $result = $sub_datasource->save_form();
				if($result){
				    if($sub_datasource_id==0){
					    $this->sub_datasources[] = array(
					        'id' => $sub_datasource->id,
							'name' => $this->parameters['sub_datasource']
						);
					}
				}
			}

			//selecteur
			$selector_id = 0;
			if (is_countable($this->selectors)) {
    			for($i=0 ; $i<count($this->selectors) ; $i++){
    				if($this->parameters['selector'] == $this->selectors[$i]['name']){
    					$selector_id = $this->selectors[$i]['id'];
    					break;
    				}
    			}
			}
			if($this->parameters['selector']){
				$selector = new $this->parameters['selector']($selector_id);
				$selector->set_parent($this->id);
				$selector->set_cadre_parent($this->cadre_parent);
				$result = $selector->save_form();
				if($result){
					if($selector_id==0){
						$this->selectors[] = array(
							'id' => $selector->id,
							'name' => $this->parameters['selector']
						);
					}
					return true;
				}else{
					//création de la source de donnée ratée, on supprime le hash de la table...
					$this->delete_hash();
					return false;
				}
			}else{
				return true;
			}
		}else{
			//création de la source de donnée ratée, on supprime le hash de la table...
			$this->delete_hash();
			return false;
		}
	}

	/*
	 * Méthode de suppression
	 */
	public function delete(){
		if($this->id){
			//on commence par éliminer le sélecteur associé...
			$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				//la logique voudrait qu'il n'y ai qu'un seul sélecteur (enfin sous-élément, la conception peut évoluer...), mais sauvons les brebis égarées...
				while($row = pmb_mysql_fetch_object($result)){
					$sub_elem = new $row->cadre_content_object($row->id_cadre_content);
					$success = $sub_elem->delete();
					if(!$success){
						//TODO verbose mode
						return false;
					}
				}
			}
			//on est tout seul, éliminons-nous !
			$query = "delete from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result){
				$this->delete_hash();
				return true;
			}else{
				return false;
			}
		}
	}

	/*
	 * Méthode pour renvoyer les données tel que défini par le sélecteur
	 */
	public function get_datas(){
	    $sub_datasource = $this->get_selected_sub_datasource();
	    if (!empty($sub_datasource)) {
	        return $sub_datasource->get_datas();
	    }
		return false;
	}

	public function get_headers($datas=array()){
		$headers=array();
		if($this->parameters['selector']){
			$selector = $this->get_selected_selector();
			$headers = array_merge($headers,$selector->get_headers($datas));
			$headers = array_unique($headers);
		}
		return $headers;
	}

	protected function get_selected_selector(){
		//on va chercher
	    if(is_countable($this->selectors) && $this->parameters['selector']!= ""){
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->selectors[$i]['name'] == $this->parameters['selector']){
				    return cms_modules_parser::get_module_class_content($this->parameters['selector'],$this->selectors[$i]['id']);
				}
			}
		}else{
			return false;
		}
	}

	protected function get_selected_sub_datasource(){
	    if(is_countable($this->sub_datasources) && !empty($this->parameters['sub_datasource'])){
			for($i=0 ; $i<count($this->sub_datasources) ; $i++){
				if($this->sub_datasources[$i]['name'] == $this->parameters['sub_datasource']){
				    return cms_modules_parser::get_module_class_content($this->parameters['sub_datasource'],$this->sub_datasources[$i]['id']);
				}
			}
		}else{
			return false;
		}
	}

	public function set_module_class_name($module_class_name){
		$this->module_class_name = $module_class_name;
		$this->fetch_managed_datas();
	}

	protected function fetch_managed_datas($type="datasources"){
		parent::fetch_managed_datas($type);
	}

	/*
	 * Méthode pour filtrer les résultats en fonction de la visibilité
	 */
	protected function filter_datas($type,$datas){
		//la méthode générique permet de filter les entités de base...
		switch($type){
			case "notices" :
				$result = $this->filter_notices($datas);
				break;
			case "articles" :
				$result = $this->filter_articles($datas);
				break;
			case "article" :
				$result = $this->filter_article($datas);
				break;
			case "sections" :
				$result = $this->filter_sections($datas);
				break;
			case "section" :
				$result = $this->filter_section($datas);
				break;
			case "explnums" :
				$result = $this->filter_explnums($datas);
				break;
			case "authorities" :
				$result = $this->filter_authorities($datas);
				break;
			case "animations" :
			case "animation" :
			    $result = $this->filter_animations($datas);
				break;
			default :
				//si on est pas avec une entité connue, on s'en charge quand même...
				if(method_exists($this,"filter_".$type)){
					$result = call_user_func(array($this,"filter_".$type),$datas);
				}else{
					$result = $datas;
				}
				break;
		}

		if (! empty($this->used_external_filters)) {
		    foreach ($this->external_filters as $external_filter) {
		        $result = $external_filter->filter($result);
		    }
		}

		if (empty($result)) {
			$result = array();
		}

		return $result;
	}

	protected function filter_notices($datas){
		$finaldatas = array();

		if( is_array($datas) && is_countable($datas) && count($datas) ){
			$notices_ids = "";
			for($i=0 ; $i<count($datas) ; $i++){
				if($notices_ids) $notices_ids.= ",";
				$notices_ids.= intval($datas[$i]);
			}
			$filter = new filter_results($notices_ids);
			$notices_ids = $filter->get_results();
			//les données sont déjà filtrées...dont on s'assure que ca ne bouge pas !
			$tmpdatas = explode(",", $notices_ids);
			$finaldatas = array_merge(array_intersect($datas, $tmpdatas));
		}
		return $finaldatas;
	}

	protected function filter_articles($datas){

	    global $gestion_acces_active;
	    global $gestion_acces_empr_cms_article;

	    if(!is_array($datas) || empty($datas)) {
	        return [];
	    }

		//quand on filtre un article, on cherche déjà si la rubrique parente est visible...
	    $filtered_sections = $sections = [];
		$datas = $this->array_int_caster($datas);
		$query = "select distinct num_section from cms_articles where id_article in ('".implode("','",$datas)."')";
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$sections[] = $row->num_section;
			}
			$filtered_sections = $this->filter_sections($sections);
		}
		if(empty($filtered_sections)) {
            return [];
        }

	    $id_empr = intval($_SESSION['id_empr_session']);
	    $query = '';

	    //articles accessibles a l'utilisateur courant avec les droits d'acces
	    if( ($gestion_acces_active==1) && ($gestion_acces_empr_cms_article==1) ) {

	        if( !isset(static::$articles_by_user[$id_empr]) ) {
    	        static::$articles_by_user[$id_empr] = array();
    	        $ac= new acces();
    	        $dom_8= $ac->setDomain(8);
    	        $qac = $dom_8->getJoin($id_empr, 4, 'id_article');
    	        $qac = "select id_article from cms_articles ".$qac;
    	        $rac = pmb_mysql_query($qac);
    	        if(pmb_mysql_num_rows($rac)) {
    	            while($row = pmb_mysql_fetch_row($rac)) {
    	                static::$articles_by_user[$id_empr][] = $row[0];
    	            }
    	        }
	        }
            if(empty(static::$articles_by_user[$id_empr])) {
                return [];
            }

            $articles_intersection = array_intersect($datas, static::$articles_by_user[$id_empr]);
            if(!is_countable($articles_intersection) || !count($articles_intersection)) {
                return [];
            }

            $query = "select id_article from cms_articles ";
            $query.= "where num_section in (".implode(',',$filtered_sections).") ";
            $query.= "and id_article in (".implode(',', $articles_intersection).") ";

        //articles accessibles a l'utilisateur courant avec les statuts
	    } else {
			$query = "select id_article from cms_articles join cms_editorial_publications_states on id_publication_state = article_publication_state ";
			$query.= "where num_section in (".implode(',',$filtered_sections).") ";
			$query.= "and id_article in (".implode(',', $datas).") ";
			$query.= "and editorial_publication_state_opac_show = 1 ";
			if (!$id_empr ) {
			    $query.= "and editorial_publication_state_auth_opac_show = 0 ";
			}
	    }

	    if(!$query) {
	        return [];
	    }

	    $clause_date = "
        ((article_start_date != 0 and to_days(article_start_date)<=to_days(now()) and to_days(article_end_date)>=to_days(now()))||(article_start_date != 0 and article_end_date =0 and to_days(article_start_date)<=to_days(now()))||(article_start_date=0 and article_end_date=0)||(article_start_date = 0 and to_days(article_end_date)>=to_days(now())))";

        $query.= "and ".$clause_date;
        $result = pmb_mysql_query($query);


		if(!pmb_mysql_num_rows($result)){
		    return [];
		}

		$filtered_articles = [];
		while($row = pmb_mysql_fetch_row($result)){
		    $filtered_articles[]=$row[0];
		}
		//Conservons le tri appliqué
		$articles = [];
		foreach($datas as $article_id){
		    if(in_array($article_id,$filtered_articles)){
		        $articles[] = $article_id;
		    }
		}

		return $articles;
	}

	protected function filter_article($datas) {
		return $this->filter_articles($datas);
	}

	protected function filter_sections($datas){

	    if(!is_array($datas) || empty($datas)) {
	        return [];
	    }

		$valid_sections = [];
		//on caste les données
		$datas = $this->array_int_caster($datas);
		//on initialise un arbre avec les sections
		if (empty(self::$sections_tree)) {
			self::$sections_tree = $this->get_sections_tree(0,"",self::$sections_path);
		}

		foreach($datas as $id_section){
			if(isset(self::$sections_path[$id_section])){
				$section_path_ids = explode("/",self::$sections_path[$id_section]);
				$current_tree = self::$sections_tree[$section_path_ids[0]];
				if($current_tree['valid'] == 1){
					$valid = true;
					if (!is_countable($section_path_ids)) {
					    continue;
					}
					for($i=1 ; $i< count($section_path_ids) ; $i++){
						$current_tree = $current_tree['children'][$section_path_ids[$i]];
						if($current_tree['valid'] == 0){
							$valid = false;
							break;
						}
					}
					if($valid){
					    $valid_sections[]=$id_section;
					}
				}
			} else {
				continue;
			}
		}
		return $valid_sections;
	}

	protected function filter_section($datas) {
		return $this->filter_sections($datas);
	}

	protected function filter_authorities($datas) {
		// En prévision d'un filtrage à l'avenir
		return $datas;
	}

	protected function get_sections_tree($id_parent = 0,$path="",&$paths=array()){

	    global $gestion_acces_active;
	    global $gestion_acces_empr_cms_section;

		$id_parent = intval($id_parent);
		$tree = [];
		$nb_days_to_1970 = 719528;
		$nb_days_today = round((time()/(3600*24)))+$nb_days_to_1970;

		if(!empty($_SESSION['id_empr_session'])) {
			$id_empr = intval($_SESSION['id_empr_session']);
		} else {
			$id_empr = 0;
		}

		//rubriques accessibles a l'utilisateur courant pour les droits d'acces
		if( ($gestion_acces_active==1) && ($gestion_acces_empr_cms_section==1) && (!isset(static::$sections_by_user[$id_empr])) ) {

		    static::$sections_by_user[$id_empr] = array();
		    $ac= new acces();
		    $dom_7= $ac->setDomain(7);
		    $qac = $dom_7->getJoin($id_empr, 4, 'id_section');
		    $qac = "select id_section from cms_sections ".$qac;
		    $rac = pmb_mysql_query($qac);
		    if(pmb_mysql_num_rows($rac)) {
		        while($row = pmb_mysql_fetch_row($rac)) {
		            static::$sections_by_user[$id_empr][] = $row[0];
		        }
		    }
		}

		$query = "select id_section,to_days(section_start_date) as start_day, to_days(section_end_date) as end_day , editorial_publication_state_opac_show,editorial_publication_state_auth_opac_show ";
		$query.= "from cms_sections ";
		$query.= "join cms_editorial_publications_states on id_publication_state = section_publication_state ";
		$query.= "where section_num_parent = ".$id_parent;

		$result = pmb_mysql_query($query);

		if(!pmb_mysql_num_rows($result)){
		      return $tree;
		}

		while($row = pmb_mysql_fetch_object($result)){
			$paths[$row->id_section] = ($path ? $path."/" : "").$row->id_section ;

			$valid = 1;

			$tree[$row->id_section] = [
			    'start_day' => $row->start_day,
			    'end_day' => $row->end_day,
			    'opac_show'=> $row->editorial_publication_state_opac_show,
			    'auth_opac_show'=> $row->editorial_publication_state_auth_opac_show,
			    ];

			//verification de validite sur les droits d'accès
			if( ($gestion_acces_active==1) && ($gestion_acces_empr_cms_section==1) ) {

			    if ( in_array($row->id_section, static::$sections_by_user[$id_empr]) ) {
			        $tree[$row->id_section]['opac_show'] = 1;
			        $tree[$row->id_section]['auth_opac_show'] = 0;
			    } else {
			        $tree[$row->id_section]['opac_show'] = 0;
			        $tree[$row->id_section]['auth_opac_show'] = 0;
			        $valid = 0;
			    }
			//ou les statuts
			} elseif(   !($row->editorial_publication_state_opac_show && (!$row->editorial_publication_state_auth_opac_show || ($row->editorial_publication_state_auth_opac_show && $id_empr)) ) ){
			    $valid = 0;
			}

			//vérification de validité sur les dates...
			if($valid && ($row->start_day!= 0 && $row->end_day!=0 && ($row->start_day>$nb_days_today || $row->end_day<$nb_days_today)) ){
			    $valid = 0;
			}
			if($valid && ($row->start_day!=0 && !$row->end_day && $row->start_day>$nb_days_today) ){
			    $valid = 0;
			}
			if($valid && ($row->end_day!=0 && !$row->start_day && $row->end_day<$nb_days_today) ){
				$valid = 0;
			}
			$tree[$row->id_section]['valid'] = $valid;

			if($valid){
                $tree[$row->id_section]['children'] = $this->get_sections_tree($row->id_section,($path ? $path."/" : "").$row->id_section,$paths);
			}
		}

		return $tree;
	}

	protected function filter_explnums($datas=array()){
	    global $gestion_acces_active;
	    global $gestion_acces_empr_docnum;

	    $filtered_datas = array();
	    if(is_countable($datas) && count($datas)){
	        $datas = $this->array_int_caster($datas);
		    $acces='';
	        $restrict = '';
	        if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	        	$ac = new acces();
	        	$dom_3 = $ac->setDomain(3);
	        	$acces = $dom_3->getJoin($_SESSION['id_empr_session'],16,'explnum_id');
	        } else {
	        	$restrict= "and explnum_docnum_statut in (select id_explnum_statut from explnum_statut where (explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	        }
	        $explnum = 'select explnum_id from explnum '.$acces.' where explnum_id in("'.implode('","', $datas).'") '.$restrict.'  ';
	        $result = pmb_mysql_query($explnum);
	        while($row = pmb_mysql_fetch_object($result)){
	          $filtered_datas[] = $row->explnum_id;
	        }
	    }
	    return $filtered_datas;
	}

	public function get_format_data_structure(){
		return array();
	}

	public function get_exported_datas(){
		$infos = parent::get_exported_datas();
		$infos['cadre_parent'] = $this->cadre_parent;
		$infos['selector'] = $this->get_selected_selector();
		return $infos;
	}

	public function get_human_description($context_name){
		$description = "<span class = 'cms_module_common_datasource_name_human_description'>".$context_name."</span>";
		return $description;
	}

	public function set_brothers($brothers = array()) {
		$this->brothers = $brothers;
	}

	public function get_brothers() {
		if (empty($this->brothers) || !in_array($this->id, $this->brothers)) {
			$this->brothers[] = $this->id;
		}
		return $this->brothers;
	}

	public function get_datasource_data() {
	    if(!isset($this->datasource_data)){
	    	$uniqid = PHP_log::prepare_time(cms_module_common_module::get_name_from_id($this->cadre_parent), 'cms');
	        $this->datasource_data = $this->get_datas();
	        PHP_log::register($uniqid);
	    }
	    return $this->datasource_data;
// 	    return $this->get_datas();
	}

	protected function filter_contribution_areas($data){
	    global $gestion_acces_active;
	    global $opac_contribution_area_activate;

	    $filtered_datas = array();
	    if (!$opac_contribution_area_activate) {
	        return $filtered_datas;
	    }

	    if ($gestion_acces_active) {
	        if(is_countable($data) && count($data)){
	            $data = $this->array_int_caster($data);
	            $lenght = count($data);
	            for ($i = 0; $i < $lenght; $i++) {
	                $ac = new acces();
	                $dom_4 = $ac->setDomain(4);
	                if(!$dom_4->getRights($_SESSION['id_empr_session'], $data[$i], 4)){
	                    unset($data[$i]);
	                }
	            }
	        }
	    }

	    $filtered_datas = $data;
	    return $filtered_datas;
	}

	public function get_default_entities_list() {
	    return [];
	}

	protected function filter_animations($datas){
        // Un jour peut-etre cela sera utile
        return $datas;
	}
}