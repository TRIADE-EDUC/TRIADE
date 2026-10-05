<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2bretagne.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/marc_table.class.php");
require_once("$class_path/category.class.php");
require_once($base_path."/admin/convert/convert.class.php");

class pmb2bretagne extends convert {

	public static function _export_notice_($id,$keep_expl=0,$params=array()) {
		global $charset;
		$requete="select * from notices where notice_id=$id";
		$resultat=pmb_mysql_query($requete);
		
		$rn=pmb_mysql_fetch_object($resultat);
		
		$dt=$rn->typdoc;
		$bl=$rn->niveau_biblio;
		
		$notice ="<notice>\n";
		$notice.="  <rs>n</rs>\n";
	  	$notice.="  <dt>".$dt."</dt>\n";
	  	$notice.="  <bl>".$bl."</bl>\n";
	  	$notice.="  <hl>*</hl>\n";
	  	$notice.="  <el>1</el>\n";
	  	$notice.="  <ru>i</ru>\n";
		
		
		//ISBN ou autre et PRIX
		if ($rn->code || $rn->prix ) {
			$notice_prix_code_temp="";
			if ($rn->code) $notice_prix_code_temp.="    <s c='a'>".htmlspecialchars($rn->code,ENT_QUOTES,$charset)."</s>\n";
			if ($rn->prix) $notice_prix_code_temp.="    <s c='d'>".htmlspecialchars($rn->prix,ENT_QUOTES,$charset)."</s>\n";
			if ($notice_prix_code_temp)
				$notice.="  <f c='010' ind='  '>\n".$notice_prix_code_temp."  </f>\n";
		}
		
		//Langues
		$notice_langue_temp="";
		$notice_org_langue_temp="";
		$rqttmp_lang = "select code_langue from notices_langues where num_notice='$rn->notice_id' and type_langue=0 order by ordre_langue ";
		$restmp_lang = pmb_mysql_query($rqttmp_lang);
		while ($tmp_lang = pmb_mysql_fetch_object($restmp_lang)) $notice_langue_temp.="    <s c='a'>".htmlspecialchars($tmp_lang->code_langue,ENT_QUOTES,$charset)."</s>\n";  
	
		$rqttmp_lang = "select code_langue from notices_langues where num_notice='$rn->notice_id' and type_langue=1 order by ordre_langue ";
		$restmp_lang = pmb_mysql_query($rqttmp_lang);
		while ($tmp_lang = pmb_mysql_fetch_object($restmp_lang)) $notice_org_langue_temp.="    <s c='c'>".htmlspecialchars($tmp_lang->code_langue,ENT_QUOTES,$charset)."</s>\n";
	
		if ($notice_langue_temp || $notice_org_langue_temp) {
			$notice.="  <f c='101' ind='  '>\n";
			$notice.=$notice_langue_temp ;
			$notice.=$notice_org_langue_temp ;
			$notice.="  </f>\n";
			}
		
		//Titres
		if ($rn->tit1) {
			$notice.="  <f c='200' ind='1 '>\n";
		    $notice.="    <s c='a'>".htmlspecialchars($rn->tit1,ENT_QUOTES,$charset)."</s>\n";
			if ($rn->tit2) {
			    $notice.="    <s c='c'>".htmlspecialchars($rn->tit2,ENT_QUOTES,$charset)."</s>\n";
			}
			if ($rn->tit3) {
			    $notice.="    <s c='d'>".htmlspecialchars($rn->tit3,ENT_QUOTES,$charset)."</s>\n";
			}
			if ($rn->tit4) {
			    $notice.="    <s c='e'>".htmlspecialchars($rn->tit4,ENT_QUOTES,$charset)."</s>\n";
			}
			$notice.="  </f>\n";
		}
		
		//Mention d'édition
		if ($rn->mention_edition) {
			$notice.="  <f c='205' ind='  '>\n";
			$notice.="    <s c='a'>".htmlspecialchars($rn->mention_edition,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
	
		//Editeur
		if ($rn->ed1_id) {
		    $requete="select * from publishers where ed_id=".$rn->ed1_id;
			$resultat=pmb_mysql_query($requete);
			$red=pmb_mysql_fetch_object($resultat);
			$notice.="  <f c='210' ind='  '>\n";
			$notice.="    <s c='c'>".htmlspecialchars($red->ed_name,ENT_QUOTES,$charset)."</s>\n";
			if ($red->ed_ville) $notice.="    <s c='a'>".htmlspecialchars($red->ed_ville,ENT_QUOTES,$charset)."</s>\n";
			//Year
			if ($rn->year) {
				$notice.="    <s c='d'>".htmlspecialchars($rn->year,ENT_QUOTES,$charset)."</s>\n";
			}
			$notice.="  </f>\n";
		}
		if ($rn->ed2_id) {
		    $requete="select * from publishers where ed_id=".$rn->ed2_id;
			$resultat=pmb_mysql_query($requete);
			$red=pmb_mysql_fetch_object($resultat);
			$notice.="  <f c='210' ind='  '>\n";
			$notice.="    <s c='c'>".htmlspecialchars($red->ed_name,ENT_QUOTES,$charset)."</s>\n";
			if ($red->ed_ville) $notice.="    <s c='a'>".htmlspecialchars($red->ed_ville,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
			
		//Collation
		if ($rn->npages || $rn->ill || $rn->size || $rn->accomp) {
		    $notice.="  <f c='215' ind='  '>\n";
		    if ($rn->npages) $notice.="    <s c='a'>".htmlspecialchars($rn->npages,ENT_QUOTES,$charset)."</s>\n";
			if ($rn->ill)    $notice.="    <s c='c'>".htmlspecialchars($rn->ill,ENT_QUOTES,$charset)."</s>\n";
			if ($rn->size)   $notice.="    <s c='d'>".htmlspecialchars($rn->size,ENT_QUOTES,$charset)."</s>\n";
			if ($rn->accomp) $notice.="    <s c='e'>".htmlspecialchars($rn->accomp,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
			}
		
		//Collection
		if ($rn->coll_id) {
			$requete="select collection_name from collections where collection_id=".$rn->coll_id;
			$resultat=pmb_mysql_query($requete);
			$notice.="  <f c='225' ind='  '>\n";
			$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,0,0),ENT_QUOTES,$charset)."</s>\n";	
			//sous-collection
			if ($rn->subcoll_id){
				$requete="select * from sub_collections where sub_coll_id=".$rn->subcoll_id;
				$resultat=pmb_mysql_query($requete);		    
				$subcoll=pmb_mysql_fetch_object($resultat);
				$notice.="    <s c='i'>".htmlspecialchars($subcoll->sub_coll_name,ENT_QUOTES,$charset)."</s>\n";
			}
			if ($rn->nocoll) $notice.="    <s c='v'>".htmlspecialchars($rn->nocoll,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
		
		//Notes
		//Générale
		if ($rn->n_gen) {
		    $notice.="  <f c='300' ind='  '>\n";
			$notice.="    <s c='a'>".htmlspecialchars($rn->n_gen,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
		//de contenu
		if ($rn->n_contenu) {
		    $notice.="  <f c='327' ind='  '>\n";
			$notice.="    <s c='a'>".htmlspecialchars($rn->n_contenu,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
		//Résumé
		if ($rn->n_resume) {
		    $notice.="  <f c='330' ind='  '>\n";
			$notice.="    <s c='a'>".htmlspecialchars($rn->n_resume,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
		
		//dewey
		if ($rn->indexint) {
			$requete = "select * from indexint where indexint_id=".$rn -> indexint;
			$resultat = pmb_mysql_query($requete);
			if ($code_dewey=pmb_mysql_fetch_object($resultat)) {
				$notice.="  <f c='676' ind='  '>\n";
				$notice.="    <s c='a'>".htmlspecialchars( $code_dewey -> indexint_name,ENT_QUOTES,$charset)."</s>\n";
				$notice.="    <s c='l'>".htmlspecialchars( $code_dewey -> indexint_comment,ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";
				}
		}
		
		//Titre de série
		$serie="";
		if ($rn->tparent_id!=0 || $rn->tnvol!==false) {
			$requete="select serie_name from series where serie_id=".$rn->tparent_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) $serie=pmb_mysql_result($resultat,0,0);
			$notice_461temp="";
			if ($serie!=="") $notice_461temp.="    <s c='t'>".htmlspecialchars($serie,ENT_QUOTES,$charset)."</s>\n";
			if ($rn->tnvol) $notice_461temp.="    <s c='v'>".htmlspecialchars($rn->tnvol,ENT_QUOTES,$charset)."</s>\n";
			if ($notice_461temp) $notice.="  <f c='461' ind='  '>\n".$notice_461temp."  </f>\n";
		}
		
		//Auteurs
		$requete = "SELECT author_name, author_rejete, author_type, responsability_fonction, responsability_type ";
		$requete .= "FROM authors, responsability where responsability_notice=$id and responsability_author=author_id ";
		$requete .= "ORDER BY responsability_type, responsability_ordre, author_type, responsability_fonction";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
			$resptype=pmb_mysql_result($resultat,$i, 4);
			$prenom=pmb_mysql_result($resultat,$i, 1);
		    if (!$resptype) {
				//Auteur principal
				$notice.="  <f c='700' ind='  '>\n";
			
			if (!$prenom) {
				$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 0),ENT_QUOTES,$charset)."</s>\n";			
			} 
			else {
				$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 0),ENT_QUOTES,$charset)."</s>\n";
				$notice.="    <s c='b'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 1),ENT_QUOTES,$charset)."</s>\n";
			}
			$notice.="    <s c='4'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 3),ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";		
			}
			if ($resptype==1) {
				//Co-auteurs
				$notice.="  <f c='701' ind='  '>\n";
				if (!$prenom) {				
					$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 0),ENT_QUOTES,$charset)."</s>\n";
				} 
				else {
					$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 0),ENT_QUOTES,$charset)."</s>\n";			
					$notice.="    <s c='b'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 1),ENT_QUOTES,$charset)."</s>\n";
				}
				$notice.="    <s c='4'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 3),ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";		
			}
			if ($resptype==2) {
			//Auteurs secondaires
			$notice.="  <f c='702' ind='  '>\n";
			if (!$prenom) {
					$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 0),ENT_QUOTES,$charset)."</s>\n";
			}
			else {
			$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 0),ENT_QUOTES,$charset)."</s>\n";				
			$notice.="    <s c='b'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 1),ENT_QUOTES,$charset)."</s>\n";
			}
			$notice.="    <s c='4'>".htmlspecialchars(pmb_mysql_result($resultat,$i, 3),ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
			}
			}
		}	
		
		//Lien
		if ($rn->lien) {
		    $notice.="  <f c='856' ind='  '>\n";
			$notice.="    <s c='u'>".htmlspecialchars($rn->lien,ENT_QUOTES,$charset)."</s>\n";
			if ($rn->eformat) $notice.="    <s c='q'>".htmlspecialchars($rn->eformat,ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
		
		
		//Périodique
		if ($rn->niveau_biblio=="a") {
			//Récupération du titre du périodique
			$requete="select tit1,bulletin_numero,bulletin_notice,mention_date, date_date, bulletin_titre from notices, bulletins, analysis where analysis_notice=$id and analysis_bulletin=bulletin_id and bulletin_notice=notice_id";
			$resultat=pmb_mysql_query($requete);
			$r_bull=@pmb_mysql_fetch_object($resultat);
			$data_bull="";
			if (($r_bull)&&($r_bull->tit1)) {
				if ($r_bull->tit1) $data_bull.="    <s c='t'>".htmlspecialchars($r_bull->tit1,ENT_QUOTES,$charset)."</s>\n";
				if ($r_bull->bulletin_numero) $data_bull.="	  <s c='v'>".htmlspecialchars($r_bull->bulletin_numero,ENT_QUOTES,$charset)."</s>\n";
				if ($r_bull->mention_date) $data_bull.="    <s c='d'>".htmlspecialchars($r_bull->mention_date,ENT_QUOTES,$charset)."</s>\n";
				if ($r_bull->bulletin_titre) $data_bull.="    <s c='u'>".htmlspecialchars($r_bull->bulletin_titre,ENT_QUOTES,$charset)."</s>\n";
				if ($r_bull->date_date) $data_bull.="    <s c='e'>".htmlspecialchars($r_bull->date_date,ENT_QUOTES,$charset)."</s>\n";
				if ($rn->npages) $data_bull.="    <s c='p'>".htmlspecialchars($rn->npages,ENT_QUOTES,$charset)."</s>\n";
			}
			if ($data_bull) $notice.="  <f c='464' ind='  '>\n".$data_bull."  </f>\n";
		}
		
		//Mots_clés
		if ($rn->index_l) {
			global $pmb_keyword_sep;
			$tmp=explode($pmb_keyword_sep,$rn->index_l);
			foreach ( $tmp as $value ) {
	       		if($tmp2=trim($value)){
	       			$notice.="  <f c='610' ind='  '>\n";
					$notice.="    <s c='a'>".htmlspecialchars($tmp2,ENT_QUOTES,$charset)."</s>\n";
					$notice.="  </f>\n";
	       		}
			}
		}
		
		$requete = "SELECT libelle_categorie FROM categories, notices_categories ";
		$requete .= "WHERE notcateg_notice=$id AND categories.num_noeud = notices_categories.num_noeud ";
		$requete .= "ORDER BY ordre_categorie, libelle_categorie ";
		$resultat=pmb_mysql_query($requete);
		
		//Descripteurs
		if (pmb_mysql_num_rows($resultat)) {
		    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$notice.="  <f c='606' ind='  '>\n";
				$notice.="     <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i),ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";
			}
		}
			
		//Thème(s) 
		$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=$id and ncv.notices_custom_champ=nc.idchamp and name='theme' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$notice.="  <f c='900' ind='  '>\n";
				$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i),ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";
			}
		}
		//Genre(s)
		$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=$id and ncv.notices_custom_champ=nc.idchamp and name='genre' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$notice.="  <f c='901' ind='  '>\n";
				$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i),ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";
			}
		}
		//Niveau
		$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=$id and ncv.notices_custom_champ=nc.idchamp and name='niveau' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$notice.="  <f c='906' ind='  '>\n";
				$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i),ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";
			}
		}
		//Discipline
		$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=$id and ncv.notices_custom_champ=nc.idchamp and name='discipline' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$notice.="  <f c='902' ind='  '>\n";
				$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,$i),ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";
			}
		}
		//Année de péremption
		$requete="select ncv.notices_custom_integer from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=$id and ncv.notices_custom_champ=nc.idchamp and name='annee_peremption'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    $notice.="  <f c='903' ind='  '>\n";
			$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,0),ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
		//Date de saisie
		$requete="select ncv.notices_custom_date from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=$id and ncv.notices_custom_champ=nc.idchamp and name='date_creation'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
				$notice.="  <f c='904' ind='  '>\n";
				$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,0),ENT_QUOTES,$charset)."</s>\n";
				$notice.="  </f>\n";
			}
		//Type doc
		$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=$id and ncv.notices_custom_champ=nc.idchamp and name='type_nature' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    $notice.="  <f c='905' ind='  '>\n";
			$notice.="    <s c='a'>".htmlspecialchars(pmb_mysql_result($resultat,0),ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}
		//Origine
		$requete="select orinot_nom from notices, origine_notice where notice_id=$id and origine_catalogage=orinot_id";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    $notice.="  <f c='801' ind='  '>\n";
			$notice.="    <s c='b'>".htmlspecialchars(pmb_mysql_result($resultat,0),ENT_QUOTES,$charset)."</s>\n";
			$notice.="  </f>\n";
		}	
		
		// Ajout, eventuel, des exemplaires :
		if($keep_expl) {
			$requete = "select expl_cb, expl_typdoc, expl_cote, expl_section, expl_statut, expl_note, expl_comment from exemplaires where expl_notice = $id";
			$resultat = pmb_mysql_query($requete);
			$nb = pmb_mysql_num_rows($resultat);
			for($i=0; $i < $nb ; $i++) {
				$expl =@pmb_mysql_fetch_object($resultat);
				$notice.="  <f c='995' ind='  '>\n";
				if($expl->expl_cb && $expl->expl_cb != "") {
					$notice.="    <s c='f'>".htmlspecialchars($expl->expl_cb,ENT_QUOTES,$charset)."</s>\n";
				}
				if($expl->expl_typdoc && $expl->expl_typdoc != "") {
					$notice.="    <s c='r'>".htmlspecialchars($expl->expl_typdoc,ENT_QUOTES,$charset)."</s>\n";
				}
				if($expl->expl_cote && $expl->expl_cote != "") {
					$notice.="    <s c='k'>".htmlspecialchars($expl->expl_cote,ENT_QUOTES,$charset)."</s>\n";
				}
				if($expl->expl_section && $expl->expl_section != "") {
					$notice.="    <s c='t'>".htmlspecialchars($expl->expl_section,ENT_QUOTES,$charset)."</s>\n";
				}
				if($expl->expl_statut && $expl->expl_statut != "") {
					$notice.="    <s c='q'>".htmlspecialchars($expl->expl_statut,ENT_QUOTES,$charset)."</s>\n";
				}
				if($expl->expl_note && $expl->expl_note != "") {
					$notice.="    <s c='u'>".htmlspecialchars($expl->expl_note,ENT_QUOTES,$charset)."</s>\n";
				}
				$notice.="  </f>\n";
			}
		}
		
		$notice.="</notice>\n";
		return $notice;
	}
}
