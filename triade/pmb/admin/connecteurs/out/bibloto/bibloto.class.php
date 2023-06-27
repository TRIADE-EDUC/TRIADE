<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bibloto.class.php,v 1.34 2019-06-10 12:36:37 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/connecteurs_out.class.php");
require_once($class_path."/connecteurs_out_sets.class.php");
require_once($class_path."/external_services_converters.class.php");
require_once($class_path."/encoding_normalize.class.php");

class bibloto extends connecteur_out {
	
	public function get_config_form() {
		//Rien
		return '';
	}
	
	public function update_config_from_form() {
		return;
	}
	
	public function instantiate_source_class($source_id) {
		return new bibloto_source($this, $source_id, $this->msg);
	}
	
	public function process($source_id, $pmb_user_id) {
		global $opac_biblio_name,$opac_biblio_email;
		global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email,$biblio_website;
		global $biblio_logo;
		
		$source = new bibloto_source($this, $source_id, $this->msg);
		$param = $source->config;
		$param['biblio']['name'] =$opac_biblio_name;
		$param['biblio']['adr1'] =$biblio_adr1;
		$param['biblio']['adr2'] =$biblio_adr2;
		$param['biblio']['cp'] =$biblio_cp;
		$param['biblio']['town'] =$biblio_town;
		$param['biblio']['phone'] =$biblio_phone;
		$param['biblio']['email'] =$opac_biblio_email;
		if (!empty($param['auth_password'])) {
		  $param['auth_password'] = md5($param['auth_password']);
		}		
		echo encoding_normalize::json_encode($param);
		return;
	}
}

class bibloto_source extends connecteur_out_source {
	
	public function  __construct($connector, $id, $msg) {
		parent::__construct($connector, $id, $msg);
	}
	
