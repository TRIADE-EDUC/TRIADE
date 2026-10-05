<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_user.class.php,v 1.2 2019/06/25 15:39:14 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_user extends event {
    
    protected $user_id = 0;
    protected $synchro_step = '';
    
    public function get_user_id() {
        return $this->user_id;
    }
    
    public function set_user_id($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function get_synchro_step() {
        return $this->synchro_step;
    }
    
    public function set_synchro_step($synchro_step) {
        $this->synchro_step = $synchro_step;
        return $this;
    }
}