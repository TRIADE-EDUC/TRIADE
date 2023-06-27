<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: audit.php,v 1.13 2019-06-10 08:57:12 btafforeau Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "\$msg[audit_titre]";

require_once ("$base_path/includes/init.inc.php");  
require_once($class_path.'/audit.class.php');

switch($pmb_type_audit) {
	case '1':
		$audit = new audit($type_obj, $object_id) ;
		$audit->get_all();
		if(count($audit->all_audit) == 1){
			$all[0] =  $audit->get_creation() ;
		} else {
			$all[0] =  $audit->get_creation() ;
			$all[1] =  $audit->get_last() ;
		}		
		break;
	case '2':
		$audit = new audit($type_obj, $object_id) ;
		$audit->get_all() ;
		$all = $audit->all_audit ;
		break;
	default:
	case '0':
		echo "<script> self.close(); </script>" ;
		break;
	}

$audit_list = "<script type='text/javascript' src='./javascript/sorttable.js'></script>
<table class='sortable' ><tr><th>".$msg['demandes_demandeur']."<th>".$msg['audit_col_nom']."</th><th>".$msg['audit_col_username'].
"</th><th>".$msg['audit_col_type_action']."</th><th>".$msg['audit_col_date_heure']."</th><th>".$msg['audit_comment']."</th></tr>";
foreach ($all as $cle => $valeur) {
	//user_id, user_name, type_modif, quand, concat(prenom, ' ', nom) as prenom_nom
	$info=json_decode($valeur->info);
	$info_display="";
	if(is_object($info)){
		if($info->comment)$info_display.=$info->comment."<br>";
		if(count($info->fields)){
			foreach($info->fields as $fieldname => $values){
				if(is_object($values)){
					$info_display.=$fieldname." : ".$values->old." => ".$values->new."<br>";
				}
			}
		}
	}else $info_display=$valeur->info;
	
	$type_user_libelle='';
	if($valeur->type_user == 1) {
		$type_user_libelle = $msg['empr_nom_prenom'];
	}else {
		$type_user_libelle = $msg[86];
	}
	$audit_list .= "
		<tr>
			<td>".$type_user_libelle." (".$valeur->user_id.")</td>
			<td>$valeur->prenom_nom</td>
			<td>$valeur->user_name</td>
			<td>".$msg['audit_type'.$valeur->type_modif]."</td>
			<td>$valeur->aff_quand</td>
			<td>".$info_display."</td>
			</tr>";
		}
$audit_list .= "</table>";

echo $audit_list ;

if ($type_obj == 1 || $type_obj == 3) { //Audit notices/notices de bulletin
	if ($type_obj == 1) {
		$requete = "SELECT date_format(create_date, '".$msg["format_date_heure"]."') as aff_create, date_format(update_date, '".$msg["format_date_heure"]."') as aff_update FROM notices WHERE notice_id='$object_id' LIMIT 1 ";
	} else {
		$requete = "SELECT date_format(create_date, '".$msg["format_date_heure"]."') as aff_create, date_format(update_date, '".$msg["format_date_heure"]."') as aff_update FROM notices, bulletins WHERE num_notice = notice_id AND bulletin_id='$object_id' LIMIT 1 ";
	}
	$result = pmb_mysql_query($requete, $dbh);
	if(pmb_mysql_num_rows($result)) {
		$notice = pmb_mysql_fetch_object($result);
		echo "<br>";
		echo htmlentities($msg["noti_crea_date"],ENT_QUOTES, $charset)." ".$notice->aff_create."<br>";
		echo htmlentities($msg["noti_mod_date"],ENT_QUOTES, $charset)." ".$notice->aff_update."<br>";
	}
}

pmb_mysql_close($dbh);