	public function get_config_form() {
		global $charset, $pmb_url_base, $_tableau_databases, $_libelle_databases;
		
		$result = parent::get_config_form();
		if(!$this->id){
			$this->config['pmb_ws_url'] = "http://...pmb/ws/connector_out.php?source_id=1";			
			$this->config['auth_login'] = "";
			$this->config['auth_password'] = "";			
			$this->config['style_url'] = "styles/bibloto.css";
			$this->config['checkout_activate'] = 1;
			$this->config['auto_checkout'] = 1;
			$this->config['checkin_activate'] = 1;
			$this->config['resa_activate'] = 0;
			$this->config['default_action'] = 1;
			$this->config['default_action_end'] = 1;
			$this->config['rfid_activate'] = 1;
			$this->config['rfid_driver'] = "3m";
			$this->config['rfid_activate_empr'] = 1;
			$this->config['rfid_activate_expl'] = 1;
			$this->config['rfid_serveur_url'] = "http://localhost:30000";
			$this->config['rfid_library_code'] = "0123456789";
			$this->config['rfid_afi_security_code_on'] = "07";
			$this->config['rfid_afi_security_code_off'] = "C2";
			$this->config['sound_activate'] = 1;
			$this->config['trombinoscope_enabled'] = 0;
			$this->config['thumbnail_url'] = 'http://website/thumbnails/!!empr_cb!!.jpg';
			$this->config['rfid_security_activate'] = 1;	
			$this->config['printer_activate'] = 0;
			$this->config['css'] = "";			
			$this->config['home_tpl']="
<div class='templateContent'>
    <div class='TitleContent'>
        <h1>Automate de pr&ecirc;t</h1>
        <p><img border='0' class='align_middle' src='images/carte_adherent.jpg'></p>
        <p class='IntroMsg'>Placez votre carte de lecteur</p>
    </div>
</div>";		
			$this->config['empr_tpl']=
"<div class='templateContent'>
    <div class='MainContent'>
        <h1>\${nom} \${prenom}</h1>               
        <p class='itemContent'>\${adr1}</p>
        <p class='itemContent'>\${cp} \${ville}</p>
    </div>
</div>";
					
			$this->config['printer_tpl']=
"\x1B\x40\x1B\x21\x16{{biblio.name}}\x1B\x21\x04
{{biblio.adr1}}
{{biblio.town}}
{{biblio.phone}}
{{biblio.email}}

Imprimé le \n
Emprunteur:
{% for empr in empr_list %}
 {{empr.name}} {{empr.fistname}}
{% endfor %}
{% for expl in expl_list %}

{{expl.tit}} 
 {{expl.cb}}
 {{expl.location}} / {{expl.section}} / {{expl.cote}}
 Prêté le {{expl.date_pret}}. \x1B\x21\x14 A retourner le{{expl.date_retour}} \x1B\x21\x04
 ______________________________________
{% endfor %}
\x1D\x56\x41 \x1B\x40";	
			
			$this->config['msg_checkout_button'] = $this->msg['bibloto_msg_checkout_button_value'];
			$this->config['msg_checkout_valid_button'] = $this->msg['bibloto_msg_checkout_valid_button_value'];
			$this->config['msg_checkin_button'] = $this->msg['bibloto_msg_checkin_button_value'];
			$this->config['msg_resa_button'] = $this->msg['bibloto_msg_resa_button_value'];
			$this->config['msg_exit_button'] = $this->msg['bibloto_msg_exit_button_value'];
			$this->config['msg_action_title'] = $this->msg['bibloto_msg_action_title_value'];
			$this->config['msg_checkout_title'] = $this->msg['bibloto_msg_checkout_title_value'];
			$this->config['msg_checkin_title'] = $this->msg['bibloto_msg_checkin_title_value'];
			$this->config['msg_resa_title'] = $this->msg['bibloto_msg_resa_title_value'];
			$this->config['msg_dialog_place_item_checkout'] = $this->msg['bibloto_msg_dialog_place_item_checkout_value'];
			$this->config['msg_dialog_place_item_checkin'] = $this->msg['bibloto_msg_dialog_place_item_checkin_value'];
			$this->config['msg_dialog_too_many_items'] = $this->msg['bibloto_msg_dialog_too_many_items_value'];
			$this->config['msg_dialog_item_cb_unknown'] = $this->msg['bibloto_msg_dialog_item_cb_unknown_value'];
			$this->config['msg_dialog_checkout_possible'] = $this->msg['bibloto_msg_dialog_item_cb_unknown_value'];
			$this->config['msg_dialog_checkout_ok'] = $this->msg['bibloto_msg_dialog_checkout_ok_value'];
			$this->config['msg_dialog_checkout_no'] = $this->msg['bibloto_msg_dialog_checkout_no_value'];
			$this->config['msg_dialog_checkin_ok'] = $this->msg['bibloto_msg_dialog_checkin_ok_value'];
			$this->config['msg_dialog_checkin_no_checkout'] = $this->msg['bibloto_msg_dialog_checkin_no_checkout_value'];
			$this->config['msg_dialog_antivol_error'] = $this->msg['bibloto_msg_dialog_antivol_error_value'];
			$this->config['msg_printer_exit'] = $this->msg['bibloto_msg_printer_exit_value'];
			$this->config['msg_dialog_exit'] = $this->msg['bibloto_msg_dialog_exit_value'];
			$this->config['msg_no_user_found'] = $this->msg['bibloto_msg_no_user_found_value'];
			$this->config['msg_search_title'] = $this->msg['bibloto_msg_search_title_value'];
			$this->config['timeout_disconnect'] = "60";
		}
/*		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_printer_activate'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_printer_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='printer_activate' value='1' ".($this->config["printer_activate"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_printer_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='printer_activate' value='0' ".(!$this->config["printer_activate"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='printer_name'>".htmlentities($this->msg['bibloto_printer_name'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='printer_name' name='printer_name' value='".$this->config['printer_name']."' />		
		</div>		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='printer_tpl'>".htmlentities($this->msg['bibloto_printer_tpl'],ENT_QUOTES,$charset)."</label><br />			
			<textarea id='printer_tpl' class='saisie-80em' wrap='virtual' rows='8' cols='62' name='printer_tpl'>".$this->config['printer_tpl']."</textarea>
		</div>	*/
		//Adresse d'utilisation
		$result .= "<div class=row><label class='etiquette' for='api_exported_functions'>".htmlentities($this->msg['bibloto_service_endpoint'],ENT_QUOTES,$charset)."</label><br />";
		if ($this->id) {
			$result .= "<a target='_blank' href='".$pmb_url_base."ws/connector_out.php?source_id=".$this->id."";
			$result .= count($_tableau_databases)>1?"&database=".$_libelle_databases[array_search(LOCATION,$_tableau_databases)]:"";
			$result .= "'>".$pmb_url_base."ws/connector_out.php?source_id=".$this->id."";
			$result .= count($_tableau_databases)>1?"&database=".$_libelle_databases[array_search(LOCATION,$_tableau_databases)]:"";
			$result .= "</a>";
		}else {
			$result .= htmlentities($this->msg["bibloto_service_endpoint_unrecorded"],ENT_QUOTES,$charset);
		}
		
		$result .= "</div>					
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='pmb_ws_url'>".htmlentities($this->msg['bibloto_pmb_ws_url'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='pmb_ws_url' name='pmb_ws_url' value='".$this->config['pmb_ws_url']."' />		
		</div>
		<div class='row'>			
			<label for=''>".$this->msg["pmb_username"]."</label><br />
			<input type='text' name='auth_login' id='auth_login' value='".htmlentities($this->config['auth_login'],ENT_QUOTES,$charset)."' />
		</div>
		<div class='row'>
			<label for=''>".$this->msg["pmb_password"]."</label><br />
			<input type='text' name='auth_password' id='auth_password' value='".htmlentities($this->config['auth_password'],ENT_QUOTES,$charset)."' />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='style_url'>".htmlentities($this->msg['bibloto_style_url'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='style_url' name='style_url' value='".$this->config['style_url']."' />		
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_checkout_activate'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_checkout_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='checkout_activate' value='1' ".($this->config["checkout_activate"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_checkout_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='checkout_activate' value='0' ".(!$this->config["checkout_activate"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_auto_checkout'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_auto_checkout_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='auto_checkout' value='1' ".($this->config["auto_checkout"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_auto_checkout_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='auto_checkout' value='0' ".(!$this->config["auto_checkout"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_checkin_activate'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_checkin_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='checkin_activate' value='1' ".($this->config["checkin_activate"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_checkin_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='checkin_activate' value='0' ".(!$this->config["checkin_activate"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_resa_activate'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_resa_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='resa_activate' value='1' ".($this->config["resa_activate"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_resa_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='resa_activate' value='0' ".(!$this->config["resa_activate"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_default_action'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_default_action_default'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='default_action' value='0' ".(!$this->config["default_action"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_default_action_checkout'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='default_action' value='1' ".($this->config["default_action"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
			<span>".htmlentities($this->msg['bibloto_default_action_checkin'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='default_action' value='2' ".($this->config["default_action"] == "2" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_default_action_resa'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='default_action' value='3' ".($this->config["default_action"] == "3" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_default_action_end'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_default_action_default'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='default_action_end' value='0' ".(!$this->config["default_action_end"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_default_action_end_home'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='default_action_end' value='1' ".($this->config["default_action_end"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_sound_activate'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_sound_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='sound_activate' value='1' ".($this->config["sound_activate"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_sound_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='sound_activate' value='0' ".(!$this->config["sound_activate"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_enable_trombinoscope'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_sound_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='trombinoscope_enabled' value='1' ".($this->config["trombinoscope_enabled"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_sound_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='trombinoscope_enabled' value='0' ".(!$this->config["trombinoscope_enabled"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>	
        <div class='row'>&nbsp;</div>
        <div class='row'>
			<label class='etiquette' for='thumbnail_url'>".htmlentities($this->msg['bibloto_thumbnail_folder'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='thumbnail_url' name='thumbnail_url' value='".$this->config['thumbnail_url']."' />		
		</div>
		<div class='row'>&nbsp;</div>
        <div class='row'>
			<label class='etiquette' for='timeout_disconnect'>".htmlentities($this->msg['bibloto_timeout_disconnect'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='timeout_disconnect' name='timeout_disconnect' value='".$this->config['timeout_disconnect']."' />		
		</div>
        <hr/>
        <div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_rfid_activate'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_rfid_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='rfid_activate' value='1' ".($this->config["rfid_activate"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_rfid_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='rfid_activate' value='0' ".(!$this->config["rfid_activate"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_rfid_activate_empr'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_rfid_activate_empr_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='rfid_activate_empr' value='1' ".($this->config["rfid_activate_empr"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_rfid_activate_empr_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='rfid_activate_empr' value='0' ".(!$this->config["rfid_activate_empr"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($this->msg['bibloto_rfid_activate_expl'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_rfid_activate_expl_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='rfid_activate_expl' value='1' ".($this->config["rfid_activate_expl"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_rfid_activate_expl_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='rfid_activate_expl' value='0' ".(!$this->config["rfid_activate_expl"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>					
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='rfid_driver'>".htmlentities($this->msg['bibloto_rfid_driver'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='rfid_driver' name='rfid_driver' value='".$this->config['rfid_driver']."' />		
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='rfid_serveur_url '>".htmlentities($this->msg['bibloto_rfid_serveur_url'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='rfid_serveur_url' name='rfid_serveur_url' value='".$this->config['rfid_serveur_url']."' />		
		</div>		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='rfid_library_code'>".htmlentities($this->msg['bibloto_rfid_library_code'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='rfid_library_code' name='rfid_library_code' value='".$this->config['rfid_library_code']."' />		
		</div>			
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='rfid_security_activate'>".htmlentities($this->msg['bibloto_rfid_security_activate'],ENT_QUOTES,$charset)."</label><br />			
			<span>".htmlentities($this->msg['bibloto_rfid_security_activate_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='rfid_security_activate' value='1' ".($this->config["rfid_security_activate"] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
			<span>".htmlentities($this->msg['bibloto_rfid_security_activate_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='rfid_security_activate' value='0' ".(!$this->config["rfid_security_activate"] ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>	
		</div>			
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='rfid_afi_security_code_on'>".htmlentities($this->msg['bibloto_rfid_afi_security_code_on'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='rfid_afi_security_code_on' name='rfid_afi_security_code_on' value='".$this->config['rfid_afi_security_code_on']."' />		
		</div>			
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='rfid_afi_security_code_off'>".htmlentities($this->msg['bibloto_rfid_afi_security_code_off'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='rfid_afi_security_code_off' name='rfid_afi_security_code_off' value='".$this->config['rfid_afi_security_code_off']."' />		
		</div>					
		<div class='row'>&nbsp;</div>
		<hr/>
        <div class='row'>
			<label class='etiquette' for='home_tpl'>".htmlentities($this->msg['bibloto_home_tpl'],ENT_QUOTES,$charset)."</label><br />			
			<textarea id='home_tpl' class='saisie-80em' wrap='virtual' rows='8' cols='62' name='home_tpl'>".$this->config['home_tpl']."</textarea>
		</div>			
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='empr_tpl'>".htmlentities($this->msg['bibloto_empr_tpl'],ENT_QUOTES,$charset)."</label><br />			
			<textarea id='empr_tpl' class='saisie-80em' wrap='virtual' rows='8' cols='62' name='empr_tpl'>".$this->config['empr_tpl']."</textarea>
		</div>
        <div class='row'>
			<label class='etiquette' for='home_tpl'>".htmlentities($this->msg['bibloto_css'],ENT_QUOTES,$charset)."</label><br />			
			<textarea id='css' class='saisie-80em' wrap='virtual' rows='8' cols='62' name='css'>".$this->config['css']."</textarea>
		</div>			
		<div class='row'>&nbsp;</div>				
        <hr/>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_checkout_button'>".htmlentities($this->msg['bibloto_msg_checkout_button'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='msg_checkout_button' name='msg_checkout_button' value='".htmlentities($this->config['msg_checkout_button'],ENT_QUOTES,$charset)."' />		
		</div>		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_checkout_valid_button'>".htmlentities($this->msg['bibloto_msg_checkout_valid_button'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='msg_checkout_valid_button' name='msg_checkout_valid_button' value='".htmlentities($this->config['msg_checkout_valid_button'],ENT_QUOTES,$charset)."' />		
		</div>		
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_checkin_button'>".htmlentities($this->msg['bibloto_msg_checkin_button'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='msg_checkin_button' name='msg_checkin_button' value='".htmlentities($this->config['msg_checkin_button'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_resa_button'>".htmlentities($this->msg['bibloto_msg_resa_button'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='msg_resa_button' name='msg_resa_button' value='".htmlentities($this->config['msg_resa_button'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_exit_button'>".htmlentities($this->msg['bibloto_msg_exit_button'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='msg_exit_button' name='msg_exit_button' value='".htmlentities($this->config['msg_exit_button'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_action_title'>".htmlentities($this->msg['bibloto_msg_action_title'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text'  class='saisie-80em' id='msg_action_title' name='msg_action_title' value='".htmlentities($this->config['msg_action_title'],ENT_QUOTES,$charset)."' />		
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_checkout_title'>".htmlentities($this->msg['bibloto_msg_checkout_title'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text'  class='saisie-80em' id='msg_checkout_title' name='msg_checkout_title' value='".htmlentities($this->config['msg_checkout_title'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_checkin_title'>".htmlentities($this->msg['bibloto_msg_checkin_title'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text'  class='saisie-80em' id='msg_checkin_title' name='msg_checkin_title' value='".htmlentities($this->config['msg_checkin_title'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_resa_title'>".htmlentities($this->msg['bibloto_msg_resa_title'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text'  class='saisie-80em' id='msg_resa_title' name='msg_resa_title' value='".htmlentities($this->config['msg_resa_title'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_place_item_checkout'>".htmlentities($this->msg['bibloto_msg_dialog_place_item_checkout'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_place_item_checkout' name='msg_dialog_place_item_checkout' value='".htmlentities($this->config['msg_dialog_place_item_checkout'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_place_item_checkin'>".htmlentities($this->msg['bibloto_msg_dialog_place_item_checkin'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_place_item_checkin' name='msg_dialog_place_item_checkin' value='".htmlentities($this->config['msg_dialog_place_item_checkin'],ENT_QUOTES,$charset)."' />		
		</div>			
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_too_many_items'>".htmlentities($this->msg['bibloto_msg_dialog_too_many_items'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_too_many_items' name='msg_dialog_too_many_items' value='".htmlentities($this->config['msg_dialog_too_many_items'],ENT_QUOTES,$charset)."' />		
		</div>				
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_item_cb_unknown'>".htmlentities($this->msg['bibloto_msg_dialog_item_cb_unknown'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_item_cb_unknown' name='msg_dialog_item_cb_unknown' value='".htmlentities($this->config['msg_dialog_item_cb_unknown'],ENT_QUOTES,$charset)."' />		
		</div>			
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_checkout_possible'>".htmlentities($this->msg['bibloto_msg_dialog_checkout_possible'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_checkout_possible' name='msg_dialog_checkout_possible' value='".htmlentities($this->config['msg_dialog_checkout_possible'],ENT_QUOTES,$charset)."' />		
		</div>				
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_checkout_ok'>".htmlentities($this->msg['bibloto_msg_dialog_checkout_ok'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_checkout_ok' name='msg_dialog_checkout_ok' value='".htmlentities($this->config['msg_dialog_checkout_ok'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_checkout_no'>".htmlentities($this->msg['bibloto_msg_dialog_checkout_no'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_checkout_no' name='msg_dialog_checkout_no' value='".htmlentities($this->config['msg_dialog_checkout_no'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_checkin_ok'>".htmlentities($this->msg['bibloto_msg_dialog_checkin_ok'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_checkin_ok' name='msg_dialog_checkin_ok' value='".htmlentities($this->config['msg_dialog_checkin_ok'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_checkin_no_checkout'>".htmlentities($this->msg['bibloto_msg_dialog_checkin_no_checkout'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_checkin_no_checkout' name='msg_dialog_checkin_no_checkout' value='".htmlentities($this->config['msg_dialog_checkin_no_checkout'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_antivol_error'>".htmlentities($this->msg['bibloto_msg_dialog_antivol_error'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_dialog_antivol_error' name='msg_dialog_antivol_error' value='".htmlentities($this->config['msg_dialog_antivol_error'],ENT_QUOTES,$charset)."' />		
		</div>	
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_dialog_exit'>".htmlentities($this->msg['bibloto_msg_dialog_exit'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='msg_dialog_exit' name='msg_dialog_exit' value='".htmlentities($this->config['msg_dialog_exit'],ENT_QUOTES,$charset)."' />		
		</div>
        <div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_no_user_found'>".htmlentities($this->msg['bibloto_msg_no_user_found'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_no_user_found' name='msg_no_user_found' value='".htmlentities($this->config['msg_no_user_found'],ENT_QUOTES,$charset)."' />		
		</div>
        <div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_search_title'>".htmlentities($this->msg['bibloto_msg_search_title'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' class='saisie-80em' id='msg_search_title' name='msg_search_title' value='".htmlentities($this->config['msg_search_title'],ENT_QUOTES,$charset)."' />		
		</div>	
		";
		/*						
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='msg_printer_button'>".htmlentities($this->msg['bibloto_msg_printer_button'],ENT_QUOTES,$charset)."</label><br />			
			<input type='text' id='msg_printer_button' name='msg_printer_button' value='".htmlentities($this->config['msg_printer_button'],ENT_QUOTES,$charset)."' />		
		</div>	
		*/
		return $result;
	}
	
	public function update_config_from_form() {
		global $dbh;
		global $pmb_ws_url;
		global $auth_login;
		global $auth_password;
		global $style_url;
		global $checkout_activate;
		global $auto_checkout;
		global $checkin_activate;
		global $resa_activate;
		global $printer_activate;
		global $printer_name;
		global $printer_tpl;
		global $sound_activate;
		global $trombinoscope_enabled;
		global $thumbnail_url;
		global $rfid_activate;
		global $rfid_driver;
		global $rfid_activate_empr ;
		global $rfid_activate_expl;
		global $rfid_serveur_url;
		global $rfid_library_code;
		global $rfid_security_activate;
		global $rfid_afi_security_code_on ;
		global $rfid_afi_security_code_off ;
		global $msg_checkout_button;
		global $msg_checkout_valid_button;
		global $msg_checkin_button;
		global $msg_resa_button;
		global $msg_exit_button;
		global $msg_action_title;
		global $msg_checkout_title;
		global $msg_checkin_title;
		global $msg_resa_title;
		global $msg_dialog_place_item_checkout;
		global $msg_dialog_place_item_checkin;
		global $msg_dialog_too_many_items;
		global $msg_dialog_item_cb_unknown;
		global $msg_dialog_checkout_possible;
		global $msg_dialog_checkout_no;
		global $msg_dialog_checkout_ok;
		global $msg_dialog_checkin_ok;
		global $msg_dialog_checkin_no_checkout;
		global $msg_dialog_antivol_error;
		global $msg_printer_button;
		global $msg_dialog_exit;
		global $msg_no_user_found;
		global $msg_search_title;
		global $timeout_disconnect;
		global $home_tpl;
		global $empr_tpl;
		global $default_action;
		global $default_action_end;
		global $css;
		
		parent::update_config_from_form();

		$this->config['pmb_ws_url'] = $pmb_ws_url;
		$this->config['auth_login'] = $auth_login;
		$this->config['auth_password'] = $auth_password;
		$this->config['style_url'] = $style_url;
		$this->config['checkout_activate'] = $checkout_activate;
		$this->config['auto_checkout'] = $auto_checkout;
		$this->config['checkin_activate'] = $checkin_activate;
		$this->config['resa_activate'] = $resa_activate;
		$this->config['printer_activate'] = $printer_activate;
		$this->config['printer_name'] = stripslashes($printer_name);
		$this->config['printer_tpl'] = stripslashes($printer_tpl);
		$this->config['default_action'] = $default_action;
		$this->config['default_action_end'] = $default_action_end;
		$this->config['sound_activate'] = $sound_activate;
		$this->config['trombinoscope_enabled'] = $trombinoscope_enabled;
		$this->config['thumbnail_url'] = $thumbnail_url;
		$this->config['rfid_activate'] = $rfid_activate;
		$this->config['rfid_driver'] = $rfid_driver;
		$this->config['rfid_activate_empr'] = $rfid_activate_empr;
		$this->config['rfid_activate_expl'] = $rfid_activate_expl;
		$this->config['rfid_serveur_url'] = $rfid_serveur_url;
		$this->config['rfid_library_code'] = $rfid_library_code;
		$this->config['rfid_afi_security_code_on'] = $rfid_afi_security_code_on;
		$this->config['rfid_afi_security_code_off'] = $rfid_afi_security_code_off;
		$this->config['rfid_security_activate'] = $rfid_security_activate;

		$this->config['home_tpl'] = stripslashes($home_tpl);
		$this->config['empr_tpl'] = stripslashes($empr_tpl);
		$this->config['msg_checkout_button'] = stripslashes($msg_checkout_button);
		$this->config['msg_checkout_valid_button'] = stripslashes($msg_checkout_valid_button);
		$this->config['msg_checkin_button'] = stripslashes($msg_checkin_button);
		$this->config['msg_resa_button'] = stripslashes($msg_resa_button);
		$this->config['msg_printer_button'] = stripslashes($msg_printer_button);
		$this->config['msg_exit_button'] = stripslashes($msg_exit_button);
		$this->config['msg_action_title'] = stripslashes($msg_action_title);
		$this->config['msg_checkout_title'] = stripslashes($msg_checkout_title);
		$this->config['msg_checkin_title'] = stripslashes($msg_checkin_title);
		$this->config['msg_resa_title'] = stripslashes($msg_resa_title);
		$this->config['msg_dialog_place_item_checkout'] = stripslashes($msg_dialog_place_item_checkout);
		$this->config['msg_dialog_place_item_checkin'] = stripslashes($msg_dialog_place_item_checkin);
		$this->config['msg_dialog_too_many_items'] = stripslashes($msg_dialog_too_many_items);
		$this->config['msg_dialog_item_cb_unknown'] = stripslashes($msg_dialog_item_cb_unknown);
		$this->config['msg_dialog_checkout_possible'] = stripslashes($msg_dialog_checkout_possible);
		$this->config['msg_dialog_checkout_ok'] = stripslashes($msg_dialog_checkout_ok);
		$this->config['msg_dialog_checkout_no'] = stripslashes($msg_dialog_checkout_no);
		$this->config['msg_dialog_checkin_ok'] = stripslashes($msg_dialog_checkin_ok);
		$this->config['msg_dialog_checkin_no_checkout'] = stripslashes($msg_dialog_checkin_no_checkout);
		$this->config['msg_dialog_antivol_error'] = stripslashes($msg_dialog_antivol_error);
		$this->config['msg_dialog_exit'] = stripslashes($msg_dialog_exit);
		$this->config['msg_no_user_found'] = stripslashes($msg_no_user_found);
		$this->config['msg_search_title'] = stripslashes($msg_search_title);
		$this->config['timeout_disconnect'] = $timeout_disconnect;
		$this->config['css'] = $css;
		return;
	}
}
