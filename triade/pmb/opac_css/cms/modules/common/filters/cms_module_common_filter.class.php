<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter.class.php,v 1.18.2.1.2.3 2025/05/07 14:49:15 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter extends cms_module_root{
	protected $cadre_parent;
	protected $selectors=array();
	protected $generic_type;

	public function __construct($id=0){
	    $this->id = (int) $id;
		parent::__construct();
	}

	public function get_available_selectors(){
		return array();
	}

	public function get_filter_from_selectors(){
		return array();
	}

	public function get_filter_by_selectors(){
		return array();
	}

	public function set_cadre_parent($id){
	    $this->cadre_parent = (int) $id;
	}

	/*
	 * Récupération des informations en base
	 */
	protected function fetch_datas(){
		if($this->id){
			//on commence par aller chercher ses infos
			$query = " select id_cadre_content, cadre_content_hash, cadre_content_num_cadre, cadre_content_data from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = (int) $row->id_cadre_content;
				$this->hash = $row->cadre_content_hash;
				$this->cadre_parent = (int) $row->cadre_content_num_cadre;
				$this->unserialize($row->cadre_content_data);
			}
			$this->selectors = $this->parameters['selectors'];
		}
	}

	/*
	 * Méthode de génération du formulaire...
	 */
	public function get_form(){
		$selectors_by = $this->get_filter_by_selectors();
		$selectors_from = $this->get_filter_from_selectors();
		$selector_from_form_value_name = $this->get_form_value_name('selector_from_form');
		$selector_by_form_value_name = $this->get_form_value_name("selector_by_form");
		$form = $this->get_hash_form();

		$form .= "
        <input type='hidden' name='cms_module_common_module_filters[]' value='$this->class_name'/>
		<div class='row'>";

		// On commence avec la valeur à comparer (filter_from)
		$form .= $this->get_selectors_form("from");
		if (!empty($this->parameters['selector']['from']) || (is_countable($selectors_from) && count($selectors_from) == 1)) {
			$selector_id = 0;
			if (is_countable($this->selectors['from']) && !empty($this->parameters['selector']['from'])) {
			    $nb_selector_from = count($this->selectors['from']);
				for ($i = 0; $i < $nb_selector_from; $i++) {
					if ($this->selectors['from'][$i]['name'] == $this->parameters['selector']['from']) {
						$selector_id = $this->selectors['from'][$i]['id'];
						break;
					}
				}
				$selector_name = $this->parameters['selector']['from'];
			} elseif (count($selectors_from) == 1) {
				$selector_name = $selectors_from[0];
			}
			$form .= "
			<script type='text/javacsript'>
			    cms_module_load_elem_form('$selector_name', '$selector_id', '$selector_from_form_value_name');
			</script>";
		}
		$form .= "
		</div>
		<div class='row'>
            <div class='colonne3'>
                <label>".$this->format_text($this->msg['cms_module_common_filter_compare_from'])."</label>
            </div>
            <div class='colonne3'>&nbsp;</div>
            <div class='colonne-suite'>
                <input type='button' class='bouton' value='X' onclick=\"destroy_filter(this, ".$this->id.", '".$this->class_name."');\"/>
            </div>
            <script>
    			if (typeof destroy_filter != 'function') {
    				function destroy_filter(node, id, class_name) {
    					dojo.xhrGet({
    						url : './ajax.php?module=cms&categ=module&elem=' + class_name + '&action=delete&id=' + id
    					});
    					var content = dijit.byId(node.parentNode.parentNode.parentNode.id);
    					if (content) {
    						content.destroyRecursive(false);
    					}
    				}
    			}
		    </script>
		</div>
		<div class='row'>
		    <div id='$selector_from_form_value_name' dojoType='dojox.layout.ContentPane'></div>
		</div>";

		// On continue avec la valeur à laquelle comparer (filter_by)
		$form .= "
		<div class='row'>
			<div class='colonne3'>
				<label>".$this->format_text($this->msg['cms_module_common_filter_compare_with'])."</label>
			</div>
  			<div class='colonne-suite'>";
		$form .= $this->get_selectors_form("by");
		if (!empty($this->parameters['selector']['by']) || (is_countable($selectors_by) && count($selectors_by) == 1)) {
			if (!empty($this->parameters['selector']['by'])) {
			    $nb_selector_by = is_countable($this->selectors['by']) ? count($this->selectors['by']) : 0;
			    for ($i = 0; $i < $nb_selector_by; $i++) {
					if ($this->selectors['by'][$i]['name'] == $this->parameters['selector']['by']) {
						$selector_id = $this->selectors['by'][$i]['id'];
						break;
					}
				}
				$selector_name = $this->parameters['selector']['by'];
			} elseif (count($selectors_by) == 1) {
 				$selector_name = $selectors_by[0];
 			}
 			$form .= "
 			 	<script type='text/javacsript'>
 			 		cms_module_load_elem_form('$selector_name', '$selector_id', '$selector_by_form_value_name');
 			 	</script>";
		}
		$form.="
			</div>
		</div>
		<div class='row'>
			<div id='$selector_by_form_value_name' dojoType='dojox.layout.ContentPane'></div>
		</div>";

		return $form;
	}

	protected function get_selectors_form($type) {
		switch ($type) {
			case "from":
				$selectors = $this->get_filter_from_selectors();
				break;
			case "by":
				$selectors = $this->get_filter_by_selectors();
				break;
		}

		if (is_countable($selectors) && count($selectors) > 1) {
			$form = "
			<select name='".$this->get_form_value_name("selector_".$type."_choice")."' onchange='cms_module_load_elem_form(this.value,0,\"".$this->get_form_value_name("selector_by_form")."\");'>
                <option value=''>" . $this->msg['cms_module_common_filter_selector_by_choice_default'] . "</option>";
			foreach ($selectors as $selector) {
			    $selected = "";
			    if (
			        !empty($this->parameters) &&
			        !empty($this->parameters['selector']) &&
			        !empty($this->parameters['selector'][$type]) &&
			        $this->parameters['selector'][$type] == $selector
			    ) {
    			    $selected = "selected='selected'";
			    }

			    $form .= sprintf('<option value="%s" %s>%s</option>', $selector, $selected, $this->format_text($this->msg[$selector]));
			}
			$form .= "
			</select>";
		} else {
			$form = "
			<input type='hidden' name='".$this->get_form_value_name("selector_".$type."_choice")."' value='".$selectors[0]."'/>";
		}

		return $form;
	}

	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){

		$this->parameters['selector']['by'] = $this->get_value_from_form("selector_by_choice");
		$this->parameters['selector']['from'] = $this->get_value_from_form("selector_from_choice");

		$this->get_hash();
		if($this->id){
			$query = "update cms_cadre_content set";
			$clause = " where id_cadre_content=".$this->id;
		}else{
			$query = "insert into cms_cadre_content set";
			$clause = "";
		}
		$query.= "
			cadre_content_hash = '".$this->hash."',
			cadre_content_type = 'filter',
			cadre_content_object = '".$this->class_name."',".
			($this->cadre_parent ? "cadre_content_num_cadre = '".$this->cadre_parent."'," : "")."
			cadre_content_data = '".addslashes($this->serialize())."'
			".$clause;
		$result = pmb_mysql_query($query);

		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}

			//sélecteur
			$selector_by_id = $selector_from_id = 0;
			if (is_countable($this->selectors['by']) && !empty($this->selectors['by'])){
    			for($i=0 ; $i<count($this->selectors['by']) ; $i++){
    				if($this->parameters['selector']['by'] == $this->selectors['by'][$i]['name']){
    					$selector_by_id = $this->selectors['by'][$i]['id'];
    					break;
    				}
    			}
			}
			if (is_countable($this->selectors['from']) && !empty($this->selectors['from'])){
    			for($i=0 ; $i<count($this->selectors['from']) ; $i++){
    				if($this->parameters['selector']['from'] == $this->selectors['from'][$i]['name']){
    					$selector_from_id = $this->selectors['from'][$i]['id'];
    					break;
    				}
    			}
			}
			if($this->parameters['selector']['by'] && $this->parameters['selector']['from']){
				$selector_from = new $this->parameters['selector']['from']($selector_from_id);
				$selector_from->set_parent($this->id);
				$selector_from->set_cadre_parent($this->cadre_parent);
				$result = $selector_from->save_form();
				if($result){
					if($selector_from_id==0){
						$this->selectors['from'][] = array(
								'id' => $selector_from->id,
								'name' => $this->parameters['selector']['from']
						);
					}
					$selector_by = new $this->parameters['selector']['by']($selector_by_id);
					$selector_by->set_parent($this->id);
					$selector_by->set_cadre_parent($this->cadre_parent);
					$result = $selector_by->save_form();
					if($result){
						if($selector_by_id==0){
							$this->selectors['by'][] = array(
									'id' => $selector_by->id,
									'name' => $this->parameters['selector']['by']
							);
						}

						//on a tout sauvegardé, on garde la trace dans le filtre pour pas tout chamboulé dans les sélecteurs...
						$this->parameters['selectors'] = $this->selectors;
						pmb_mysql_query("update cms_cadre_content set cadre_content_data = '".addslashes($this->serialize())."' where id_cadre_content='".$this->id."'");
						return true;
					}else{
						$this->delete_hash();
						return false;
					}
				}else{
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

	public function get_headers($datas=array()){
		$headers=array();
		if($this->parameters['selector']){
			$selector = $this->get_selected_selector('none');
			$headers = array_merge($headers,$selector->get_headers($datas));
			$headers = array_unique($headers);
		}
		return $headers;
	}

	protected function get_selected_selector($origin){
		//on va chercher
	    if(is_countable($this->selectors) && is_countable($this->selectors[$origin]) && $this->parameters['selector'][$origin]!= ""){
			$current_selector_id = 0;
			for($i=0; $i<count($this->selectors[$origin]); $i++){
				if($this->selectors[$origin][$i]['name'] == $this->parameters['selector'][$origin]){
					return new $this->selectors[$origin][$i]['name']($this->selectors[$origin][$i]['id']);
				}
			}
		} else {
			return false;
		}
	}

	public function set_module_class_name($module_class_name){
		$this->module_class_name = $module_class_name;
	}

	protected function get_exported_datas(){
		$infos = parent::get_exported_datas();
		$infos['type'] = "filter";
		return $infos;
	}

	public function filter($datas){
		$filtered_datas= array();
		//on récupère le champ à tester...
		$field_from = '';
		$selector_from = $this->get_selected_selector("from");
		if(!empty($selector_from)) {
			$field_from = $selector_from->get_value();
		}
		//a quoi...
		$field_by = '';
		$selector_by = $this->get_selected_selector("by");
		if(!empty($selector_by)) {
			$field_by = $selector_by->get_value();
		}
		if($field_by){
			$fields = new cms_editorial_parametres_perso($field_from['type']);
			if(!isset($fields->t_fields[$field_from['field']])){
				$fields = new cms_editorial_parametres_perso($this->generic_type);
			}
		    $current = $fields->t_fields[$field_from['field']];
		    $field_by_data = explode(';', $field_by);
		    $field_by_data = array_map('trim', $field_by_data);
		    $query = "select cms_editorial_custom_origine from cms_editorial_custom_values where
                cms_editorial_custom_champ = ".$field_from['field'] ." and
                cms_editorial_custom_".$current['DATATYPE']." IN (\"".implode('", "', $field_by_data)."\")";
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)){
		        while($row = pmb_mysql_fetch_object($result)){
		            $tmp[] = $row->cms_editorial_custom_origine;
				}
		        $filtered_datas = array_intersect($datas, $tmp);
                // Pour retrouver un tableau indice dans l'ordre du premier (qui est déjà trié)
		        $filtered_datas = array_reverse(array_reverse($filtered_datas));
			}
		}else{
			//pas de valeur pour le filtre, on filtre pas...
			$filtered_datas=$datas;
		}
		return $filtered_datas;
	}
}