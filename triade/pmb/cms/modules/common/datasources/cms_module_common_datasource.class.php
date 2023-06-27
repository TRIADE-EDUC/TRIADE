<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource.class.php,v 1.43 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource extends cms_module_root{
	protected $cadre_parent;
	protected $num_cadre_content;
	protected $selectors=array();
	private $used_external_filter = false;
	private $external_filter;
	public static $sections_tree = array();
	public static $sections_path = array();
	
	/**
	 * Datasources au même niveau dans le cas d'une datasource multiple, pour ne pas supprimer les supprimer notamment
	 * @var array
	 */
	protected $brothers = array();
		
	public function __construct($id=0){
	    $this->id = (int) $id;
		parent::__construct();
	}
	
	public function get_available_selectors(){
		return array();
	}
	
	public function set_cadre_parent($id){
	    $this->cadre_parent = (int) $id;
	}
	
	public function set_num_cadre_content($id){
	    $this->num_cadre_content = (int) $id;
	}
	
	public function set_filter($filter){
		$this->used_external_filter = true;
		$this->external_filter = $filter;
	}
	
	/*
	 * Récupération des informations en base
	 */
	protected function fetch_datas(){
		global $dbh;
		if($this->id){
			//on commence par aller chercher ses infos
			$query = " select id_cadre_content, cadre_content_hash, cadre_content_num_cadre, cadre_content_data from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = (int) $row->id_cadre_content;
				$this->hash = $row->cadre_content_hash;
				$this->cadre_parent = (int) $row->cadre_content_num_cadre;
				$this->unserialize($row->cadre_content_data);
			}
			//on va chercher les infos des sélecteurs...
			$query = "select id_cadre_content, cadre_content_object from cms_cadre_content where cadre_content_type='selector' and cadre_content_num_cadre != 0 and cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$this->selectors[] = array(
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
		
		if(isset($this->parameters['selector']) && $this->parameters['selector']!= "" || count($selectors)==1){
			$current_selector_id = 0;
			$selector_id = 0;
			if($this->parameters['selector']!= ""){
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
			<script type='text/javacsript'>
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
		if(count($selectors)>1){
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
					<script type='text/javascript'>
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
		global $dbh;
		
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
		$result = pmb_mysql_query($query,$dbh);
		
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciennes sources de données...
			$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where id_cadre_content not in ('".implode("','", $this->get_brothers())."') and cadre_content_type='datasource' and cadre_content_num_cadre = '".$this->cadre_parent."'";
			$query.=  !empty($this->num_cadre_content) ? " and cadre_content_num_cadre_content=".$this->num_cadre_content : "";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row= pmb_mysql_fetch_object($result)){
					$obj = new $row->cadre_content_object($row->id_cadre_content);
					$obj->delete();
				}
			}
			//sélecteur
			$selector_id = 0;
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->parameters['selector'] == $this->selectors[$i]['name']){
					$selector_id = $this->selectors[$i]['id'];
					break;
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
		global $dbh;
		if($this->id){
			//on commence par éliminer le sélecteur associé...
			$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query,$dbh);
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
			$result = pmb_mysql_query($query,$dbh);
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
		if($this->parameters['selector']!= ""){
			$current_selector_id = 0;
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->selectors[$i]['name'] == $this->parameters['selector']){
					return new $this->parameters['selector']($this->selectors[$i]['id']);
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
	
	protected function get_exported_datas(){
		$infos = parent::get_exported_datas();
		$infos['type'] = "datasource";
		return $infos;
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
			case "sections" :
				$result = $this->filter_sections($datas);
				break;
			case "explnums" :
				$result = $this->filter_explnums($datas);
				break;
			case "authorities" :
				$result = $this->filter_authorities($datas);
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
		if ($this->used_external_filter){
			$result = $this->external_filter->filter($result);
		}
		if(!$result){
			$result=array();
		}
		return $result;
	}

	protected function filter_notices($datas){
		return $datas;
	}

	protected function filter_articles($datas){
		global $dbh;
		$valid_datas = $valid_datas = array();
		//quand on filtre un article, on cherche déjà si la rubrique parente est visible...
		$valid_sections = $sections = array();
		array_walk($datas, 'static::int_caster');
		$query = "select distinct num_section from cms_articles where id_article in ('".implode("','",$datas)."')";
		$result = pmb_mysql_query($query,$dbh);
		if($result && pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$sections[] = $row->num_section;
			}
			$valid_sections = $this->filter_sections($sections);
		}
		
		$clause_date = "
		((article_start_date != 0 and to_days(article_start_date)<=to_days(now()) and to_days(article_end_date)>=to_days(now()))||(article_start_date != 0 and article_end_date =0 and to_days(article_start_date)<=to_days(now()))||(article_start_date=0 and article_end_date=0)||(article_start_date = 0 and to_days(article_end_date)>=to_days(now())))";
		
		$articles = array();
		if(count($valid_sections)){
			$query = "select id_article from cms_articles 
				join cms_editorial_publications_states on id_publication_state = article_publication_state 
				where num_section in ('".implode("','",$valid_sections)."') and id_article in ('".implode("','",$datas)."') and editorial_publication_state_opac_show = 1".(!$_SESSION['id_empr_session'] ? " and editorial_publication_state_auth_opac_show = 0" : "")." and ".$clause_date;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$valid_datas[]=$row->id_article;
				}
			}
			foreach($datas as $article_id){
				if(in_array($article_id,$valid_datas)){
					$articles[] = $article_id;
				}
			}
		}
		return $articles;
	}
	
	protected function filter_sections($datas){
		$valid_datas = array();
		//on caste les données
		array_walk($datas, 'static::int_caster');
		//on initialise un arbre avec les sections
		if(!count(self::$sections_tree)){
			self::$sections_tree = $this->get_sections_tree(0,"",self::$sections_path);
		}
		foreach($datas as $id_section){
			if(isset(self::$sections_path[$id_section])){
				$section_path_ids = explode("/",self::$sections_path[$id_section]);
				$current_tree = self::$sections_tree[$section_path_ids[0]];
				if($current_tree['valid'] == 1){
					$valid = true;
					for($i=1 ; $i< count($section_path_ids) ; $i++){
						$current_tree = $current_tree['children'][$section_path_ids[$i]];
						if($current_tree['valid'] == 0){
							$valid = false;
							break;
						}
					}
					if($valid){
						$valid_datas[]=$id_section;
					}
				}
			}else{
				continue;
			}
		}
		return $valid_datas;
	}
	
	protected function filter_authorities($datas) {
		// En prévision d'un filtrage à l'avenir
		return $datas;
	}
	
	protected function get_sections_tree($id_parent = 0,$path="",&$paths){
		global $dbh;
		$id_parent+=0;
		$tree = array();
		$nb_days_since_1970 = 719528;
		$nb_days_today = round((time()/(3600*24)))+$nb_days_since_1970;
		
		$clause = "((section_start_date != 0 and section_start_date<now() and section_end_date>now())||(section_start_date != 0 and section_end_date =0 and section_start_date <now())||(section_start_date = 0 and section_end_date>now()))";
		
		$query="select id_section,to_days(section_start_date) as start_day, to_days(section_end_date) as end_day , editorial_publication_state_opac_show,editorial_publication_state_auth_opac_show from cms_sections join cms_editorial_publications_states on id_publication_state = section_publication_state where section_num_parent = '".($id_parent*1)."'";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$paths[$row->id_section] = ($path ? $path."/" : "").$row->id_section ;	
				$valid = 1; 
				//vérification sur le statut
				if(!($row->editorial_publication_state_opac_show && (!$row->editorial_publication_state_auth_opac_show || ($row->editorial_publication_state_auth_opac_show && $_SESSION['id_empr_session'])))){
					$valid = 0;
				}else{
					//vérification sur les dates...
					if($row->start_day!= 0 && $row->end_day!=0 && ($row->start_day>$nb_days_today || $row->end_day<$nb_days_today)){
						$valid = 0;
					}else if ($row->start_day!=0 && !$row->end_day && $row->start_day>$nb_days_today){
						$valid = 0;
					}else if ($row->end_day!=0 && !$row->start_day && $row->end_day<$nb_days_today){
						$valid = 0;
					}
				}
				$tree[$row->id_section] = array(
					'start_day' => $row->start_day,
					'end_day' => $row->end_day,
					'opac_show'=> $row->editorial_publication_state_opac_show,
					'auth_opac_show'=> $row->editorial_publication_state_auth_opac_show,
					'valid' => $valid
				);
				if($valid){
					$tree[$row->id_section]['children'] = $this->get_sections_tree($row->id_section,($path ? $path."/" : "").$row->id_section,$paths);
				}	
			}
		}
		return $tree;
	}
	
	protected function filter_explnums($datas=array()){
		return $datas;
	}
	
	public function get_format_data_structure(){
		return array();
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
}