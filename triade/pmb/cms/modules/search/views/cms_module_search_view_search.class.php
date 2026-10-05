<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_search_view_search.class.php,v 1.48.2.2.2.1 2025/04/30 07:28:37 qvarin Exp $

use Pmb\Common\Library\CSRF\CollectionCSRF;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $include_path."/h2o/h2o.php";

class cms_module_search_view_search extends cms_module_common_view{

	protected $cadre_parent;

	public function __construct($id=0){
	    parent::__construct((int) $id);

	    if(!isset($this->parameters) || !is_array($this->parameters)){
	    	$this->parameters=[];
	    }
	    if(!isset($this->parameters['help'])) $this->parameters['help'] = 0;
	    if(!isset($this->parameters['title'])) $this->parameters['title'] = '';
	    if(!isset($this->parameters['link_search_advanced'])) $this->parameters['link_search_advanced'] = 0;
	    if(!isset($this->parameters['link_search_advanced_go_tab'])) $this->parameters['link_search_advanced_go_tab'] = '';
	    if(!isset($this->parameters['input_placeholder'])) $this->parameters['input_placeholder'] = '';
	    if(!isset($this->parameters['others_links'])) $this->parameters['others_links'] = [];
	    if(!isset($this->parameters['nofill'])) $this->parameters['nofill'] = 0;
	    if(!isset($this->parameters['limit_completion'])) $this->parameters['limit_completion'] = 1;
	    if(!isset($this->parameters['selector_aff_input_search'])) $this->parameters['selector_aff_input_search'] = "radio";
	}

