<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: audit.php,v 1.17 2023/08/02 06:40:33 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "\$msg[audit_titre]";

require_once ("$base_path/includes/init.inc.php");

global $class_path, $msg, $charset, $pmb_type_audit, $type_obj, $object_id;

require_once($class_path.'/audit.class.php');

$type_obj = intval($type_obj);
$object_id = intval($object_id);
if(empty($pmb_type_audit)) {
    echo "<script> self.close(); </script>" ;
}
if(!empty($type_obj) && !empty($object_id)) {
    $filters = array('types' => array($type_obj), 'objects' => array($object_id));
    echo list_audit_type_ui::get_instance($filters)->get_display_list();
}

if ($type_obj == 1 || $type_obj == 3) { //Audit notices/notices de bulletin
	if ($type_obj == 1) {
		$requete = "SELECT date_format(create_date, '".$msg["format_date_heure"]."') as aff_create, date_format(update_date, '".$msg["format_date_heure"]."') as aff_update FROM notices WHERE notice_id='$object_id' LIMIT 1 ";
	} else {
		$requete = "SELECT date_format(create_date, '".$msg["format_date_heure"]."') as aff_create, date_format(update_date, '".$msg["format_date_heure"]."') as aff_update FROM notices, bulletins WHERE num_notice = notice_id AND bulletin_id='$object_id' LIMIT 1 ";
	}
	$result = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($result)) {
		$notice = pmb_mysql_fetch_object($result);
		echo "<br>";
		echo htmlentities($msg["noti_crea_date"],ENT_QUOTES, $charset)." ".$notice->aff_create."<br>";
		echo htmlentities($msg["noti_mod_date"],ENT_QUOTES, $charset)." ".$notice->aff_update."<br>";
	}
}

pmb_mysql_close();
