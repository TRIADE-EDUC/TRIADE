<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_search.class.php,v 1.7.8.1.2.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_search extends cms_module_common_module {

	public function __construct($id=0){
		$this->module_path = str_replace(basename(__FILE__),"",__FILE__);
		parent::__construct($id);
	}

	public function get_manage_form(){
		global $base_path;
		global $search_dest;

		$form="
		<h3>".$this->format_text($this->msg['cms_module_search_admin_form_label'])."</h3>
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:300px;' >";
		if($this->managed_datas['module']['search_dests']){
			foreach($this->managed_datas['module']['search_dests'] as $key => $cal){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&search_dest=".$key."&action=get_form'>".$this->format_text($cal['name'])."</a>
					&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&search_dest_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_search_delete_search_dest'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
			$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&search_dest=new'/>".$this->format_text($this->msg['cms_module_search_add_search_dest'])."</a>
			";
		$form.="
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center' >";
		if($search_dest){
			$form.=$this->get_managed_form_start(array('search_dest'=>$search_dest));
			$form.=$this->get_managed_search_dest_form($search_dest);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;

	}

	protected function get_managed_search_dest_form($search_dest){
		global $opac_opac_view_activate;

		if($search_dest != "new"){
			$infos = $this->managed_datas['module']['search_dests'][$search_dest];
		}else{
			$infos = array(
				'name' => "",
				'page' => 0
			);
		}
		$form = "";

		//nom
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_search_dest_name'>".$this->format_text($this->msg['cms_module_search_search_dest_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_search_search_dest_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		//page
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_search_dest_page'>".$this->format_text($this->msg['cms_module_search_search_dest_page'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='cms_module_search_page_dest'>";
		if($opac_opac_view_activate) {
			$form.= $this->gen_options_opac_view($infos['page']);
		} else {
		    $form.="
                        <option value='0' ".(!$infos['page'] ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_search_classique_dest'])."</option>";
		}
		//on va chercher les infos pour les pages du portail !
		$query = "select id_page,page_name from cms_pages order by page_name asc";
		$result = pmb_mysql_query($query);
		$pages = array();
		$pages[0] = $this->msg["cms_module_menu_menu_entry_page_choice"];
		if(pmb_mysql_num_rows($result)){
			$form.= "
					<optgroup label='".$this->format_text($this->msg['cms_module_search_cms_pages'])."'>";
			while($row = pmb_mysql_fetch_object($result)){
				$form.="
						<option value='".$row->id_page."' ".($row->id_page == $infos['page'] ? "selected='selected'" : "").">".$this->format_text($row->page_name)."</option>";
			}
			$form.= "
					</optgroup>";
		}
		$form.="
					</select>
				</div>
			</div>";
		return $form;
	}

	protected function get_opac_views_list(){
		$opac_views = array();
		$query = "select opac_view_id, opac_view_name from opac_views";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)) {
				$opac_views[$row->opac_view_id] = $row->opac_view_name;
			}
		}
		return $opac_views;
	}

	protected function gen_options_opac_view($selected){
		$opac_views = $this->get_opac_views_list();
		$select = "
					<optgroup label='".$this->format_text($this->msg['cms_module_search_opac_views'])."'>
						<option value='0' ".(($selected == '0') ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_search_opac_view_current'])."</option>
						<option value='view_-1' ".(($selected === 'view_-1') ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_search_opac_view_any'])."</option>";
		foreach($opac_views as $key => $name){
			$select.="
						<option value='view_".$key."' ".(($selected === 'view_'.$key) ? "selected='selected'" : "").">".$this->format_text($name)."</option>";
		}
		$select.= "
					</optgroup>";
		return $select;
	}

	public function save_manage_form(){
		global $search_dest;
		global $search_dest_delete;
		global $cms_module_search_search_dest_name;
		global $cms_module_search_page_dest;

		$params = $this->managed_datas['module'];

		if($search_dest_delete){
			unset($params['search_dests'][$search_dest_delete]);
		}else{
			if($search_dest == "new"){
				$search_dest = "search_dest".(cms_module_search::get_max_search_dest_id($params['search_dests'])+1);
			}
			$params['search_dests'][$search_dest] = array(
					'name' => stripslashes($cms_module_search_search_dest_name),
					'page' => stripslashes($cms_module_search_page_dest)
			);
		}
		return $params;
	}

	protected static function get_max_search_dest_id($datas){
		$max = 0;
		if(is_countable($datas) && count($datas)){
			foreach	($datas as $key => $val){
			    $key = intval(str_replace("search_dest","",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}
}