	public function get_form(){
		global $charset;

		$form ="
		<div class='row'>
			<div class='row'>
				<div class='colonne3'>
					<label>".$this->format_text($this->msg['cms_module_search_view_help'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_help' value='1' ".($this->parameters['help'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_help' value='0' ".(!$this->parameters['help'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_title'>".$this->format_text($this->msg['cms_module_search_view_title'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='cms_module_search_view_title' name='cms_module_search_view_title' value='".($this->parameters['title'] ? htmlentities($this->parameters['title'],ENT_QUOTES,$charset) : "")."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label>".$this->format_text($this->msg['cms_module_search_view_link_search_advanced'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_link_search_advanced' value='1' ".($this->parameters['link_search_advanced'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_link_search_advanced' value='0' ".(!$this->parameters['link_search_advanced'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label>".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_go_tab'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_selector_go_tabs()."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_input_placeholder'>".$this->format_text($this->msg['cms_module_search_view_input_placeholder'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='cms_module_search_view_input_placeholder' name='cms_module_search_view_input_placeholder' value='".($this->parameters['input_placeholder'] ? htmlentities($this->parameters['input_placeholder'],ENT_QUOTES,$charset) : "")."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label>".$this->format_text($this->msg['cms_module_search_view_nofill'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_nofill' value='1' ".($this->parameters['nofill'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_nofill' value='0' ".(!$this->parameters['nofill'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label>".$this->format_text($this->msg['cms_module_search_view_limit_completion'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_limit_completion' value='1' ".($this->parameters['limit_completion'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_limit_completion' value='0' ".(!$this->parameters['limit_completion'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_no'])."
				</div>
			</div>";

    		// Permet de choisir l'affichage des types de recherche soit en checkbox ou en dropdown
    		$form .= "
    			<div class='row'>
    				<div class='colonne3'>
    					<label for='cms_module_search_selector_aff_input_search'>".$this->format_text($this->msg['cms_module_search_selector_aff_input_search'])."</label>
    				</div>
    				<div class='colonne-suite'>
    					<input type='radio' name='cms_module_search_selector_aff_input_search' value='radio' ".($this->parameters['selector_aff_input_search'] == 'radio' ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_selector_aff_input_radio'])."
    					&nbsp;<input type='radio' name='cms_module_search_selector_aff_input_search' value='dropdown' ".($this->parameters['selector_aff_input_search'] == 'dropdown' ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_selector_aff_input_dropdown'])."
    				</div>
    			</div>";

		$collectionCSRF = new CollectionCSRF();

		$advanced_parameters = "
		<script type='text/javascript'>
			const tabTokens_search_view = " . json_encode($collectionCSRF->getArrayTokens()) . ";
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
					var csrf_token = tabTokens_search_view[0];
					tabTokens_search_view.splice(0, 1);
					var testlink = encodeURIComponent(link.value);
					var check = new http_request();
					if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&link='+testlink+'&csrf_token='+csrf_token)){
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
                var title_label = document.createTextNode('titre');
				var title = document.createElement('input');
		        title.setAttribute('name','cms_module_search_view_others_links['+cpt+'][title]');
		        title.setAttribute('id','cms_module_search_view_other_link_title_'+cpt);
		        title.setAttribute('type','text');
				title.setAttribute('class','saisie-15em');
		        title.setAttribute('value','');
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
					document.getElementById('cms_module_search_view_other_link_title_0').value = '';
				}
			}
		</script>
		<div class='row'>
			<label>".$this->format_text($this->msg['cms_module_search_view_others_links'])."</label>
			<input class='bouton' type='button' value='+' onclick=\"add_other_link_();\" />
		</div>
		";

		if (is_array($this->parameters['others_links']) && count($this->parameters['others_links'])) {
		    $advanced_parameters .= "
				<input id='cms_module_search_view_other_link_count' type='hidden' name='cms_module_search_view_other_link_count' value='" . count($this->parameters['others_links']) . "'>
			";
		    foreach ($this->parameters['others_links'] as $key => $other_link) {
		        $advanced_parameters .= "
					<div class='row' id='cms_module_search_view_other_link_" . $key . "'>
						<div id='cms_module_search_view_other_link_check_" . $key . "' style='display:inline'></div>
						" . $this->format_text($this->msg['cms_module_search_view_link_url']) . "
						<input type='text' id='cms_module_search_view_other_link_url_" . $key . "' class='saisie-20em' name='cms_module_search_view_others_links[" . $key . "][url]' value='" . $this->format_text($other_link['url']) . "'/>
						<input class='bouton' type='button' value='" . $this->format_text($this->msg['cms_module_search_view_link_check']) . "' onclick='other_link_chklnk($key,this);'>
						" . $this->format_text($this->msg['cms_module_search_view_link_label']) . "<input id='cms_module_search_view_other_link_label_" . $key . "' type='text' class='saisie-15em' size='50' name='cms_module_search_view_others_links[" . $key . "][label]' value='" . $this->format_text($other_link['label']) . "'>
						" . $this->format_text($this->msg['cms_module_search_view_link_title']) . "<input id='cms_module_search_view_other_link_title_" . $key . "' type='text' class='saisie-15em' size='50' name='cms_module_search_view_others_links[" . $key . "][title]' value='" . $this->format_text($other_link['title']) . "'>
						<input id='cms_module_search_view_other_link_linktarget_" . $key . "' type='checkbox' name='cms_module_search_view_others_links[" . $key . "][linktarget]' value='1' " . (isset($other_link['linktarget']) && $other_link['linktarget'] ? "checked='checked'" : "") . ">
						<label for='cms_module_search_view_other_link_linktarget_" . $key . "'>" . $this->format_text($this->msg['cms_module_search_view_link_target']) . "</label>
						<input class='bouton' type='button' value='X' onclick=\"del_other_link_(" . $key . ");\" />
	 				</div>
					";
		    }
		} else {
		    $advanced_parameters .= "
				<input id='cms_module_search_view_other_link_count' type='hidden' name='cms_module_search_view_other_link_count' value='1'>
				<div class='row' id='cms_module_search_view_other_link_0'>
					<div id='cms_module_search_view_other_link_check_0' style='display:inline'></div>
					" . $this->format_text($this->msg['cms_module_search_view_link_url']) . "
					<input type='text' id='cms_module_search_view_other_link_url_0' class='saisie-20em' name='cms_module_search_view_others_links[0][url]' value=''/>
					<input class='bouton' type='button' value='" . $this->format_text($this->msg['cms_module_search_view_link_check']) . "' onclick='other_link_chklnk(0,this);'>
					" . $this->format_text($this->msg['cms_module_search_view_link_label']) . "<input id='cms_module_search_view_other_link_label_0' type='text' class='saisie-15em' size='50' name='cms_module_search_view_others_links[0][label]' value=''>
					" . $this->format_text($this->msg['cms_module_search_view_link_title']) . "<input id='cms_module_search_view_other_link_title_0' type='text' class='saisie-15em' size='50' name='cms_module_search_view_others_links[0][title]' value=''>
					<input id='cms_module_search_view_other_link_linktarget_0' type='checkbox' name='cms_module_search_view_others_links[0][linktarget]' value='1'>
					<label for='cms_module_search_view_other_link_linktarget_0'>" . $this->format_text($this->msg['cms_module_search_view_link_target']) . "</label>
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
		global $cms_module_search_view_link_search_advanced_go_tab;
		global $cms_module_search_view_input_placeholder;
		global $cms_module_search_view_others_links;
		global $cms_module_search_view_nofill;
		global $cms_module_search_view_limit_completion;
		global $cms_module_search_selector_aff_input_search;

		if(!isset($cms_module_search_view_help)) $cms_module_search_view_help = 0;
		if(!isset($cms_module_search_view_title)) $cms_module_search_view_title = '';
		if(!isset($cms_module_search_view_link_search_advanced)) $cms_module_search_view_link_search_advanced = 0;
		if(!isset($cms_module_search_view_link_search_advanced_go_tab)) $cms_module_search_view_link_search_advanced_go_tab = '';
		if(!isset($cms_module_search_view_input_placeholder)) $cms_module_search_view_input_placeholder = '';
		if(!isset($cms_module_search_view_others_links)) $cms_module_search_view_others_links = [];
		if(!isset($cms_module_search_view_nofill)) $cms_module_search_view_nofill = 0;
		if(!isset($cms_module_search_view_limit_completion)) $cms_module_search_view_limit_completion = 1;
		if(!isset($cms_module_search_view_limit_completion)) $cms_module_search_selector_aff_input_search = "radio";

		$this->parameters = [];
		$this->parameters['help'] = (int) $cms_module_search_view_help;
		$this->parameters['title'] = stripslashes($cms_module_search_view_title);
		$this->parameters['link_search_advanced'] = (int) $cms_module_search_view_link_search_advanced;
		$this->parameters['link_search_advanced_go_tab'] = stripslashes($cms_module_search_view_link_search_advanced_go_tab);
		$this->parameters['input_placeholder'] = stripslashes($cms_module_search_view_input_placeholder);
		$this->parameters['nofill'] = (int) $cms_module_search_view_nofill;
		$this->parameters['limit_completion'] = (int) $cms_module_search_view_limit_completion;
		$this->parameters["selector_aff_input_search"] = $cms_module_search_selector_aff_input_search;

		$others_links = array();
		$nb_others_link = 0;
		if(is_array($cms_module_search_view_others_links)) {
			foreach ($cms_module_search_view_others_links as $other_link) {
				if($other_link['url'] != '') {
					$others_links[$nb_others_link]['url'] = $other_link['url'];
					$others_links[$nb_others_link]['label'] = stripslashes($other_link['label']);
					$others_links[$nb_others_link]['title'] = stripslashes($other_link['title']);
					$others_links[$nb_others_link]['linktarget'] = (int) $other_link['linktarget'];
					$nb_others_link++;
				}
			}
		}
		$this->parameters['others_links'] = $others_links;
		return parent::save_form();
	}

	protected function get_tabs() {
		global $opac_allow_personal_search, $opac_allow_extended_search, $opac_allow_extended_search_authorities;
		global $opac_allow_term_search, $opac_allow_tags_search, $opac_show_onglet_perio_a2z;

		$tabs = array();
		$tabs[] = 'simple_search';
		if ($opac_allow_personal_search) {
			$tabs[] = 'search_perso';
		}

		if ($opac_allow_extended_search) {
			$tabs[] = 'extended_search';
		}
		if ($opac_allow_extended_search_authorities) {
			$tabs[] = 'extended_search_authorities';
		}
		if ($opac_allow_term_search) {
			$tabs[] = 'term_search';
		}
		if ($opac_allow_tags_search) {
			$tabs[] = 'tags_search';
		}
		if ($opac_show_onglet_perio_a2z) {
			$tabs[] = 'perio_a2z';
		}
		return $tabs;
	}

	protected function get_selector_go_tabs() {
		$selector = "<select name='cms_module_search_view_link_search_advanced_go_tab'>";
		$tabs = $this->get_tabs();
		foreach ($tabs as $value) {
			$selector .= "<option value='".$value."' ".(!empty($this->parameters['link_search_advanced_go_tab']) && $this->parameters['link_search_advanced_go_tab'] == $value ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_go_tab_'.$value])."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}

	public function render($datas){
		global $base_path,$include_path,$opac_autolevel2;
		global $opac_modules_search_title,$opac_modules_search_author,$opac_modules_search_publisher,$opac_modules_search_titre_uniforme;
		global $opac_modules_search_collection,$opac_modules_search_subcollection,$opac_modules_search_category,$opac_modules_search_indexint;
		global $opac_modules_search_keywords,$opac_modules_search_abstract,$opac_modules_search_concept,$opac_modules_search_docnum,$opac_simple_search_suggestions;
		global $user_query, $charset, $opac_search_autocomplete, $msg;
		global $opac_rgaa_active;

		$onsubmit = "if (".$this->get_module_dom_id()."_searchbox.user_query.value.length == 0) { ".$this->get_module_dom_id()."_searchbox.user_query.value='*';}";

		if ($opac_autolevel2==2) {
			$action = $base_path."/index.php?lvl=more_results&autolevel1=1";
		} else {
			$action = $base_path."/index.php?lvl=search_result&search_type_asked=simple_search";
		}

		//juste une searchbox...
		if (is_countable($datas) && count($datas) == 1){
			if (!empty($datas[0]['universe'])) {
				if($datas[0]['default_segment'] != 0){
					$action = $base_path."/index.php?lvl=search_segment&action=segment_results&id=".$datas[0]['default_segment'];
				}else{
					$action=$base_path."/index.php?lvl=search_universe&id=".$datas[0]['universe'];
				}
			}else if ($datas[0]['page']>0) {
				$action = $base_path."/index.php?lvl=cmspage&pageid=".$datas[0]['page'];
			}
			if (strpos($datas[0]['page'], 'view_') !== false) {
				$action.= "&opac_view=".substr($datas[0]['page'], 5);
			}
		}else{
		    $func = $this->get_module_dom_id()."_change_dest";
		    $onsubmit .= "if (typeof {$func} == 'function') { {$func}(); }";
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
		if ($opac_modules_search_docnum==2) {
		    $look["look_DOCNUM"] = 1;
		}

		$searchbox_aff_select = "";
		if ('radio' != $this->parameters['selector_aff_input_search']) {
		    $searchbox_aff_select = "searchbox_aff_input_search";
		}
		$html = "
			<form method='post' class='searchbox " . $searchbox_aff_select . "' action='".$action."' name='".$this->get_module_dom_id()."_searchbox' ".($onsubmit!= "" ? "onsubmit=\"".$onsubmit."\"" : "").">
				";
		foreach($look as $looktype => $lookflag) {
			$html.="
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
			if($opac_rgaa_active){
				$html.="
				<span class='searchbox_title'>".htmlentities(get_msg_to_display($this->parameters['title']),ENT_QUOTES,$charset)."</span>";
			}else{
				$html.="
				<h4 class='searchbox_title'>".htmlentities(get_msg_to_display($this->parameters['title']),ENT_QUOTES,$charset)."</h4>";
			}
		}
		if ($opac_rgaa_active) {
		    $html .= "<div class='research_inputs' role='search'>";
		} else {
		    $html .= "<span class='research_inputs' role='search'>";
		}

		$placeholder = (($this->parameters['input_placeholder']) ? stripslashes(htmlentities(get_msg_to_display($this->parameters['input_placeholder']), ENT_QUOTES, $charset)) : '');
		$user_query_value = ($this->parameters['nofill'] == 1 ? '' : stripslashes(htmlentities($user_query, ENT_QUOTES, $charset)));

		if ($opac_simple_search_suggestions) {
		    $html .= "
					<script src='$include_path/javascript/ajax.js'></script>
					<label class='visually-hidden' for='" . $this->get_module_dom_id() . "_user_query_lib'>" . htmlentities($msg["autolevel1_search"], ENT_QUOTES, $charset) . "</label>
					<input class='search_txt' type='text' name='user_query' id='" . $this->get_module_dom_id() . "_user_query_lib' value='$user_query_value' " . (($this->parameters['limit_completion']) ? "" : "expand_mode='1' ") . "completion='suggestions' disableCompletion='false' word_only='no' placeholder='$placeholder' title='" . htmlentities($msg['autolevel1_search'], ENT_QUOTES, $charset) . "' />
					<script>
					function " . $this->get_module_dom_id() . "_toggleCompletion(destValue){
						if((destValue.indexOf('view_') == -1) && (destValue != '0')){
							document.getElementById('" . $this->get_module_dom_id() . "_user_query_lib').setAttribute('disableCompletion','true');
						}else{
							document.getElementById('" . $this->get_module_dom_id() . "_user_query_lib').setAttribute('disableCompletion','false');
						}
					}
					ajax_pack_element(document.getElementById('" . $this->get_module_dom_id() . "_user_query_lib'));
				</script>";
		} else {
		    $html .= "
				<label class='visually-hidden' for='" . $this->get_module_dom_id() . "_user_query_lib'>" . $this->format_text($msg['autolevel1_search']) . "</label>
				<input type='text' name='user_query' id='" . $this->get_module_dom_id() . "_user_query_lib' value='$user_query_value' placeholder='$placeholder'/>";
		}

		$html.="
				<input class='bouton button_search_submit' type='submit' value='".$this->format_text($this->msg['cms_module_search_button_label'])."' />";
		if ($this->parameters['help']) {
			if($opac_rgaa_active){
				$html.="
					<a class='bouton button_search_help search_link' href='#' onclick='window.open(\"./help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' >".$this->format_text($this->msg['cms_module_search_help'])."</a>";
			}else{
				$html.="
					<input class='bouton button_search_help' title='".$this->format_text($this->msg['cms_module_search_help'])."' type='button' onclick='window.open(\"./help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' value='".$this->format_text($this->msg['cms_module_search_help'])."'>";
			}
		}
		if ($opac_rgaa_active) {
		    $html .= "</div>";
		} else {
		    $html .= "</span>";
		}
		if (is_countable($datas) && count($datas) > 1) {
		    $html .= "<br/>";
		    if ('radio' == $this->parameters['selector_aff_input_search']) {
		        if ($opac_rgaa_active) {
		            $html .= "<fieldset class='search_type_option'><legend class='visually-hidden'>" . htmlentities($msg["cms_search_label_select_search_type"], ENT_QUOTES, $charset) . "</legend>";
		        }
		        $html .= $this->gen_search_radio_button($datas);
		        if ($opac_rgaa_active) {
		            $html .= "</fieldset>";
		        }
		    } else {
		        if ($opac_rgaa_active) {
		            $html .= "<div class='search_type_option'>";
		        }
		        $html .= $this->gen_search_dropdown_button($datas);
		        if ($opac_rgaa_active) {
		            $html .= "</div>";
		        }
		    }
		}
		if ($opac_rgaa_active) {
		    $html .= "<div class='search_link_container'>";
		}
		if ($this->parameters['link_search_advanced']) {
			if(!empty($this->parameters['link_search_advanced_go_tab'])) {
				$search_type_asked = $this->parameters['link_search_advanced_go_tab'];
			} else {
				$search_type_asked = 'simple_search';
			}

			if($opac_rgaa_active){
				$html.="
					<a class='search_link search_advanced_link' href='./index.php?search_type_asked=".$search_type_asked."' title='".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_display'])."'> ".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_display'])."</a>";
			}else{
				$html.="
					<p class='search_advanced_link'><a href='./index.php?search_type_asked=".$search_type_asked."' title='".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_display'])."'> ".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_display'])."</a></p>";
			}
		}
		if (is_countable($this->parameters['others_links']) && count($this->parameters['others_links'])) {
		    if ($opac_rgaa_active) {
		        foreach ($this->parameters['others_links'] as $key => $other_link) {
		            $html .= "
						<a class='search_link search_other_link' id='search_other_link_" . $key . "' href='" . $other_link['url'] . "' " . (isset($other_link['linktarget']) && $other_link['linktarget'] ? "target='_blank'" : "") . " title='" . $this->format_text($other_link['title']) . "'>" . $this->format_text($other_link['label']) . "</a>";
		        }
		    } else {
		        foreach ($this->parameters['others_links'] as $key => $other_link) {
		            $html .= "
						<p class='search_other_link' id='search_other_link_" . $key . "'><a href='" . $other_link['url'] . "' " . (isset($other_link['linktarget']) && $other_link['linktarget'] ? "target='_blank'" : "") . " title='" . $this->format_text($other_link['title']) . "'>" . $this->format_text($other_link['label']) . "</a></p>";
		        }
		    }
		}
		if ($opac_rgaa_active) {
		    $html .= "</div>";
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
                var universe = 0;
                var default_segment = 0;
				if(document.forms['".$this->get_module_dom_id()."_searchbox'].dest) {
					var dests = document.forms['".$this->get_module_dom_id()."_searchbox'].dest;
					for(var i = 0; i < dests.length; i++){
    					if(dests[i].checked || dests[i].selected ){
    						page = dests[i].getAttribute('page');
	                        universe = dests[i].getAttribute('universe');
	                        default_segment = dests[i].getAttribute('default_segment');
							break;
						}
					}
				}
                if(universe > 0){
                    if(default_segment > 0){
                        document.forms['".$this->get_module_dom_id()."_searchbox'].action = '".$base_path."/index.php?lvl=search_segment&action=segment_results&id='+default_segment;
                    } else {
                        document.forms['".$this->get_module_dom_id()."_searchbox'].action = '".$base_path."/index.php?lvl=search_universe&id='+universe;
                    }
                } else if(page>0){
					document.forms['".$this->get_module_dom_id()."_searchbox'].action = '".$base_path."/index.php?lvl=cmspage&pageid='+page;
				}
                if (page.toString().indexOf('view_') != -1) {
					var view_id = page.substr(5);
				    document.forms['".$this->get_module_dom_id()."_searchbox'].action += '&opac_view='+view_id;
				}
			}
		</script>";
		return $headers;
	}

	public function gen_search_radio_button($data) {
	    global $opac_simple_search_suggestions;
	    global $dest;

		if (!is_countable($data)) {
			return "";
		}

		$html = "";
	    for($i=0 ; $i<count($data) ; $i++){
	        $checked ="";
	        $dest_i = "dest_" . $i;
	        if (!isset($dest)) {
	            $dest = $dest_i;
	            $checked= " checked='checked'";
	        } else if ($dest_i == $dest) {
	            $checked= " checked='checked'";
	        }

	        if ($opac_simple_search_suggestions) {
	            $html .= "
					<span class='search_radio_button' id='search_radio_button_" . $i . "'>
						<input id='input_search_radio_button_" . $i . "' type='radio' name='dest' value='dest_" . $i . "' default_segment='" . (!empty($data[$i]['default_segment']) ? $data[$i]['default_segment'] : "0") . "' page='" . $data[$i]['page'] . "' universe='" . (!empty($data[$i]['universe']) ? $data[$i]['universe'] : "") . "'" . $checked . " onClick='" . $this->get_module_dom_id() . "_toggleCompletion(this.value);' />
						&nbsp;
						<label for='input_search_radio_button_" . $i . "'>" . $this->format_text($data[$i]['name']) . "</label>
					</span>";
	        } else {
	            $html .= "
					<span class='search_radio_button' id='search_radio_button_" . $i . "'>
						<input id='input_search_radio_button_" . $i . "' type='radio' name='dest' value='dest_" . $i . "' default_segment='" . (!empty($data[$i]['default_segment']) ? $data[$i]['default_segment'] : "0") . "' page='" . $data[$i]['page'] . "' universe='" . (!empty($data[$i]['universe']) ? $data[$i]['universe'] : "") . "'" . $checked . "/>
						&nbsp;
						<label for='input_search_radio_button_" . $i . "'>" . $this->format_text($data[$i]['name']) . "</label>
					</span>";
	        }
	    }
	    return $html;
	}

	public function gen_search_dropdown_button($data) {
	    global $opac_simple_search_suggestions;
	    global $msg, $charset;

	    $html = "<label class='visually-hidden' for='search_dropdown_button'>" . htmlentities($msg["cms_search_label_select_search_type"], ENT_QUOTES, $charset) . "</label>";
	    $html .= "<select id='search_dropdown_button' class='search_dropdown_select' name='dest'>";
		if (is_countable($data)) {
			for ($i = 0; $i < count($data); $i++) {
				$selected = "";
				$dest_i = "dest_" . $i;
				if (!isset($dest)) {
					$dest = $dest_i;
					$selected = " selected='selected'";
				} else if ($dest_i == $dest) {
					$selected = " selected='selected'";
				}
				if($opac_simple_search_suggestions){
					$html.="
						<option
							class='search_dropdown_option'
							value='dest_" . $i . "'
							default_segment='".(!empty($data[$i]['default_segment'])?$data[$i]['default_segment']:"0")."'
							page='" . $data[$i]['page'] . "'
							universe='".(!empty($data[$i]['universe'])?$data[$i]['universe']:"")."'".$selected."
							onClick='".$this->get_module_dom_id()."_toggleCompletion(this.value);'
						>
							".$this->format_text($data[$i]['name'])."
						</option>";
				}else{
					$html.="
						<option
							class='search_dropdown_option'
							value='dest_" . $i . "'
							default_segment='".(!empty($data[$i]['default_segment'])?$data[$i]['default_segment']:"0")."'
							page='" . $data[$i]['page'] . "'
							universe='".(!empty($data[$i]['universe'])?$data[$i]['universe']:"")."'".$selected."
						>
							".$this->format_text($data[$i]['name'])."
						</option>";
				}
			}
		}
	    $html .= "</select>";

	    return $html;

	}
}