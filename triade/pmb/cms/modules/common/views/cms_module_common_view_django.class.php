<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_django.class.php,v 1.37 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_common_view_django extends cms_module_common_view{
	protected $cadre_parent;

	public function __construct($id=0){
	    parent::__construct((int) $id);
	}

	public function get_form(){
		$form = '';
		if(isset($this->managed_datas['templates']) && count($this->managed_datas['templates'])){
			//sélection d'un template définie en adminsitration
			$form.="
		<div clas='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_django_template_choice'>".$this->format_text($this->msg['cms_module_common_view_django_template_choice'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='cms_module_common_view_django_template_choice' onchange='load_cms_template_content(this.value);'>
					<option value='0'>".$this->format_text($this->msg['cms_module_common_view_django_template_choice'])."</value>";
			foreach($this->managed_datas['templates'] as $key => $infos){
				$form.="
					<option value='".$key."'>".$this->format_text($infos['name'])."</option>";
			}
			$form.="
				</select>

				<script type='text/javascript'>
					function load_cms_template_content(template){
						switch(template){";
			foreach($this->managed_datas['templates'] as $key => $infos){
				$contents = explode("\n",$infos['content']);
				$form.="
							case '".$key."' :
								if(pmbDojo.aceManager.getEditor('cms_module_common_view_django_template_content')){
									pmbDojo.aceManager.getEditor('cms_module_common_view_django_template_content').selectAll();
									pmbDojo.aceManager.getEditor('cms_module_common_view_django_template_content').remove();
								}else{
									dojo.byId('cms_module_common_view_django_template_content').value='';		
								}
								";
				foreach($contents as $content){
					$form.="
							if(pmbDojo.aceManager.getEditor('cms_module_common_view_django_template_content')){
								pmbDojo.aceManager.getEditor('cms_module_common_view_django_template_content').insert(\"".str_replace(array("\n","\r"),"",addslashes($content))."\"+\"\\n\")
							}else{
								dojo.byId('cms_module_common_view_django_template_content').value+= \"".str_replace(array("\n","\r"),"",addslashes($content)).'\n'."\"		
							}
							";
				}
				$form.="
								break;";
			}
			$form.="
							default :
								//do nothing
								break;
						}
					}
				</script>
			</div>
		</div>";
		}
		
		if(!isset($this->parameters['active_template']) || $this->parameters['active_template'] == ""){
			$this->parameters['active_template'] = $this->default_template;
		}
	
		$form.="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_django_template_content'>".$this->format_text($this->msg['cms_module_common_view_django_template_content'])."</label>
				".$this->get_format_data_structure_tree("cms_module_common_view_django_template_content")."
			</div>
			<div class='colonne-suite'>
				<textarea name='cms_module_common_view_django_template_content' id='cms_module_common_view_django_template_content'>".$this->format_text($this->parameters['active_template'])."</textarea>
			</div>
		</div>".$this->get_ace_editor_script();

		return $form;
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $cms_module_common_view_template_choice;
		global $cms_module_common_view_templates;
		global $cms_module_common_view_django_template_content;

		$this->parameters['active_template'] = $this->stripslashes($cms_module_common_view_django_template_content);
		return parent::save_form();
	}

	public function render($datas){
	    
	    if(!isset($datas) || !is_array($datas)){
	    	$datas=array();
	    }
		if(!isset($datas['id']) || !$datas['id']){
			$datas['id'] = $this->get_module_dom_id();
		}
		if(!isset($datas['get_vars']) || !$datas['get_vars']){
			$datas['get_vars'] = $_GET;
		}
		if(!isset($datas['post_vars']) || !$datas['post_vars']){
			$datas['post_vars'] = $_POST;
		}
		try{
			$html = H2o::parseString($this->parameters['active_template'])->render($datas);
			if (!empty($datas['css'])) $html.= '<style>'.$datas['css'].'</style>';
		}catch(Exception $e){
			$html = $this->msg["cms_module_common_view_error_template"];
		}
		return $html;
	}

	public function get_manage_form(){
		global $base_path;
		//variables persos...
		global $cms_template;
		global $cms_template_delete;

		if(!$this->managed_datas) $this->managed_datas = array();
		if(isset($this->managed_datas['templates'][$cms_template_delete])) unset($this->managed_datas['templates'][$cms_template_delete]);

		$form="
		<div data-dojo-type='dijit/layout/BorderContainer' style='width: 100%; height: 800px;'>
			<div data-dojo-type='dijit/layout/ContentPane' region='left' splitter='true' style='width:200px;' >";
		if(isset($this->managed_datas['templates'])){
			foreach($this->managed_datas['templates'] as $key => $infos){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=views&elem=".$this->class_name."&cms_template=".$key."&action=get_form'>".$this->format_text($infos['name'])."</a>
						&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=views&elem=".$this->class_name."&cms_template_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_common_view_django_delete_template'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
			$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=views&elem=".$this->class_name."&cms_template=new&action=get_form'/>".$this->format_text($this->msg['cms_module_common_view_django_add_template'])."</a>
			";
		$form.="
			</div>
			<div data-dojo-type='dijit/layout/ContentPane' region='center' >";
		if($cms_template){
			$form.=$this->get_managed_form_start(array('cms_template'=>$cms_template));
			$form.=$this->get_managed_template_form($cms_template);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;
	}

	protected function get_managed_template_form($cms_template){
		global $opac_url_base;

		if($cms_template != "new"){
			$infos = $this->managed_datas['templates'][$cms_template];
		}else{
			$infos = array(
				'name' => "Nouveau Template",
				'content' => (isset($this->default_template) ? $this->default_template : '')
			);
		}
		//nom
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_django_template_name'>".$this->format_text($this->msg['cms_module_common_view_django_template_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_django_template_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		//contenu
		$form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_django_template_content'>".$this->format_text($this->msg['cms_module_common_view_django_template_content'])."</label><br/>
					".$this->get_format_data_structure_tree("cms_module_common_view_django_template_content")."
				</div>
				<div class='colonne-suite'>
					<textarea id='cms_module_common_view_django_template_content' name='cms_module_common_view_django_template_content'>".$this->format_text($infos['content'])."</textarea>
				</div>
			</div>
			".$this->get_ace_editor_script();
		return $form;
	}

	public function save_manage_form($managed_datas){
		global $cms_template;
		global $cms_template_delete;
		global $cms_module_common_view_django_template_name,$cms_module_common_view_django_template_content;

		if($cms_template_delete){
			unset($managed_datas['templates'][$cms_template_delete]);
		}else{
			if($cms_template == "new"){
				$cms_template = "template".(cms_module_common_view_django::get_max_template_id($managed_datas['templates'])+1);
			}
			$managed_datas['templates'][$cms_template] = array(
					'name' => stripslashes($cms_module_common_view_django_template_name),
					'content' => stripslashes($cms_module_common_view_django_template_content)
			);
		}
		return $managed_datas;
	}

	protected function get_max_template_id($datas){
		$max = 0;
		if(count($datas)){
			foreach	($datas as $key => $val){
				$key = str_replace("template","",$key)*1;
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}

	public function get_format_data_structure(){
		$format_datas = array();
		$format_datas[]= array(
			'var' => "id",
			'desc'=> $this->msg['cms_module_common_view_django_id_desc']
		);
		$format_datas[] = array(
			'var' => "get_vars.<variable>",
			'desc' => $this->msg['cms_module_common_view_django_get_vars_desc']
		);
		$format_datas[] = array(
			'var' => "post_vars.<variable>",
			'desc' => $this->msg['cms_module_common_view_django_post_vars_desc']
		);
		$format_datas[] = array(
			'var' => "session_vars",
			'desc' => $this->msg['cms_module_common_view_django_session_vars_desc'],
			'children' =>array(
				array(
					'var' => "session_vars.view",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_view_desc'],
				),
				array(
					'var' => "session_vars.id_empr",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_id_empr_desc'],
				),
				array(
					'var' => "session_vars.empr_nom",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_nom_desc'],
				),
				array(
					'var' => "session_vars.empr_prenom",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_prenom_desc'],
				),
				array(
					'var' => "session_vars.empr_mail",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_mail_desc'],
				),
				array(
					'var' => "session_vars.empr_login",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_login_desc'],
				),
				array(
					'var' => "session_vars.empr_adr1",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_adr1_desc'],
				),
				array(
					'var' => "session_vars.empr_adr2",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_adr2_desc'],
				),
				array(
					'var' => "session_vars.empr_cp",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_cp_desc'],
				),
				array(
					'var' => "session_vars.empr_ville",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_ville_desc'],
				),
				array(
					'var' => "session_vars.empr_categ",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_categ_desc'],
				),
				array(
					'var' => "session_vars.empr_codestat",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_codestat_desc'],
				),
				array(
					'var' => "session_vars.empr_sexe",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_sexe_desc'],
				),
				array(
					'var' => "session_vars.empr_year",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_year_desc'],
				),
				array(
					'var' => "session_vars.empr_cb",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_cb_desc'],
				),
				array(
					'var' => "session_vars.empr_prof",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_prof_desc'],
				),
				array(
					'var' => "session_vars.empr_tel1",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_tel1_desc'],
				),
				array(
					'var' => "session_vars.empr_tel2",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_tel2_desc'],
				),
				array(
					'var' => "session_vars.empr_location",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_location_desc'],
				),
				array(
					'var' => "session_vars.empr_date_adhesion",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_date_adhesion_desc'],
				),
				array(
					'var' => "session_vars.empr_date_expiration",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_date_expiration_desc'],
				),
				array(
					'var' => "session_vars.empr_statut",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_statut_desc'],
				),
				array(
					'var' => "session_vars.empr_first_authentification",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_empr_first_authentification_desc'],
				)
			)
		);
		$format_datas[] = array(
			'var' => "env_vars",
			'desc' => $this->msg['cms_module_common_view_django_env_vars_desc'],
			'children' =>array(
				array(
					'var' => "env_vars.script",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_script_desc'],
				),
				array(
					'var' => "env_vars.request",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_request_desc'],
				),
				array(
					'var' => "env_vars.opac_url",
					'desc' => $this->msg['cms_module_common_view_django_session_vars_opac_url_desc'],
				),
				array(
						'var' => "env_vars.platform",
						'desc' => $this->msg['cms_module_common_view_django_session_vars_platform_desc'],
				),
				array(
						'var' => "env_vars.browser",
						'desc' => $this->msg['cms_module_common_view_django_session_vars_browser_desc'],
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
				datas.items = ".json_encode($this->utf8_encode($this->get_format_data_structure())).";

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
						if(pmbDojo.aceManager.getEditor('".$textarea."')){
							pmbDojo.aceManager.getEditor('".$textarea."').insert('{{'+item.var[0]+'}}');
						}else{
							document.getElementById('".$textarea."').value = document.getElementById('".$textarea."').value + '{{'+item.var[0]+'}}';		
						}
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
	
}