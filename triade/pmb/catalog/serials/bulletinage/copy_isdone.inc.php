<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: copy_isdone.inc.php,v 1.1 2011-12-05 15:17:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc.class.php");
$serialcirc=new serialcirc(0);
$serialcirc->copy_isdone($bul_id);

// mise à jour de l'entête de page
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4011], $serial_header);

$form = show_bulletinage_info_catalogage($bul_id);

if($art_to_show) { 
	$form.=  "<script>document.location='#anchor_$art_to_show'</script>";
}
print $form;
?>