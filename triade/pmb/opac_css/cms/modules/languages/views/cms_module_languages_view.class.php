<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_languages_view.class.php,v 1.6.6.1.2.2 2025/04/30 13:45:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_languages_view extends cms_module_common_view{

    private $abreviations = [
        "fr_FR"=>"FR",
        "de_DE"=>"DE",
        "es_ES"=>"ES",
        "en_US"=>"US",
        "en_UK"=>"UK",
        "it_IT"=>"IT",
        "nl_NL"=>"NL",
        "pt_PT"=>"PT",
        "pt_BR"=>"BR",
        "ca_ES"=>"ES",
        "la_LA"=>"LA",
        "ar"=>"AR"
    ];


	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){
	    return parent::get_form();
	}

	public function save_form(){
	    return parent::save_form();
	}

	public function render($datas){
	    return $this->generate_list($datas);
	}

	public function generate_list($datas) {
	    global $include_path,$charset;

	    $langues = new XMLlist("$include_path/messages/languages.xml");
	    $langues->analyser();
	    $clang = $langues->table;

	    if ($datas['display_type']) {//type abrégé
	        $options = $clang;
	    } else {
	        $this->init_abbreviations();
	        $options = $this->abreviations;
	    }
	    $method="GET";
	    switch($_REQUEST['REQUEST_METHOD']){
	        case "GET":
	            $action=substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1);
	            break;
	        case "POST":
	            $action=substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/')+1);
	            $method='POST';
	            break;
	    }
        $form = '';
        $form .= "<div id='lang_options_wrapper'>";
        $form .= '<script>
                    function change_lang(langue){
                        var form = document.getElementById("change_lang_form");
                        var input = document.getElementById("change_lang_input");
                        input.value = langue;
                        form.submit();
                    }

                </script>';
        $form .= "<form id='change_lang_form' method=\"".htmlentities($method,ENT_QUOTES,$charset)."\" action=\"".htmlentities($action,ENT_QUOTES,$charset)."\" >";
        //on récupère ce qui a été posté pour rejouer les recherches
        $form .= $this->get_hidden_form();
        $form .= "         <input type='hidden' id='change_lang_input' value=''  name=\"lang_sel\"/>";

        if ($datas["input_type"]==0) {
            $form .= $this->get_display_list($datas, $options);
        }
        else {
            $form .= $this->get_display_selector($datas, $options);
        }


        $form .= "</form></div>";
	    return $form;
	}

	private function get_hidden_form() {
	    global $charset;
	    $form = '';
	    if(count($_POST)) {
	        foreach ($_POST as $name=>$value) {
	            if(is_string($value) && $name !="lang_sel") {
	            	$form .= sprintf("<input type='hidden' name='%s' value='%s' />",
	            		htmlentities($name, ENT_QUOTES, $charset),
	            		htmlentities($value, ENT_QUOTES, $charset));
	            } elseif(is_array($value)) {
	                foreach ($value as $sub_key=>$sub_value) {
	                	if(is_string($sub_value)) {
	                		$form .= sprintf("<input type='hidden' name='%s[%s]' value='%s' />",
	                			htmlentities($name, ENT_QUOTES, $charset),
	                			htmlentities($sub_key, ENT_QUOTES, $charset),
	                			htmlentities($sub_value, ENT_QUOTES, $charset));
	                    }
	                }
	            }
	        }
	    }
	    if(count($_GET)) {
	        foreach ($_GET as $name=>$value) {
	            if(is_string($value) && $name !="lang_sel") {
	                $form .= sprintf("<input type='hidden' name='%s' value='%s' />",
	                    htmlentities($name, ENT_QUOTES, $charset),
	                    htmlentities($value, ENT_QUOTES, $charset));
	            } elseif(is_array($value)) {
	                foreach ($value as $sub_key=>$sub_value) {
	                    if(is_string($sub_value)) {
	                        $form .= sprintf("<input type='hidden' name='%s[%s]' value='%s' />",
	                            htmlentities($name, ENT_QUOTES, $charset),
	                            htmlentities($sub_key, ENT_QUOTES, $charset),
	                            htmlentities($sub_value, ENT_QUOTES, $charset));
	                    }
	                }
	            }
	        }
	    }
	    return $form;
	}

	private function get_display_option_selector($value, $options) {
	    global $lang;
	    
	    $langue = $value;
	    $spe_class = '';
	    $selected = "";
	    if ($langue == $lang) {
	        $spe_class ='lang_active';
	        $selected = "selected";
	    }
	    return "<option $selected value='".$value."' class='lang_option lang_$langue $spe_class' lang='".strtolower(substr($this->abreviations[$langue], 0, 2))."'>
                ".$options[$value]."
                </option>";
	}
	    
	private function get_display_selector($datas, $options) {
	    $form = "<select id='change_lang_selector' name='lang_sel' onChange='this.form.submit()'>";
	    if (is_countable($datas["lang"])) {
    	    for ($i = 0; $i < count($datas["lang"]); $i++) {
    	        $form .= $this->get_display_option_selector($datas["lang"][$i], $options);
    	    }
	    }
	    $form .= "</select>";
	    return $form;
	}

	private function get_display_option_list($value, $options) {
	    global $lang;
	    
	    $langue = $value;
	    $spe_class = '';
	    $aria_current = false;
	    if ($langue == $lang) {
	        $spe_class='lang_active';
	        $aria_current = true;
	    }
	    return "<li class='lang_option lang_$langue $spe_class' lang='".strtolower(substr($this->abreviations[$langue], 0, 2))."' ".($aria_current ? "aria-current='true'" : "").">
              <a href='#'  onClick='change_lang(\"$langue\")'>".$options[$value]."</a>
	      </li>";
	}
	
	private function get_display_list($datas, $options) {
	    $form = "<ul id='change_lang_list' >";
	    if (is_countable($datas["lang"])) {
    	    for ($i = 0; $i < count($datas["lang"]); $i++) {
    	        $form .= $this->get_display_option_list($datas["lang"][$i], $options);
    	    }
	    }
	    $form .= "</ul>";
	    return $form;
	}

	private function init_abbreviations() {
	    global $msg;
	    foreach ($this->abreviations as $code => $abr) {
	        if (isset($msg["language_code_".$code])) {
	            $this->abreviations[$code] = $msg["language_code_".$code];
	        }
	    }
	}
}