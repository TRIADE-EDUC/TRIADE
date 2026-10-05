<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_search_segment.class.php,v 1.10.2.1 2025/01/16 11:24:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector.class.php");
require_once($class_path.'/searcher/searcher_factory.class.php');

class selector_search_segment extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'search_segment';
	}

	public function proceed() {
	    global $action;

	    $entity_form = '';
	    $response = '';

	    switch($action) {
	        case 'simple_search':
                $entity_form = $this->get_simple_search_form();
                break;
	        case 'advanced_search':
	            $entity_form = $this->get_advanced_search_form();
	            break;
	        case 'serialize_search':
	            $response = $this->get_serialize_search();
	            break;
            default:
                print $this->get_sel_header_template();
                print $this->get_js_script();
                print $this->get_sel_footer_template();
                print $this->get_sub_tabs();
                break;
        }
        if (!empty($entity_form)) {
            header("Content-Type: text/html; charset=UTF-8");
            print encoding_normalize::utf8_normalize($entity_form);
        } elseif (!empty($response)) {
            print encoding_normalize::json_encode($response);
        }
	}

	protected function get_js_script() {
	    global $jscript;
	    global $jscript_common_selector;

	    if(!isset($jscript)) {
	        $jscript = $jscript_common_selector;
	    }
	    return $jscript;
	}

	protected function get_add_link() {
	    return $this->data['form_id'] ?? "";
	}

	protected function get_add_label() {
		global $msg;
        return $msg['selector_author_add'];
	}

	public function get_sel_header_template() {
	    global $base_path;

	    $sel_header = "
			<div id='att' style='z-Index:1000'></div>
			<script src='".$base_path."/includes/javascript/ajax.js'></script>
		    <div class='row'>
			    <label for='selector_title' class='etiquette'></label>
			</div>
			<div class='row'>
			";
	    return $sel_header;
	}

	public function get_title() {
	    global $msg;
	    return $msg["empr_menu_contribution_area"];
	}

	protected function get_searcher_tabs_instance() {
	    if(!isset($this->searcher_tabs_instance)) {
	        $type = $this->data['type'] ?? "record";
	        switch ($type) {
	            case "record" :
	                $this->searcher_tabs_instance = new searcher_selectors_tabs('records');
	                break;
	            default:
	                $this->searcher_tabs_instance = new searcher_selectors_tabs('authorities');
	                break;
	        }
	    }
	    return $this->searcher_tabs_instance;
	}

	public function get_objects_type() {
	    if (isset($this->data['type'])) {
	        return entities::get_searcher_mode_from_type($this->data['type']);
	    }
	    return $this->objects_type;
	}

	protected function get_sel_search_form_name() {
	    if(!empty($this->objects_type) && !empty($this->data['form_id'])) {
	        return "selector_".$this->objects_type."_search_form_".$this->data['form_id'];
	    } else {
	        return "selector_search_form";
	    }
	}

	protected function get_search_instance() {
        $type = $this->data['type'] ?? TYPE_NOTICE;
        switch ($type) {
            case TYPE_NOTICE :
                $search = new search('search_fields');
                break;
            case TYPE_EXTERNAL :
                $search = new search('search_fields_unimarc');
                break;
            default:
                $search = new search_authorities('search_fields_authorities');
                break;
        }
	    $search->add_context_parameter('in_selector', true);
	    $search->add_context_parameter('search_segment', true);
	    return $search;
	}

	protected function get_search_fields_filtered_objects_types() {
	    $type = $this->data['type'] ?? TYPE_NOTICE;
	    if ($type != TYPE_NOTICE) {
	        if ($type > 1000) {
	            global $authperso_id;
	            $authperso_id = ($type - 1000);
	        }
	        $type = entities::get_string_from_const_type($type);
	        return array($type, "authorities");
	    }
	    return [];
	}

	protected function get_search_perso_instance($id=0) {
	    $type = $this->data['type'] ?? TYPE_NOTICE;
	    if ($type != TYPE_NOTICE) {
	        return new search_perso($id, 'AUTHORITIES');
	    }
	    return new search_perso($id);
	}

	protected function get_advanced_search_form() {
	    $advanced_search_form = $this->get_sel_header_template();
	    $advanced_search_form .= parent::get_advanced_search_form();
	    return $advanced_search_form;
	}

	private function get_serialize_search() {
	    $search = $this->get_search_instance();
	    $serialize_search = $search->serialize_search();
	    return [
	        "serialize_search" => $serialize_search
	    ];
	}

	protected function get_sub_tabs(){
	    $current_url = static::get_base_url();
	    $current_url = str_replace('select.php?', 'ajax.php?module=selectors&', $current_url);

	    $searcher_tab = $this->get_searcher_tabs_instance();
	    return '
				<div id="widget-container"></div>
				<script>
					require(["apps/pmb/form/search_segment/FormSearchSegmentSelector", "dojo/dom"], function(FormSelector, dom){
						new FormSelector({doLayout: false, selectorURL:"'.$current_url.'", multicriteriaMode: "'.$searcher_tab->get_mode_multi_search_criteria().'"}, "widget-container");
					});
				</script>
				';
	}

	protected function get_simple_search_form() {
	    global $msg, $charset;

	    $form = "
                <form name='search_input' id='search_input' action='' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">
    				<input type='text' name='user_query' class='text_query' value=\"\" size='65' title='".htmlentities($msg['autolevel1_search'], ENT_QUOTES, $charset)."' />
    				<input type='button' id='launch_search_button' name='ok' value='".htmlentities($msg["142"], ENT_QUOTES, $charset)."' class='bouton'/>
                </form>";
	    return $form;
	}
}