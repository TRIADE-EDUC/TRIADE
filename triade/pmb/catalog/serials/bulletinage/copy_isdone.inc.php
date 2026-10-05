<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: copy_isdone.inc.php,v 1.2 2022/02/01 07:57:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $bul_id, $art_to_show, $serial_header;

require_once("$class_path/serialcirc/serialcirc_copy.class.php");

serialcirc_copy::copy_isdone($bul_id);

// mise à jour de l'entête de page
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4011], $serial_header);

$form = show_bulletinage_info_catalogage($bul_id);

if($art_to_show) { 
	$form.=  "<script>document.location='#anchor_$art_to_show'</script>";
}
print $form;
?>