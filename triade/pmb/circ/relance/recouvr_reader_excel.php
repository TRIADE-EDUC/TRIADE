<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recouvr_reader_excel.php,v 1.11 2023/12/08 08:48:09 dgoron Exp $

//Affichage des recouvrements pour un lecteur, format Excel HTML

// définition du minimum nécéssaire 
$base_path="../..";                            
$base_auth = "CIRCULATION_AUTH";  
$base_noheader=1;
$base_nosession=1;
//$base_nocheck = 1 ;
require_once ("$base_path/includes/init.inc.php");  
header("Content-Type: application/download\n");
header("Content-Disposition: atachement; filename=\"tableau.xls\"");

global $class_path, $charset;
global $id_empr;

require_once($class_path."/emprunteur.class.php");
require_once($class_path."/comptes.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/serial_display.class.php");

$id_empr = intval($id_empr);
$empr=new emprunteur($id_empr,'', FALSE, 0);

print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>";
print "<table role='presentation'>";
print pmb_bidi("<tr><td>".$empr->prenom." ".$empr->nom."</td></tr>
<tr><td>".$empr->adr1."</td></tr>
<tr><td>".$empr->adr2."</td></tr>
<tr><td>".$empr->cp." ".$empr->ville."</td></tr>
<tr><td>".$empr->mail."</td></tr>
<tr><td>".$empr->tel1."</td></tr>
<tr><td>".$empr->tel2."</td></tr>
</table>");

//Liste des recouvrements
print list_recouvr_reader_ui::get_instance(array('id_empr' => $id_empr))->get_display_html_list();

?>