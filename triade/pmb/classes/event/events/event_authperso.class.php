<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_authperso.class.php,v 1.2.10.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once "$class_path/event/event.class.php";
require_once "$class_path/authperso.class.php";

class event_authperso extends event
{
	protected $id_authperso;
	protected $id_author;
	protected $replacement_id;
	protected $checked_elts = array();

	public function get_id_authperso()
	{
	    return $this->id_authperso;
	}

	public function set_id_authperso($id_authperso)
	{
	    $this->id_authperso = $id_authperso;
		return $this;
	}

	public function get_replacement_id()
	{
		return $this->replacement_id;
	}

	public function set_replacement_id($replacement_id)
	{
		$this->replacement_id = $replacement_id;
	}

	public function get_class_authperso()
	{
	    $authority = new authority(0, $this->get_id_authperso(), AUT_TABLE_AUTHPERSO);
		return $authority->get_object_instance();
	}
}