<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_multiple.class.php,v 1.12.2.2 2025/01/21 15:29:48 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_multiple extends cms_module_common_datasource {
	
	public function get_available_datasources(){
		return array();
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
			$query = " select id_cadre_content, cadre_content_hash, cadre_content_num_cadre, cadre_content_data from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = (int) $row->id_cadre_content;
				$this->hash = $row->cadre_content_hash;
				$this->cadre_parent = (int) $row->cadre_content_num_cadre;
				$this->unserialize($row->cadre_content_data);
			}
			//on va chercher les infos des sous-sources
			$query = "select id_cadre_content, cadre_content_object from cms_cadre_content where cadre_content_type='datasource' and cadre_content_num_cadre != 0 and cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$this->datasources[] = array(
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
		$this->get_available_datasources();
		
		$form = "
			<div class='row'>";
		$form.=$this->get_hash_form();
		$form.= $this->get_datasources_list_form();
		$form.="
				<input type='hidden' name='".$this->get_form_value_name("nb_source")."' id='".$this->get_form_value_name("nb_source")."' value='0'/>
			</div>
			<div class='row' id='subdatasources'>
			</div>";
		return $form;
	}	
	
	/*
	 * Formulaire de sélection d'un sélecteur
	 */
	protected function get_datasources_list_form(){
		$datasources = $this->get_available_datasources();
		$form= "
			<div class='colonne3'>
				<label for='".$this->get_form_value_name("datasource_choice")."'>".$this->format_text($this->msg['cms_module_common_datasource_add_datasource'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='".$this->get_form_value_name("datasource_choice")."' id='".$this->get_form_value_name("datasource_choice")."'>
					<option value=''>".$this->format_text($this->msg['cms_module_common_datasource_datasource_choice'])."</option>";
		foreach($datasources as $datasource){
			$form.= "
					<option value='".$datasource."' ".(!empty($this->parameters['datasource']) && ($datasource == $this->parameters['datasource']) ? "selected='selected'":"").">".$this->format_text($this->msg[$datasource])."</option>";
		}
		$form.="
				</select>
			</div>
			<script type='text/javascript'>
				var multiple_msg = {}";
		foreach($datasources as $datasource){
			$form.="
				multiple_msg['".$datasource."'] = \"".$this->msg[$datasource]."\"";
		}
		$form.="
				require(['dijit/registry', 'dojo/on', 'dojo/topic', 'dojox/layout/ContentPane', 'dojo/dom','dojo/dom-construct'], function (registry, on, topic, ContentPane, dom, domConstruct) {
					on(dom.byId('".$this->get_form_value_name("datasource_choice")."'),'change',function (evt){	
						datasource = evt.target.value;
						cms_load_sub_datassource(datasource,0);
						evt.target.selectedIndex=0;
						evt.target.options[0].selected= true;
					});

					cms_load_sub_datassource=function (datasource,id){
						if(!id) id = 0;
						var nb = dom.byId('".$this->get_form_value_name("nb_source")."');
						nb.value = (nb.value*1)+1;
						var BaseId =  '".$this->get_form_value_name("datasource")."_'+nb.value;
						var ParentId = BaseId+'Parent';
						var ChildId = BaseId+'Child';
						var title = multiple_msg[datasource];
						var container = domConstruct.toDom('<div class=\"row\" id=\"'+BaseId+'\"><div class=\"colonne80\"><div id=\"'+ParentId+'\" class=\"parent\"><img id=\"'+BaseId+'Img\" class=\"img_plus\" src=\"".get_url_icon('minus.gif')."\" name=\"imEx\" title=\"'+title+'\" onclick=\"expandBase(\''+BaseId+'\',true)\" border=\"0\" hspace=\"3\"/><span class=\"heada\">'+title+'</span></div><div id=\"'+ChildId+'\" class=\"child\" style=\"margin-bottom:6px;display:block;\"></div></div><div class=\"colonne_suite\"><input type=\"button\" class=\"bouton\" value=\"X\" id=\"'+BaseId+'_delete\" /></div></div>');
						domConstruct.place(container,subdatasources);
						var input = domConstruct.toDom('<input type=\"hidden\" name=\"".$this->get_form_value_name("datasources_name")."[]\" value=\"'+datasource+'\"/>');
						domConstruct.place(input,ChildId);
						var input = domConstruct.toDom('<input type=\"hidden\" name=\"".$this->get_form_value_name("datasources_id")."[]\" value=\"'+id+'\"/>');
 						on(dom.byId(BaseId+'_delete'),'click',function(){
 							var node = dom.byId(BaseId);
 							widgets = registry.findWidgets(node);
 							for(var i=0 ; i<widgets.length; i++){
 								widgets[i].destroy()
 							}
 							node.remove();
 						});
						domConstruct.place(input,ChildId);
						var cp = new ContentPane({
							href: './ajax.php?module=cms&categ=module&elem='+datasource+'&action=get_form&id='+id,
							preload:true,
						}).placeAt(ChildId).startup();
					}
				});
			</script>";
		if(is_countable($this->parameters['datasources_name']) && count($this->parameters['datasources_name'])>0){
			$form.="
			<script type='text/javascript'>";
			for($i=0; $i<count($this->parameters['datasources_name']) ; $i++){
				$form.="
				cms_load_sub_datassource('".$this->parameters['datasources_name'][$i]."','".$this->parameters['datasources_id'][$i]."');";
			}
			$form.="
			</script>";
		}
		return $form;
	}
	
	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
		$this->parameters['datasources_name'] = $this->get_value_from_form('datasources_name');
		$this->parameters['datasources_id'] = $this->get_value_from_form('datasources_id');
		
		//TODO A REPRENDRE
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
			($this->cadre_parent ? "cadre_content_num_cadre = '".$this->cadre_parent."'," : "")."		
			cadre_content_data = '".addslashes($this->serialize())."'
			".$clause;
		$result = pmb_mysql_query($query);
		
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciennes sources de données...
			$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where id_cadre_content != '".$this->id."' and cadre_content_type='datasource' and cadre_content_num_cadre = '".$this->cadre_parent."' and cadre_content_num_cadre_content=0";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row= pmb_mysql_fetch_object($result)){
					$obj = new $row->cadre_content_object($row->id_cadre_content);
					$obj->delete();
				}
			}
 			//sous-sources
			if (is_countable($this->parameters['datasources_name'])) {
    			for($i=0 ; $i<count($this->parameters['datasources_name']) ; $i++){
    				$datasource = new $this->parameters['datasources_name'][$i]($this->parameters['datasources_id'][$i]);
    				$datasource->set_cadre_parent($this->cadre_parent);
    				$datasource->set_num_cadre_content($this->id);
    				$datasource->set_brothers($this->parameters['datasources_id']);
    				$result = $datasource->save_form();
    				$this->parameters['datasources_id'][$i] = $datasource->id;
    			}
 			}
			//On nettoie les sous sources absentes du cadres//on supprime les anciennes sources de données...
			$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where ";
			if(is_countable($this->parameters['datasources_id']) && count($this->parameters['datasources_id'])){
				$query.= "id_cadre_content not in (".implode(",",$this->parameters['datasources_id']).") and ";
			}
			$query.= "cadre_content_type='datasource' and cadre_content_num_cadre = '".$this->cadre_parent."' and cadre_content_num_cadre_content=".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row= pmb_mysql_fetch_object($result)){
					$obj = new $row->cadre_content_object($row->id_cadre_content);
					$obj->delete();
				}
			}
			//on remet à jour la liste des sous-sources dans la source multiple
			$query = "update cms_cadre_content set cadre_content_data = '".addslashes($this->serialize())."' where id_cadre_content='".$this->id."'";
			$result = pmb_mysql_query($query);
			return true;
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
		$datas = array();
		if (is_countable($this->parameters['datasources_name'])) {
    		for($i=0 ; $i<count($this->parameters['datasources_name']) ; $i++){
    			$datasource = new $this->parameters['datasources_name'][$i]($this->parameters['datasources_id'][$i]);
    			$datas = array_merge_recursive($datas,$datasource->get_datas());
    		}
		}
		return $datas;
	}
	
	public function get_headers($datas=array()){
		$headers=array();
		if($this->parameters['datasource']){
			$datasource = $this->get_selected_datasource();
			$headers = array_merge($headers,$datasource->get_headers($datas));
			$headers = array_unique($headers);
		}	
		return $headers;
	}
	
	protected function get_selected_datasource(){
		//on va chercher
	    if(is_countable($this->datasources) && $this->parameters['datasource']!= ""){
			for($i=0 ; $i<count($this->datasources) ; $i++){
				if($this->datasources[$i]['name'] == $this->parameters['datasource']){
					return new $this->parameters['datasource']($this->datasources[$i]['id']);
				}
			}
		}else{
			return false;
		}
	}
	
	public function get_format_data_structure(){
		return array();
	}
}