<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet_stick_interface.class.php,v 1.1 2016/07/26 13:38:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

interface sticks_sheet_stick_interface {
	
	public function render(&$pdf, $data);
}