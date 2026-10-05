<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_tagcloud_datasource_tagcloud_manuel.class.php,v 1.2.14.2 2025/01/21 15:29:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_tagcloud_datasource_tagcloud_manuel extends cms_module_tagcloud_datasource_tagcloud{

	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
		);
	}

	public function get_form(){
		$form = parent::get_form();
		$form.= $this->format_text($this->msg['cms_module_tagcloud_datasource_tagcloud_no_parameters']);

		return $form;
	}
	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;

		$this->parameters= array();
		return parent::save_form();
	}

	public function get_manage_form(){
		global $base_path;
		//variables persos...
		global $cms_cloud;
		global $cms_cloud_delete;

		if(!$this->managed_datas) $this->managed_datas = array();
		if($this->managed_datas['clouds'][$cms_cloud_delete]) unset($this->managed_datas['clouds'][$cms_cloud_delete]);

		$form="
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:200px;' >";
		if($this->managed_datas['clouds']){
			foreach($this->managed_datas['clouds'] as $key => $infos){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_cloud=".$key."&action=get_form'>".$this->format_text($infos['name'])."</a>
						&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_cloud_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_tagcloud_datasource_tagcloud_delete_cloud'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
			$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_cloud=new&action=get_form'/>".$this->format_text($this->msg['cms_module_tagcloud_datasource_tagcloud_add_cloud'])."</a>
			";
		$form.="
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center' >";
		if($cms_cloud){
			$form.=$this->get_managed_form_start(array('cms_cloud'=>$cms_cloud));
			$form.=$this->get_managed_cloud_form($cms_cloud);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;
	}

	protected function get_managed_cloud_form($cms_cloud){
		global $opac_url_base;

		if($cms_cloud != "new"){
			$infos = $this->managed_datas['clouds'][$cms_cloud];
		}else{
			$infos = array(
					'name' => "Nouveau Nuage"
			);
		}
		//nom
		$form ="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_tagcloud_datasource_tagcloud_cloud_name'>".$this->format_text($this->msg['cms_module_tagcloud_datasource_tagcloud_cloud_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_tagcloud_datasource_tagcloud_cloud_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		//contenu
// 		$form.="
// 			<div class='row'>
// 				<div class='colonne3'>
// 					<label for='cms_module_common_view_django_template_content'>".$this->format_text($this->msg['cms_module_common_view_django_template_content'])."</label><br/>
// 					".$this->get_format_data_structure_tree("cms_module_common_view_django_template_content")."
// 				</div>
// 				<div class='colonne-suite'>
// 					<textarea id='cms_module_common_view_django_template_content' name='cms_module_common_view_django_template_content'>".$this->format_text($infos['content'])."</textarea>
// 				</div>
// 			</div>";
		return $form;
	}

	public function save_manage_form($managed_datas){
		global $cms_cloud;
		global $cms_cloud_delete;
		global $cms_module_tagcloud_datasource_tagcloud_cloud_name;

		if($cms_cloud_delete){
			unset($managed_datas['clouds'][$cms_cloud_delete]);
		}else{
			if($cms_cloud == "new"){
				$cms_cloud = "cloud".(self::get_max_cloud_id($managed_datas['cloud'])+1);
			}
			$managed_datas['clouds'][$cms_cloud] = array(
					'name' => stripslashes($cms_module_tagcloud_datasource_tagcloud_cloud_name),
			);
		}
		return $managed_datas;
	}

	protected static function get_max_cloud_id($datas){
		$max = 0;
		if (is_countable($datas) && count($datas)) {
			foreach	($datas as $key => $val){
				$key = intval(str_replace("cloud","",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}














	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
// 	$selector = $this->get_selected_selector();
// 		if($selector){
// 			$article_id = $selector->get_value();
// 			$article_ids = $this->filter_datas("articles",array($selector->get_value()));
// 			if($article_ids[0]){
// 				$article = new cms_article($article_ids[0]);
// 				return $article->format_datas();
// 			}
// 		}
// 		return false;
		return array(
			array(
				'label' => "un label 1",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label ",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 4",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 5",
				'link' => "ici un lien a mettre",
				'weight' => 2,
				'js' => ""
			),
			array(
				'label' => "un label 6",
				'link' => "ici un lien a mettre",
				'weight' => 4,
				'js' => ""
			),
			array(
				'label' => "un label 7",
				'link' => "ici un lien a mettre",
				'weight' => 10,
				'js' => ""
			),
			array(
				'label' => "un label 8",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 9",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 10",
				'link' => "ici un lien a mettre",
				'weight' => 2,
				'js' => ""
			)
		);
	}
}