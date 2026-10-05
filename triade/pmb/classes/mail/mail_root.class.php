<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_root.class.php,v 1.25.2.6 2024/12/17 07:18:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/user.class.php");
require_once($include_path."/h2o/pmb_h2o.inc.php");

abstract class mail_root {
	
	protected $type;
	
    protected $formatted_data;
    
    protected $model_instance;
    
    protected $settings = array();
    
    protected static $language = '';
    protected static $languages_messages = array();
    protected static $languages_specifics_globals = array();
    
    protected $mail_to_id;
    protected $mail_to_name;
    protected $mail_to_mail;
    protected $mail_object;
    protected $mail_content;
    protected $mail_from_id;
    protected $mail_from_name;
    protected $mail_from_mail;
    protected $mail_attachments = array();
    protected $mail_embedded_attachments = array();
    protected $mail_is_mailing;
    
    protected $associated_campaign;
    
    protected $associated_num_campaign;
    
    public function __construct() {
    	if(empty($this->type)) {
    		$this->type = str_replace(array('mail_', '_subst'), '', get_class($this));
    	}
        $this->_substitution_parameters();
        $this->_init();
        $this->_init_settings_from_database();
    }
    
    protected function _substitution_parameters() {
        global $include_path;
        global $deflt2docs_location;
        
        //Globalisons tout d'abord les paramètres communs à toutes les localisations
        if (file_exists($include_path."/parameters_subst/mail_per_localisations_subst.xml")){
            $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations_subst.xml", 0);
        } else {
            $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations.xml", 0);
        }
        $parameter_subst->extract();
        
        if(isset($deflt2docs_location)) {
            if (file_exists($include_path."/parameters_subst/mail_per_localisations_subst.xml")){
                $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations_subst.xml", $deflt2docs_location);
            } else {
                $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations.xml", $deflt2docs_location);
            }
            $parameter_subst->extract();
        }
    }
    
    protected function _init_default_parameters() {
        
    }
    
    protected function _init_default_settings() {
    	
    }
    
    protected function _init() {
        $this->_init_default_parameters();
        $this->_init_default_settings();
    }
    
    protected function _init_settings_from_database() {
        $id = mail_setting::get_id_from_classname(static::class);
        if($id) {
            $mail_setting = new mail_setting($id);
            $this->_init_setting_value('sender', $mail_setting->get_sender());
            $this->_init_setting_value('copy_cc', $mail_setting->get_copy_cc());
            $this->_init_setting_value('copy_bcc', $mail_setting->get_copy_bcc());
            $this->_init_setting_value('reply', $mail_setting->get_reply());
        }
    }
    
    protected static function get_parameter_prefix() {
		return '';
	}
	
	protected function is_exist_parameter($parameter_name) {
		global ${$parameter_name};
		if(isset(${$parameter_name})) {
			return true;
		}
		return false;
	}
	
	protected function get_evaluated_parameter($parameter_name) {
		global $biblio_name, $biblio_email, $biblio_phone, $biblio_commentaire, $biblio_website;
		global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town;
		global ${$parameter_name};
		
	    eval ("\$evaluated=\"".addslashes(${$parameter_name})."\";");
	    return stripslashes($evaluated);
	}
	
	protected function get_parameter_id($type_param, $sstype_param) {
	    $query = "SELECT id_param FROM parametres WHERE type_param='".addslashes($type_param)."' AND sstype_param='".addslashes($sstype_param)."'";
	    return pmb_mysql_result(pmb_mysql_query($query), 0, 'id_param');
	}
	
	protected function get_parameter_value($name) {
	    $id_param = $this->get_parameter_id(static::get_parameter_prefix(), $name);
	    $parameter_value = translation::get_translated_text($id_param, 'parametres', 'valeur_param', '', static::$language);
	    if($parameter_value) {
	        return $parameter_value;
	    } else {
    		$parameter_name = static::get_parameter_prefix().'_'.$name;
    		return $this->get_evaluated_parameter($parameter_name);
	    }
	}
	
	protected function _init_parameter_value($name, $value) {
		$parameter_name = static::get_parameter_prefix().'_'.$name;
		global ${$parameter_name};
		if(empty(${$parameter_name})) {
			${$parameter_name} = $value;
		}
	}
	
