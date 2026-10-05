<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_mails_waiting_ui.class.php,v 1.7 2023/09/29 06:46:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/mail_waiting.class.php');

class list_mails_waiting_ui extends list_mails_ui {
	
	protected function _get_query_base() {
		$query = 'select id_mail from mails_waiting';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new mail_waiting($row->id_mail);
	}
	
	protected function init_default_columns() {
		$this->add_column('to_name');
		$this->add_column('to_mail');
		$this->add_column('object');
		$this->add_column('from_name');
		$this->add_column('from_mail');
		$this->add_column('copy_cc');
		$this->add_column('reply_name');
		$this->add_column('reply_mail');
		$this->add_column('date');
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'to_name' :
	        case 'to_mail' :
	        case 'object' :
	        case 'from_name' :
	        case 'from_mail' :
	        case 'copy_cc' :
	        case 'copy_bcc' :
	        case 'reply_name' :
	        case 'reply_mail' :
	        case 'date' :
	            return 'mail_waiting_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_interval_restriction('date', 'mail_waiting_date', 'datetime');
	}
	
	public static function delete_object($id) {
		$mail = new mail_waiting($id);
		$mail->delete();
	}
}