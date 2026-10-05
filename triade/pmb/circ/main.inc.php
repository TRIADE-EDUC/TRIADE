<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.108.4.3 2025/05/02 12:27:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $include_path, $database_window_title, $pmb_javascript_office_editor;
global $categ, $msg, $id, $plugin,$sub;
global $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;

// on a besoin des fonctions emprunteurs
require_once './circ/empr/empr_func.inc.php';
require_once './circ/expl/expl_func.inc.php';
require_once './circ/pret_func.inc.php';
require_once "{$class_path}/serial_display.class.php";
require_once "{$class_path}/emprunteur.class.php";
require_once "{$class_path}/mono_display.class.php";
require_once "{$class_path}/notice.class.php";
require_once "{$class_path}/author.class.php";
require_once "{$class_path}/editor.class.php";
require_once "{$class_path}/collection.class.php";
require_once "{$class_path}/subcollection.class.php";
require_once "{$class_path}/serie.class.php";
require_once "{$class_path}/indexint.class.php";
require_once "{$class_path}/category.class.php";
require_once "{$include_path}/notice_authors.inc.php";
require_once "{$include_path}/notice_categories.inc.php";
require_once "{$include_path}/expl_info.inc.php";
require_once "{$include_path}/explnum.inc.php";
require_once "{$include_path}/resa_func.inc.php";
require_once "{$include_path}/isbn.inc.php";
require_once "{$class_path}/docs_location.class.php";
require_once "{$class_path}/bannette.class.php";
require_once "{$class_path}/audit.class.php";
require_once "{$class_path}/indexation_stack.class.php";
require_once "{$class_path}/pnb/pnb_loan.class.php";
require_once "{$class_path}/password/password.class.php";

if (($categ=='pretrestrict') && ($form_login) && ($form_password)) {

	$id_empr = 0;
	$form_cb = '';
	$encrypted_password = '';
	$password_match = false;

	$query = "select id_empr, empr_cb, empr_password from empr where empr_login='".$form_login."' limit 1" ;
	$result = pmb_mysql_query($query);
	if (pmb_mysql_num_rows($result)) {
		$row = pmb_mysql_fetch_assoc($result);
		$id_empr = $row['id_empr'];
		$form_cb = $row['empr_cb'];
		$encrypted_password = $row['empr_password'];
		$hash_format = password::get_hash_format($encrypted_password);
		if( 'bcrypt' == $hash_format ) {
			$password_match = password::verify_hash($form_password, $encrypted_password);
		} elseif( $encrypted_password == password::gen_previous_hash($form_password, $id_empr) ) {
			$password_match = true;
		}
	}
	if ($password_match && $id_empr && $form_cb) {
		$categ='pret' ;
	}
}
if (SESSrights & RESTRICTCIRC_AUTH) {
    //Circulation restreinte : on autorise l'historique des relances
    if($sub != 'show_late') {
        $sub="" ;
    }
}

