<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_empr.class.php,v 1.1 2020/05/07 09:50:24 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_empr extends event {

    protected $id_empr;
    protected $empr_cb = '';
    protected $additionnal_informations = [];

    public function get_id_empr() {
        return $this->id_empr;
    }
    
    public function set_id_empr($id_empr) {
        $this->id_empr = $id_empr;
        return $this;
    }
    
    public function get_additionnal_informations() {
        return $this->additionnal_informations;
    }
    
    public function set_additionnal_informations($additionnal_informations) {
        $this->additionnal_informations= $additionnal_informations;
    }

    public function get_empr_cb() {
        return $this->empr_cb;
    }
    
    public function set_empr_cb($cb) {
        $this->empr_cb= $cb;
    }
}