	protected function _init_setting_value($name, $value) {
		$this->settings[$name] = $value;
	}
	
	protected function set_language($language) {
		global $msg, $lang;
		
		if(empty(static::$languages_messages)) {
			static::$languages_messages[$lang] = $msg;
		}
		if($lang != $language) {
		    $msg = static::get_language_messages($language);
		    parameter::set_language_parameters($language);
		    static::set_language_specifics_globals($language);
		}
		static::$language = $language;
	}
	
	protected function restaure_language() {
		global $msg, $lang;
		
		if(!empty(static::$languages_messages[$lang])) {
			$msg = static::$languages_messages[$lang];
		}
		parameter::set_language_parameters($lang);
		static::set_language_specifics_globals($lang);
	}
	
	protected function get_mail_to_id() {
		if(!empty($this->mail_to_id)) {
			return $this->mail_to_id;
		}
		return 0;
	}
	
	protected function get_mail_to_name() {
		if(!empty($this->mail_to_name)) {
			return $this->mail_to_name;
		}
		return '';
	}
	
	protected function get_mail_to_mail() {
		if(!empty($this->mail_to_mail)) {
			return $this->mail_to_mail;
		}
		return '';
	}
	
	protected function get_mail_object() {
		if(!empty($this->mail_object)) {
			return $this->mail_object;
		}
		return '';
	}
	
	protected function get_mail_content() {
		if(!empty($this->mail_content)) {
			return $this->mail_content;
		}
		return '';
	}

	protected function get_mail_from_id() {
		if(!empty($this->mail_from_id)) {
			return $this->mail_from_id;
		}
		return 0;
	}
	
	protected function get_mail_from_name() {
	    global $opac_biblio_name;
		global $biblio_name;
		global $PMBuserprenom, $PMBusernom;
		
		switch ($this->get_setting_value('sender')) {
			case 'parameter':
				return $opac_biblio_name;
			case 'docs_location':
				if(!empty($this->mail_from_id)) {
					$docs_location = new docs_location($this->mail_from_id);
					return $docs_location->libelle;
				} else {
					return $biblio_name;
				}
			case 'user':
				if(!empty($this->mail_from_id)) {
					$user_email = user::get_param($this->mail_from_id, 'user_email');
					if($user_email) {
						return user::get_name($this->mail_from_id);
					}
				}
				return $PMBuserprenom." ".$PMBusernom;
		}
		return '';
	}
	
	protected function get_mail_from_mail() {
	    global $opac_biblio_email;
		global $biblio_email;
		global $PMBuseremail;
		
		switch ($this->get_setting_value('sender')) {
			case 'parameter':
				return $opac_biblio_email;
			case 'docs_location':
				if(!empty($this->mail_from_id)) {
					$docs_location = new docs_location($this->mail_from_id);
					return $docs_location->email;
				} else {
					return $biblio_email;
				}
			case 'user':
				if(!empty($this->mail_from_id)) {
					$user_email = user::get_param($this->mail_from_id, 'user_email');
					if($user_email) {
						return $user_email;
					}
				}
				return $PMBuseremail;
		}
		return '';
	}
	
	protected function get_mail_headers() {
		return "";
	}
	
	protected function get_mail_copy_cc() {
	    if(!empty($this->get_setting_value('copy_cc'))) {
	        $copy_cc = $this->get_setting_value('copy_cc');
	        if(is_valid_mail($copy_cc)) {
	            return $copy_cc;
	        }
	        $values = mail_setting::get_values_copy_cc();
	        if(!empty($values[$copy_cc])) {
	            $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
	            $matches = [];
	            preg_match($pattern, $values[$copy_cc], $matches);
	            if(!empty($matches[0]) && is_valid_mail($matches[0])) {
	                return $matches[0];
	            }
	        }
	    }
		return "";
	}
	
	protected function get_mail_copy_bcc() {
		global $PMBuseremailbcc;
		
		if(!empty($this->get_setting_value('copy_bcc'))) {
			return $PMBuseremailbcc;
		}
		return '';
	}
	
	protected function get_mail_do_nl2br() {
		return 0;
	}
	
