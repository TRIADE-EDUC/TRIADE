<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_view_django.class.php,v 1.11 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class frbr_entity_common_view_django extends frbr_entity_common_view{

	public function __construct($id=0){
	    parent::__construct((int) $id);
	}

	public function get_form(){
		global $class_path, $charset;
		$form = '';

		if(!isset($this->parameters->active_template) || $this->parameters->active_template == ""){
			$this->parameters->active_template = $this->default_template;
		}
		
		$form.="
		<div class='row'>
			<div class='colonne3'>
				<label for='frbr_entity_common_view_django_template_content'>".$this->format_text($this->msg['frbr_entity_common_view_django_template_content'])."</label>
				".$this->get_format_data_structure_tree("frbr_entity_common_view_django_template_content")."
			</div>
			<div class='colonne-suite'>
				<textarea name='frbr_entity_common_view_django_template_content' id='frbr_entity_common_view_django_template_content'>".htmlentities($this->parameters->active_template, ENT_QUOTES, $charset)."</textarea>
				<script src='./javascript/ace/ace.js' type='text/javascript' charset='utf-8'></script>
				<script>
				 	pmbDojo.aceManager.initEditor('frbr_entity_common_view_django_template_content');						
				</script>
			</div>
		</div>";
		return $form;
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $frbr_entity_common_view_django_template_content;

		$this->parameters->active_template = stripslashes($frbr_entity_common_view_django_template_content);
		return parent::save_form();
	}

	public function render($datas){
		if(!isset($datas['id']) || !$datas['id']){
			$datas['id'] = $this->get_entity_dom_id();
		}
		if(!isset($datas['get_vars']) || !$datas['get_vars']){
			$datas['get_vars'] = $_GET;
		}
		if(!isset($datas['post_vars']) || !$datas['post_vars']){
			$datas['post_vars'] = $_POST;
		}
		try{
			$html = H2o::parseString($this->parameters->active_template)->render($datas);
		}catch(Exception $e){
			$html = $this->msg["frbr_entity_common_view_error_template"];
		}
		return $html;
	}

	public function get_format_data_structure(){
		$format_datas = array();
		$format_datas[]= array(
			'var' => "id",
			'desc'=> $this->msg['frbr_entity_common_view_django_id_desc']
		);
		$format_datas[] = array(
			'var' => "get_vars.<variable>",
			'desc' => $this->msg['frbr_entity_common_view_django_get_vars_desc']
		);
		$format_datas[] = array(
			'var' => "post_vars.<variable>",
			'desc' => $this->msg['frbr_entity_common_view_django_post_vars_desc']
		);
		$format_datas[] = array(
			'var' => "session_vars",
			'desc' => $this->msg['frbr_entity_common_view_django_session_vars_desc'],
			'children' =>array(
				array(
					'var' => "session_vars.view",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_view_desc'],
				),
				array(
					'var' => "session_vars.id_empr",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_id_empr_desc'],
				),
				array(
					'var' => "session_vars.empr_nom",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_nom_desc'],
				),
				array(
					'var' => "session_vars.empr_prenom",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_prenom_desc'],
				),
				array(
					'var' => "session_vars.empr_mail",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_mail_desc'],
				),
				array(
					'var' => "session_vars.empr_login",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_login_desc'],
				),
				array(
					'var' => "session_vars.empr_adr1",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_adr1_desc'],
				),
				array(
					'var' => "session_vars.empr_adr2",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_adr2_desc'],
				),
				array(
					'var' => "session_vars.empr_cp",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_cp_desc'],
				),
				array(
					'var' => "session_vars.empr_ville",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_ville_desc'],
				),
				array(
					'var' => "session_vars.empr_categ",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_categ_desc'],
				),
				array(
					'var' => "session_vars.empr_codestat",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_codestat_desc'],
				),
				array(
					'var' => "session_vars.empr_sexe",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_sexe_desc'],
				),
				array(
					'var' => "session_vars.empr_year",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_year_desc'],
				),
				array(
					'var' => "session_vars.empr_cb",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_cb_desc'],
				),
				array(
					'var' => "session_vars.empr_prof",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_prof_desc'],
				),
				array(
					'var' => "session_vars.empr_tel1",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_tel1_desc'],
				),
				array(
					'var' => "session_vars.empr_tel2",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_tel2_desc'],
				),
				array(
					'var' => "session_vars.empr_location",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_location_desc'],
				),
				array(
					'var' => "session_vars.empr_date_adhesion",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_date_adhesion_desc'],
				),
				array(
					'var' => "session_vars.empr_date_expiration",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_date_expiration_desc'],
				),
				array(
					'var' => "session_vars.empr_statut",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_empr_statut_desc'],
				)
			)
		);
		$format_datas[] = array(
			'var' => "env_vars",
			'desc' => $this->msg['frbr_entity_common_view_django_env_vars_desc'],
			'children' =>array(
				array(
					'var' => "env_vars.script",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_script_desc'],
				),
				array(
					'var' => "env_vars.request",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_request_desc'],
				),
				array(
					'var' => "env_vars.opac_url",
					'desc' => $this->msg['frbr_entity_common_view_django_session_vars_opac_url_desc'],
				),
				array(
						'var' => "env_vars.platform",
						'desc' => $this->msg['frbr_entity_common_view_django_session_vars_platform_desc'],
				),
				array(
						'var' => "env_vars.browser",
						'desc' => $this->msg['frbr_entity_common_view_django_session_vars_browser_desc'],
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
				datas.items = ".json_encode(encoding_normalize::utf8_normalize($this->get_format_data_structure())).";

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