<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authentication.class.php,v 1.9 2023/08/29 09:04:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Helper\MySQL;

class authentication {
	
	protected $id;
	
	public function __construct($id=0) {
		$this->id = intval($id);
	}
	
	public function get_content_form_pwd() {
		$interface_content_form = new interface_content_form(static::class);
		
		$interface_content_form->add_element('form_pwd', '87')
		->add_input_node('password')
		->set_class('saisie-20em');
		$interface_content_form->add_element('form_pwd2', '88')
		->add_input_node('password')
		->set_class('saisie-20em');
		return $interface_content_form->get_display();	
	}

	public function get_content_form_mfa() {
		global $msg, $action, $PMBuserid, $opac_biblio_name;
		$interface_content_form = new interface_content_form(static::class);

		switch($action) {
			case 'initialization':
				$mfa_root = new mfa_root();
				$secret_code = $mfa_root->generate_secret_code(7);

				$mfa_service = (new \Pmb\MFA\Controller\MFAServicesController())->getData("GESTION");
				$mfa_otp = (new \Pmb\MFA\Controller\MFAOtpController())->getData("GESTION");

				$interface_content_form->add_element('mfa_secret_code', 'mfa_secret_code', 'flat')
				->add_html_node($secret_code);

				$interface_content_form->add_element('mfa_secret_code_hidden')
				->add_input_node('hidden', base32_upper_encode($secret_code));

				$interface_content_form->add_element('mfa_secret_code_base32', 'mfa_secret_code_base32', 'flat')
				->add_html_node(base32_upper_encode($secret_code));
				
				$mfa_totp = new mfa_totp();
				$mfa_totp->set_hash_method("sha1");
				$mfa_totp->set_length_code(10);

				$interface_content_form->add_element('mfa_reset_secret_code', 'mfa_reset_secret_code', 'flat')
				->add_html_node($mfa_totp->get_totp($secret_code, 1000) . "<i> (" . $msg["mfa_reset_secret_code_info"] . "</i>)");
				
				$src = $mfa_root->get_qr_code_url('totp', 'gestion', base32_upper_encode($secret_code),
					[
						'algorithm' => $mfa_otp->hashMethod,
						'digits' => $mfa_otp->lengthCode, 
						'period' => $mfa_otp->lifetime,
						'issuer' => $opac_biblio_name
					]);
				
				$element = $interface_content_form->add_element('mfa_app_code', '', 'flat');
				$element->add_html_node('<div class="colonne3"><img src="' . $src . '"></div>');
				if(!empty($mfa_service->suggestMessage)) {
					$element->add_html_node('<div class="colonne3">' . nl2br($mfa_service->getTranslatedSuggestMessage()) . '</div>');
				}

				$interface_content_form->add_element('mfa_confirm_code', 'mfa_confirm_code', 'flat')
				->add_input_node('text')
				->set_class('saisie-20em');

				$interface_content_form->add_element('mfa_notify')
				->add_html_node('<span id="mfa_notify"></span>');

				$mfa_send = $interface_content_form->add_element('mfa_send', '', 'flat');

				if(!empty(user::get_param($PMBuserid, "user_email"))) {
					$mfa_send->add_input_node('button', $msg['mfa_send_mail'])
					->set_id('mfa_send_mail')
					->set_click("mfa_send_ajax('send_mail', '" . $secret_code . "')");
				}

				// $mfa_send->add_input_node('button', $msg['mfa_send_sms'])
				// ->set_id('mfa_send_sms');

				break;
			case 'initialized':
				$interface_content_form->add_element('mfa_secret_code', 'mfa_secret_code', 'flat')
				->add_html_node($msg['mfa_already_init']);

				$interface_content_form->add_element('mfa_reset')
				->add_input_node('button')
				->set_value($msg['mfa_reset'])
				->set_class('bouton')
				->set_click("if(confirm('". $msg['mfa_reset_confirm'] ."')){document.location='./account.php?categ=authentication&sub=mfa&action=reset_mfa'}");

				$favorite = user::get_param($PMBuserid, "mfa_favorite");
				$options = array(
					'app' => $msg['mfa_favorite_app'],
					'mail' => $msg['mfa_favorite_mail'],
					//'sms' => $msg['mfa_favorite_sms']
				);

				$interface_content_form->add_element('mfa_favorite_select', 'mfa_favorite', 'flat')
				->add_select_node($options, $favorite);

				break;
			default:
				$interface_content_form->add_element('mfa_secret_code', 'mfa_secret_code', 'flat')
				->add_html_node($msg['mfa_empty_code']);
				break;
		}


		return $interface_content_form->get_display();	
	}
	
	public function get_form() {
		global $PMBuserid, $security_mfa_active;

		$form = "";
		
		if((SESSrights & PREF_AUTH) || ($PMBuserid == $this->id)) {
			$form .= $this->get_pwd_form();

			$mfa_service = (new \Pmb\MFA\Controller\MFAServicesController())->getData("GESTION");
			if($security_mfa_active && $mfa_service->application) {
				$form .= $this->get_mfa_form();
			}
		}

		return $form;
	}

