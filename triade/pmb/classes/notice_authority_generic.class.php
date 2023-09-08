<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_authority_generic.class.php,v 1.1 2012-01-30 11:01:02 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

interface notice_authority_generic {
	function format_authority_number($authority_number);
	function get_type();
	function get_informations();
	function get_common_informations();
	function get_specifics_informations();
	function get_rejected_forms();
	function get_associated_forms();
	function get_parallel_forms();
	function check_if_exists($data);
}