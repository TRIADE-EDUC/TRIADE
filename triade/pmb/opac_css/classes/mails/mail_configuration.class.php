<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_configuration.class.php,v 1.11.2.2 2024/10/01 08:24:25 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
use PHPMailer\PHPMailer\PHPMailer;

class mail_configuration {

    // Id configuration
    protected $id = 0;

    // Type de configuration = domain | address
    protected $type = 'domain';

    // Nom = Domain | Adresse mail
	protected $name = '';

    // Protocole = SMTP
    protected $protocol = "SMTP";

    // Hote
    protected $hote = '';

    // Port
    protected $port = 25;

    // Authentification = 0 | 1
    protected $authentification = 0;

    // Utilisateur
    protected $user = '';

    // Mot de passe
	protected $password;

    // Protocole sécurisé = '' | ssl | tls
    protected $secure_protocol = '';

    // Type d'authentification = '' | CRAM-MD5 | LOGIN | PLAIN | XOAUTH2
    protected $authentification_type = '';

    // Parametres liés au type d'authentification
    protected $authentification_type_settings = [];

    // Autoriser la redéfinition de l'hote = 0 | 1
    protected $allowed_hote_override = 0;

    // Autoriser la redefinition de l'authentification = 0 | 1
    protected $allowed_authentification_override = 0;

    // Configuration validée = 0 | 1
    protected $validated = 0;

    // Infos
    protected $informations = [];

	protected $confidential;

	protected $domain;

	protected $domain_name;

	protected $auto_fill;

	protected $uses;

	//Types d'authentification en SMTP
	const SMTP_SECURE_PROTOCOLS = [
			'ssl' => 'SSL/TLS',
			'tls' => 'STARTTLS',
	];

	public function __construct($name='') {
		$this->id = 0;
		$this->name = $name;
		if(strpos($this->name, '@') !== false) {
			$this->type = 'address';
		} else {
			$this->type = 'domain';
		}
		$this->fetch_data();
	}

	protected function init_properties() {
		if(strpos($this->name, '@') !== false) {
			$this->domain_name = substr($this->name, strpos($this->name, '@')+1);
		} else {
			$this->domain_name = $this->name;
		}
	}

	protected function load_properties_from_generic_smtp_servers() {
		if(mails_configuration::has_exists_domain($this->domain_name)) {
			$this->hote = mails_configuration::get_hote_from_domain($this->domain_name);
			$this->port = mails_configuration::get_port_from_domain($this->domain_name);
			$this->secure_protocol = mails_configuration::get_secure_protocol_from_domain($this->domain_name);
		} else {
			$this->hote = '';
		}
		$this->authentification = 1;
		$this->allowed_authentification_override = 1;
		$this->auto_fill = true;
	}

	protected function fetch_data_hote($row) {
		$this->hote = $row->mail_configuration_hote;
		$this->port = $row->mail_configuration_port;
		$this->secure_protocol = $row->mail_configuration_secure_protocol;
		$this->authentification_type = $row->mail_configuration_authentification_type;
	}

	protected function fetch_data_authentification($row) {
		$this->authentification = $row->mail_configuration_authentication;
		$this->user = $row->mail_configuration_user;
		if($row->mail_configuration_password != '') {
			$this->password = convert_uudecode($row->mail_configuration_password);
		}
		$this->authentification_type_settings = encoding_normalize::json_decode($row->mail_configuration_authentification_type_settings, true);
	}

	protected function fetch_data_override($row) {
		$this->allowed_hote_override = $row->mail_configuration_allowed_hote_override;
		$this->allowed_authentification_override = $row->mail_configuration_allowed_authentification_override;
	}

