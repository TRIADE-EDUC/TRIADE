<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_expl_bdp.class.php,v 1.3 2021/12/10 09:49:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl.class.php");

class import_expl_bdp extends import_expl {
	
	public static function traite_exemplaires($function_name='') {
		global $nb_expl_ignores,$bulletin_ex ;
		global $prix, $notice_id, $info_995, $typdoc_995, $tdoc_codage, $book_lender_id,
		$section_995, $sdoc_codage, $book_statut_id, $codstatdoc_995, $statisdoc_codage,
		$cote_mandatory, $book_location_id ;
		
		// lu en 010$d de la notice
		$price = (!empty($prix[0]) ? $prix[0] : '');
		
		$nb_infos_995 = count($info_995);
		// la zone 995 est répétable
		for ($nb_expl = 0; $nb_expl < $nb_infos_995; $nb_expl++) {
			/* RAZ expl */
			$expl = array();
			
			/* préparation du tableau à passer à la méthode */
			switch ($function_name) {
				case 'bdp59':
					// numéro de la banque de prêt (bdp) = 0021 pour la Médiathèque
					//départementale du Nord
					$num_bdp="0021";
					$expl['cb']         = $info_995[$nb_expl]['h'].$num_bdp;
					break;
				default:
					$expl['cb'] 	    = $info_995[$nb_expl]['f'];
					break;
			}
			
			if ($bulletin_ex) {
				$expl['bulletin']=$bulletin_ex;
				$expl['notice']=0;
			} else {
				$expl['notice']     = $notice_id ;
				$expl['bulletin']=0;
			}
			
			// $expl['typdoc']     = $info_995[$nb_expl]['r']; à chercher dans docs_typdoc
			$data_doc=array();
			//$data_doc['tdoc_libelle'] = $info_995[$nb_expl]['r']." -Type doc importé (".$book_lender_id.")";
			$data_doc['tdoc_libelle'] = $typdoc_995[$info_995[$nb_expl]['r']];
			if (!$data_doc['tdoc_libelle']) $data_doc['tdoc_libelle'] = "\$r non conforme -".$info_995[$nb_expl]['r']."-" ;
			$data_doc['duree_pret'] = 0 ; /* valeur par défaut */
			$data_doc['tdoc_codage_import'] = $info_995[$nb_expl]['r'] ;
			if ($tdoc_codage) $data_doc['tdoc_owner'] = $book_lender_id ;
			else $data_doc['tdoc_owner'] = 0 ;
			$expl['typdoc'] = docs_type::import($data_doc);
			
			$expl['cote'] = $info_995[$nb_expl]['k'];
			
			$info_995[$nb_expl]['q']=trim($info_995[$nb_expl]['q']);
			if (!$info_995[$nb_expl]['q'])
				$info_995[$nb_expl]['q'] = "u";
				
				// $expl['section']    = $info_995[$nb_expl]['q']; à chercher dans docs_section
				$data_doc=array();
				if(!empty($info_995[$nb_expl]['j'])) {
					$info_995[$nb_expl]['j']=trim($info_995[$nb_expl]['j']);
				}
				if(!empty($info_995[$nb_expl]['j']) && !empty($section_995[$info_995[$nb_expl]['j']])) {
					$data_doc['section_libelle'] = $section_995[$info_995[$nb_expl]['j']];
					$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['j'];
				} elseif(!empty($info_995[$nb_expl]['j']) && !empty($codstatdoc_995[$info_995[$nb_expl]['j']])) {
					$data_doc['section_libelle'] = $codstatdoc_995[$info_995[$nb_expl]['j']];
					$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['j'];
				} elseif(!empty($codstatdoc_995[$info_995[$nb_expl]['q']])) {
					$data_doc['section_libelle'] = $codstatdoc_995[$info_995[$nb_expl]['q']];
					$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['q'];
				} else {
					$data_doc['section_libelle'] = $section_995[$info_995[$nb_expl]['q']];
					$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['q'];
				}
				if ($sdoc_codage) $data_doc['sdoc_owner'] = $book_lender_id ;
				else $data_doc['sdoc_owner'] = 0 ;
				$expl['section'] = docs_section::import($data_doc);
				
				/* $expl['statut']     à chercher dans docs_statut */
				/* TOUT EST COMMENTE ICI, le statut est maintenant choisi lors de l'import
				 if ($info_995[$nb_expl]['o']=="") $info_995[$nb_expl]['o'] = "e";
				 $data_doc=array();
				 $data_doc['statut_libelle'] = $info_995[$nb_expl]['o']." -Statut importé (".$book_lender_id.")";
				 $data_doc['pret_flag'] = 1 ;
				 $data_doc['statusdoc_codage_import'] = $info_995[$nb_expl]['o'] ;
				 $data_doc['statusdoc_owner'] = $book_lender_id ;
				 $expl['statut'] = docs_statut::import($data_doc);
				 FIN TOUT COMMENTE */
				
				$expl['statut'] = $book_statut_id;
				
				$expl['location'] = $book_location_id;
				
				// $expl['codestat']   = $info_995[$nb_expl]['q']; 'q' utilisé, éventuellement à fixer par combo_box
				$data_doc=array();
				//$data_doc['codestat_libelle'] = $info_995[$nb_expl]['q']." -Pub visé importé (".$book_lender_id.")";
				$data_doc['codestat_libelle'] = $codstatdoc_995[$info_995[$nb_expl]['q']];
				$data_doc['statisdoc_codage_import'] = $info_995[$nb_expl]['q'] ;
				if ($statisdoc_codage) $data_doc['statisdoc_owner'] = $book_lender_id ;
				else $data_doc['statisdoc_owner'] = 0 ;
				$expl['codestat'] = docs_codestat::import($data_doc);
				
				
				// $expl['creation']   = $info_995[$nb_expl]['']; à préciser
				// $expl['modif']      = $info_995[$nb_expl]['']; à préciser
				
				switch ($function_name) {
					case 'bdp42':
						$expl['note']       = $info_995[$nb_expl]['z'];
						$expl['comment']   	= $info_995[$nb_expl]['u'];
						break;
					default:
						$expl['note']       = $info_995[$nb_expl]['u'];
						break;
				}
				$expl['prix']       = $price;
				$expl['expl_owner'] = $book_lender_id ;
				$expl['cote_mandatory'] = $cote_mandatory ;
				
				if (!empty($info_995[$nb_expl]['m'])) {
					$expl['date_depot'] = substr($info_995[$nb_expl]['m'],0,4)."-".substr($info_995[$nb_expl]['m'],4,2)."-".substr($info_995[$nb_expl]['m'],6,2) ;
				}
				if (!empty($info_995[$nb_expl]['n'])) {
					$expl['date_retour'] = substr($info_995[$nb_expl]['n'],0,4)."-".substr($info_995[$nb_expl]['n'],4,2)."-".substr($info_995[$nb_expl]['n'],6,2) ;
				}
				
				// quoi_faire
				if ($info_995[$nb_expl]['0']) $expl['quoi_faire'] = $info_995[$nb_expl]['0']  ;
				else $expl['quoi_faire'] = 2 ;
				
				$expl_id = exemplaire::import($expl);
				if ($expl_id == 0) {
					$nb_expl_ignores++;
				}
				
				//debug : affichage zone 995
				/*
				 echo "995\$a =".$info_995[$nb_expl]['a']."<br />";
				 echo "995\$b =".$info_995[$nb_expl]['b']."<br />";
				 echo "995\$c =".$info_995[$nb_expl]['c']."<br />";
				 echo "995\$d =".$info_995[$nb_expl]['d']."<br />";
				 echo "995\$f =".$info_995[$nb_expl]['f']."<br />";
				 echo "995\$k =".$info_995[$nb_expl]['k']."<br />";
				 echo "995\$m =".$info_995[$nb_expl]['m']."<br />";
				 echo "995\$n =".$info_995[$nb_expl]['n']."<br />";
				 echo "995\$o =".$info_995[$nb_expl]['o']."<br />";
				 echo "995\$q =".$info_995[$nb_expl]['q']."<br />";
				 echo "995\$r =".$info_995[$nb_expl]['r']."<br />";
				 echo "995\$u =".$info_995[$nb_expl]['u']."<br /><br />";
				 */
		} // fin for
	}
	
	
}
