<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webdav_group.class.php,v 1.4 2020/11/25 11:07:50 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class webdav_group {
	
	protected $config;
	
	protected $collections;
	
	protected $msg;
	
	public function __construct($config, $collections, $msg){
		$this->config = $config;
		$this->collections = $collections;
		$this->msg = $msg;
	}
	
	public function get_config_form(){
		//A surcharger
	}
	
	public function get_config_form_script() {
		//A surcharger
	}
	
	protected function get_collections_tree(){
		global $charset;
		global $base_path;
		if(empty($this->config['tree'])){
			$this->config['tree'] = array();
		}
		
		$result="<div id='collection_container'>
					<div class='row'>
						<label for='tree'>".htmlentities($this->msg['webdav_tree'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='row'>
						<select name='tree_elem' id='select_tree_elem' onchange='load_tree_elem(this)'>
							<option value='0'>".htmlentities($this->msg['webdav_select_tree_elem'],ENT_QUOTES,$charset)."</option>";
		foreach ($this->collections as $name=>$label) {
			$result.="<option value='".$name."'>".htmlentities($label, ENT_QUOTES,$charset)."</option>";
		}
		$result.="</select>
					</div>
					<table id='tree'>";
		foreach($this->config['tree'] as $pos => $elem){
			$result.="
						<tr id='tree_elem_tr".$pos."'>
							<td recept='yes' recepttype='tree_elem' highlight='tree_elem_show_recept' downlight='tree_elem_hide_recept' id='tree_elem_td".$pos."' draggable='yes' callback_after='move_tree_elem' dragtype='tree_elem' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=''>
								<input type='hidden' name='tree[]' value='".$elem."' />
										<img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle'/>".htmlentities($this->collections[$elem], ENT_QUOTES, $charset)."</td>
							<td onclick='tree_elem_delete(\"tree_elem_tr".$pos."\");'><img src=\"".get_url_icon('trash.png')."\" /></td>
						</tr>";
		}
		$result.="
					</table>
				</div>";
		
		return $result;
	}
	
	public function get_collections_tree_script() {
		global $base_path;
		
		return "
						var nb_tree_elems = ".count($this->config['tree']).";
		
						function load_tree_elem(elem){
							var selected_option = elem.selectedOptions[0];
							if(selected_option.value){
								var tr = document.createElement('tr');
								document.getElementById('tree').appendChild(tr);
								tr.setAttribute('id','tree_elem_tr'+nb_tree_elems);
								var td = document.createElement('td');
								td.setAttribute('recept','yes');
								td.setAttribute('recepttype','tree_elem');
								td.setAttribute('highlight','tree_elem_show_recept');
								td.setAttribute('downlight','tree_elem_hide_recept');
								td.setAttribute('id','tree_elem_td'+nb_tree_elems);
								td.setAttribute('draggable','yes');
								td.setAttribute('callback_after','move_tree_elem');
								td.setAttribute('dragtype','tree_elem');
								td.setAttribute('dragicon','".get_url_icon('icone_drag_notice.png')."');
								td.setAttribute('dragtext',selected_option.innerHTML);
								td.innerHTML = '<input type=\"hidden\" name=\"tree[]\" value=\"'+selected_option.value+'\" /> <img src=\"".get_url_icon('sort.png')."\" style=\"width:12px; vertical-align:middle\"/>'+selected_option.innerHTML;
								tr.appendChild(td);
								var td = document.createElement('td');
								td.setAttribute('onclick','tree_elem_delete(\"tree_elem_tr'+nb_tree_elems+'\")');
								td.innerHTML = '<img src=\"".get_url_icon('trash.png')."\" />';
								tr.appendChild(td);
								nb_tree_elems++;
								init_drag();
								document.getElementById('select_tree_elem').selectedIndex=0;
							}
						}
						
						function move_tree_elem(elem,evt,target){
						
							if(target != 'false' || target != 'null'){
								elem = elem.parentNode;
								target = document.getElementById(target).parentNode;
								parent = target.parentNode;
								parent.insertBefore(elem,target);
							}
						}
						
						function tree_elem_show_recept(obj){
							obj.style.background='#DDD';
						}
						
						function tree_elem_hide_recept(obj){
							obj.style.background='';
						}
						
						function tree_elem_delete(id){
							document.getElementById(id).parentNode.removeChild(document.getElementById(id));
						}";
	}

	public static function update_config_from_form(){
		global $tree;
		
		return array(
				'tree' => $tree
		);
	}
 }