	protected function fetch_domain_data() {
		$query = "SELECT * FROM mails_configuration WHERE name_mail_configuration = '".addslashes($this->get_domain()->get_name())."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if (empty($this->hote)) {
    			if(!$this->id || ($this->id && !$row->mail_configuration_allowed_hote_override)) {
    				$this->fetch_data_hote($row);
    			}
			}
			if (empty($this->authentification)) {
    			if(!$this->id || ($this->id && !$row->mail_configuration_allowed_authentification_override)) {
    				$this->fetch_data_authentification($row);
    			}
            }
		}
	}

	protected function fetch_data() {
		$this->init_properties();
		// Désactivé en OPAC
		/*
		if($this->is_confidential()) {
			pmb_error::get_instance(static::class)->add_message("permission_denied", "mail_configuration_action_edit_locked");
		}
		*/
		if($this->name) {
			$query = "SELECT * FROM mails_configuration WHERE name_mail_configuration = '".addslashes($this->name)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->id = $row->id_mail_configuration;
				$this->fetch_data_hote($row);
				$this->fetch_data_authentification($row);
				$this->fetch_data_override($row);
				$this->validated = $row->mail_configuration_validated;
				if(!empty($row->mail_configuration_informations)) {
					$this->informations = encoding_normalize::json_decode($row->mail_configuration_informations, true);
				}
			}
			if($this->type == 'address' && $this->get_domain()->get_name()) {
				$this->fetch_domain_data();
			}
		}
	}

	protected function get_protocol_selector() {
		return "
			<select id='mail_configuration_protocol' name='mail_configuration_protocol' ".($this->is_hote_readonly() ? "readonly='readonly' disabled='disabled'" : "").">
					<option value='SMTP' ".($this->protocol == 'SMTP' ? "selected='selected'" : "").">SMTP</option>
			</select>";
	}

	protected function get_secure_protocol_selector() {
		return "
			<select id='mail_configuration_secure_protocol' name='mail_configuration_secure_protocol' ".($this->is_hote_readonly() ? "readonly='readonly' disabled='disabled'" : "").">
					<option value='' ".(empty($this->secure_protocol) ? "selected='selected'" : "")."></option>
					<option value='ssl' ".($this->secure_protocol == 'ssl' ? "selected='selected'" : "").">".static::SMTP_SECURE_PROTOCOLS['ssl']."</option>
					<option value='tls' ".($this->secure_protocol == 'tls' ? "selected='selected'" : "").">".static::SMTP_SECURE_PROTOCOLS['tls']."</option>
			</select>";
	}

	protected function get_authentification_type_selector() {
	    global $msg, $charset;

	    $selector = "<script>
			function switchAuthTypeSetting(event) {
				const select = event.target;
				const node = document.getElementById('auth_type_settings');
				if (node) {
					if (select.selectedOptions[0].value == '') {
						node.style.display = 'none';
						return true;
					} else {
						node.style.removeProperty('display');
					}
				}

				for (let i = 0; i < select.options.length; i++) {
					const option = select.options.item(i);
					if (!option.value) {
						continue;
					}

					const node = document.getElementById(option.value.toLocaleLowerCase());
					if (!node) {
						continue;
					}

					if (i == select.selectedIndex) {
						node.style.removeProperty('display');
					} else {
						node.style.display = 'none';
					}
				}
			}
            function defaultAuthTypeSetting(type, selectedPlainIndex=0) {
                let has_authentification = true;
                if(type == 'domain') {
                    let = authentification_1 = document.getElementById('mail_configuration_authentification_1');
                    if(authentification_1 && authentification_1.checked) {
                        has_authentification = true;
                    } else {
                        has_authentification = false;
                    }
                }
                let authentification_type = document.getElementById('mail_configuration_authentification_type');
                if(authentification_type) {
                    if(has_authentification) {
                        if(!authentification_type.options.selectedIndex) {
                            authentification_type.options.selectedIndex = selectedPlainIndex;
                            document.getElementById('auth_type_settings').style.removeProperty('display');
                        }
                    } else {
                        authentification_type.options.selectedIndex = 0;
                        document.getElementById('auth_type_settings').style.display = 'none';
                    }
                }
            }
 		</script>";
		$selector .= "<select id='mail_configuration_authentification_type' name='mail_configuration_authentification_type' onchange='switchAuthTypeSetting(event)'
			".($this->is_authentification_readonly() ? "readonly='readonly' disabled='disabled'" : "").">
						<option value='' ".(empty($this->authentification_type) ? "selected='selected'" : "").">
							". htmlentities($msg['mail_configuration_authentification_type_none'], ENT_QUOTES, $charset) ."
						</option>";
	    foreach (mail::SMTP_AUTH_TYPES as $auth_type) {
	        $selector .= "<option value='".$auth_type."' ".($this->authentification_type == $auth_type ? "selected='selected'" : "").">".$auth_type."</option>";
	    }
	    $selector .= "</select>";

		if ($this->authentification) {
		    $selector .= "<script>addLoadEvent(function() {defaultAuthTypeSetting('".$this->type."', '".(array_search('PLAIN', mail::SMTP_AUTH_TYPES)+1)."');});</script>";
		}
	    return $selector;
	}

	/**
	 * Permet de mettre les valeurs dans les champs pour chaque paramètre d'un type d'authentification
	 *
	 * @param string $content_form
	 * @return string
	 */
	public function get_authentification_type_settings_content_form($content_form) {
	    global $charset;

	    $pattern = [];

	    // On parcoure les types d'authentification pour remplacer les pattern pour mettre les valeur enregistre en BDD
	    foreach (mail::SMTP_AUTH_TYPES as $auth_type) {
	        $authType = strtolower($auth_type);

	        $pattern["!!{$authType}_display!!"] = ($this->authentification_type == $auth_type ? "" : "display:none;");

	        foreach (mail::SMTP_AUTH_TYPES_FIELDS[$authType] ?? [] as $field) {
	            $fieldname = "{$authType}_{$field}"; // Exemple : xoauth2_tenant_id
	            $value = "";

				if (
					($this->type == "domain" || $this->get_domain()->is_allowed_authentification_override()) &&
					!empty($this->authentification_type_settings[$fieldname])
				) {
	                $value = htmlentities($this->authentification_type_settings[$fieldname], ENT_QUOTES, $charset);
	            }
	            $pattern["!!{$fieldname}!!"] = $value;
	        }
	    }

	    if (!empty($this->authentification_type_settings["encoding_quoted_printable"])) {
	        $pattern["!!encoding_quoted_printable_check!!"] = "checked='checked'";
	    } else {
	        $pattern["!!encoding_quoted_printable_check!!"] = "";
	    }

	    if (empty($this->authentification_type)) {
	        $pattern["!!none_auth_type_display!!"] = "display:none;";
	    } else {
	        $pattern["!!none_auth_type_display!!"] = "";
	    }

	    // Cas spécifique pour le provider, on a un select au lieu d'un input
	    $xoauth2ProviderOptions = "";
	    foreach (mail::XOAUTH2_PROVIDER_LIST as $provider) {
	        $optionValue = htmlentities($provider, ENT_QUOTES, $charset);
	        $optionLabel = ucfirst($optionValue);
	        $selected = "";

	        if (
	            !empty($this->authentification_type_settings["xoauth2_provider"]) &&
	            $optionValue == $this->authentification_type_settings["xoauth2_provider"]
	            ) {
	                $selected = "selected='selected'";
	            }

	            $xoauth2ProviderOptions .= "<option value='{$optionValue}' {$selected}>{$optionLabel}</option>";
	    }
	    $pattern["!!xoauth2_provider_options!!"] = $xoauth2ProviderOptions;

	    return str_replace(
	        array_keys($pattern),
	        $pattern,
	        $content_form
        );
	}

	protected function build_server_content_form($interface_content_form, $readonly=false) {
	    if($readonly) {
	        if($this->hote) {
	            $interface_content_form->add_element('mail_configuration_hote', 'mail_configuration_hote')
	            ->add_html_node($this->hote);
	            $interface_content_form->add_element('mail_configuration_port', 'mail_configuration_port')
	            ->add_html_node($this->port);
	        }
	        if(!empty($this->secure_protocol)) {
	            $interface_content_form->add_element('mail_configuration_secure_protocol', 'mail_configuration_secure_protocol')
	            ->add_html_node(static::SMTP_SECURE_PROTOCOLS[$this->secure_protocol]);
	        }
	    } else {
	        $element_hote = $interface_content_form->add_element('mail_configuration_hote', 'mail_configuration_hote');
	        if(!empty($this->auto_fill)) {
	            $element_hote->set_label($element_hote->get_label()." *");
	        }
	        $element_hote->add_input_node('text', $this->hote);
	        $element_port = $interface_content_form->add_element('mail_configuration_port', 'mail_configuration_port');
	        if(!empty($this->auto_fill)) {
	            $element_port->set_label($element_port->get_label()." *");
	        }
	        $element_port->add_input_node('integer', $this->port);

	        $element_secure_protocol = $interface_content_form->add_element('mail_configuration_secure_protocol', 'mail_configuration_secure_protocol');
	        if(!empty($this->auto_fill)) {
	            $element_secure_protocol->set_label($element_secure_protocol->get_label()." *");
	        }
	        $element_secure_protocol->add_html_node($this->get_secure_protocol_selector());
	    }
	    return $interface_content_form;
	}
	
	protected function build_encoding_content_form($interface_content_form) {
	    global $msg;
	    
	    $html_node = " 
        <input id='encoding_quoted_printable' type='checkbox' class='switch' value='1'
        		name='mail_configuration_authentification_type_settings[encoding_quoted_printable]' !!encoding_quoted_printable_check!!>
        	<label for='encoding_quoted_printable'>&nbsp;</label>
        ";
	    $element = $interface_content_form->add_element('encoding_quoted_printable', 'encoding_quoted_printable');
	    $element->add_html_node($html_node);
	    return $interface_content_form;
	}
	
	protected function get_domain_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    
	    $element = $interface_content_form->add_element('name', 'mail_configuration_domain');
	    $element->add_html_node($this->name);
	    $element->add_input_node('hidden', $this->name);
	    
	    $interface_content_form = $this->build_server_content_form($interface_content_form);
	    
	    $interface_content_form = $this->build_encoding_content_form($interface_content_form);
	    
	    $element = $interface_content_form->add_element('mail_configuration_authentification', 'mail_configuration_authentification');
	    $element->add_input_node('radio', '0')
	    ->set_checked((!$this->authentification ? true : false))
	    ->set_attributes(array('onchange' => "defaultAuthTypeSetting('".$this->type."', '".(array_search('PLAIN', mail::SMTP_AUTH_TYPES)+1)."');"))
	    ->set_label_code('mail_configuration_authentification_none');
	    $element->add_input_node('radio', '1')
	    ->set_checked(($this->authentification && !$this->allowed_authentification_override ? true : false))
	    ->set_attributes(array('onchange' => "defaultAuthTypeSetting('".$this->type."', '".(array_search('PLAIN', mail::SMTP_AUTH_TYPES)+1)."');"))
	    ->set_label_code('mail_configuration_authentification_domain_reserved');
	    $element->add_input_node('radio', '2')
	    ->set_checked(($this->authentification && $this->allowed_authentification_override ? true : false))
	    ->set_attributes(array('onchange' => "defaultAuthTypeSetting('".$this->type."', '".(array_search('PLAIN', mail::SMTP_AUTH_TYPES)+1)."');"))
	    ->set_label_code('mail_configuration_authentification_mail_reserved');
	    
	    $interface_content_form->add_element('mail_configuration_authentification_type', 'mail_configuration_authentification_type')
	    ->add_html_node($this->get_authentification_type_selector());
	    
	    return $interface_content_form->get_display();
	}
	
	protected function get_address_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $element = $interface_content_form->add_element('name', 'mail_configuration_address');
	    $element->add_html_node($this->name);
	    $element->add_input_node('hidden', $this->name);
	    
	    $interface_content_form = $this->build_server_content_form($interface_content_form, $this->is_hote_readonly());
	    
	    $interface_content_form = $this->build_encoding_content_form($interface_content_form);
	    
	    if ($this->domain->is_allowed_authentification_override()) {
	        $interface_content_form->add_element('mail_configuration_authentification_type', 'mail_configuration_authentification_type')
	        ->add_html_node($this->get_authentification_type_selector());
	    }
	    return $interface_content_form->get_display();
	}
	
	public function get_content_form() {
	    if($this->type == 'domain') {
	        return $this->get_domain_content_form();
	    } else {
	        return $this->get_address_content_form();
	    }
	}
	
	public function get_form() {
		global $msg, $charset, $current_module;
		global $mail_configuration_authentification_type_settings_content_form;
		global $mail_configuration_is_valid;

		$content_form = '';
		if(!$this->id && $this->type == 'domain') {
			$this->load_properties_from_generic_smtp_servers();
		}
		$content_form = $this->get_content_form();
		if($this->type == 'domain' || ($this->type == 'address' && $this->domain->is_allowed_authentification_override())) {
				    $content_form .= $mail_configuration_authentification_type_settings_content_form;
				}
		if ( $this->is_validated() ) {
		    $content_form.= str_replace('<!-- value -->', $mail_configuration_is_valid['yes'], $mail_configuration_is_valid['form']);
		} else {
		    $content_form.= str_replace('<!-- value -->', $mail_configuration_is_valid['no'], $mail_configuration_is_valid['form']);
		}

		if($current_module == 'account') {
			$interface_form = new interface_account_form('mail_configuration_form');
		} else {
			$interface_form = new interface_form('mail_configuration_form');
		}
		$interface_form->set_label($msg['mail_configuration_edit']);
		
		$content_form = $this->get_authentification_type_settings_content_form($content_form);

		$content_form = str_replace('!!type!!', htmlentities($this->type, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!user!!', htmlentities($this->user, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!password!!', (!$this->is_authentification_readonly() ? htmlentities($this->password, ENT_QUOTES, $charset) : ''), $content_form);
		$content_form = str_replace('!!password_placeholder!!', ($this->password ? htmlentities($msg['empr_pwd_opac_affected'], ENT_QUOTES, $charset) : ''), $content_form);
		$content_form = str_replace('!!allowed_hote_override!!', ($this->get_domain()->is_allowed_hote_override() ? "checked='checked'" : ""), $content_form);

		$interface_form->set_object_id(($this->is_in_database() ? 1 : 0))
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($content_form)
		->set_table_name('mails_configuration');
		if($this->type == 'domain') {
			$interface_form->set_field_focus('mail_configuration_hote');
		} else {
			$interface_form->set_field_focus('mail_configuration_user');
		}
		return $interface_form->get_display();
	}

	public function set_properties_from_form() {
		global $mail_configuration_hote, $mail_configuration_port;
		global $mail_configuration_authentification, $mail_configuration_user, $mail_configuration_password;
		global $mail_configuration_secure_protocol, $mail_configuration_authentification_type;
		global $mail_configuration_allowed_hote_override;

		switch ($this->type) {
			case 'domain':
				$mail_configuration_authentification = intval($mail_configuration_authentification);
				$this->hote = stripslashes($mail_configuration_hote);
				$this->port = intval($mail_configuration_port);
				if($mail_configuration_authentification == 1) {
					$this->user = stripslashes($mail_configuration_user);
					if($mail_configuration_password != '') {
						$this->password = stripslashes($mail_configuration_password);
					}
				}
				if($mail_configuration_authentification) {
					$this->authentification = 1;
				} else {
					$this->authentification = 0;
				}
				$this->secure_protocol = stripslashes($mail_configuration_secure_protocol);
				$this->authentification_type = stripslashes($mail_configuration_authentification_type);
				$this->authentification_type_settings = $this->fetch_auth_type_settings_from_form();
 				$this->allowed_hote_override = intval($mail_configuration_allowed_hote_override);
 				if($mail_configuration_authentification == 2) {
 					$this->allowed_authentification_override = 1;
 				} else {
 					$this->allowed_authentification_override = 0;
 				}
 				$this->validated = 0;
 				$this->informations = array();
				break;
			case 'address':

			    $domain = $this->get_domain();

				if($domain->is_allowed_hote_override()) {
					$this->hote = stripslashes($mail_configuration_hote);
					$this->port = intval($mail_configuration_port);
					$this->secure_protocol = stripslashes($mail_configuration_secure_protocol);
				} else {
					$this->hote = '';
					$this->port = '';
					$this->secure_protocol = '';
				}
				if($domain->is_allowed_authentification_override()) {
					$this->user = stripslashes($mail_configuration_user);
					if($mail_configuration_password != '') {
						$this->password = stripslashes($mail_configuration_password);
					}
					$this->authentification = $domain->get_authentification();
					$this->authentification_type = stripslashes($mail_configuration_authentification_type);
					$this->authentification_type_settings = $this->fetch_auth_type_settings_from_form();
				} else {
					$this->authentification = 0;
					$this->user = '';
					$this->password = '';
					$this->authentification_type = '';
					$this->authentification_type_settings = array();
				}
 				$this->allowed_hote_override = 1;
 				$this->allowed_authentification_override = 1;
 				$this->validated = 0;
 				$this->informations = array();
				break;
		}
	}

	public function is_in_database() {
		$query = 'SELECT * FROM mails_configuration
			WHERE name_mail_configuration = "'.addslashes($this->name).'"';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}

	public function check_configuration() {
		global $msg;

		$this->validated = 0;
		if($this->hote && (!$this->authentification || ($this->authentification && $this->user && $this->password))) {
			$mail = new mail();
			$param = array(
					'method' => strtolower($this->protocol),
					'host' => $this->hote.(!empty($this->port) ? ":".$this->port : ''),
					'auth' => $this->authentification,
					'user' => $this->user,
					'pass' => $this->password,
					'secure' => $this->secure_protocol,
					'auth_type' => $this->authentification_type
			);
			if(!empty($this->authentification_type_settings) && is_array($this->authentification_type_settings)) {
				$param = array_merge($param, $this->authentification_type_settings);
			}
			$mailer = $mail->get_instance_PHPMailer($param);
			$mailer->Timeout = 3;
			$mailer->SMTPDebug = 1;
			ob_start();
			if ($mailer->smtpConnect($mailer->SMTPOptions)) {
				$this->validated = 1;
			} else {
				$smtpConnect_error = str_replace(array('<br>', '<br />'), '. ', clean_string(html_entity_decode(ob_get_clean())));
				$this->informations['smtpConnect_error'] = $smtpConnect_error;
			}
		}
		$query = "update mails_configuration set mail_configuration_validated = ".$this->validated.", mail_configuration_informations = '".encoding_normalize::json_encode($this->informations)."' where  name_mail_configuration = '".addslashes($this->name)."'";
		pmb_mysql_query($query);
	}

	public function save() {
		if($this->is_in_database()){
			$query = "update mails_configuration set ";
			$clause = " where name_mail_configuration = '".addslashes($this->name)."'";
		}else{
			$query = "insert into mails_configuration set
				mail_configuration_type = '".addslashes($this->type)."',
				name_mail_configuration = '".addslashes($this->name)."',
			";
			$clause= "";
		}
		// $this->validated = 1;
		$query.= "
			mail_configuration_protocol = '".addslashes($this->protocol)."',
			mail_configuration_hote = '".addslashes($this->hote)."',
			mail_configuration_port = '".$this->port."',
			mail_configuration_authentication = '".$this->authentification."',
			mail_configuration_user = '".addslashes($this->user)."',
			mail_configuration_password = '".addslashes(convert_uuencode($this->password))."',
			mail_configuration_secure_protocol = '".addslashes($this->secure_protocol)."',
			mail_configuration_authentification_type = '".addslashes($this->authentification_type)."',
			mail_configuration_authentification_type_settings = '".encoding_normalize::json_encode($this->authentification_type_settings)."',
			mail_configuration_allowed_hote_override = '".intval($this->allowed_hote_override)."',
			mail_configuration_allowed_authentification_override = '".intval($this->allowed_authentification_override)."',
			mail_configuration_validated = '".intval($this->validated)."',
			mail_configuration_informations = '".encoding_normalize::json_encode($this->informations)."'
			".$clause;

		$result = pmb_mysql_query($query);
		if($result){
			if($this->type == 'address' && $this->get_domain()->get_name()) {
				$this->fetch_domain_data();
				$this->check_configuration();
			} elseif($this->type == 'domain' && $this->name) {
				$this->upgrade_domain_childs();
				if(!$this->is_allowed_authentification_override()) {
					$this->check_configuration();
				}
			}
			return true;
		}
		return false;
	}

	public function upgrade_domain_childs() {
		$mails_configuration = list_mails_configuration_ui::get_instance()->get_objects();
		if(!empty($mails_configuration)) {
			foreach ($mails_configuration as $mail_configuration) {
				if($mail_configuration->get_id() && $mail_configuration->get_domain_name() == $this->name) {
					if(empty($this->allowed_hote_override)) {
						$mail_configuration->set_hote('')
							->set_port('')
							->set_secure_protocol('');
					} else {
						$mail_configuration->set_allowed_hote_override(1);
					}
					if(empty($this->allowed_authentification_override)) {
						$mail_configuration->set_authentification(0)
							->set_user('')
							->set_password('')
							->set_authentification_type('')
							->set_authentification_type_settings(array());
					} else {
						$mail_configuration->set_authentification(1)
							->set_allowed_authentification_override(1);
					}
					$mail_configuration->save();
				}
			}
		}
	}

	public static function delete($name) {
		$query = "delete from mails_configuration where name_mail_configuration = '".addslashes($name)."'";
		pmb_mysql_query($query);
		//Initialisation des mails du domaine
		$mail_configuration = new mail_configuration($name);
		$mail_configuration->upgrade_domain_childs();
		return true;
	}

	public function initialization() {
		$this->init_properties();
		if($this->type == 'domain') {
			$this->load_properties_from_generic_smtp_servers();
		}
		$this->save();
		if($this->type == 'domain') {
			//Initialisation des mails du domaine
			$this->upgrade_domain_childs();
		}
		return true;
	}

	public function get_id() {
		return $this->name;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_domain() {
		if(!isset($this->domain)) {
			$this->domain = new mail_configuration($this->domain_name);
		}
		return $this->domain;
	}

	public function get_domain_name() {
		return $this->domain_name;
	}

	public function get_protocol() {
		return $this->protocol;
	}

	public function get_hote() {
		return $this->hote;
	}

	public function get_port() {
		return $this->port;
	}

	public function get_authentification() {
		return $this->authentification;
	}

	public function get_user() {
		return $this->user;
	}

	public function get_password() {
		return $this->password;
	}

	public function get_secure_protocol() {
		return $this->secure_protocol;
	}

	public function get_authentification_type() {
		return $this->authentification_type;
	}

	public function get_authentification_type_settings() {
		return $this->authentification_type_settings;
	}

	public function get_information($name) {
		if(!empty($this->informations[$name])) {
			return $this->informations[$name];
		}
		return '';
	}

	public function get_informations() {
		return $this->informations;
	}

	public function get_uses() {
		global $pmb_mail_adresse_from, $opac_mail_adresse_from, $opac_biblio_email;

		if(!isset($this->uses)) {
			$this->uses = array();
			$this->uses['users'] = users::get_users_from_mail($this->name);
			$locations = array();
			$query = "SELECT idlocation FROM docs_location WHERE email = '".addslashes($this->name)."'";
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				$locations[$row->idlocation] = new docs_location($row->idlocation);
			}
			$this->uses['locations'] = $locations;

			$this->uses['parameters'] = array();
			if($pmb_mail_adresse_from) {
				$tmp_array_email = explode(';', $pmb_mail_adresse_from);
				if($tmp_array_email[0] == $this->name) {
					$this->uses['parameters'][] = 'pmb_mail_adresse_from';
				}
			}
			if($opac_mail_adresse_from) {
				$tmp_array_email = explode(';', $opac_mail_adresse_from);
				if($tmp_array_email[0] == $this->name) {
					$this->uses['parameters'][] = 'opac_mail_adresse_from';
				}
			}
			if($opac_biblio_email == $this->name) {
				$this->uses['parameters'][] = 'opac_biblio_email';
			}
			$coords = array();
			$query = "SELECT id_entite, raison_sociale FROM coordonnees JOIN entites ON entites.id_entite = coordonnees.num_entite WHERE type_entite = 1 and email = '".addslashes($this->name)."'";
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				$coords[$row->id_entite] = $row->raison_sociale;
			}
			$this->uses['coords'] = $coords;
		}
		return $this->uses;
	}

	public function is_used() {
	    $used = true;
	    $uses = $this->get_uses();
	    if(empty($uses['users']) && empty($uses['locations']) && empty($uses['parameters']) && empty($uses['coords'])) {
	        $used = false;
	    }
	    return $used;
	}

	public function set_type($type) {
		$this->type = $type;
		return $this;
	}

	public function set_name($name) {
		$this->name = $name;
		if(empty($this->type)) {
			if(strpos($this->name, '@') !== false) {
				$this->type = 'address';
			} else {
				$this->type = 'domain';
			}
		}
		return $this;
	}

	public function set_protocol($protocol) {
		$this->protocol = $protocol;
		return $this;
	}

	public function set_hote($hote) {
		$this->hote = $hote;
		return $this;
	}

	public function set_port($port) {
		$this->port = $port;
		return $this;
	}

	public function set_authentification($authentification) {
		$this->authentification = $authentification;
		return $this;
	}

	public function set_user($user) {
		$this->user = $user;
		return $this;
	}

	public function set_password($password) {
		$this->password = $password;
		return $this;
	}

	public function set_secure_protocol($secure_protocol) {
		$this->secure_protocol = $secure_protocol;
		return $this;
	}

	public function set_authentification_type($authentification_type) {
		$this->authentification_type = $authentification_type;
		return $this;
	}

	public function set_authentification_type_settings($authentification_type_settings) {
		$this->authentification_type_settings = $authentification_type_settings;
		return $this;
	}

	public function update_xoauth2_refresh_token( string $token, string $validity)
	{
	    $this->authentification_type_settings['xoauth2_refresh_token'] = $token;
	    $this->authentification_type_settings['xoauth2_refresh_token_validity'] = $validity;
	}

	public function set_informations($informations) {
		$this->informations = $informations;
		return $this;
	}

	public function is_allowed_hote_override() {
		return $this->allowed_hote_override;
	}

	public function is_allowed_authentification_override() {
		return $this->allowed_authentification_override;
	}

	public function is_validated() {
		return $this->validated;
	}

	public function set_allowed_hote_override($allowed_hote_override) {
		$this->allowed_hote_override = intval($allowed_hote_override);
		return $this;
	}

	public function set_allowed_authentification_override($allowed_authentification_override) {
		$this->allowed_authentification_override = intval($allowed_authentification_override);
		return $this;
	}

	public function set_validated($validated) {
		$this->validated = intval($validated);
		return $this;
	}

	public function set_confidential($confidential) {
		$this->confidential = intval($confidential);
		return $this;
	}

	public function is_confidential() {
		global $pmb_mail_adresse_from, $opac_mail_adresse_from, $opac_biblio_email;
		global $PMBuseremail;

		if(!isset($this->confidential)) {
			$this->confidential = true;
			switch ($this->type) {
				case 'domain':
					if($this->name) {
						$this->confidential = static::domain_is_confidential($this->name);
					}
					break;
				case 'address':
				default:
					if($pmb_mail_adresse_from == $this->name || $opac_mail_adresse_from == $this->name || $opac_biblio_email == $this->name) {
						$this->confidential = false;
					}

					if($PMBuseremail == $this->name) {
						$this->confidential = false;
					}
					$query = "SELECT count(*) as nb FROM docs_location WHERE email = '".$this->name."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_result($result, 0, 'nb')) {
						$this->confidential = false;
					}
					$query = "SELECT count(*) as nb FROM coordonnees JOIN entites ON entites.id_entite = coordonnees.num_entite WHERE type_entite = 1 AND email = '".$this->name."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_result($result, 0, 'nb')) {
						$this->confidential = false;
					}
					break;
			}
		}
		return $this->confidential;
	}

	public function is_hote_readonly() {
		if($this->type == 'address' && empty($this->get_domain()->is_allowed_hote_override())) {
			return true;
		}
		return false;
	}

	public function is_authentification_readonly() {
		if($this->type == 'address' && empty($this->get_domain()->is_allowed_authentification_override())) {
			return true;
		}
		return false;
	}

	protected static function table_exists() {
		$query = "SHOW TABLES LIKE 'mails_configuration'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}

	/**
	 * Recupere la configuration SMTP en fonction de l'adresse mail expeditrice
	 *
	 * @param string $address
	 * @return array
	 */
    public static function get_address_configuration($address = '')
    {
        $ret = [];
		if(!static::table_exists()) {
            return $ret;
		}
		$mail_configuration = new mail_configuration($address);
		if($mail_configuration->is_in_database() && $mail_configuration->get_hote() && $mail_configuration->is_validated()) {
            $ret =  [
                'id'        => $mail_configuration->get_id(),
                'method'    => strtolower($mail_configuration->get_protocol()),
                'host'      => $mail_configuration->get_hote() . (! empty($mail_configuration->get_port()) ? ":" . $mail_configuration->get_port() : ''),
                'auth'      => $mail_configuration->get_authentification(),
                'user'      => $mail_configuration->get_user(),
                'pass'      => $mail_configuration->get_password(),
                'secure'    => $mail_configuration->get_secure_protocol(),
                'auth_type' => $mail_configuration->get_authentification_type(),
                'auth_settings' => $mail_configuration->get_authentification_type_settings(),
            ];

		} else {
			$mail_configuration = $mail_configuration->get_domain();
			if($mail_configuration->is_in_database() && $mail_configuration->get_hote() && $mail_configuration->is_validated()) {
                $ret = [
                    'id' => $mail_configuration->get_id(),
                    'method' => strtolower($mail_configuration->get_protocol()),
                    'host' => $mail_configuration->get_hote() . (! empty($mail_configuration->get_port()) ? ":" . $mail_configuration->get_port() : ''),
                    'auth' => $mail_configuration->get_authentification(),
                    'user' => $mail_configuration->get_user(),
                    'pass' => $mail_configuration->get_password(),
                    'secure' => $mail_configuration->get_secure_protocol(),
                    'auth_type' => $mail_configuration->get_authentification_type(),
                    'auth_settings' => $mail_configuration->get_authentification_type_settings(),
                ];
			}
		}
        //Gorreterie pour recuperer les parametres non prevus a l'origine
        if ( !empty($ret) && !empty($ret['auth_settings']) ) {
            $ret['encoding'] = !empty($ret['auth_settings']['encoding_quoted_printable']) ? PHPMailer::ENCODING_QUOTED_PRINTABLE : '';

            $auth_type = $ret['auth_type'];
            foreach($ret['auth_settings'] as $k=>$v) {
                if(stripos($k, $auth_type) === 0 ) {
                    $ret[$k] = $v;
                }
            }
            unset($ret['auth_settings']);
        }
        return $ret;
	}

	public static function domain_is_confidential($domain) {
		global $pmb_mail_adresse_from, $opac_mail_adresse_from, $opac_biblio_email;
		global $PMBuseremail;

		$confidential = true;
		if (defined('SESSrights') && (SESSrights & ADMINISTRATION_AUTH)) {
			$confidential = false;
		}
		if((!empty($pmb_mail_adresse_from) && strpos($pmb_mail_adresse_from, $domain) !== false)
				|| (!empty($opac_mail_adresse_from) && strpos($opac_mail_adresse_from, $domain) !== false)
				|| (!empty($opac_biblio_email) && strpos($opac_biblio_email, $domain) !== false)) {
			$confidential = false;
		}
		if(!empty($PMBuseremail) && strpos($PMBuseremail, $domain) !== false) {
			$confidential = false;
		}
		$query = "SELECT count(*) as nb FROM docs_location WHERE email LIKE '%".addslashes($domain)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_result($result, 0, 'nb')) {
			$confidential = false;
		}
		$query = "SELECT count(*) as nb FROM coordonnees JOIN entites ON entites.id_entite = coordonnees.num_entite WHERE type_entite = 1 AND email LIKE '%".addslashes($domain)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_result($result, 0, 'nb')) {
			$confidential = false;
		}
		return $confidential;
	}

	protected function fetch_auth_type_settings_from_form() {
	    global $mail_configuration_authentification_type_settings;

	    $mail_configuration_authentification_type_settings = stripslashes_array($mail_configuration_authentification_type_settings);
	    $settings = [
	        "encoding_quoted_printable" => intval($mail_configuration_authentification_type_settings['encoding_quoted_printable'] ?? 0)
	    ];

	    $authType = strtolower($this->authentification_type);
	    foreach (mail::SMTP_AUTH_TYPES_FIELDS[$authType] ?? [] as $field) {
	        $settings["{$authType}_{$field}"] = $mail_configuration_authentification_type_settings["{$authType}_{$field}"] ?? '';
	    }

	    return $settings;
	}
	
	public static function get_source_from_mail($mail) {
	    $mailConfiguation = new mail_configuration($mail);
	    $uses = $mailConfiguation->get_uses();
	    
	    if(!empty($uses['users'])) {
	        return [
	            "id" => array_key_first($uses['users']),
	            "type" => "user"
	        ];
	    }
	    if(!empty($uses['locations'])) {
	        return [
	            "id" => array_key_first($uses['locations']),
	            "type" => "docs_location"
	        ];
	    }
	    if(!empty($uses['parameters'])) {
	        return [
	            "id" => 0,
	            "type" => "parameter"
	        ];
	    }
	    if(!empty($uses['coords'])) {
	        return [
	            "id" => array_key_first($uses['coords']),
	            "type" => "coord"
	        ];
	    }
	    
	    return [
	        "id" => 0,
	        "type" => "parameter"
	    ];
	}
}

