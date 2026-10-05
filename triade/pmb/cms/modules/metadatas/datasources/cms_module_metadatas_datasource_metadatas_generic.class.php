<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_metadatas_datasource_metadatas_generic.class.php,v 1.9.10.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_metadatas_datasource_metadatas_generic extends cms_module_common_datasource{

	public function __construct($id=0){
		parent::__construct($id);
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;

		$this->parameters= array();
		$this->parameters['selector'] = $selector_choice;
		$metadatas_list = $this->get_metas_list();
		foreach ($metadatas_list as $key_metadata_list=>$metadata_list) {
			$this->parameters[$this->get_form_value_name($key_metadata_list."_active")] = stripslashes($this->get_value_from_form($key_metadata_list."_active"));
			foreach ($metadata_list["items"] as $key=>$metadata) {
				$this->parameters[$this->get_form_value_name($key_metadata_list."_".$key)] = stripslashes($this->get_value_from_form($key_metadata_list."_".$key));
			}
		}
		return parent::save_form();
	}

	/*
	 * Récupération des métadatas utilisable pour cette source de donnée
	*/
	public function get_metas_list(){
		$metadatas = new cms_module_metadatas();
		return $metadatas->get_metas_list();
	}

	public function get_format_data_structure(){
		$format_datas = array();
		$format_datas[]= array(
				'var' => "id",
				'desc'=> $this->msg['cms_module_metadatas_datasource_django_id_desc']
		);
		$format_datas[]= array(
				'var' => "title",
				'desc'=> $this->msg['cms_module_metadatas_datasource_django_title_desc']
		);
		$format_datas[]= array(
				'var' => "resume",
				'desc'=> $this->msg['cms_module_metadatas_datasource_django_resume_desc']
		);
		$format_datas[]= array(
				'var' => "logo_url",
				'desc'=> $this->msg['cms_module_metadatas_datasource_django_logo_desc']
		);
		$format_datas[]= array(
				'var' => "link",
				'desc'=> $this->msg['cms_module_metadatas_datasource_django_link_desc']
		);
		$format_datas[]= array(
				'var' => "type",
				'desc'=> $this->msg['cms_module_metadatas_datasource_django_type_desc']
		);
		$format_datas[] = array(
				'var' => "get_vars.<variable>",
				'desc' => $this->msg['cms_module_metadatas_datasource_django_get_vars_desc']
		);
		$format_datas[] = array(
				'var' => "post_vars.<variable>",
				'desc' => $this->msg['cms_module_metadatas_datasource_django_post_vars_desc']
		);
		$format_datas[] = array(
				'var' => "session_vars",
				'desc' => $this->msg['cms_module_metadatas_datasource_django_session_vars_desc'],
				'children' =>array(
						array(
								'var' => "session_vars.view",
								'desc' => $this->msg['cms_module_metadatas_datasource_django_session_vars_view_desc'],
						),
						array(
								'var' => "session_vars.id_empr",
								'desc' => $this->msg['cms_module_metadatas_datasource_django_session_vars_id_empr_desc'],
						)
				)
		);
		$format_datas[] = array(
				'var' => "env_vars",
				'desc' => $this->msg['cms_module_metadatas_datasource_django_env_vars_desc'],
				'children' =>array(
						array(
								'var' => "env_vars.script",
								'desc' => $this->msg['cms_module_metadatas_datasource_django_session_vars_script_desc'],
						),
						array(
								'var' => "env_vars.request",
								'desc' => $this->msg['cms_module_metadatas_datasource_django_session_vars_request_desc'],
						)
				)
		);
		return $format_datas;
	}

	public function get_format_data_structure_tree($textarea){
		$html = "
		<div id='struct_tree' class='row'>
		</div>
		<script type='text/javascript'>
			require(['dojo/data/ItemFileReadStore', 'dijit/tree/ForestStoreModel', 'dijit/Tree','dijit/Tooltip'],function(Memory,ForestStoreModel,Tree,Tooltip){
				var datas = {identifier:'var',label:'var'};
				datas.items = ".encoding_normalize::json_encode($this->get_format_data_structure()).";

				var store = Memory({
					data :datas
				});
				var model = new ForestStoreModel({
					store: store,
					rootId: 'root',
					rootLabel:'Vars'
				});
				var tree = new Tree({
					model: model,
					showRoot: false,
					onDblClick: function(item){
						document.getElementById('".$textarea."').value = document.getElementById('".$textarea."').value + '{{'+item.var[0]+'}}';
					},

				},'struct_tree');
				new Tooltip({
					connectId: 'struct_tree',
					selector: 'span',
					getContent: function(matchedNode){
						return dijit.getEnclosingWidget(matchedNode).item.desc[0];
					}
				});
			});


		</script>";

		return $html;
	}

	protected function _get_display_toggle($key_metadata_list) {
		return "<input type='checkbox' id='".$this->get_form_value_name($key_metadata_list."_active")."' name='".$this->get_form_value_name($key_metadata_list."_active")."' class='switch' value='1' ".(!isset($this->parameters[$this->get_form_value_name($key_metadata_list."_active")]) || $this->parameters[$this->get_form_value_name($key_metadata_list."_active")] ? "checked='checked'" : "")." />
			<label for='".$this->get_form_value_name($key_metadata_list."_active")."'>".$this->format_text($this->msg['cms_module_metadatas_datasource_metadatas_generic_active'])."</label>";
	}

	public function get_form(){
		$form = parent::get_form();

		$metadatas_list = $this->get_metas_list();

		$form.= "<div class='row'>
						<label for='cms_module_metadatas_datasource_metadatas_generic_def_metadatas'>".$this->format_text($this->msg['cms_module_metadatas_datasource_metadatas_generic_def_metadatas'])."</label>
				</div>";
		$metadatas_format_form = "";
		foreach ($metadatas_list as $key_metadata_list=>$metadata_list) {
			$metadata_format_form = "
				<div class='row'>
					".$this->_get_display_toggle($key_metadata_list)."
				</div>
				<div class='row'>&nbsp;</div>";
			foreach ($metadata_list["items"] as $key=>$metadata) {
				if (!isset($this->parameters[$this->get_form_value_name($key_metadata_list."_".$key)])) {
					$active_template_content = $metadata["default_template"];
				} else {
					$active_template_content = $this->parameters[$this->get_form_value_name($key_metadata_list."_".$key)];
				}
				$metadata_format_form .= "<div class='row'>
					<div class='left'>
						<label for='".$this->get_form_value_name($key_metadata_list."_".$key)."' title='".$this->format_text($metadata["desc"])."'>".$metadata["label"]." (".$metadata_list["prefix"].$metadata_list["separator"].$key.")</label>
					</div>
					<div class='right'>
						<textarea name='".$this->get_form_value_name($key_metadata_list."_".$key)."' id='".$this->get_form_value_name($key_metadata_list."_".$key)."'>".$this->format_text($active_template_content)."</textarea>
					</div>
				</div>";
			}
			$metadatas_format_form.= gen_plus("metadatas_parameters_".$key_metadata_list, $this->format_text($metadata_list["name"]),$metadata_format_form,false);

		}

		$form.="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_metadatas_datasource_metadatas_generic_django_template_content'>".$this->format_text($this->msg['cms_module_metadatas_datasource_metadatas_generic_django_template_content'])."</label>
				".$this->get_format_data_structure_tree("")."
			</div>
			<div class='left'>
				".$metadatas_format_form."
			</div>
		</div>";

		return $form;
	}

	/*
	 * Récupération des groupes de méta-données...
	*/
	public function get_group_metadatas(){
		$datas = array();
		$metadatas_list = $this->get_metas_list();
		foreach ($metadatas_list as $i=>$metadata_list) {
			// !isset <=> activé par défaut pour ne pas avoir à éditer tous les cadres
			if (!isset($this->parameters[$this->get_form_value_name($i."_active")]) || $this->parameters[$this->get_form_value_name($i."_active")]) {
				$data = array();
				$data['replace'] = (isset($metadata_list['replace']) && $metadata_list['replace'] != "" ? true : false);
				$data["group_template"] = $metadata_list["group_template"];
				foreach ($metadata_list["items"] as $key=>$metadata) {
					if (isset($this->parameters[$this->get_form_value_name($i."_".$key)])) {
					    // On évite la boulette si pas de préfixe
					    if($metadata_list["prefix"] != ""){
					        $data["metadatas"][$metadata_list["prefix"].$metadata_list["separator"].$key] = $this->parameters[$this->get_form_value_name($i."_".$key)];
					    }else{
					        $data["metadatas"][$key] = $this->parameters[$this->get_form_value_name($i."_".$key)];
					    }
					}
				}
				$datas[] = $data;
			}
		}
		return $datas;
	}

	/*
	 * Récupération des données de la source...
	*/
	public function get_datas() {
		$datas = array();

		$datas['id'] = $this->get_module_dom_id();
		$datas['get_vars'] = $_GET;
		$datas['post_vars'] = $_POST;
		$datas['session_vars']['view'] = (isset($_SESSION['opac_view']) ? $_SESSION['opac_view'] : '');
		$datas['session_vars']['id_empr'] = $_SESSION['id_empr_session'];
		$datas['env_vars']['script'] = basename($_SERVER['SCRIPT_NAME']);
		$datas['env_vars']['request'] = basename($_SERVER['REQUEST_URI']);

		return $datas;
	}

	public function get_object_datas($datas) {
	    if (!is_object($datas)) {
	       return null;
	    }

	    if ((method_exists($datas, "get_id") === false && empty($datas->id)) || (method_exists($datas, "get_id") && empty($datas->get_id()))) {
			$datas->id = $this->get_module_dom_id();
		}
		if (empty($datas->get_vars)) {
			$datas->get_vars = $_GET;
		}
		if (empty($datas->post_vars)) {
			$datas->post_vars = $_POST;
		}
		if (empty($datas->session_vars)) {
		    $datas->session_vars = array();
		    $datas->session_vars['view'] = (isset($_SESSION['opac_view']) ? $_SESSION['opac_view'] : '');
			$datas->session_vars['id_empr'] = $_SESSION['id_empr_session'];
		}
		if (empty($datas->env_vars)) {
		    $datas->env_vars = array();
		    $datas->env_vars['script'] = basename($_SERVER['SCRIPT_NAME']);
		    $datas->env_vars['request'] = basename($_SERVER['REQUEST_URI']);
		}

		return $datas;
	}

// 	public function get_manage_form(){
// 		global $base_path;
// 		//variables persos...
// 		global $cms_template;
// 		global $cms_template_delete;

// 		if(!$this->managed_datas) $this->managed_datas = array();
// 		if($this->managed_datas['templates'][$cms_template_delete]) unset($this->managed_datas['templates'][$cms_template_delete]);

// 		$form="
// 		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
// 			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:200px;' >";
// 		if($this->managed_datas['templates']){
// 			foreach($this->managed_datas['templates'] as $key => $infos){
// 				$form.="
// 					<p>
// 						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_template=".$key."&action=get_form'>".$this->format_text($infos['name'])."</a>
// 						&nbsp;
// 						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_template_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_metadatas_datasource_django_delete_template'])."\")'>
// 							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
// 						</a>
// 					</p>";
// 			}
// 		}
// 		$form.="
// 				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_template=new&action=get_form'/>".$this->format_text($this->msg['cms_module_metadatas_datasource_django_add_template'])."</a>
// 			";
// 		$form.="
// 			</div>
// 			<div dojoType='dijit.layout.ContentPane' region='center' >";
// 		if($cms_template){
// 			$form.=$this->get_managed_form_start(array('cms_template'=>$cms_template));
// 			$form.=$this->get_managed_template_form($cms_template);
// 			$form.=$this->get_managed_form_end();
// 		}
// 		$form.="
// 			</div>
// 		</div>";
// 		return $form;
// 	}

// 	protected function get_managed_template_form($cms_template){
// 		global $opac_url_base;

// 		if($cms_template != "new"){
// 			$infos = $this->managed_datas['templates'][$cms_template];
// 		}else{
// 			$infos = array(
// 					'name' => "Nouveau Template",
// 					'content' => $this->default_template
// 			);
// 		}
// 		//nom
// 		$form.="
// 			<div class='row'>
// 				<div class='colonne3'>
// 					<label for='cms_module_metadatas_datasource_django_template_name'>".$this->format_text($this->msg['cms_module_metadatas_datasource_django_template_name'])."</label>
// 				</div>
// 				<div class='colonne-suite'>
// 					<input type='text' name='cms_module_metadatas_datasource_django_template_name' value='".$this->format_text($infos['name'])."'/>
// 				</div>
// 			</div>";
// 		//contenu
// 		$form.="
// 			<div class='row'>
// 				<div class='colonne3'>
// 					<label for='cms_module_metadatas_datasource_django_template_content'>".$this->format_text($this->msg['cms_module_metadatas_datasource_django_template_content'])."</label><br/>
// 					".$this->get_format_data_structure_tree("cms_module_metadatas_datasource_django_template_content")."
// 				</div>
// 				<div class='colonne-suite'>
// 					<textarea id='cms_module_metadatas_datasource_django_template_content' name='cms_module_metadatas_datasource_django_template_content'>".$this->format_text($infos['content'])."</textarea>
// 				</div>
// 			</div>";
// 		return $form;
// 	}

// 	public function save_manage_form($managed_datas){
// 		global $cms_template;
// 		global $cms_template_delete;
// 		global $cms_module_metadatas_datasource_django_template_name,$cms_module_metadatas_datasource_django_template_content;

// 		if($cms_template_delete){
// 			unset($managed_datas['templates'][$cms_template_delete]);
// 		}else{
// 			if($cms_template == "new"){
// 				$cms_template = "template".(cms_module_metadatas_datasource_metadatas_generic::get_max_template_id($managed_datas['templates'])+1);
// 			}
// 			$managed_datas['templates'][$cms_template] = array(
// 					'name' => stripslashes($cms_module_metadatas_datasource_django_template_name),
// 					'content' => stripslashes($cms_module_metadatas_datasource_django_template_content)
// 			);
// 		}
// 		return $managed_datas;
// 	}

// 	protected function get_max_template_id($datas){
// 		$max = 0;
// 		if(count($datas)){
// 			foreach	($datas as $key => $val){
// 				$key = intval(str_replace("template","",$key));
// 				if($key>$max) $max = $key;
// 			}
// 		}
// 		return $max;
// 	}

}