<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state.class.php,v 1.11 2023/05/04 14:15:47 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/editions_state.tpl.php");
require_once($class_path."/editions_state_order.class.php");
require_once($class_path."/editions_datasource.class.php");

class editions_state {
	public $id = 0;
	public $name="";
	public $classement;
	public $comment = "";
	public $used_datasource = "";
	public $datasource;
	public $state_fields_list = array();	
	public $state_fields_params = array();
	public $fields;
	
	public function __construct($id=0){
		$this->id = intval($id);
		$this->fetch_data();
	} 

	protected function fetch_data(){
		if(!$this->id){
			$this->name = "";
			$this->classement= 0;
			$this->comment = "";
			$this->used_datasource = "";			
		}else{
			$query = "select editions_state_name, editions_state_used_datasource, editions_state_comment, editions_state_num_classement,editions_state_fieldslist, editions_state_fieldsparams from editions_states where id_editions_state = ".$this->id;			
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->name = $row->editions_state_name;
				$this->used_datasource = $row->editions_state_used_datasource;
				$this->comment = $row->editions_state_comment;
				$this->classement = $row->editions_state_num_classement;
				$this->state_fields_list = unserialize($row->editions_state_fieldslist);
				$this->state_fields_params = unserialize($row->editions_state_fieldsparams);
//				$this->datasource = new $this->used_datasource();
			}
		}
		$this->datasource = new editions_datasource($this->used_datasource);
		$this->fields = $this->datasource->get_struct_format();
		$this->get_filter();//Je récupère les valeurs des filtres si besoin
	}
	
	public function save(){
		if($this->id){
			$query = "update editions_states";
			$clause= " where id_editions_state = ".$this->id;
		}else{
			$query ="insert into editions_states";
			$clause= "";
		}
		//On supprimer les informations de paramétrage pour les vues au cas ou on supprime un champ utilisé pour une vue
		$this->state_fields_params["view"]=array();
		
		//on va chercher les infos des filtres, tris, groupements...
		$query.=" set
			editions_state_name = '".addslashes($this->name)."',
			editions_state_num_classement = '".addslashes($this->classement)."',
			editions_state_used_datasource = '".addslashes($this->used_datasource)."',
			editions_state_comment = '".addslashes($this->comment)."',
			editions_state_fieldslist = '".addslashes(serialize($this->state_fields_list))."',
			editions_state_fieldsparams = '".addslashes(serialize($this->state_fields_params))."'";
		pmb_mysql_query($query.$clause);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
		}
	}
	
	public function update_param_view($param){
		if($this->id){
			$this->state_fields_params["view"]=$param;
			$query = "update editions_states ";
			$query.=" set editions_state_fieldsparams = '".addslashes(serialize($this->state_fields_params))."'";
			$clause= " where id_editions_state = ".$this->id;
			pmb_mysql_query($query.$clause);
		}
	}
	
	public static function delete($id){
		$id = intval($id);
		if($id){
			$query = "delete from editions_states where id_editions_state=".$id;
			pmb_mysql_query($query);
		}
		return true;
	}
	
	public function get_from_form(){
		global $editions_state_name;
		global $editions_state_classement;
		global $editions_state_datasource;
		global $editions_state_comment;
		global $editions_state_fields_fields;
		global $editions_state_fields_content_fields;
		global $editions_state_filters_fields;
		global $editions_state_filters_content_fields;
		global $editions_state_orders_fields;
		global $editions_state_orders_content_fields;
		global $class_path;
		global $partial_submit,$action;
		
		$this->name = 	stripslashes($editions_state_name);
		$this->classement =	$editions_state_classement;
		$this->used_datasource = $editions_state_datasource;
		
//		$this->datasource = new $this->used_datasource();
		$this->datasource = new editions_datasource($this->used_datasource);
		$this->comment = stripslashes($editions_state_comment);
		$this->fields = $this->datasource->get_struct_format();
		if($partial_submit == 1 || $action == "save"){// On vient de déplacer une information pour créer l'état
			//Je garde les valeurs
			$this->state_fields_list=array(
				'fields' => array(
					'fields' => (is_array($editions_state_fields_fields) ? $editions_state_fields_fields : array()),
					'content' => (is_array($editions_state_fields_content_fields) ? $editions_state_fields_content_fields : array()) 
				),
				'filters' => array(
					'fields' => (is_array($editions_state_filters_fields) ? $editions_state_filters_fields : array()),
					'content' => (is_array($editions_state_filters_content_fields) ? $editions_state_filters_content_fields : array()),
				),
				'orders' => array(
					'fields' => (is_array($editions_state_orders_fields) ? $editions_state_orders_fields : array()),
					'content' => (is_array($editions_state_orders_content_fields) ? $editions_state_orders_content_fields : array()),
				)
			);
			$this->get_filter();
			
			$this->state_fields_params['orders']=array();//on initialise
			foreach($this->state_fields_list['orders']['content'] as $field){
				$order = new editions_state_order($this->fields[$field]);
				$this->state_fields_params['orders'][$field] = $order->get_params();
			}
		
		}else{//$partial_submit == 2 On vient de changer la source de données
			//Je réinitialise toutes les informations
			$this->state_fields_list=array(
				'fields' => array(
					'fields' => array(),
					'content' => array() 
				),
				'filters' => array(
					'fields' => array(),
					'content' => array(),
				),
				'orders' => array(
					'fields' => array(),
					'content' => array(),
				)
			);
		}
	}
	
	public function get_form(){
		global $msg,$charset;
		global $editions_state_content_form;
		
		$content_form = $editions_state_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_form('editions_state_form');
		if(!$this->id){
			$interface_form->set_label($msg['704']);
		}else{
			$interface_form->set_label($msg['procs_modification']);
		}
		
		//positionnement auto sur le dernier onglet, ca marche tout seul, pas besoin de s'en soucier !
		global $editionsstate_active_tab;
		$content_form = str_replace('!!active_tab!!', htmlentities($editionsstate_active_tab ?? "",ENT_QUOTES, $charset), $content_form);
		
		//nom 
		$content_form = str_replace('!!name!!', htmlentities($this->name,ENT_QUOTES, $charset), $content_form);
		//commentaire 
		$content_form = str_replace('!!comment!!', htmlentities($this->comment,ENT_QUOTES, $charset), $content_form);
		//classement
		$combo_clas= gen_liste ("SELECT idproc_classement,libproc_classement FROM procs_classements ORDER BY libproc_classement ", "idproc_classement", "libproc_classement", "editions_state_classement", "", $this->classement, 0, $msg['proc_clas_aucun'],0, $msg['proc_clas_aucun']) ;
		$content_form = str_replace('!!classement!!', $combo_clas, $content_form);
		
		//source de données
		$datasource_options = "
			<option value='0'>".$msg['editions_state_datasource_choice']."</options>";
		$datasources_list = $this->datasource->get_datasources_list();
		foreach($datasources_list as $data_key => $label){
			$datasource_options.="
			<option value='".htmlentities($data_key,ENT_QUOTES,$charset)."' ".($this->used_datasource == $data_key ? "selected='selected'":"").">".htmlentities($label,ENT_QUOTES,$charset)."</option>";
		}
//		
//		$datasource_options.="
//			<option value='editions_datasource_loans' ".($this->used_datasource == "editions_datasource_loans" ? "selected='selected'":"").">Prêts</option>";
		$content_form = str_replace('!!datasource_options!!', $datasource_options, $content_form);
		
		if((isset($this->state_fields_list['filters']['content']) && count($this->state_fields_list['filters']['content'])) || (isset($this->state_fields_list['fields']['content']) && count($this->state_fields_list['fields']['content']))){
			//J'ai commencé à créer un état je ne peux donc pas changer de source
			$content_form = str_replace('!!datasource_readonly!!', "disabled='disabled'", $content_form);
			$content_form = str_replace('<!--editions_state_datasource-->', "<input type='hidden' name='editions_state_datasource' id='editions_state_datasource' value='".$this->used_datasource."'/>", $content_form);
		}else{
			$content_form = str_replace('!!datasource_readonly!!', "", $content_form);
		}
		
		if(!$this->used_datasource){
			$content_form = str_replace("!!tabs!!","",$content_form);
		}else{
			$content_form = str_replace("!!tabs!!",$this->get_tabs_form(),$content_form);
		}
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['editions_state_confirm_delete'])
		->set_content_form($content_form)
		->set_table_name('editions_states')
		->set_field_focus('editions_state_name');
		return $interface_form->get_display();
	}
	
	public function get_tabs_form(){
		global $msg,$charset;
		global $class_path;
		global $editions_state_form_tabs;
		
		$form = $editions_state_form_tabs;
		
		if(count($this->state_fields_list['fields']['fields']) ==0 && count($this->state_fields_list['fields']['content']) == 0){
			foreach($this->fields as $id => $field){
				$this->state_fields_list['fields']['fields'][] = $id;
				$this->state_fields_list['filters']['fields'][] = $id;
			}
		}else{
			$nb_champ=count($this->state_fields_list['fields']['fields']) + count($this->state_fields_list['fields']['content']);
			if($nb_champ < count($this->fields)){
				//On a ajouté des champs dans le fichier datasources.xml
				foreach($this->fields as $id => $field){
					if(!in_array($id,$this->state_fields_list['fields']['fields']) && !in_array($id,$this->state_fields_list['fields']['content'])){
						$this->state_fields_list['fields']['fields'][] = $id;
						$this->state_fields_list['filters']['fields'][] = $id;
					}
				}
			}elseif($nb_champ > count($this->fields)){
				//On a enlevé des champs dans le fichier datasources.xml
				foreach($this->state_fields_list['fields']['fields'] as $key => $field){
					if(!($this->fields[$field])){
						unset($this->state_fields_list['fields']['fields'][$key]);
						$key_fiters=array_search ($field, $this->state_fields_list['filters']['fields']);
						if( $key_fiters !== false){
							unset($this->state_fields_list['filters']['fields'][$key_fiters]);
						}
						$key_fiters=array_search ($field, $this->state_fields_list['filters']['content']);
						if( $key_fiters !== false){
							unset($this->state_fields_list['filters']['content'][$key_fiters]);
						}
					}
				}
				foreach($this->state_fields_list['fields']['content'] as $key => $field){
					if(!($this->fields[$field])){
						unset($this->state_fields_list['fields']['content'][$key]);
						$key_fiters=array_search ($field, $this->state_fields_list['filters']['fields']);
						if( $key_fiters !== false){
							unset($this->state_fields_list['filters']['fields'][$key_fiters]);
						}
						$key_fiters=array_search ($field, $this->state_fields_list['filters']['content']);
						if( $key_fiters !== false){
							unset($this->state_fields_list['filters']['content'][$key_fiters]);
						}
						$key_fiters=array_search ($field, $this->state_fields_list['orders']['fields']);
						if( $key_fiters !== false){
							unset($this->state_fields_list['orders']['fields'][$key_fiters]);
						}
						$key_fiters=array_search ($field, $this->state_fields_list['orders']['content']);
						if( $key_fiters !== false){
							unset($this->state_fields_list['orders']['content'][$key_fiters]);
						}
					}
				}
			}
		}
		//contruction de la liste des champs pour l'onglets champs
		$form = str_replace("!!fields_fields_list!!",$this->gen_tab_list('fields'),$form);
		$form = str_replace("!!fields_fields_content!!",$this->gen_tab_content('fields'),$form);	
		//contruction de la liste des champs pour l'onglets filtres
		$form = str_replace("!!filters_fields_list!!",$this->gen_tab_list('filters'),$form);
		$form = str_replace("!!filters_fields_content!!",$this->gen_tab_content('filters'),$form);		
		//contruction de la liste des champs pour l'onglets tri
		$form = str_replace("!!order_fields_list!!",$this->gen_tab_list('orders'),$form);
		$form = str_replace("!!order_fields_content!!",$this->gen_tab_content('orders'),$form);		
		return $form;
	}
	
	protected function _compare_fields($a, $b) {
		if(isset($this->fields[$a]['label']) && $this->fields[$b]['label']) {
			return strcmp(convert_diacrit($this->fields[$a]['label']), convert_diacrit($this->fields[$b]['label']));
		}
		return '';
	}
	
	protected function _sort_tab_fields($tab) {
		usort($this->state_fields_list[$tab]['fields'], array($this, "_compare_fields"));
	}
	
	public function gen_tab_list($tab){
		$list = "";
		$this->_sort_tab_fields($tab);
		foreach($this->state_fields_list[$tab]['fields'] as $field){
			switch($tab){
				case "fields" :
					$id = $this->fields[$field]['id'];
					break;
				case "filters" :
					$id = "filter_".$this->fields[$field]['id'];
					break;
				case "orders" :
					$id = "crit_".$this->fields[$field]['id'];
					break;
			}
			$list.="
						<div id='".$id."' class='row' dragtype='editionsstate".$tab."' draggable='yes'>
							<input type='hidden' name='editions_state_".$tab."_fields[]' value='".$this->fields[$field]['id']."' />
							<span style='cursor: pointer;'>".$this->fields[$field]['label']."</span>
						</div>";
		}
		return $list;
	}

	public function gen_tab_content($tab,$draggable=true){
		global $class_path;
		global $msg,$charset;
		
		$content = "";
		foreach($this->state_fields_list[$tab]['content'] as $field){
			switch($tab){
				case "fields" :
					if($draggable){
						$content.= "<div class='row' id='".$tab."_".$field."' draggable='yes' dragtype='editionsstate".$tab."list' style='cursor: pointer;'>";
					}else{
						$content.= "<div class='row' id='".$tab."_".$field."'>";
					}
					$content.= "
							<input type='hidden' name='editions_state_".$tab."_content_fields[]' value='".$this->fields[$field]['id']."' />
							".$this->fields[$field]['label']."
						</div>";
					break;
				case "filters" :
					$class = $this->get_filter_class($field);
					if(file_exists($class_path."/".$class.".class.php")) {
						require_once($class_path."/".$class.".class.php");
						if(!isset($this->state_fields_params['filters'][$field])) {
							$this->state_fields_params['filters'][$field] = array();
						}
						$filter= new $class($this->fields[$field],$this->state_fields_params['filters'][$field]);
						$content.= "
						<div class='row' id='".$tab."_".$field."'>
							<input type='hidden' name='editions_state_".$tab."_content_fields[]' value='".$this->fields[$field]['id']."' />"; 
						$content.= $filter->get_form($draggable);	
						$content.="
						</div>";
					}
					break;
				case "orders" :
					$order = new editions_state_order($this->fields[$field],$this->state_fields_params['orders'][$field]);
					$content.= "
					<div class='row' id='".$tab."_".$field."'>
						<input type='hidden' name='editions_state_".$tab."_content_fields[]' value='".$this->fields[$field]['id']."' />"; 
					$content.= $order->get_form($draggable);
					$content.="
					</div>";
					break;
			}
		}
		return $content;
	}
	
	public function get_filter_class($field){
		$this->fields=$this->datasource->redo_values($field);//Je récupère les valeurs pour le cas où le champ est de type liste
		if($this->fields[$field]['input'] == "list"){
			$class = "editions_state_filter_list";
		}elseif($this->fields[$field]['input'] == "auth"){
			$class = "editions_state_filter_auth";
		}else{
			$class = "editions_state_filter_".$this->fields[$field]['type'];
		}
		return $class;
	}
	
	/*
	 * Récupération des filtres à partir de la variable global si elle est définit
	 */
	public function get_filter(){
		global $class_path;
		global $editions_state_filters_content_fields;
		if(isset($this->state_fields_list['filters']['content']) && is_array($this->state_fields_list['filters']['content']) && count($this->state_fields_list['filters']['content'])){//Si les filtres sont présent dans la variable
			foreach($this->state_fields_list['filters']['content'] as $field){
				$class = $this->get_filter_class($field);
				if(file_exists($class_path."/".$class.".class.php")) {
					require_once($class_path."/".$class.".class.php");
					$filter= new $class($this->fields[$field]);
					if($tmp=$filter->get_params()){
						$this->state_fields_params['filters'][$field] =$tmp;
					}
				}
			}
		}
	}
	
	public function show($sub="tab",$elem=""){
		global $class_path,$base_path;
		global $charset,$msg;
		global $edition_state_render;
		global $edition_state_filter_form;
		
	
		//jouons avec DoJo sur cette partie...
		//4 onglets : tableaux, TCD, groupement,graph
		$html = str_replace("!!id!!",$this->id,$edition_state_render);

		$html = str_replace("!!name!!","<a href='".$base_path."/edit.php?categ=state&action=edit&id=".$this->id."'>".htmlentities($this->name,ENT_QUOTES,$charset)."</a>",$html);
		
		$filter_form=$this->gen_tab_content("filters",false);
		
		if($filter_form){
			$html = str_replace("<!-- filter_form_content -->",str_replace(array("<!-- filter_form -->","!!sub!!"),array($filter_form,$sub),$edition_state_filter_form),$html);
		}
		
		$html = str_replace("!!class_".$sub."!!","onglet-perio-selected",$html);
		switch($sub){
			case "tab" :	
				$html = str_replace("!!class_tcd!!","onglets-perio",$html);
				$html = str_replace("!!class_group!!","onglets-perio",$html);
				$html = str_replace("!!class_graph!!","onglets-perio",$html);
				$view_class = "editions_state_view";
				break;
			case "tcd" :
				$html = str_replace("!!class_tab!!","onglets-perio",$html);
				$html = str_replace("!!class_group!!","onglets-perio",$html);
				$html = str_replace("!!class_graph!!","onglets-perio",$html);
				$view_class = "editions_state_view_tcd";
				break;
			case "group" :
				$html = str_replace("!!class_tab!!","onglets-perio",$html);
				$html = str_replace("!!class_tcd!!","onglets-perio",$html);
				$html = str_replace("!!class_graph!!","onglets-perio",$html);
				$view_class = "editions_state_view_group";
				break;
			case "graph" :
				$html = str_replace("!!class_tab!!","onglets-perio",$html);
				$html = str_replace("!!class_group!!","onglets-perio",$html);
				$html = str_replace("!!class_tcd!!","onglets-perio",$html);
				$view_class = "editions_state_view_graph";
				break;
		}
		$datas = $this->datasource->get_datas($this->state_fields_list,$this->state_fields_params);
		
		require_once($class_path."/".$view_class.".class.php");

		$view = new $view_class($datas,$this->id,$this->state_fields_params["view"]);
		$this->update_param_view($view->get_param());
		
		$html = str_replace("!!editions_state_render!!",$view->show(),$html);
		switch($elem){
			case "xls" :
				$view->render_xls_file("plop");
				break;
			default : 
				return $html;
				break;
		}
//		return $html;
	}
	
	public function render_file($sub,$elem){
		global $class_path;
		$datas = $this->datasource->get_datas($this->state_fields_list,$this->state_fields_params);
		
		switch($sub){
			case "tab" :
				$view_class = "editions_state_view";
				break;
			case "tcd" :
				$view_class = "editions_state_view_tcd";
				break;
			case "group" :
				$view_class = "editions_state_view_group";
				break;
			case "graph" :
				$view_class = "editions_state_view_graph";
				break;
		}
		
		require_once($class_path."/".$view_class.".class.php");
		$view = new $view_class($datas,$this->id);
		$file_name = convert_diacrit(str_replace(" ","_",$this->name));
		switch($elem){
			case "xls" :
				$view->render_xls_file($file_name);
				break;
			default : 
				return $html;
				break;
		}		
	}
}