	protected function get_mail_attachments() {
		return $this->mail_attachments;
	}

	protected function get_mail_embedded_attachments() {
		return $this->mail_embedded_attachments;
	}
	
	protected function get_mail_reply_name() {
	    global $opac_biblio_name;
	    global $biblio_name;
	    global $PMBuserprenom, $PMBusernom;
	    
	    switch ($this->get_setting_value('reply')) {
	        case 'parameter':
	            return $opac_biblio_name;
	        case 'docs_location':
                return $biblio_name;
	        case 'user':
	            return $PMBuserprenom." ".$PMBusernom;
	    }
	    return '';
	}
	
	protected function get_mail_reply_mail() {
	    global $opac_biblio_email;
	    global $biblio_email;
	    global $PMBuseremail;
	    
	    switch ($this->get_setting_value('reply')) {
	        case 'parameter':
	            return $opac_biblio_email;
	        case 'docs_location':
	            return $biblio_email;
	        case 'user':
	            return $PMBuseremail;
	    }
		return '';
	}
	
	protected function get_mail_is_mailing() {
		if(!empty($this->mail_is_mailing)) {
			return $this->mail_is_mailing;
		}
		return false;
	}
	
	public function set_mail_to_id($mail_to_id) {
		$this->mail_to_id = intval($mail_to_id);
		return $this;
	}
	
	public function set_mail_to_name($mail_to_name) {
		$this->mail_to_name = $mail_to_name;
		return $this;
	}
	
	public function set_mail_to_mail($mail_to_mail) {
		$this->mail_to_mail = $mail_to_mail;
		return $this;
	}
	
	public function set_mail_object($mail_object) {
		$this->mail_object = $mail_object;
		return $this;
	}
	
	public function set_mail_content($mail_content) {
		$this->mail_content = $mail_content;
		return $this;
	}
	
	public function set_mail_from_id($mail_from_id) {
		$this->mail_from_id = intval($mail_from_id);
		return $this;
	}
	
	public function set_mail_from_name($mail_from_name) {
		$this->mail_from_name = $mail_from_name;
		return $this;
	}
	
	public function set_mail_from_mail($mail_from_mail) {
		$this->mail_from_mail = $mail_from_mail;
		return $this;
	}
	
	public function set_mail_attachments($mail_attachments) {
		$this->mail_attachments = $mail_attachments;
		return $this;
	}

	public function set_mail_embedded_attachments($mail_embedded_attachments) {
		$this->mail_embedded_attachments = $mail_embedded_attachments;
		return $this;
	}
	
	public function set_mail_is_mailing($mail_is_mailing) {
		$this->mail_is_mailing = $mail_is_mailing;
		return $this;
	}
	
	public function mailpmb() {
		if($this->get_mail_to_mail()) {
			if(!empty($this->associated_campaign) && $this->get_mail_to_id()) {
				$campaign = $this->get_campaign();
				$sended = $campaign->send_mail(
						$this->get_mail_to_id(), 
						$this->get_mail_to_name(),
						$this->get_mail_to_mail(),
						$this->get_mail_object(),
						$this->get_mail_content(),
						$this->get_mail_from_name(),
						$this->get_mail_from_mail(),
						$this->get_mail_headers(),
						$this->get_mail_copy_cc(),
						$this->get_mail_copy_bcc(),
						$this->get_mail_do_nl2br(),
						$this->get_mail_attachments(),
						$this->get_mail_reply_name(),
						$this->get_mail_reply_mail(), 
						$this->get_mail_is_mailing(),
						$this->type
				);
			} else {
				$sended = mailpmb(
						$this->get_mail_to_name(),
						$this->get_mail_to_mail(),
						$this->get_mail_object(),
						$this->get_mail_content() ,
						$this->get_mail_from_name(),
						$this->get_mail_from_mail(),
						$this->get_mail_headers(),
						$this->get_mail_copy_cc(),
						$this->get_mail_copy_bcc(),
						$this->get_mail_do_nl2br(),
						$this->get_mail_attachments(),
						$this->get_mail_reply_name(),
						$this->get_mail_reply_mail(),
						$this->get_mail_is_mailing(),
						$this->type
				);
			}
		} else {
			$sended = false;
		}
		return $sended;
	}
	