switch($categ) {
	case 'pret':
		echo window_title($database_window_title.$msg['5']." : ".$msg['13']);
		switch($sub) {
			case 'do_pret_resa':
				require_once('./circ/do_pret_resa.inc.php');
				if(is_countable($ids_resa) && count($ids_resa)) {
					$query_empr = "select empr_cb from empr where id_empr='".$id_empr."' ";
					$result_empr = pmb_mysql_query($query_empr);
					$form_cb = pmb_mysql_result($result_empr, 0, 'empr_cb');
					if ($form_cb) {
						$temp = array();
						foreach($ids_resa as $id_resa) {
							$temp[] = do_pret_resa($id_resa, $force_pret);
						}
						$erreur_affichage = do_pret_resa_retour_affichage($temp);
						$ficEmpr = new emprunteur($id_empr, $erreur_affichage, FALSE, 1);
						print pmb_bidi($ficEmpr->fiche);
					}else {
						// emprunteur inconnu
						error_message($msg['391'], $msg['392'], 1, './circ.php');
					}
				}else {
					include("./circ/pret.inc.php");
				}
				break;
			case 'pret_prolongation':
			case 'pret_prolongation_bloc':
				if (!empty($id_doc)) {
					$id_bloc=$id_doc;
				}
				if ($id_bloc) {
					require_once('./circ/prolongation.inc.php');
					$query_empr="select id_empr from empr where empr_cb='".$form_cb."'";
					$result_empr=pmb_mysql_query($query_empr);
					$id_empr=pmb_mysql_result($result_empr,0,'id_empr');
					$temp=array();
					if ($sub=='pret_prolongation') {
						$bloc_prolongation=0;
						$ids=array(0=>($id_bloc*1));
						$ok_msg='390';
					} else {
						$bloc_prolongation=1;
						$ids=explode("  ",$id_bloc);
						$date_retour=$date_retbloc;
						$ok_msg='prets_prolong';
					}
					if (($id_empr)&&(count($ids)>0)) {
						require_once "{$class_path}/emprunteur.class.php";
						foreach ($ids as $dummykey=>$id){
							$temp[]= prolonger($id);
						}
						$erreur_affichage = prolonger_retour_affichage($temp, $bloc_prolongation, $form_cb, $date_retour);

						$ficEmpr = new emprunteur($id_empr, $erreur_affichage, FALSE, 1);
						$affichage= $ficEmpr->fiche;
						print pmb_bidi($affichage);
					} else {
						// prolongation d'un prêt. exemplaire ou emprunteur inconnu
						error_message($msg['391'], $msg['392'], 1, './circ.php');
					}
				} else {
					include("./circ/pret.inc.php");
				}
				break;
			case 'compte':
				//Gestion des comptes financiers
				$empr=new emprunteur($id,'', FALSE, 1);
				$empr->do_fiche_compte($typ_compte);
				print pmb_bidi($empr->fiche_compte);
				break;
			case 'pret_express':
				$pe_isbn=traite_code_isbn(stripslashes($pe_isbn));
				$suite_rqt="";
				$requete_idexpl = "select expl_id from exemplaires where expl_cb='".addslashes($pe_excb)."'";
				$result = pmb_mysql_query($requete_idexpl);
				if(pmb_mysql_num_rows($result)==0) {
					if (isISBN($pe_isbn)) {
						if (strlen($pe_isbn)==13)
							$suite_rqt=" or code='".formatISBN($pe_isbn,13)."' ";
						else $suite_rqt="or code='".formatISBN($pe_isbn,10)."' ";
					}
					$acreer = 1 ;
					if ($pe_isbn) {
						$requete = "select notice_id from notices where code='".addslashes($pe_isbn)."' ".$suite_rqt." and niveau_biblio='m' and niveau_hierar='0' ";
						$result = pmb_mysql_query($requete);
						if ($tmp_not = pmb_mysql_fetch_object($result)) {
							$id_notice=$tmp_not->notice_id;
							$acreer = 0 ;
						}
					}
					if ($acreer) {
						$requete = "INSERT INTO notices SET code='".addslashes($pe_isbn)."', tit1='$pe_titre', statut='$pmb_pret_express_statut', niveau_biblio='m', niveau_hierar='0', create_date=sysdate() ";
						$result = pmb_mysql_query($requete);
						if (!$result) die ('ERROR PE: insert into notice');
						$id_notice=pmb_mysql_insert_id();

						audit::insert_creation (AUDIT_NOTICE, $id_notice) ;
						indexation_stack::push($id_notice, TYPE_NOTICE);

						if ($gestion_acces_active==1) {
							require_once "{$class_path}/acces.class.php";
							$ac= new acces();

							//traitement des droits acces user_notice
							if ($gestion_acces_user_notice==1) {
								$dom_1= $ac->setDomain(1);
								$dom_1->storeUserRights(0, $id_notice);
							}
							//traitement des droits acces empr_notice
							if ($gestion_acces_empr_notice==1) {
								$dom_2= $ac->setDomain(2);
								$dom_2->storeUserRights(0, $id_notice);
							}
						}

					}
					if (!$id_notice) die ('ERROR PE: aucun id_notice pour exemplaire...');

					// exemplaire express
					if ($pe_excb=="") $pe_excb='PE'.rand(0,100000);

					$requete = "INSERT INTO exemplaires
								SET expl_cb='$pe_excb',
									expl_notice='$id_notice',
									expl_typdoc='$pe_tdoc',
									expl_location='$deflt_docs_location',
									expl_section='$deflt_docs_section',
									expl_statut='$deflt_docs_statut',
									expl_codestat='$deflt_docs_codestat',
									expl_owner='$deflt_lenders',
									create_date=CURRENT_TIMESTAMP()
									";
					$result = pmb_mysql_query($requete);
					if (!$result) {
						error_message($msg[350], $msg['pecb_already_exist'], 1 ,'');
						exit();
					}
					$id_expl= pmb_mysql_insert_id();
					audit::insert_creation (AUDIT_EXPL, $id_expl) ;

					if (preg_match('/^PE/',$pe_excb)) {
						//redefine exemplaires.expl_cb if $pe_excb is random
						$pe_excb='PE'.$id_expl;
						$requete = "UPDATE exemplaires SET expl_cb='$pe_excb' WHERE expl_id='$id_expl'";
						$result = pmb_mysql_query($requete);
						if (!$result) die ('ERROR PE: update exemplaires');
					}
					$cb_doc=$pe_excb;
					$rqtstatut = "select gestion_libelle from notice_statut where id_notice_statut='$pmb_pret_express_statut' ";
					$resstatut = pmb_mysql_fetch_object(pmb_mysql_query($rqtstatut));
					$noteexpress = addslashes($resstatut->gestion_libelle) ;
					$requete = "UPDATE exemplaires SET expl_note='$noteexpress' WHERE expl_id='$id_expl'";
					$result = pmb_mysql_query($requete);
					include("./circ/pret.inc.php");
				} else error_message($msg[350], $msg['pecb_already_exist'], 1 ,'');
				break;
			case 'suppr_resa_from_fiche':
				include("./circ/listeresa/main.inc.php");
				include("./circ/pret.inc.php");
				break;
			case 'suppr_resa_planning_from_fiche' :
				include("./circ/resa_planning/main.inc.php");
				include("./circ/pret.inc.php");
				break;
			case 'show_late':
				$empr=new emprunteur($id,'', FALSE, 1);
				$empr->do_fiche_retard();
				print $empr->fiche_retard;
				break;
			default:
				include("./circ/pret.inc.php");
				break;
		}
		break;
	case 'retour':
		echo window_title($database_window_title.$msg["5"]." : ".$msg["14"]);
		include("./circ/retour.inc.php");
		break;
	case 'retour_secouru':
		include("./circ/retour_secouru_download.inc.php");
		break;
	case 'retour_secouru_int':
		include("./circ/retour_secouru.inc.php");
		break;
	case 'resa':
		include("./circ/resa/main.inc.php");
		break;
	case 'express':
		include("./circ/express/main.inc.php");
		break;
	case 'visu_rech':
		echo window_title($database_window_title.$msg["5"]." : ".$msg["voir_document"]);
		include("./circ/visu_rech/visu_rech.inc.php");
		break;
	case 'empr_update':
		// update/insert d'un emprunteur
		include("./circ/empr/empr_update.inc.php");
		break;
	case 'empr_create':
		echo window_title($database_window_title.$msg["5"]." : ".$msg["15"]);
		// récupération code barre en vue création d'un emprunteur
		include("./circ/empr/empr_create.inc.php");
		break;
	case 'empr_delete':
		// suppression d'un emprunteur
		include("./circ/empr/delete.inc.php");
		break;
	case 'empr_saisie':
		if($pmb_javascript_office_editor){
			print $pmb_javascript_office_editor;
			print "<script type='text/javascript'>
                pmb_include('$base_path/javascript/tinyMCE_interface.js');
            </script>";
		}
		// affichage formulaire de saisie d'un emprunteur
		include("./circ/empr/empr_saisie.inc.php");
		break;
	case 'empr_duplicate':
		echo window_title($database_window_title.$msg["empr_duplicate"].$msg[1003].$msg[1001]);
		$rqt = "select max(id_empr+1) as max_id from empr ";
		$res = pmb_mysql_query($rqt);
		$id_initial = pmb_mysql_fetch_object($res);
		$id_a_creer = (string)$id_initial->max_id;
		// modif pour nouvelle méthode d'incrémentation	*********************************************************************
		$pmb_num_carte_auto_array=array();
		$pmb_num_carte_auto_array=explode(",",$pmb_num_carte_auto);

		if ($pmb_num_carte_auto_array[0] == "1" ) {
			$rqt = "select max(empr_cb+1) as max_cb from empr ";
			$res = pmb_mysql_query($rqt);
			$cb_initial = pmb_mysql_fetch_object($res);
			$cb_a_creer = (string)$cb_initial->max_cb;
		} elseif ($pmb_num_carte_auto_array[0] == "2" ) {
			$long_prefixe = $pmb_num_carte_auto_array[1];
			$nb_chiffres = $pmb_num_carte_auto_array[2];
			$prefix = $pmb_num_carte_auto_array[3];
		    $rqt =  "SELECT CAST(SUBSTRING(empr_cb,".($long_prefixe+1).") AS UNSIGNED) AS max_cb, SUBSTRING(empr_cb,1,".($long_prefixe*1).") AS prefixdb FROM empr ORDER BY max_cb DESC limit 0,1" ; // modif f cerovetti pour sortir dernier code barre tri par ASCII
			$res = pmb_mysql_query($rqt);
			$cb_initial = pmb_mysql_fetch_object($res);
			$cb_a_creer = ($cb_initial->max_cb*1)+1;
			if (!$nb_chiffres) $nb_chiffres=strlen($cb_a_creer);
			if (!$prefix) $prefix = $cb_initial->prefixdb;
			$cb_a_creer = $prefix.substr((string)str_pad($cb_a_creer, $nb_chiffres, "0", STR_PAD_LEFT),-$nb_chiffres);
			// fin modif pour nouvelle méthode d'incrémentation*******************************************************************
		} else $cb_a_creer="";
		if($pmb_javascript_office_editor){
			print $pmb_javascript_office_editor;
			print "<script type='text/javascript'>
                pmb_include('$base_path/javascript/tinyMCE_interface.js');
            </script>";
		}
		show_empr_form("./circ.php?categ=empr_update","./circ.php?categ=empr_create", $id, (string)$cb_a_creer,(string)$id_a_creer);
		break;
	case 'empr_reset_mfa':
	    include("./circ/empr/empr_reset_mfa.inc.php");
	    break;
	case 'visu_ex':
		echo window_title($database_window_title.$msg["5"]." : ".$msg["voir_exemplaire"]);
		// visualisation d'un exemplaire
		include("./circ/visu_ex.inc.php");
		break;
	case 'note_ex':
		// visualisation d'un exemplaire
		include("./circ/note_ex.inc.php");
		break;
	case 'groups':
		// interface de gestion des groupes
		include("./circ/groups/group_main.inc.php");
		break;
	case 'listeresa':
		// gestion des réservations
		include("./circ/listeresa/main.inc.php");
		break;
	case 'resa_planning':
		// gestion des réservations planifiées
		include("./circ/resa_planning/main.inc.php");
		break;
	case 'relance':
		//Gestion des relances
		include("./circ/relance/main.inc.php");
		break;
	case 'caddie':
		include('./circ/caddie/caddie.inc.php');
		break;
	case 'sug' :
		//Création de suggestion
		include("./circ/suggestions/make_sug.inc.php");
		break;
	case 'resa_from_catal' :
		// on est en pose de résa en arrivant avec un id_notice ou bulletin mais sans emprunteur
		$id_notice = intval($id_notice);
		$id_bulletin = intval($id_bulletin);
		if(!isset($cb_initial)) $cb_initial = '';
		if ($id_notice || $id_bulletin) {
			require_once "{$class_path}/event/events/event_resa.class.php";
			$evt = new event_resa('resa', 'resa_from_catal');
			$evt->set_resa($id_notice, $id_bulletin);
			$evth = events_handler::get_instance();
			$evth->send($evt);
			if($evt->get_result()){
				$cb_initial= $evt->get_result();
			}
			get_cb( $msg['reserv_doc'], $msg[34], $msg['circ_tit_form_cb_empr'], './circ.php?categ=pret&id_notice='.$id_notice.'&id_bulletin='.$id_bulletin.(isset($force_resa) && $force_resa && $pmb_resa_records_no_expl ? '&force_resa=1' : ''), 0, $cb_initial);

		}
		break;
	case 'resa_planning_from_catal' :
		// on est en pose de prévision en arrivant avec un id_notice mais sans emprunteur
	    if(!isset($id_notice)) $id_notice = 0;
	    if(!isset($id_bulletin)) $id_bulletin = 0;
		if ($id_notice || $id_bulletin) {
			get_cb( $msg['prevision_doc'], $msg[34], $msg['circ_tit_form_cb_empr'], './circ.php?categ=pret&id_notice='.$id_notice.'&id_bulletin='.$id_bulletin.'&type_resa=1', 0);
		}
		break;
	case 'trans' :
		// Transferts entre bibliothèques
		include("./circ/transferts/main.inc.php");
		break;
	case 'rfid_prog' :
		// programmer les étiquettes rfid en masse
		include("./circ/rfid/rfid_prog.inc.php");
		break;
	case 'rfid_del' :
		// effacer les étiquettes rfid en masse
		include("./circ/rfid/rfid_del.inc.php");
		break;
	case 'rfid_read' :
		// lire les étiquettes rfid en masse
		include("./circ/rfid/rfid_read.inc.php");
		break;
	case 'ret_todo' :
		// voir les exemplaires qui nécessitent un traitement non effectué lors d'un retour
		include("./circ/ret_todo/ret_todo.inc.php");
		break;
	case 'search' :
		// recherches emprunteurs
		switch ($sub) {
			case "launch":
				include("./circ/pret.inc.php");
				break;
			default:
				include('./circ/empr/search.inc.php');
				break;
		}
		break;
	case 'serialcirc' :
		echo window_title($database_window_title.$msg["5"]." : ".$msg["serialcirc_circ_menu"]);
		// voir les exemplaires qui nécessitent un traitement non effectué lors d'un retour
		include("./circ/serialcirc/serialcirc.inc.php");
		break;
	case 'groupexpl' :
		include("./circ/groupexpl/main.inc.php");
		break;
	case 'scan_request' :
		require_once($class_path."/scan_request/scan_requests_controller.class.php");
		scan_requests_controller::proceed($id);
		break;
	case 'search_perso' :
		require_once("$class_path/search_perso.class.php");
		$search_p= new search_perso($id, 'EMPR');
		$search_p->proceed();
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed("circ",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	default:
		echo window_title($database_window_title.$msg["5"]." : ".$msg["13"]);
		if (SESSrights & RESTRICTCIRC_AUTH) get_login_empr_pret ( $msg[13], $msg[34], $msg['circ_tit_form_cb_empr'], './circ.php?categ=pretrestrict', 0);
		else get_cb( $msg[13], $msg[34], $msg['circ_tit_form_cb_empr'], './circ.php?categ=pret', 0);
		break;
	}
