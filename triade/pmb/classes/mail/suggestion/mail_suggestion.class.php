<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_suggestion.class.php,v 1.1 2023/09/14 13:15:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/mail/mail_root.class.php");

abstract class mail_suggestion extends mail_root {
	
    protected $suggestion;
    
    protected function _init_default_settings() {
        parent::_init_default_settings();
        $this->_init_setting_value('sender', 'docs_location');
    }
    
    protected function get_mail_object() {
        return '';
    }
    
    protected function get_template_mail_content() {
        return '';
    }
    
    protected function get_mail_content() {
        global $msg, $pmb_mail_html_format;
        
        $mail_content = $this->get_template_mail_content();
        $mail_content .= "\n\n ".$msg['acquisition_sug_tit']." :\t ".$this->suggestion->titre."\n";
        if($this->suggestion->auteur) {
            $mail_content .= $msg['acquisition_sug_aut']." :\t ".$this->suggestion->auteur."\n";
        }
        if($this->suggestion->editeur) {
            $mail_content .= $msg['acquisition_sug_edi']." :\t ".$this->suggestion->editeur."\n";
        }
        if($this->suggestion->code) {
            $mail_content .= $msg['acquisition_sug_cod']." :\t ".$this->suggestion->code."\n";
        }
        if($this->suggestion->prix) {
            $mail_content .= $msg['acquisition_sug_pri']." :\t ".$this->suggestion->prix."\n";
        }
        if($this->suggestion->commentaires) {
            $mail_content .= $msg['acquisition_sug_com']." :\t ".$this->suggestion->commentaires."\n";
        }
        $mail_content .= "\n\n";
        
        if ($pmb_mail_html_format) {
            $mail_content = str_replace("\n","<br />",$mail_content);
        }
        
        $mail_content = str_replace('!!date!!', formatdate($this->suggestion->date_creation), $mail_content);
        return $mail_content;
    }
    
    protected function get_mail_headers() {
        global $charset;
        
        $headers = "Content-Type: text/plain; charset=\"$charset\"\n";
        return $headers;
    }
    
    public function set_suggestion($suggestion) {
        $this->suggestion = $suggestion;
    }
}