	public function send_mail() {
		return $this->mailpmb();
	}
	
	public function get_model_instance() {
		return $this->model_instance;
	}
	
	public function get_settings() {
		return $this->settings;
	}
	
	public function get_setting_value($name) {
		return $this->settings[$name] ?? null;
	}
	
	public function set_model_instance($model_instance) {
		$this->model_instance = $model_instance;
	}
	
	public function set_associated_campaign($associated_campaign) {
		$this->associated_campaign = $associated_campaign;
		return $this;
	}
	
	public function set_associated_num_campaign($associated_num_campaign) {
		$this->associated_num_campaign = $associated_num_campaign;
		return $this;
	}
	
	public function get_campaign() {
		if($this->associated_campaign) {
			if($this->associated_num_campaign) {
				$campaign = new campaign($this->associated_num_campaign);
			} else {
				$campaign = new campaign();
				$campaign->set_type($this->type);
				$campaign->set_label($this->get_mail_object());
				$saved = $campaign->save();
				//On conserve l'identifiant de la nouvelle campagne pour la suite
				if($saved) {
					$this->associated_num_campaign = $campaign->get_id();
				}
			}
			return $campaign;
		}
		return null;
	}
	
	public static function render($tpl, $data) {
	    global $charset;
        $data=encoding_normalize::utf8_normalize($data);
        $tpl=encoding_normalize::utf8_normalize($tpl);
        $data_to_return = H2o::parseString($tpl)->render($data);
        if ($charset !="utf-8") {
            $data_to_return = encoding_normalize::utf8_decode($data_to_return);
        }
        return $data_to_return;
	}
	
	public static function get_instance() {
		$className = static::class;
		$prefix = static::get_parameter_prefix();
		$mail_parameter = $prefix."_mail";
		global ${$mail_parameter};
		if(!empty(${$mail_parameter}) && class_exists(${$mail_parameter})) {
		    $className = ${$mail_parameter};
		} elseif(class_exists($className.'_subst')) {
			$className = $className.'_subst';
		}
		return new $className();
	}
	
	public static function get_language_messages($language) {
	    global $include_path;
	    
	    if(!isset(static::$languages_messages[$language])) {
	        $messages_instance = new XMLlist($include_path."/messages/".$language.".xml");
	        $messages_instance->analyser();
	        static::$languages_messages[$language] = $messages_instance->table;
	    }
	    return static::$languages_messages[$language];
	}
	
	public static function get_language_specifics_globals($language) {
	    global $lang;
	    global $deflt2docs_location, $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_town;
	    
	    if(!isset(static::$languages_specifics_globals[$lang])) {
	        static::$languages_specifics_globals[$lang] = [];
	        static::$languages_specifics_globals[$lang]['biblio_name'] = $biblio_name;
	        static::$languages_specifics_globals[$lang]['biblio_adr1'] = $biblio_adr1;
	        static::$languages_specifics_globals[$lang]['biblio_adr2'] = $biblio_adr2;
	        static::$languages_specifics_globals[$lang]['biblio_town'] = $biblio_town;
	    }
	    if(!isset(static::$languages_specifics_globals[$language])) {
	        static::$languages_specifics_globals[$language] = [];
	        static::$languages_specifics_globals[$language]['biblio_name'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'name', $biblio_name, $language);
	        static::$languages_specifics_globals[$language]['biblio_adr1'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'adr1', $biblio_adr1, $language);
	        static::$languages_specifics_globals[$language]['biblio_adr2'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'adr2', $biblio_adr2, $language);
	        static::$languages_specifics_globals[$language]['biblio_town'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'town', $biblio_town, $language);
	    }
	    return static::$languages_specifics_globals[$language];
	}
	
	public static function set_language_specifics_globals($language) {
	    
	    $globals = static::get_language_specifics_globals($language);
	    if(!empty($globals)) {
	        foreach ($globals as $global_name=>$global_value) {
	            global ${$global_name};
	            ${$global_name} = $global_value;
	        }
	    }
	}

	public function set_setting_value($name, $value) {
		$this->settings[$name] = $value;
	}
}