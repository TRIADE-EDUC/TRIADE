<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_contribution.class.php,v 1.1 2023/09/12 13:52:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader_contribution extends mail_reader {
	
	protected $empr;
	protected $datastore_results;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'docs_location');
	}
	
	protected function get_mail_object() {
	    global $msg;
	    return $msg['subject_mail_confirm_validate_contribution'];;
	}
	
	protected function get_mail_content() {
	    global $include_path, $msg, $charset, $opac_url_base;
	    
	    // On genere le template de mail
	    $template_path = $include_path."/templates/contribution_area/contribution_validate_mail.tpl.html";
	    if (file_exists($include_path."/templates/contribution_area/contribution_validate_mail.subst.tpl.html")) {
	        $template_path = $include_path."/templates/contribution_area/contribution_validate_mail.subst.tpl.html";
	    }
	    
	    $mail_content = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
	    
	    $dateTime = new DateTime();
	    $last_edit = $dateTime->setTimestamp($this->datastore_results[0]->last_edit);
	    
	    $messages =  [
	        "subject" => $msg['subject_mail_confirm_validate_contribution'],
	        "url" => $msg['mail_confirm_contribution_url']
	        
	    ];
	    $url = $opac_url_base."empr.php?tab=contribution_area&lvl=contribution_area_done";
	    
	    //on fait le rendu du template pour l'envoyer aux administrateur
	    $h2o = H2o_collection::get_instance($template_path);
	    $mail_content .= $h2o->render(['empr' => $this->empr, 'isbd' => $this->datastore_results[0]->display_label, 'date_contrib' => $last_edit->format('d-m-Y'), 'msg' => $messages, 'url' => $url]);
	    return $mail_content;
	}
	
	protected function get_mail_headers() {
	    global $charset;
	    
	    $headers  = "MIME-Version: 1.0\n";
	    $headers .= "Content-type: text/html; charset=".$charset."\n";
	    return $headers;
	}
	
	protected function get_mail_do_nl2br() {
		return 1;
	}
	
	public function set_datastore_results($datastore_results) {
	    $this->datastore_results = $datastore_results;
	}
}