<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_indexpresse_references.inc.php,v 1.2 2022/10/25 08:20:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");
require_once($class_path."/serials.class.php");

function recup_noticeunimarc_suite($notice) {
    global $info_461,$info_461_t,$info_461_v;
    
	$record = new iso2709_record($notice, AUTO_UPDATE);
    $info_461=$record->get_subfield("461","t","v");
    $info_461_t=$record->get_subfield("461","t");
    $info_461_v=$record->get_subfield("461","v");

} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $id_unimarc, $issn_011, $tit_200a, $editeur_date;
	
    global $info_461,$info_461_t,$info_461_v;
    global $notice_id;
    global $date_complete,$year;
    	
    global $bulletin_ex;
    
    if($id_unimarc){
    	$requete = "UPDATE notices SET thumbnail_url = 'https://vignette.indexpresse.fr/vignetteB.asp?not=" . intval($id_unimarc) . "' WHERE notice_id = ".$notice_id;
		pmb_mysql_query($requete);
	}

	if (is_array($info_461)) {
        $chapeau_id=0;
		//Notice chapeau existe-t-elle, Recherche sur le titre
		$requete="select notice_id from notices where tit1 LIKE '".addslashes($info_461_t[0])."' and niveau_hierar='1' and niveau_biblio='s'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat) == 1) {
			$chapeau_id=pmb_mysql_result($resultat,0,0);
		}elseif(pmb_mysql_num_rows($resultat) > 1){
            while($line=pmb_mysql_fetch_array($resultat)){
				if(!$chapeau_id){
					$chapeau_id=$line['notice_id'];
				}
			}
		}
		
		if ($chapeau_id) {
			//Bulletin existe-t-il ?
			$requete="select bulletin_id from bulletins where bulletin_numero LIKE '%".addslashes(preg_replace("/^[ ]+|^[0]+|^[nN°]+|[ ]+$/i","",$info_461_v[0]))."%' and bulletin_notice=$chapeau_id";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat) == 1) {
				$bulletin_id=pmb_mysql_result($resultat,0,0);
			}elseif(pmb_mysql_num_rows($resultat) > 1){
				while($line=pmb_mysql_fetch_array($resultat)){
					if(!$bulletin_id){
						$bulletin_id=$line['bulletin_id'];
					}
				}
			}else{
				$bulletin=new bulletinage("",$chapeau_id);
				$info=array();
				$info['bul_no']=$info_461_v[0];
				if(isset($editeur_date[0])) {
					$info['bul_date']= $editeur_date[0];
					$date =  date_create_from_format('d/m/y', $editeur_date[0]);
					$info['date_date']= date_format($date, 'Y-m-d');
					$year = date_format($date, 'Y');
				} else {
					$info['bul_date']= '';
					$info['date_date']= '';
					$year = '';
				}
				$bulletin_id=$bulletin->update($info);
			}
		}

		//On regarde si l'article existe
		$requete="select notice_id from notices, analysis where index_sew LIKE ' ".addslashes(strip_empty_words(implode (" ; ",$tit_200a)))." ' and niveau_hierar='2' and niveau_biblio='a' and analysis_bulletin='".$bulletin_id."' and analysis_notice=notice_id LIMIT 1";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			notice::del_notice($notice_id);
			$bulletin_ex=0;
		} else {
			//Notice objet ?
			if($bulletin_id){
				//Passage de la notice en article
				$requete="update notices set niveau_biblio='a', niveau_hierar='2', typdoc='b', year='".addslashes($year)."', date_parution='".addslashes($date_complete)."', tparent_id='0', tnvol='', ed1_id=0, ed2_id=0, coll_id=0, subcoll_id=0, nocoll='', mention_edition='', ill='', size='', accomp='', code='', prix='' where notice_id=$notice_id";
				pmb_mysql_query($requete);
				$requete="insert into analysis (analysis_bulletin,analysis_notice) values($bulletin_id,$notice_id)";
				pmb_mysql_query($requete);
				$bulletin_ex=$bulletin_id;
			}
		}
	}
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('bdp_without_categ');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}
