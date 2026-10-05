<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_authority_generic.class.php,v 1.2 2019/07/05 13:25:14 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

interface notice_authority_generic {
	public function format_authority_number($authority_number);
	public function get_type();
	public function get_informations();
	public function get_common_informations();
	public function get_specifics_informations();
	public function get_rejected_forms();
	public function get_associated_forms();
	public function get_parallel_forms();
	public function check_if_exists($data);
}