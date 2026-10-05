<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_toolkit.class.php,v 1.16 2024/03/22 15:31:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_toolkit{
	
	protected $name = "";
	
	protected $active = 0;
	
	protected $data = array();
	
	protected $order = 1;
	
	public function __construct($name=""){
		$this->name = $name;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$query = "select cms_toolkit_active, cms_toolkit_data, cms_toolkit_order from cms_toolkits where cms_toolkit_name = '".addslashes($this->name)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->active = $row->cms_toolkit_active;
			$this->data = json_decode($row->cms_toolkit_data, true);
			$this->order = $row->cms_toolkit_order;
		}
	}
	
	public function get_title() {
		global $msg;
		
		$title = $this->name. " - ";
		if($this->active) {
			$title .= "<span style='color:green'>".$msg['cms_toolkit_activated']."</span>";
		} else {
			$title .= "<span style='color:red'>".$msg['cms_toolkit_disabled']."</span>";
		}
		if($this->name == 'jquery') {
			$title .= $this->get_jquery_title();
		} elseif(substr($this->name, 0, 5) == 'uikit') {
			$title .= $this->get_uikit_title();
		} elseif($this->name == 'dsfr') {
		    $title .= $this->get_dsfr_title();
		} else {
			$title .= " / ";
			if(isset($this->data['components'])) {
				$title .= count($this->data['components']);
			} else {
				$title .= "0";
			}
			$title .= " ".$msg['cms_toolkit_data_components'];
		}
		return $title;
	}
	
	public function get_form() {
		global $msg;
		
		$form = "
		<div class='row'>
			<b>".$msg["cms_toolkit_active"]."</b>&nbsp;
			<input type='radio' id='cms_toolkit_".$this->name."_active_no' name='cms_toolkits[".$this->name."][active]' data-dojo-type='dijit/form/RadioButton' value='0' ".(!$this->active ? "checked='checked'" : "")." />&nbsp;<span style='color:red'>".$msg['39']."</span>
			<input type='radio' id='cms_toolkit_".$this->name."_active_yes' name='cms_toolkits[".$this->name."][active]' data-dojo-type='dijit/form/RadioButton' value='1' ".($this->active ? "checked='checked'" : "")." />&nbsp;<span style='color:green'>".$msg['40']."</span>
		</div>
		<div class='row'>&nbsp;</div>";
		if($this->name == 'jquery') {
			$form .= $this->get_jquery_form_content();
		} elseif(substr($this->name, 0, 5) == 'uikit') {
			$form .= $this->get_uikit_form_content();
		} elseif($this->name == 'dsfr') {
		    $form .= $this->get_dsfr_form_content();
		} else {
			$form .= $this->get_form_content();
		}
		$form = gen_plus('cms_toolkit_'.$this->name, "<span id='cms_toolkit_".$this->name."_title'>".$this->get_title()."</span>", $form, 1);
		return $form;
	}
	
	public function save() {
		global $base_path;
	
		if($this->name && file_exists($base_path.'/opac_css/styles/common/toolkits/'.$this->name)) {
			$query = "select cms_toolkit_name from cms_toolkits where cms_toolkit_name = '".addslashes($this->name)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$query = "update cms_toolkits set
					cms_toolkit_active = '".$this->active."',
					cms_toolkit_data = '".json_encode($this->data)."',
					cms_toolkit_order = '".$this->get_order()."'
					where cms_toolkit_name = '".addslashes($this->name)."'";
			} else {
				$query = "insert into cms_toolkits set
					cms_toolkit_name = '".addslashes($this->name)."',
					cms_toolkit_active = '".$this->active."',
					cms_toolkit_data = '".json_encode($this->data)."',
					cms_toolkit_order = '".$this->set_order(0)."'";
			}
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function load() {
		$headers = array();
		if($this->name == 'jquery') {
			$headers = $this->jquery_load();
		} elseif(substr($this->name, 0, 5) == 'uikit') {
			$headers = $this->uikit_load();
		} elseif($this->name == 'dsfr') {
		    $headers = $this->dsfr_load();
		} else {
			$headers = $this->generic_load();
		}
		return $headers;
	}
	
	public function get_order() {
		if(!$this->order) {
			$query = "select max(cms_toolkit_order) as max from cms_toolkits";
			$result = pmb_mysql_query($query);
			$this->order = pmb_mysql_result($result, 0, 'max')+1;
		}
		return $this->order;
	}
	
	public function get_active() {
		return $this->active;
	}
	
	public function set_active($active) {
		$this->active = intval($active);
	}
	
	public function set_data($data) {
		$this->data = $data;
	}
	
	public function set_order($order) {
		$order = intval($order);
		if(!$order) {
			$order = $this->get_order();
		}
		$this->order = $order;
	}
	
	/********************************************/
	/***************** GENERIC ********************/
	/********************************************/
	
	protected function get_form_content(){
		global $msg;
		global $base_path;
	
		if(!isset($this->data['components'])) {
		    $this->data['components'] = array();
		}
	
		$components = array();
		if(file_exists($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/js/components')){
			$dh = opendir($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/js/components');
			while(($component = readdir($dh)) !== false){
				if($component != "." && $component != ".." && $component != "CVS"){
					$components[] = substr($component, 0, strpos($component, '.'));
				}
			}
		}
		$components = array_unique($components);
		asort($components);
		if(count($components)) {
			$js_components = "
				<script>
					function cms_toolkit_uikit_components_checkboxes(do_check) {
						var components = document.forms['cms_toolkits_form'].elements['cms_toolkits[".$this->name."][data][components][]'];
						var components_cnt  = (typeof(components.length) != 'undefined') ? components.length : 0;
						if(components_cnt) {
							for (var i = 0; i < components_cnt; i++) {
								components[i].checked = do_check;
							}
						}
				}
				</script>
				<button data-dojo-type='dijit/form/Button' onClick=\"cms_toolkit_uikit_components_checkboxes(true); return false;\">".$msg['tout_cocher_checkbox']."</button>
				<button data-dojo-type='dijit/form/Button' onClick=\"cms_toolkit_uikit_components_checkboxes(false); return false;\">".$msg['tout_decocher_checkbox']."</button><br />";
			foreach ($components as $component) {
				$js_components .= "<input type='checkbox' id='cms_toolkit_".$this->name."_data_component_".$component."' name='cms_toolkits[".$this->name."][data][components][]' value='".$component."' ".(in_array($component, $this->data['components']) ? "checked='checked'" : "")." />".$component."<br />";
			}

			$form_content = "
                <div class='row'>
                    <label class='etiquette'>".$msg["cms_toolkit_data_components_selection"]."&nbsp;</label>
                </div>
                <div class='row'>".
                    $js_components.
		"</div>";
		}
		return $form_content;
	}
	
	public function generic_load() {
		global $base_path;
		global $css;
	
		$headers = array();
		if($this->active) {
			if(is_dir($base_path.'/styles/common/toolkits/'.$this->name.'/js')){
				$dh = opendir($base_path.'/styles/common/toolkits/'.$this->name.'/js');
				while(($component = readdir($dh)) !== false){
					if(strpos($component, '.min.js')){
						if(file_exists($base_path."/styles/".$css."/toolkits/".$this->name."/js/".$component)){
							$headers[] = "<script src='".$base_path."/styles/".$css."/toolkits/".$this->name."/js/".$component."'></script>";
						} else {
							$headers[] = "<script src='".$base_path."/styles/common/toolkits/".$this->name."/js/".$component."'></script>";
						}
						if(file_exists($base_path."/styles/common/toolkits/".$this->name."/css/".str_replace('.min.js', '.min.css', $component))){
							$headers[]= "<link rel='stylesheet' type='text/css' href='".$base_path."/styles/common/toolkits/".$this->name."/css/".str_replace('.min.js', '.min.css', $component)."'/>";
						}
					}
				}
				sort($headers);
			}
			if(isset($this->data['components']) && count($this->data['components'])) {
				asort($this->data['components']); //Hack pour gérer les dépendances
				foreach ($this->data['components'] as $component) {
					if(file_exists($base_path."/styles/common/toolkits/".$this->name."/js/components/".$component.".min.js")){
						if(file_exists($base_path."/styles/".$css."/toolkits/".$this->name."/js/components/".$component.".min.js")){
							$headers[] = "<script src='".$base_path."/styles/".$css."/toolkits/".$this->name."/js/components/".$component.".min.js'></script>";
						} else {
							$headers[] = "<script src='".$base_path."/styles/common/toolkits/".$this->name."/js/components/".$component.".min.js'></script>";
						}
						if(file_exists($base_path."/styles/common/toolkits/".$this->name."/css/components/".$component.".min.css")){
							$headers[]= "<link rel='stylesheet' type='text/css' href='".$base_path."/styles/common/toolkits/".$this->name."/css/components/".$component.".min.css'/>";
						}
					}
				}
			}
		}
		return $headers;
	}
	
	/********************************************/
	/***************** JQUERY ********************/
	/********************************************/
	
	protected function get_jquery_title(){
		global $msg;
	
		$jquery_title = "";
		if($this->active && isset($this->data['version'])) {
			$jquery_title = " / ".$msg['cms_toolkit_jquery_data_version']." ".substr($this->data['version'], 7);
		}
		return $jquery_title;
	}
	
	protected function get_jquery_form_content(){
		global $msg;
		global $base_path;
	
		if(!isset($this->data['version'])) {
		    $this->data['version'] = 'jquery-2.1.1';
		}
		if(!isset($this->data['components'])) {
		    $this->data['components'] = array();
		}
		
		$jquery_form_content = "
		<div class='row'>
			<label class='etiquette'>".$msg["cms_toolkit_jquery_data_version_selection"]."&nbsp;</label>
		</div>
		<div class='row'>
			<select id='cms_toolkit_".$this->name."_data_versions' name='cms_toolkits[".$this->name."][data][version]' data-dojo-type='dijit/form/Select'>";
		$versions = array();
		if(file_exists($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/versions')){
			$dh = opendir($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/versions');
			while(($version = readdir($dh)) !== false){
				if($version != "." && $version != ".." && $version != "CVS"){
					$versions[] = str_replace('.min.js', '', $version);
				}
			}
			asort($versions);
			foreach ($versions as $version) {
				$jquery_form_content .= "
					<option value='".$version."' ".($this->data['version'] == $version ? "selected='selected'" : "").">".$version."</option>";
			}
		}
		$jquery_form_content .= "
			</select>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".$msg["cms_toolkit_jquery_data_components_selection"]."&nbsp;</label>
		</div>
		<div class='row'>";

		$components = array();
		if(file_exists($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/components')){
			$dh = opendir($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/components');
			while(($component = readdir($dh)) !== false){
				if($component != "." && $component != ".." && $component != "CVS"){
					$components[] = str_replace('.min.js', '', $component);
				}
			}
		}
		$components = array_unique($components);
		asort($components);
		$js_components = "";
		foreach ($components as $component) {
			$js_components .= "<input type='checkbox' id='cms_toolkit_".$this->name."_data_component_".$component."' name='cms_toolkits[".$this->name."][data][components][]' data-dojo-type='dijit/form/CheckBox' value='".$component."' ".(in_array($component, $this->data['components']) ? "checked='checked'" : "")." />".$component."<br />";
		}
		$jquery_form_content .= $js_components;
		$jquery_form_content .=
		"</div>";
		return $jquery_form_content;
	}
	
	public function jquery_load() {
		global $base_path;
		
		$headers = array();
		if($this->active) {
			$headers[] = "<!-- Inclusion JQuery pour uikit -->";
			$headers[] = "<!--[if (!IE)|(gt IE 8)]><!-->
				<script src='".$base_path."/styles/common/toolkits/".$this->name."/versions/".$this->data['version'].".min.js'></script>
				<!--<![endif]-->
				
				<!--[if lte IE 8]>
				  <script src='".$base_path."/styles/common/toolkits/".$this->name."/components/jquery-1.9.1.min.js'></script>
				<![endif]-->";
			if(isset($this->data['components']) && count($this->data['components'])) {
				foreach ($this->data['components'] as $component) {
					if(file_exists($base_path."/styles/common/toolkits/".$this->name."/components/".$component.".min.js")){
						$headers[] = "<script src='".$base_path."/styles/common/toolkits/".$this->name."/components/".$component.".min.js'></script>";
					}
				}
			}
		}
		return $headers;
	}
	
	/********************************************/
	/***************** UIKIT ********************/
	/********************************************/
	
	protected function get_uikit_title(){
		global $msg;
		
		$uikit_title = " / ".$msg['cms_toolkit_uikit_data_them'];
		if(isset($this->data['them'])) {
			$uikit_title .= " ".$msg['cms_toolkit_uikit_data_them_'.$this->data['them']];
		} else {
			$uikit_title .= " ".$msg['cms_toolkit_uikit_data_them_uikit'];
		}
		$uikit_title .= " / ";
		if(isset($this->data['components'])) {
			$uikit_title .= count($this->data['components']);
		} else {
			$uikit_title .= "0";
		}
		$uikit_title .= " ".$msg['cms_toolkit_uikit_data_components'];
		return $uikit_title;
	}
	
	protected function get_uikit_form_content(){
		global $msg;
		global $base_path;
	
		if(!isset($this->data['them'])) {
		    $this->data['them'] = 'uikit';
		}
		if(!isset($this->data['components'])) {
		    $this->data['components'] = array();
		}
		
		$uikit_form_content = "
		<div class='row'>
			<strong>".$msg["cms_toolkit_uikit_information"]."</strong>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".$msg["cms_toolkit_uikit_data_them_selection"]."&nbsp;</label>
		</div>
		<div class='row'>
			<select id='cms_toolkit_".$this->name."_data_thems' name='cms_toolkits[".$this->name."][data][them]' data-dojo-type='dijit/form/Select'>
				<option value='uikit' ".($this->data['them'] == 'uikit' ? "selected='selected'" : "").">".$msg['cms_toolkit_uikit_data_them_uikit']."</option>
				<option value='uikit.gradient' ".($this->data['them'] == 'uikit.gradient' ? "selected='selected'" : "").">".$msg['cms_toolkit_uikit_data_them_uikit.gradient']."</option>
				<option value='uikit.almost-flat' ".($this->data['them'] == 'uikit.almost-flat' ? "selected='selected'" : "").">".$msg['cms_toolkit_uikit_data_them_uikit.almost-flat']."</option>
			</select>
		</div>";
	
		$components = array();
		if(file_exists($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/js/components')){
			$dh = opendir($base_path.'/opac_css/styles/common/toolkits/'.$this->name.'/js/components');
			while(($component = readdir($dh)) !== false){
				if($component != "." && $component != ".." && $component != "CVS"){
					$components[] = substr($component, 0, strpos($component, '.'));
				}
			}
		}
		$components = array_unique($components);
		asort($components);
		if(count($components)) {
			$js_components = "
				<script>
					function cms_toolkit_components_checkboxes(do_check) {
						var components = document.forms['cms_toolkits_form'].elements['cms_toolkits[".$this->name."][data][components][]'];
						var components_cnt  = (typeof(components.length) != 'undefined') ? components.length : 0;
						if(components_cnt) {
							for (var i = 0; i < components_cnt; i++) {
								components[i].checked = do_check;
							}
						}
					}
				</script>
				<button data-dojo-type='dijit/form/Button' onClick=\"cms_toolkit_components_checkboxes(true); return false;\">".$msg['tout_cocher_checkbox']."</button>
				<button data-dojo-type='dijit/form/Button' onClick=\"cms_toolkit_components_checkboxes(false); return false;\">".$msg['tout_decocher_checkbox']."</button><br />";
			foreach ($components as $component) {
				$js_components .= "<input type='checkbox' id='cms_toolkit_".$this->name."_data_component_".$component."' name='cms_toolkits[".$this->name."][data][components][]' value='".$component."' ".(in_array($component, $this->data['components']) ? "checked='checked'" : "")." />".$component."<br />";
			}
    		$uikit_form_content .= "
    		<div class='row'>
    		<label class='etiquette'>".$msg["cms_toolkit_uikit_data_components_selection"]."&nbsp;</label>
    		</div>
    		<div class='row'>".$js_components."</div>";
		}
		return $uikit_form_content;
	}
	
	public function uikit_load() {
		global $base_path;
		global $css;
		
		$headers = array();
		if($this->active) {
			$headers[] = "<script src='".$base_path."/styles/common/toolkits/".$this->name."/js/uikit.min.js'></script>";
			if(file_exists($base_path."/styles/".$css."/toolkits/".$this->name."/css/".$this->data['them'].".min.css")){
				$them_css_path = $base_path."/styles/".$css."/toolkits/".$this->name."/css/".$this->data['them'].".min.css";
			}else{
				$them_css_path = $base_path."/styles/common/toolkits/".$this->name."/css/".$this->data['them'].".min.css";
			}
			$vide_cache=@filemtime($them_css_path);
			$headers[] = "<link rel='stylesheet' type='text/css' href='".$them_css_path."?".$vide_cache."'/>";
			if(isset($this->data['components']) && count($this->data['components'])) {
				asort($this->data['components']); //Hack pour gérer les dépendances
				if($this->data['them'] == 'uikit') {
					$css_suffix = '';
				} else {
					$css_suffix = '.'.str_replace('uikit.', '', $this->data['them']);
				}
				foreach ($this->data['components'] as $component) {
					if(file_exists($base_path."/styles/common/toolkits/".$this->name."/js/components/".$component.".min.js")){
						if(file_exists($base_path."/styles/".$css."/toolkits/".$this->name."/js/components/".$component.".min.js")){
							$headers[] = "<script src='".$base_path."/styles/".$css."/toolkits/".$this->name."/js/components/".$component.".min.js'></script>";
						} else {
							$headers[] = "<script src='".$base_path."/styles/common/toolkits/".$this->name."/js/components/".$component.".min.js'></script>";
						}
						$component_css_path = $base_path."/styles/".$css."/toolkits/".$this->name."/css/components/".$component.$css_suffix.".min.css";
						if(file_exists($component_css_path)){
							$vide_cache=@filemtime($component_css_path);
							$headers[]= "<link rel='stylesheet' type='text/css' href='".$component_css_path."?".$vide_cache."'/>";
						} else {
							$component_css_path = $base_path."/styles/common/toolkits/".$this->name."/css/components/".$component.$css_suffix.".min.css";
							if(file_exists($component_css_path)){
								$headers[]= "<link rel='stylesheet' type='text/css' href='".$component_css_path."?".$vide_cache."'/>";
							}
						}
					}
				}
			}
		}
		return $headers;
	}
	
	/********************************************/
	/***************** DSFR ********************/
	/********************************************/
	
	protected function get_dsfr_title(){
	    global $msg;
	    
	    $dsfr_title = "";
	    if($this->active && isset($this->data['version'])) {
	        $dsfr_title = " / ".$msg['cms_toolkit_jquery_data_version']." ".substr($this->data['version'], 5);
	    }
	    return $dsfr_title;
	}
	
	protected function get_dsfr_form_content(){
	    global $msg;
	    
	    if(!isset($this->data['version'])) {
	        $this->data['version'] = 'dsfr-1.10.0';
	    }
	    if(!isset($this->data['components'])) {
	        $this->data['components'] = array();
	    }
	    $dsfr_form_content = "
		<div class='row'>
			".$msg["cms_toolkit_dsfr_information"]."
		</div>
		<div class='row'>&nbsp;</div>";
	    return $dsfr_form_content;
	}
	
	public function dsfr_load() {
	    global $base_path;
	    global $css;
	    global $opac_cookies_consent_dsfr;
	    
	    $headers = array();
	    if($this->active) {
	        if(file_exists($base_path."/styles/".$css."/toolkits/".$this->name."/dsfr.min.css")){
	            $css_path = $base_path."/styles/".$css."/toolkits/".$this->name."/dsfr.min.css";
	        }else{
	            $css_path = $base_path."/styles/common/toolkits/".$this->name."/dsfr.min.css";
	        }
	        $vide_cache=@filemtime($css_path);
	        $headers[] = "<link rel='stylesheet' type='text/css' href='".$css_path."?".$vide_cache."'/>";
	        
	        if(file_exists($base_path."/styles/".$css."/toolkits/".$this->name."/utility/utility.min.css")){
	            $css_path = $base_path."/styles/".$css."/toolkits/".$this->name."/utility/utility.min.css";
	        }else{
	            $css_path = $base_path."/styles/common/toolkits/".$this->name."/utility/utility.min.css";
	        }
	        $vide_cache=@filemtime($css_path);
	        $headers[] = "<link rel='stylesheet' type='text/css' href='".$css_path."?".$vide_cache."'/>";
	        $headers[] = "<script type='module' src='".$base_path."/styles/common/toolkits/".$this->name."/dsfr.module.min.js'></script>";
	        $headers[] = "<script type='text/javascript' nomodule src='".$base_path."/styles/common/toolkits/".$this->name."/dsfr.nomodule.min.js'></script>";
	        if ($opac_cookies_consent_dsfr) {
	           $headers[] = "<script type='module' src='".$base_path."/styles/common/toolkits/".$this->name."/analytics/analytics.module.min.js'></script>";
	        }
	    }
	    return $headers;
	}
	
}