<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_tence.inc.php,v 1.15 2021/12/09 14:22:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// Script personnalisé pour reprise des informations de Tence
global $class_path; //Nécessaire pour certaines inclusions
require_once ($class_path."/import/import_expl_bdp.class.php");
require_once($class_path."/serials.class.php");

function recup_noticeunimarc_suite($notice) {
	global $info_207;
	$info_207 = "" ;
	$record = new iso2709_record($notice, AUTO_UPDATE); 
	$info_207=$record->get_subfield("207","a");
//	echo "<br />Lu notice :<pre>"; print_r($info_207) ;echo "</pre>";
	} // fin recup_noticeunimarc_suite = fin récupération des variables propres Tence
	
function import_new_notice_suite() {
	global $notice_id ;
	
	global $bibliographic_level_origine;
	global $hierarchic_level_origine;
	
	global $index_sujets ;
	global $pmb_keyword_sep ;
	
	global $issn_011 ;

	global $tit_200a		; // pour reconstruire et chercher la notice chapeau du pério
	$tit[0]['a'] = implode (" ; ",$tit_200a);

	if (is_array($index_sujets)) $mots_cles = implode (" $pmb_keyword_sep ",$index_sujets);
		else $mots_cles = $index_sujets;
	
	$mots_cles .= import_records::get_mots_cles();
	
	$mots_cles ? $index_matieres = strip_empty_words($mots_cles) : $index_matieres = '';
	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' where notice_id='$notice_id' " ;
	pmb_mysql_query($rqt_maj);
	
	global $bulletin_ex, $info_207 ;
	//Cas des périodiques
	if ($bibliographic_level_origine=='s') {
		// c'est une notice de pério, elle a été insérée en monographie
		//Notice chapeau existe-t-elle déjà ?
		$requete="select notice_id from notices where tit1='".addslashes(clean_string($tit[0]['a']))."' and niveau_hierar='1' and niveau_biblio='s'";
		$resultat=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($resultat)) {
			//Si oui, récupération id et destruction de la dite notice
			$chapeau_id=pmb_mysql_result($resultat,0,0);	
			$requete="delete from notices where notice_id='$notice_id' ";
			$resultat=pmb_mysql_query($requete);
			$requete="delete from responsability where responsability_notice='$notice_id' ";
			$resultat=pmb_mysql_query($requete);
			$notice_id = $chapeau_id ; 
			//Bulletin existe-t-il ?
			if ($info_207[0]) $num_bull = $info_207[0] ;
				else $num_bull = $tit[0]['a'] ;
			$requete="select bulletin_id from bulletins where bulletin_numero='".addslashes($num_bull)."' and bulletin_notice=$chapeau_id";
			$resultat=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($resultat)) {
				//Si oui, récupération id bulletin
				$bulletin_ex=pmb_mysql_result($resultat,0,0);
				} else {
					//Si non, création bulltin
					$info=array();
					$bulletin=new bulletinage("",$chapeau_id);
					$info['bul_titre']=addslashes("Bulletin N° ".$num_bull);
					$info['bul_no']=addslashes($num_bull);
					$info['bul_date']=addslashes($num_bull);
					$bulletin_ex=$bulletin->update($info);
					}
			} else {
				//Si non, update notice chapeau et création bulletin
				$requete="update notices set niveau_biblio='s', niveau_hierar='1' where notice_id='$notice_id' ";
				$resultat=pmb_mysql_query($requete);

				$info=array();
				$bulletin=new bulletinage("",$notice_id);
				$info['bul_titre']=addslashes("Bulletin N° ".$num_bull);
				$info['bul_no']=addslashes($num_bull);
				$info['bul_date']=addslashes($num_bull);
				$bulletin_ex=$bulletin->update($info);
				}
		} else $bulletin_ex=0;
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('tence');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}