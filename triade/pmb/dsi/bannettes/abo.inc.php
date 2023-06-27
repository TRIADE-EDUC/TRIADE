<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abo.inc.php,v 1.32 2017-11-13 10:24:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($suite)) $suite = '';
if(!isset($form_cb)) $form_cb = '';

$nom_prenom_abo = '';
if ($id_empr) {
	$result_empr = pmb_mysql_query("select concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom from empr where id_empr=$id_empr") ;
	$nom_prenom_abo = @ pmb_mysql_result($result_empr, '0', 'nom_prenom');
}

if ($nom_prenom_abo) print "<h1>".$msg['dsi_ban_abo']." : $nom_prenom_abo</h1>" ;
	else print "<h1>".$msg['dsi_ban_abo']."</h1>" ;

switch($suite) {
    case 'acces':
    	print dsi_list_bannettes_abo($id_empr) ;
        break;
    case 'search':
		$query = "select id_empr from empr join bannette_abon on id_empr=num_empr where empr_cb='$form_cb' limit 1";
		$result = pmb_mysql_query($query, $dbh);
		$id_empr = @ pmb_mysql_result($result, '0', 'id_empr');
		if (($id_empr) && ($form_cb)) {
			print dsi_list_bannettes_abo($id_empr) ;
		} else {
			print get_cb_dsi ($msg['circ_tit_form_cb_empr'], $msg[34], './dsi.php?categ=bannettes&sub=abo&suite=search', $form_cb);
			$ret =  dsi_list_empr($form_cb) ;
			if ($ret['id_empr']) print dsi_list_bannettes_abo($ret['id_empr']) ;
				else print $ret['message'] ;
		}
		break;
    case 'transform_equ':
    	// mettre à jour l'équation
    	$equation = new equation($id_equation) ;
		$equation->num_classement=      0;
		$s = new search() ;
		$equation->nom_equation=        $s->make_serialized_human_query(stripslashes($requete));	
		$equation->requete=				stripslashes($requete);	
		$equation->update_type=			"C";
    	$equation->save(); 
    	print dsi_list_bannettes_abo($id_empr) ;
		break;
    case 'modif':
    	$bannette = new bannette($id_bannette) ;
    	print $bannette->show_form("abo");  
		if ($pmb_javascript_office_editor) {
			print $pmb_javascript_office_editor ;
			print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
		}
		break;
    case 'delete':
    	$bannette = new bannette($id_bannette) ;
    	$bannette->delete() ;
    	print dsi_list_bannettes_abo($id_empr) ;
		break;
    case 'update':
    	$bannette = new bannette($id_bannette) ;
    	if($form_actif) {
    		$bannette->set_properties_from_form();
    		$bannette->save(); 
    	}
    	print dsi_list_bannettes_abo($id_empr) ;
        break;
    default:
		echo window_title($database_window_title.$msg['dsi_menu_title']);
		print get_cb_dsi ($msg['circ_tit_form_cb_empr'], $msg[34], './dsi.php?categ=bannettes&sub=abo&suite=search', $form_cb);
        break;
    }

