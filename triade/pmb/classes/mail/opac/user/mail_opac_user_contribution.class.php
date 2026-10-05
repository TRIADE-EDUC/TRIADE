<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user_contribution.class.php,v 1.1 2023/09/12 13:52:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_user_contribution extends mail_opac_user {
	
    protected $user;
    
	protected function _init_default_settings() {
	    parent::_init_default_settings();
	    $this->_init_setting_value('sender', 'docs_location');
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg['subject_contribution_mail'];
	}
	
	protected function get_mail_content() {
	    global $include_path, $charset, $pmb_url_base;
	    
	    // On va cherche l'emprunteur
	    $id_empr  = $_SESSION['id_empr_session'];
	    $query = "select distinct empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_tel2, empr_cp, empr_ville from empr where id_empr='$id_empr'";
	    $result = pmb_mysql_query($query);
	    $empr = pmb_mysql_fetch_assoc($result);
	    
	    // On genere le template de mail
	    $template_path = $include_path."/templates/contribution_area/contribution_alert_mail.tpl.html";
	    if (file_exists($include_path."/templates/contribution_area/contribution_alert_mail.subst.tpl.html")) {
	        $template_path = $include_path."/templates/contribution_area/contribution_alert_mail.subst.tpl.html";
	    }
	    $mail_content = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
	    
	    // A voir pour la redirection vers les contribution
	    $url = $pmb_url_base."catalog.php?categ=contribution_area&action=list";
	    
	    //on fait le rendu du template pour l'envoyer aux administrateur
	    $h2o = H2o_collection::get_instance($template_path);
	    $mail_content .= $h2o->render(['empr' => $empr, 'url' => $url, 'user' => $this->user]);
	    return $mail_content;
	}
	
	protected function get_mail_do_nl2br() {
	    return 1;
	}
	
	public function set_user($user) {
	    $this->user = $user;
	}
	
}