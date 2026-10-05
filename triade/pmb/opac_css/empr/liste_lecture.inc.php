<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_lecture.inc.php,v 1.15 2023/12/13 09:41:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
global $id_liste, $lvl, $act, $sub, $msg;

require_once ($class_path."/liste_lecture.class.php");

$id_liste = intval($id_liste);
$listes = new liste_lecture($id_liste, $act);

switch($lvl){

    //Affichage des listes publiques
	case 'public_list' :
		$listes->generate_publiclist();
		print $listes->display;
		break;

	//Affichage des listes "privees"
	case 'private_list':

		switch($sub) {

		    //Affichage listes creees par le lecteur
			case 'my_list':
			    print common::format_title($msg['list_lecture_private']);
				$listes->generate_mylist();
				print $listes->display;
				break;

            //Affichage listes partagees uniquement
			case 'shared_list':
			    print common::format_title($msg['list_lecture_private']);
				$listes->generate_sharedlist();
				print $listes->display;
				break;

			//Affichage listes auxquelles le lecteur a acces
			default:
			    if('add_list' != $act) {
			        print common::format_title($msg['list_lecture_private']);
				    $listes->generate_privatelist();
				    print $listes->display;
			    }
				break;
		}
		break;

	//Affichage des demandes d'acces
	case 'demande_list':
		$listes->generate_demandes();
		print $listes->display;
		break;
	default:
		break;
}

