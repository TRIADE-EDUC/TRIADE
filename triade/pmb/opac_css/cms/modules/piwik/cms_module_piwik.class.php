<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_piwik.class.php,v 1.2.14.1 2025/01/17 10:40:47 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_piwik extends cms_module_common_module {

	public function __construct($id=0){
		$this->module_path = str_replace(basename(__FILE__),"",__FILE__);
		parent::__construct($id);
		$this->modcache = "no_cache";
	}

	protected function get_max_server_id($datas){
		$max = 0;
		if (is_countable($datas) && count($datas)) {
			foreach	($datas as $key => $val){
				$key = intval(str_replace("server","",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}

	public function get_manage_form(){
		global $base_path;
		//variables persos...
		global $server;

		$form="
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:300px;' >";
		if($this->managed_datas['module']['servers']){
			foreach($this->managed_datas['module']['servers'] as $key => $server_infos){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&server=".$key."&action=get_form'>".$this->format_text($server_infos['name'])."</a>
					&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&server_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_piwik_delete_server'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
		$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&server=new'/>Ajouter un Serveur Piwik</a>
			";
		$form.="
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center' >";
		if($server){
			$form.=$this->get_managed_form_start(array('server'=>$server));
			$form.=$this->get_managed_server_form($server);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;
	}

	public function get_managed_server_form($server){
		$infos = array();
		if($server != "new"){
			$infos = $this->managed_datas['module']['servers'][$server];
		}
		$form="
			<div class='row'>
				<div class='colonne3'>
				</div>
				<div class='colonne-suite'>
				</div>
			</div>";
		//nom du server
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_piwik_server_name'>".$this->format_text($this->msg['cms_module_piwik_server_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_piwik_server_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		//adresse du server
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_piwik_server_url'>".$this->format_text($this->msg['cms_module_piwik_server_url'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_piwik_server_url' value='".$this->format_text($infos['url'])."'/>
				</div>
			</div>";
		//identififiant du site
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_piwik_site_id'>".$this->format_text($this->msg['cms_module_piwik_site_id'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_piwik_site_id' value='".$this->format_text($infos['site_id'])."'/>
				</div>
			</div>";
		//suivi sur tous les sous-domaines
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_piwik_server_subdomain'>".$this->format_text($this->msg['cms_module_piwik_subdomains'])."</label> <br/>
					<span>".$this->format_text($this->msg['cms_module_piwik_subdomains_description'])."</span>
				</div>
				<div class='colonne-suite'>
					<input type='checkbox' name='cms_module_piwik_server_subdomain' value='1'".($infos['subdomains'] ? " checked='checked'" : "")." />
				</div>
			</div>";
		//prefix domaine
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_piwik_domain_prefix'>".$this->format_text($this->msg['cms_module_piwik_domain_prefix'])."</label> <br/>
					<span>".$this->format_text($this->msg['cms_module_piwik_domain_prefix_description'])."</span>
				</div>
				<div class='colonne-suite'>
					<input type='checkbox' name='cms_module_piwik_server_domain_prefix' value='1'".($infos['domain_prefix'] ? " checked='checked'" : "")." />
				</div>
			</div>";
		//click sortant vers sous-domaine
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_piwik_subdomains_links'>".$this->format_text($this->msg['cms_module_piwik_subdomains_links'])."</label> <br/>
					<span>".$this->format_text($this->msg['cms_module_piwik_subdomains_links_description'])."</span>
				</div>
				<div class='colonne-suite'>
					<input type='checkbox' name='cms_module_piwik_server_subdomains_links' value='1'".($infos['subdomains_links'] ? " checked='checked'" : "")." />
				</div>
			</div>";
		return $form;
	}

	public function save_manage_form(){
		global $server;
		global $server_delete;
		global $cms_module_piwik_server_name;
		global $cms_module_piwik_server_url;
		global $cms_module_piwik_server_subdomain;
		global $cms_module_piwik_server_domain_prefix;
		global $cms_module_piwik_server_subdomains_links;
		global $cms_module_piwik_site_id;

		$params = $this->managed_datas['module'];

		if($server_delete){
			unset($params['servers'][$server_delete]);
		}else{
			//ajout d'un server
			if($server == "new"){
				$server_infos = array(
					'name' => $cms_module_piwik_server_name,
					'url' => $cms_module_piwik_server_url,
					'site_id' => $cms_module_piwik_site_id,
					'subdomains' =>	$cms_module_piwik_server_subdomain,
					'domain_prefix' => $cms_module_piwik_server_domain_prefix,
					'subdomains_links' =>$cms_module_piwik_server_subdomains_links
				);
				$params['servers']['server'.(self::get_max_server_id($this->managed_datas['module']['servers'])+1)] = $server_infos;
			}else{
				//sinon on réécrit juste l'élément
				$params['servers'][$server]['name'] = $cms_module_piwik_server_name;
				$params['servers'][$server]['url'] = $cms_module_piwik_server_url;
				$params['servers'][$server]['site_id'] = $cms_module_piwik_site_id;
				$params['servers'][$server]['subdomains'] = $cms_module_piwik_server_subdomain;
				$params['servers'][$server]['domain_prefix'] = $cms_module_piwik_server_domain_prefix;
				$params['servers'][$server]['subdomains_links'] = $cms_module_piwik_server_subdomains_links;
			}
		}
		return $params;
	}

}