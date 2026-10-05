<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_mailing.class.php,v 1.1 2020/05/07 09:50:25 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_mailing extends event {

    protected $selvars = [];
    protected $replaced_vars = [];
    protected $empr_cb = '';

    public function get_empr_cb() {
        return $this->empr_cb;
    }
    
    public function set_empr_cb($cb) {
        $this->empr_cb= $cb;
    }
    
    public function get_selvars() {
        return $this->selvars;
    }
    
    public function set_selvars($selvars){
        $this->selvars = $selvars;
    }

    public function get_replaced_vars() {
        return $this->replaced_vars;
    }
    
    public function set_replaced_vars($replaced_vars){
        $this->replaced_vars = $replaced_vars;
    }
}

