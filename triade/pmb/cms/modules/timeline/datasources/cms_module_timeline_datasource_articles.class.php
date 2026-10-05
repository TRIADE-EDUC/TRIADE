<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_timeline_datasource_articles.class.php,v 1.2 2017/10/17 10:22:10 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/notice.class.php');
require_once($class_path.'/parametres_perso.class.php');

class cms_module_timeline_datasource_articles extends cms_module_timeline_datasource_generic{

	protected static $prefix = 'cms_editorial';
	
	protected $entity_type = 'articles';
	
	protected $cp_persos_by_type = array();
		
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_articles_from_type"
		);
	}

	/**
	 * Définition des champs utilisables pour la valorisation 
	 * de la structure JSON renvoyée par la source
	 */
	protected function init_usable_fields(){
		/** Les différents champs de titres + les champs perso non répetable de type small texte **/
		$this->title_fields = array_merge(array(
			"title" => $this->msg['cms_module_timeline_datasource_articles_title']
		), $this->get_perso_fields('text', 'small_text'));

		/** Le champs résumé + les champs de type text large unique **/
		$this->resume_fields = array_merge(array(
			"resume" => $this->msg['cms_module_timeline_datasource_articles_resume']
		), $this->get_perso_fields('text', 'text'));
		
		/** Le champs résumé + les champs de type text large unique **/
		$this->image_fields = array_merge(array(
				"logo" => $this->msg['cms_module_timeline_datasource_articles_logo']
		), $this->get_perso_fields('url', 'text'));
		
		$this->date_fields = array_merge(array(
			"start_date" => $this->msg['cms_module_timeline_datasource_articles_start_date'],
			"end_date" => $this->msg['cms_module_timeline_datasource_articles_end_date'],
			"create_date" => $this->msg['cms_module_timeline_datasource_articles_create_date']
		), $this->get_perso_fields('date_box', 'date'));
	}
		
	protected function get_full_values($ids){
		
		$events = array();
		foreach($ids as $id){
			$article = new cms_article($id);
			
			$event = [];
			
			if(!empty($this->parameters['timeline_fields'])){
				foreach($this->parameters['timeline_fields'] as $field_name => $field_value){
					if (!empty($field_value)) {
						if (strpos($field_value, 'c_perso') !== false) {
							$field_value = explode('c_perso_', $field_value)[1];
							$event[$field_name] = $this->get_cp_value($field_value, $id);
						} else if ($field_value == 'logo') {
							$event[$field_name] = $article->logo->get_vign_url('vign');
						} else {
							$event[$field_name] = $article->{$field_value};
						}
					}
				}
			}
			$events[] = $event;
		}
		return $events;
	}
	
	protected function get_perso_fields($type, $datatype){
		$data = array();
		$query = 'select name, titre, idchamp, num_type, editorial_type_element from cms_editorial_custom
				join cms_editorial_types on cms_editorial_custom.num_type = cms_editorial_types.id_editorial_type
				where cms_editorial_custom.datatype = "'.$datatype.'" and cms_editorial_custom.type="'.$type.'" and cms_editorial_types.editorial_type_element in ("article", "article_generic")';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_assoc($result)){
				$data['c_perso_'.$row['name']] = $row['titre'];
				$num_type = $row['num_type'];
				if ($row['editorial_type_element'] == 'article_generic') {
					$num_type = 0;
				}
				if (!isset($this->cp_persos_by_type[$num_type])) {
					$this->cp_persos_by_type[$num_type] = array();
				}
				$this->cp_persos_by_type[$num_type][] = 'c_perso_'.$row['name'];
			}
		}
		return $data;
	}
	
	public function get_form(){
		$form = parent::get_form();
		
		$form.= '
				<script type="text/javascript">
					require(["dojo/query", "dojo/dom", "dojo/on", "dojo/dom-style"], function(query, dom, on, domStyle) {
						var cp_persos_by_type = '.json_encode($this->cp_persos_by_type).';
						var selectors = [
								"'.$this->get_form_value_name('title').'",
								"'.$this->get_form_value_name('resume').'",
								"'.$this->get_form_value_name('start_date').'",
								"'.$this->get_form_value_name('end_date').'",
								"'.$this->get_form_value_name('image').'"
						]
								
						var update_selectors = function(e) {
							var cp_type = e.target.value;
							if (!cp_persos_by_type.hasOwnProperty(cp_type)) cp_persos_by_type[cp_type] = [];
							for (var i = 0; i < selectors.length; i++) {
								query("select[name=\""+selectors[i]+"\"] > option").forEach(function(node){
									domStyle.set(node, "display", "");
									if ((node.value.indexOf("c_perso") != -1) && (cp_persos_by_type[0].indexOf(node.value) == -1) && (cp_persos_by_type[cp_type].indexOf(node.value) == -1)) {
										domStyle.set(node, "display", "none");
									}
								});
							}
						}
						
						var target = dom.byId("'.$this->get_form_value_name('selector_form').'");
						var observer = new MutationObserver(function(mutations) {
							var select = query("select", "'.$this->get_form_value_name('selector_form').'");
							if (select.length) {
								observer.disconnect();
								update_selectors({target : select[0]});
								on(select[0], "change", update_selectors);
							}
						});
						var config = {childList: true};
						observer.observe(target, config);
					});
				</script>
				';
		
		return $form;
	}
}