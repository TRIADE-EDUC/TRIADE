<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: check_password_interface.class.php,v 1.1 2020/11/20 15:31:55 dbellamy Exp $

interface check_password_interface {
	
	public static function check(int $id, string $password, array $form_values) : bool;
	
	public static function get_value(int $id);
}

