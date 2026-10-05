<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_suggestion_ordered.class.php,v 1.1 2023/09/14 13:15:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_suggestion_ordered extends mail_suggestion {
    
    protected function get_mail_object() {
        global $acquisition_mel_cde_obj;
        
        return $acquisition_mel_cde_obj;
    }
    
    protected function get_template_mail_content() {
        global $acquisition_mel_cde_cor;
        return $acquisition_mel_cde_cor;
    }
}