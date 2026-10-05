<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_27S.inc.php,v 1.11 2021/12/09 14:22:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");

// +-------------------------------------------------+

// Attention, n'a pas été modifié pour le multi-thesaurus

// +-------------------------------------------------+





// DEBUT paramétrage propre à la base de données d'importation :
//	les champs UNIMARC lus qui vont être stockés dans des champs personnalisés sont précisés ici
$id949_a = 2 ; // autres CDU
$id949_c = 1 ; // numero de document
$id949_d = 3 ; // signature du catalographe
$ISO_decode_do_not_decode = 1;

function recup_noticeunimarc_suite($notice) {
	global $info_949		;

	$info_949 = array();

	$record = new iso2709_record($notice, AUTO_UPDATE); 
	for ($i=0;$i<count($record->inner_directory);$i++) {
		$cle=$record->inner_directory[$i]['label'];
		switch($cle) {
			case "949": // infos bibliotheque 27 septembre
				$info_949=$record->get_subfield($cle,"a","c","d");
				break;
			default:
				break;
	
			} /* end of switch */
	
		} /* end of for */
	} // fin recup_noticeunimarc_suite = fin récupération des variables propres au CNL
	
	
function import_new_notice_suite() {
	global $notice_id ;
	
	global $info_949 ;
	global $id949_a, $id949_c, $id949_d;
	
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z ;
	global $id_rech_theme; 

      $id_rech_theme = 0;

//	echo "<pre>";
//	print_r ($info_949);
//	echo "</pre>";

	// 949$a est stocké dans un champ personnalisé texte
	// ce champ personnalisé a l'id $id949_a
	// TRAITEMENT :
	//	Rechercher si l'enregistrement existe déjà dans notices_custom_values = SELECT 1 FROM notices_custom_values WHERE notices_custom_champ=$id949_a AND notices_custom_origine=$notice_id
	//	Créer si besoin
	import_records::insert_value_custom_field($id949_a, $notice_id, $info_949[0]['a']);

	// 949$c est stocké dans un champ personnalisé texte
	// ce champ personnalisé a l'id $id949_c
	// TRAITEMENT :
	//	Rechercher si l'enregistrement existe déjà dans notices_custom_values = SELECT 1 FROM notices_custom_values WHERE notices_custom_champ=$id949_c AND notices_custom_origine=$notice_id
	//	Créer si besoin
	$rqt = "SELECT count(1) FROM notices_custom_values WHERE notices_custom_champ='".$id949_c."' AND notices_custom_origine='".$notice_id."' " ;
	if (!pmb_mysql_result(pmb_mysql_query($rqt),0,0)) {
		$rqt_ajout = "INSERT INTO notices_custom_values (notices_custom_champ, notices_custom_origine, notices_custom_small_text, notices_custom_integer) VALUES ('".$id949_c."', '".$notice_id."', '".$info_949[0]['c']."', ".$info_949[0]['c'].")" ;
		pmb_mysql_query($rqt_ajout) ;
	}

	// 949$d est stocké dans un champ personnalisé texte
	// ce champ personnalisé a l'id $id949_d
	// TRAITEMENT :
	//	Rechercher si l'enregistrement existe déjà dans notices_custom_values = SELECT 1 FROM notices_custom_values WHERE notices_custom_champ=$id949_d AND notices_custom_origine=$notice_id
	//	Créer si besoin
	import_records::insert_value_custom_field($id949_d, $notice_id, $info_949[0]['d']);

	// les champs $606 sont stockés dans les catégories
	//	$a >> en sous catégories de $id_rech_theme
	// 		$j en complément de $a
	//		$x en sous catégories de $a
	// TRAITEMENT :
	// pour $a=0 à size_of $info_606_a
	//	pour $j=0 à size_of $info_606_j[$a]
	//		concaténer $libelle_j .= $info_606_j[$a][$j]
	//	$libelle_final = $info_606_a[0]." ** ".$libelle_j
	//	Rechercher si l'enregistrement existe déjà dans categories = 
	//		SELECT categ_id FROM categories WHERE categ_parent='".$id_rech_theme."' AND categ_libelle='".addslashes($libelle_final)."' "
	//	Créer si besoin et récupérer l'id $categid_a
	//	$categid_parent =  $categid_a
	//	pour $x=0 à size_of $info_606_x[$a]
	//		Rechercher si l'enregistrement existe déjà dans categories = 
	//			SELECT categ_id FROM categories WHERE categ_parent='".$categ_parent."' AND categ_libelle='".addslashes($info_606_x[$a][$x])."' "
	//		Créer si besoin et récupérer l'id $categid_parent
	//
	$nb_infos_606_a = count($info_606_a);
	for ($a = 0; $a < $nb_infos_606_a; $a++) {
	    $nb_infos_606_j = count($info_606_j[$a]);
	    for ($j = 0; $j < $nb_infos_606_j; $j++) {
	        if (empty($libelle_j)) {
	            $libelle_j .= $info_606_j[$a][$j];
	        } else {
	            $libelle_j .= " ** ".$info_606_j[$a][$j];
	        }
		}
		
		if (empty($libelle_j)) {
		    $libelle_final = $info_606_a[$a][0];
		} else {
		    $libelle_final = $info_606_a[$a][0]." ** $libelle_j";
		}
		if (empty($libelle_final)) {
		    break; 
		}
		
		$rqt_a = "SELECT categ_id FROM categories WHERE categ_parent='$id_rech_theme' AND categ_libelle='".addslashes($libelle_final)."' ";
		$res_a = pmb_mysql_query($rqt_a);
		if (pmb_mysql_num_rows($res_a)) {
			$categid_a = pmb_mysql_result($res_a, 0, 0);
		} else {
			$rqt_ajout = "insert into categories set categ_parent='$id_rech_theme', categ_libelle='".addslashes($libelle_final)."', index_categorie=' ".strip_empty_words($libelle_final)." ' ";
			pmb_mysql_query($rqt_ajout);
			$categid_a = pmb_mysql_insert_id() ;
		}
		
		// récup des sous-categ en cascade sous $a
		$categ_parent = $categid_a;
		$nb_infos_606_x = count($info_606_x[$a]);
		for ($x = 0; $x < $nb_infos_606_x; $x++) {
			$rqt_x = "SELECT categ_id FROM categories WHERE categ_parent='$categ_parent' AND categ_libelle='".addslashes($info_606_x[$a][$x])."' ";
			$res_x = pmb_mysql_query($rqt_x);
			if (pmb_mysql_num_rows($res_x)) {
				$categ_parent = pmb_mysql_result($res_x, 0, 0);
			} else {
				$rqt_ajout = "insert into categories set categ_parent='$categ_parent', categ_libelle='".addslashes($info_606_x[$a][$x])."', index_categorie=' ".strip_empty_words($info_606_x[$a][$x])." ' ";
				pmb_mysql_query($rqt_ajout);
				$categ_parent = pmb_mysql_insert_id();
			}
		} // fin récup des $x en cascade sous l'id de la catégorie 606$a

		if ($categ_parent != $id_rech_theme) {
			// insertion dans la table notices_categories
			$rqt_ajout = "insert into notices_categories set notcateg_notice='$notice_id', notcateg_categorie='$categ_parent' ";
			pmb_mysql_query($rqt_ajout);
		}
	}
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('27S');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI
