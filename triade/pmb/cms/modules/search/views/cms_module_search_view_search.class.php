<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_search_view_search.class.php,v 1.27 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_search_view_search extends cms_module_common_view{
	protected $cadre_parent;
	
	public function __construct($id=0){
	    parent::__construct((int) $id);
	}
	
	public function get_form(){
		global $charset;
		if(!isset($this->parameters) || !is_array($this->parameters)){
			$this->parameters=array();
		}
		if(!isset($this->parameters['help'])) $this->parameters['help'] = '';
		if(!isset($this->parameters['title'])) $this->parameters['title'] = '';
		if(!isset($this->parameters['link_search_advanced'])) $this->parameters['link_search_advanced'] = '';
		if(!isset($this->parameters['input_placeholder'])) $this->parameters['input_placeholder'] = '';
		if(!isset($this->parameters['others_links'])) $this->parameters['others_links'] = '';
		$form ="
		<div class='row'>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_help'>".$this->format_text($this->msg['cms_module_search_view_help'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_help' value='1' ".($this->parameters['help'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_help_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_help' value='0' ".(!$this->parameters['help'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_help_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_title'>".$this->format_text($this->msg['cms_module_search_view_title'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_search_view_title' value='".($this->parameters['title'] ? htmlentities($this->parameters['title'],ENT_QUOTES,$charset) : "")."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_link_search_advanced'>".$this->format_text($this->msg['cms_module_search_view_link_search_advanced'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_link_search_advanced' value='1' ".($this->parameters['link_search_advanced'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_link_search_advanced' value='0' ".(!$this->parameters['link_search_advanced'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_input_placeholder'>".$this->format_text($this->msg['cms_module_search_view_input_placeholder'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_search_view_input_placeholder' value='".($this->parameters['input_placeholder'] ? htmlentities($this->parameters['input_placeholder'],ENT_QUOTES,$charset) : "")."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_nofill'>".$this->format_text($this->msg['cms_module_search_view_nofill'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_nofill' value='1' ".($this->parameters['nofill'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_nofill' value='0' ".(!$this->parameters['nofill'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_no'])."
				</div>
			</div>";
		
		$advanced_parameters = "
		<script type='text/javascript'>
			function other_link_chklnk(indice,element) {
				var link = element.form.elements['cms_module_search_view_others_links['+indice+'][url]'];
				if(link.value != ''){
					var wait = document.createElement('img');
					wait.setAttribute('src','".get_url_icon('patience.gif')."');
					wait.setAttribute('align','top');
					while(document.getElementById('cms_module_search_view_other_link_check_'+indice).firstChild){
						document.getElementById('cms_module_search_view_other_link_check_'+indice).removeChild(document.getElementById('cms_module_search_view_other_link_check_'+indice).firstChild);
					}
					document.getElementById('cms_module_search_view_other_link_check_'+indice).appendChild(wait);
					var testlink = encodeURIComponent(link.value);
		 			var check = new http_request();
					if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&link='+testlink)){
						alert(check.get_text());
					}else{
						var result = check.get_text();
						var type_status=result.substr(0,1);
						var img = document.createElement('img');
						var src='';
			    		if(type_status == '2' || type_status == '3'){
							if((link.value.substr(0,7) != 'http://') && (link.value.substr(0,8) != 'https://')) link.value = 'http://'+link.value;
							//impec, on print un petit message de confirmation
							src = '".get_url_icon('tick.gif')."';
						}else{
							//probleme...
							src = '".get_url_icon('error.png')."';
							img.setAttribute('style','height:1.5em;');
						}
						img.setAttribute('src',src);
						img.setAttribute('align','top');
						while(document.getElementById('cms_module_search_view_other_link_check_'+indice).firstChild){
							document.getElementById('cms_module_search_view_other_link_check_'+indice).removeChild(document.getElementById('cms_module_search_view_other_link_check_'+indice).firstChild);
						}
						document.getElementById('cms_module_search_view_other_link_check_'+indice).appendChild(img);
					}
				}
			}
							
			function add_other_link_() {
				cpt = document.getElementById('cms_module_search_view_other_link_count').value;
				var other_link = document.createElement('div');
				other_link.setAttribute('class','row');
				other_link.setAttribute('id','cms_module_search_view_other_link_'+cpt);
				var check = document.createElement('div');
				check.setAttribute('id','cms_module_search_view_other_link_check_'+cpt);
				check.setAttribute('style','display:inline');
				var link_label = document.createTextNode('".$this->msg['cms_module_search_view_link_url']."');
				var chklnk = document.createElement('input');
				chklnk.setAttribute('type','button');
				chklnk.setAttribute('value','".$this->msg['cms_module_search_view_link_check']."');
				chklnk.setAttribute('class','bouton');
				chklnk.setAttribute('onclick','other_link_chklnk('+cpt+',this);');
				document.getElementById('cms_module_search_view_other_link_count').value = cpt*1 +1;
				var link = document.createElement('input');
		        link.setAttribute('name','cms_module_search_view_others_links['+cpt+'][url]');
		        link.setAttribute('id','cms_module_search_view_other_link_url_'+cpt);
		        link.setAttribute('type','text');
				link.setAttribute('class','saisie-20em');
		        link.setAttribute('value','');
				var lib_label = document.createTextNode('".$this->msg['cms_module_search_view_link_label']."');
				var lib = document.createElement('input');
		        lib.setAttribute('name','cms_module_search_view_others_links['+cpt+'][label]');
		        lib.setAttribute('id','cms_module_search_view_other_link_label_'+cpt);
		        lib.setAttribute('type','text');
				lib.setAttribute('class','saisie-15em');
		        lib.setAttribute('value','');
				var linktarget_input = document.createElement('input');
				linktarget_input.setAttribute('name','cms_module_search_view_others_links['+cpt+'][linktarget]');
		        linktarget_input.setAttribute('id','cms_module_search_view_other_link_linktarget_'+cpt);
		        linktarget_input.setAttribute('type','checkbox');
		        linktarget_input.setAttribute('value','1');
				var linktarget_label = document.createElement('label');
				linktarget_label.setAttribute('for','cms_module_search_view_other_link_linktarget_'+cpt);
				linktarget_label.innerHTML = '".$this->msg['cms_module_search_view_link_target']."';		
				var del_button = document.createElement('input');
				del_button.setAttribute('type','button');
				del_button.className='bouton';
				del_button.setAttribute('value','X');
				del_button.setAttribute('onclick','del_other_link_('+cpt+');');
				
				other_link.appendChild(check);
				other_link.appendChild(link_label);
				space=document.createTextNode(' ');
				other_link.appendChild(space);
				other_link.appendChild(link);
				space=document.createTextNode(' ');
				other_link.appendChild(space);
				other_link.appendChild(chklnk);
				space=document.createTextNode(' ');
				other_link.appendChild(space);
				other_link.appendChild(lib_label);
				other_link.appendChild(lib);
				space=document.createTextNode(' ');
				other_link.appendChild(space);
				other_link.appendChild(linktarget_input);
				space=document.createTextNode(' ');
				other_link.appendChild(space);
				other_link.appendChild(linktarget_label);
				space=document.createTextNode(' ');
				other_link.appendChild(space);
				other_link.appendChild(del_button);
				space=document.createElement('br');
				other_link.appendChild(space);
				var parent = document.getElementById('advanced_parametersChild');
				parent.insertBefore(other_link, document.getElementById('spaceformoreotherlink'));
			}
						
			function del_other_link_(indice) {
				if(indice) {
					var parent = document.getElementById('advanced_parametersChild'); 
					var child = document.getElementById('cms_module_search_view_other_link_'+indice);
					parent.removeChild(child);
				} else {
					document.getElementById('cms_module_search_view_other_link_url_0').value = '';
					document.getElementById('cms_module_search_view_other_link_label_0').value = '';
				}
			}			
		</script>
		<div class='row'>
			<label>".$this->format_text($this->msg['cms_module_search_view_others_links'])."</label>
			<input class='bouton' type='button' value='+' onclick=\"add_other_link_();\" />
		</div>
		";
		
		if(is_array($this->parameters['others_links']) && count($this->parameters['others_links'])) {
			$advanced_parameters .= "
				<input id='cms_module_search_view_other_link_count' type='hidden' name='cms_module_search_view_other_link_count' value='".count($this->parameters['others_links'])."'>	
			";
			foreach ($this->parameters['others_links'] as $key=>$other_link) {
				$advanced_parameters .= "
					<div class='row' id='cms_module_search_view_other_link_".$key."'>
						<div id='cms_module_search_view_other_link_check_".$key."' style='display:inline'></div>
						".$this->format_text($this->msg['cms_module_search_view_link_url'])."
						<input type='text' id='cms_module_search_view_other_link_url_".$key."' class='saisie-20em' name='cms_module_search_view_others_links[".$key."][url]' value='".$this->format_text($other_link['url'])."'/>
						<input class='bouton' type='button' value='".$this->format_text($this->msg['cms_module_search_view_link_check'])."' onclick='other_link_chklnk($key,this);'>
						".$this->format_text($this->msg['cms_module_search_view_link_label'])."<input id='cms_module_search_view_other_link_label_".$key."' type='text' class='saisie-15em' size='50' name='cms_module_search_view_others_links[".$key."][label]' value='".$this->format_text($other_link['label'])."'>
						<input id='cms_module_search_view_other_link_linktarget_".$key."' type='checkbox' name='cms_module_search_view_others_links[".$key."][linktarget]' value='1' ".(isset($other_link['linktarget']) && $other_link['linktarget'] ? "checked='checked'" : "").">
						<label for='cms_module_search_view_other_link_linktarget_".$key."'>".$this->format_text($this->msg['cms_module_search_view_link_target'])."</label>
						<input class='bouton' type='button' value='X' onclick=\"del_other_link_(".$key.");\" />
	 				</div>
					";
			}
		} else {
			$advanced_parameters .= "
				<input id='cms_module_search_view_other_link_count' type='hidden' name='cms_module_search_view_other_link_count' value='1'>
				<div class='row' id='cms_module_search_view_other_link_0'>
					<div id='cms_module_search_view_other_link_check_0' style='display:inline'></div>
					".$this->format_text($this->msg['cms_module_search_view_link_url'])."
					<input type='text' id='cms_module_search_view_other_link_url_0' class='saisie-20em' name='cms_module_search_view_others_links[0][url]' value=''/>
					<input class='bouton' type='button' value='".$this->format_text($this->msg['cms_module_search_view_link_check'])."' onclick='other_link_chklnk(0,this);'>
					".$this->format_text($this->msg['cms_module_search_view_link_label'])."<input id='cms_module_search_view_other_link_label_0' type='text' class='saisie-15em' size='50' name='cms_module_search_view_others_links[0][label]' value=''>
					<input id='cms_module_search_view_other_link_linktarget_0' type='checkbox' name='cms_module_search_view_others_links[0][linktarget]' value='1' ".(isset($other_link['linktarget']) && $other_link['linktarget'] ? "checked='checked'" : "").">
					<label for='cms_module_search_view_other_link_linktarget_0'>".$this->format_text($this->msg['cms_module_search_view_link_target'])."</label>
					<input class='bouton' type='button' value='X' onclick=\"del_other_link_(0);\" />
 				</div>
				";
		}
		$advanced_parameters .= "<div id='spaceformoreotherlink'></div>";
		$form.= gen_plus("advanced_parameters", $this->format_text($this->msg['cms_module_search_view_advanced_parameters']),$advanced_parameters);
		$form .= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		global $cms_module_search_view_help;
		global $cms_module_search_view_title;
		global $cms_module_search_view_link_search_advanced;
		global $cms_module_search_view_input_placeholder;
		global $cms_module_search_view_others_links;
		global $cms_module_search_view_nofill;
		
		if(!isset($this->parameters) || !is_array($this->parameters)){
			$this->parameters=array();
		}
		$this->parameters['help'] = (int) $cms_module_search_view_help;
		$this->parameters['title'] = stripslashes($cms_module_search_view_title);
		$this->parameters['link_search_advanced'] = (int) $cms_module_search_view_link_search_advanced;
		$this->parameters['input_placeholder'] = stripslashes($cms_module_search_view_input_placeholder);
		$this->parameters['nofill'] = (int) $cms_module_search_view_nofill;		
		$others_links = array();
		$nb_others_link = 0; 
		if(is_array($cms_module_search_view_others_links)) {
			foreach ($cms_module_search_view_others_links as $other_link) {
				if($other_link['url'] != '') {
					$others_links[$nb_others_link]['url'] = $other_link['url'];
					$others_links[$nb_others_link]['label'] = stripslashes($other_link['label']);
					$others_links[$nb_others_link]['linktarget'] = (int) $other_link['linktarget'];
					$nb_others_link++;
				}
			}
		}
		$this->parameters['others_links'] = $others_links;
		return parent::save_form();
	}
	
	public function render($datas){
		global $base_path,$include_path,$opac_autolevel2;
		global $opac_modules_search_title,$opac_modules_search_author,$opac_modules_search_publisher,$opac_modules_search_titre_uniforme;
		global $opac_modules_search_collection,$opac_modules_search_subcollection,$opac_modules_search_category,$opac_modules_search_indexint;
		global $opac_modules_search_keywords,$opac_modules_search_abstract,$opac_modules_search_concept,$opac_modules_search_docnum,$opac_simple_search_suggestions;
		global $dest,$user_query,$charset;
		
		$onsubmit = "if (".$this->get_module_dom_id()."_searchbox.user_query.value.length == 0) { ".$this->get_module_dom_id()."_searchbox.user_query.value='*';}";
		
		if ($opac_autolevel2==2) {
			$action = $base_path."/index.php?lvl=more_results&autolevel1=1";
		} else {
			$action = $base_path."/index.php?lvl=search_result&search_type_asked=simple_search";
		}
		
		//juste une searchbox...
		if(count($datas) == 1){
			if (strpos($datas[0]['page'], 'view_') !== false) {
				$action.= '&opac_view=' + substr($datas[0]['page'], 5);
			} else if ($datas[0]['page']>0) {
				$action = $base_path."/index.php?lvl=cmspage&pageid=".$datas[0]['page'];
			}
		}else{
			$onsubmit.= $this->get_module_dom_id()."_change_dest();";
		}
		if(!isset($this->parameters) || !is_array($this->parameters)){
			$this->parameters=array();
		}
		if ($opac_modules_search_title==2) $look["look_TITLE"]=1;
		if ($opac_modules_search_author==2) $look["look_AUTHOR"]=1 ;
		if ($opac_modules_search_publisher==2) $look["look_PUBLISHER"] = 1 ; 
		if ($opac_modules_search_titre_uniforme==2) $look["look_TITRE_UNIFORME"] = 1 ; 
		if ($opac_modules_search_collection==2) $look["look_COLLECTION"] = 1 ;	
		if ($opac_modules_search_subcollection==2) $look["look_SUBCOLLECTION"] = 1 ;
		if ($opac_modules_search_category==2) $look["look_CATEGORY"] = 1 ;
		if ($opac_modules_search_indexint==2) $look["look_INDEXINT"] = 1 ;
		if ($opac_modules_search_keywords==2) $look["look_KEYWORDS"] = 1 ;
		if ($opac_modules_search_abstract==2) $look["look_ABSTRACT"] = 1 ;
		if ($opac_modules_search_concept==2) $look["look_CONCEPT"] = 1 ;
		
		$look["look_ALL"] = 1 ;
		if ($opac_modules_search_docnum==2) $look["look_DOCNUM"] = 1;
		$html = "
			<form method='post' class='searchbox' action='".$action."' name='".$this->get_module_dom_id()."_searchbox' ".($onsubmit!= "" ? "onsubmit=\"".$onsubmit."\"" : "").">
				";
		foreach($look as $looktype=>$lookflag) { $html.="
				<input type='hidden' value='1' name='$looktype'>"; 
		}
		$authpersos = authpersos::get_instance();
		foreach($authpersos->info as $authperso){
		    if (!$authperso['opac_search']) continue;
		    $look_name="look_AUTHPERSO_".$authperso['id']."#";
		    $html.="<input type='hidden' name='$look_name' id='$look_name' value='1' />";
		}
		
		$html.=$authpersos->get_simple_seach_list_tpl_hiden();
		if ($this->parameters['title']) {
			$html.="
			<h4 class='searchbox_title'>".htmlentities($this->parameters['title'],ENT_QUOTES,$charset)."</h4>";
		}
		$html.="<span class='research_inputs'>";
		if($opac_simple_search_suggestions){
			$html.= "
				<script type='text/javascript' src='$include_path/javascript/ajax.js'></script>
				<input type='text' name='user_query' id='".$this->get_module_dom_id()."_user_query_lib' value='".($this->parameters['nofill'] == 1 ? '' :stripslashes(htmlentities($user_query,ENT_QUOTES,$charset)))."' expand_mode='1' completion='suggestions' disableCompletion='false' word_only='no' placeholder='".(isset($this->parameters['input_placeholder']) ? stripslashes(htmlentities($this->parameters['input_placeholder'],ENT_QUOTES,$charset)) : '')."'/>
				<script type='text/javascript'>
					function ".$this->get_module_dom_id()."_toggleCompletion(destValue){
						if((destValue.indexOf('view_') == -1) && (destValue != '0')){
							document.getElementById('".$this->get_module_dom_id()."_user_query_lib').setAttribute('disableCompletion','true');
						}else{
							document.getElementById('".$this->get_module_dom_id()."_user_query_lib').setAttribute('disableCompletion','false');
						}
					}
					ajax_parse_dom();
				</script>";
		}else{
			$html.="
				<input type='text' name='user_query' value='".($this->parameters['nofill'] == 1 ? '' :stripslashes(htmlentities($user_query,ENT_QUOTES,$charset)))."' placeholder='".(isset($this->parameters['input_placeholder']) ? stripslashes(htmlentities($this->parameters['input_placeholder'],ENT_QUOTES,$charset)) : '')."'/>";
		}
		
		$html.="
				<input class='bouton' type='submit' value='".$this->format_text($this->msg['cms_module_search_button_label'])."' />";
		if ($this->parameters['help']) {
			$html.="
				<input class='bouton' type='button' onclick='window.open(\"./help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' value='".$this->format_text($this->msg['cms_module_search_help'])."'>";
		}
		$html.="</span>";
		if(count($datas) >1){
			$html.= "<br/>";
			for($i=0 ; $i<count($datas) ; $i++){
				$checked ="";
				if($dest){
					if($datas[$i]['page'] == $dest){
						$checked= " checked='checked'";
					}
				}else if($i == 0){
					$checked= " checked='checked'";
				}
				if($opac_simple_search_suggestions){
					$html.="
						<span class='search_radio_button' id='search_radio_button_".$i."'><input type='radio' name='dest' value='".$datas[$i]['page']."'".$checked." onClick='".$this->get_module_dom_id()."_toggleCompletion(this.value);' />&nbsp;".$this->format_text($datas[$i]['name'])."</span>";
				}else{
					$html.="
						<span class='search_radio_button' id='search_radio_button_".$i."'><input type='radio' name='dest' value='".$datas[$i]['page']."'".$checked."/>&nbsp;".$this->format_text($datas[$i]['name'])."</span>";
				}
			}
		}
		if ($this->parameters['link_search_advanced']) {
			$html.="
				<p class='search_advanced_link'><a href='./index.php?search_type_asked=simple_search".(isset($this->parameters['link_to_opac_view']) && $this->parameters['link_to_opac_view'] ? '&opac_view='.$this->parameters['link_to_opac_view'] : '')."'>".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_display'])."</a></p>";
		}
		if(isset($this->parameters['others_links']) && is_array($this->parameters['others_links'])) {
			foreach ($this->parameters['others_links'] as $key=>$other_link) {
				$html.="
				<p class='search_other_link' id='search_other_link_".$key."'><a href='".$other_link['url']."' ".(isset($other_link['linktarget']) && $other_link['linktarget'] ? "target='_blank'" : "").">".$this->format_text($other_link['label'])."</a></p>";
			}
		}
		$html.= "		
			</form>";
		return $html;
	}
	
	public function get_headers($datas=array()){
		global $base_path;
		$headers = array();
		
		$headers[] = "
		<script type='text/javascript'>
			function ".$this->get_module_dom_id()."_change_dest(){
				var page = 0;
				var dests = document.forms['".$this->get_module_dom_id()."_searchbox'].dest;
				for(var i = 0; i < dests.length; i++){
					if(dests[i].checked){
						page = dests[i].value;
						break;
					}
				}
				
				if (page.indexOf('view_') != -1) {
					var view_id = page.substr(5);
					document.forms['".$this->get_module_dom_id()."_searchbox'].action = document.forms['".$this->get_module_dom_id()."_searchbox'].action+'&opac_view='+view_id;
				} else if(page>0){
					document.forms['".$this->get_module_dom_id()."_searchbox'].action = '".$base_path."/index.php?lvl=cmspage&pageid='+page;
				}
				return true;
			}
		</script>";
		return $headers;	
	}
}