	public function get_pwd_form() {
		global $msg, $PMBuserid, $base_path;

		$interface_form = new interface_account_form('authentication_form');
		$interface_form->set_label($msg[86]." ".user::get_param($PMBuserid, 'username'));
		$interface_form->set_url_base($base_path . '/account.php?categ=authentication&sub=pwd&id=' . $PMBuserid);
		$interface_form->set_content_form($this->get_content_form_pwd())
		->set_table_name('users');

		return $interface_form->get_display();
	}

	public function get_mfa_form() {
		global $msg, $PMBuserid, $base_path, $action;

		$user_secret = user::get_param($PMBuserid, 'mfa_secret_code');
		if(!empty($user_secret)) {
			$action = 'initialized';
		} else {
			if($action == 'initialized') {
				$action = 'edit';
			}
		}

		$interface_form = new interface_account_form('mfa_form');
		$interface_form->set_label($msg['mfa_title']);
		$interface_form->set_url_base($base_path . '/account.php?categ=authentication&sub=mfa');
		$interface_form->set_object_id($PMBuserid);
		$interface_form->set_table_name('users');
		$interface_form->set_content_form($this->get_js_form() . $this->get_content_form_mfa());

		switch($action) {
			case 'initialization':
				$interface_form->set_field_focus('mfa_confirm_code');
				break;
			case 'initialized':
				break;
			default:
				$interface_form->add_action_extension('initialization',
				 									  $msg['mfa_init_button'],
													  './account.php?categ=authentication&sub=mfa&action=initialization');
				break;
		}
		
		return $interface_form->get_display();
	}

	public function get_js_form() {
		global $msg;
		return "
			<script type='text/javascript'>
				var mfa_counter = 30;
				var dflt_value = '';

				function mfa_counter_down() {
					var mfa_mail_btn = document.getElementById('mfa_send_mail');
					if(mfa_counter) {
						mfa_mail_btn.value = dflt_value + '(' + mfa_counter + ')'
						mfa_counter = mfa_counter - 1;

						setTimeout(mfa_counter_down, 1000);
					} else {
						mfa_counter = 30;
						mfa_mail_btn.value = dflt_value;
						mfa_mail_btn.disabled = false;
					}
				}

				function mfa_send_ajax(action, secret_code) {
					let req = new http_request();
					req.request('./ajax.php?module=account&categ=authentication', 1, '&action=' + action + '&secret_code=' + secret_code);

					let mfa_notify = document.getElementById('mfa_notify');
					if(req.get_text() == 1) {
						mfa_notify.textContent = '" . addslashes($msg['mfa_success_mail']) . "';

						var mfa_mail_btn = document.getElementById('mfa_send_mail');
						dflt_value = mfa_mail_btn.value;

						mfa_mail_btn.disabled = true;
						mfa_counter_down();
					} else {
						mfa_notify.textContent = '" . addslashes($msg['mfa_error_mail']) . "';
					}
				}
			</script>
		";
	}
	
	public function set_properties_from_form() {}
	
	public function save() {
		global $pmb_url_base, $PMBuserid;
		global $form_pwd, $form_pwd2;
		
		if((SESSrights & ADMINISTRATION_AUTH) || ($PMBuserid == $this->id)) {
			$myUser = user::get_param($PMBuserid, 'username');
			if($form_pwd==$form_pwd2 && !empty($form_pwd)) {
				$requete = "UPDATE users SET last_updated_dt=curdate(),pwd='".MySQL::password($form_pwd)."', user_digest = '".md5($myUser.":".md5($pmb_url_base).":".$form_pwd)."' WHERE userid=".$this->id;
				pmb_mysql_query($requete);
			}
			return true;
		}
		return false;
	}

	public function validate_mfa() {
		global $PMBuserid, $mfa_secret_code_hidden;
		
		if((SESSrights & PREF_AUTH)) {
			if(!empty($mfa_secret_code_hidden)) {
				$request = "UPDATE users SET mfa_secret_code = '" . addslashes($mfa_secret_code_hidden) . "', mfa_favorite = 'app' WHERE userid = " . $PMBuserid;
				pmb_mysql_query($request);
			}
			return true;
		}
		return false;
	}

	public function save_mfa() {
		global $PMBuserid, $mfa_favorite_select;
		
		if((SESSrights & PREF_AUTH)) {
			if(!empty($mfa_favorite_select)) {
				$request = "UPDATE users SET mfa_favorite = '" . $mfa_favorite_select . "' WHERE userid = " . $PMBuserid;
				pmb_mysql_query($request);
			}
			return true;
		}
		return false;
	}

	public function reset_mfa() {
		global $PMBuserid;
		
		if((SESSrights & PREF_AUTH)) {
			$request = "UPDATE users SET mfa_secret_code = NULL, mfa_favorite = NULL WHERE userid = " . $PMBuserid;
			pmb_mysql_query($request);

			return true;
		}
		return false;
	}
}
	
