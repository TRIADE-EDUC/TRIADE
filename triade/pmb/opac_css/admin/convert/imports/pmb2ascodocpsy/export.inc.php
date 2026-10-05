<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export.inc.php,v 1.4 2022/01/04 08:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/marc_table.class.php");

function _export_($id,$keep_expl=0,$params=array()) {
	global $tab_type;
	
	//Pour les périodiques avec multi-support au niveau des états de collection
	$notice_suppl="";
	
	if(!is_array($tab_type)){
		$obj_type=new marc_list('doctype');
		$tab_type=$obj_type->table;
	}
	$requete="select * from notices where notice_id=$id";
	$resultat=pmb_mysql_query($requete);
	
	$rn=pmb_mysql_fetch_object($resultat);
	$notice = array();
	
	$notice["TYPE"]=$tab_type[$rn->typdoc];
	
	$notice["AUT"]=_make_export_authors($rn);
	if(($rn->typdoc == "p") || ($rn->typdoc == "t")){//Titre de périodique et Texte officiel le champs est vide
		$notice["AUT"]="";
	}
	
	if($rn->typdoc == "p"){//Titre de périodique le champs est vide
		$notice["TIT"]="";
	}else{
		$notice["TIT"]=_make_export_title($rn);
	}
	
	$notice["EDIT"]=_make_export_publishers_name($rn);
	if(($rn->typdoc == "s") || ($rn->typdoc == "p") || ($rn->typdoc == "t")){//Article, Titre de périodique et Texte officiel le champs est vide
		$notice["EDIT"]="";
	}
	
	$notice["LIEU"]= _make_export_publishers_lieu($rn);
	if(($rn->typdoc == "s") || ($rn->typdoc == "p") || ($rn->typdoc == "t")){//Article, Titre de périodique et Texte officiel le champs est vide
		$notice["LIEU"]="";
	}elseif(($rn->typdoc == "q") && ($notice["LIEU"] == "[s.n.]")){//Document en ligne = Rapport
		$notice["LIEU"]="";
	}
	
	if ($rn->npages) {
		$notice["PAGE"]=$rn->npages;
	} else {
		$notice["PAGE"]= "[s.p.]";
	}
	if(($rn->typdoc == "s") || ($rn->typdoc == "p") || ($rn->typdoc == "t") || ($rn->typdoc == "m")){//Article, Titre de périodique, Texte officiel et Document multimédia  le champs est vide
		$notice["PAGE"]="";
	}
	
	if ($rn->year) {
		$notice["DATE"]=$rn->year;
	} else {
		$notice["DATE"]= "[s.d.]";
	}
	if(($rn->typdoc == "p") || ($rn->typdoc == "t")){//Titre de périodique et Texte officiel le champs est vide
		$notice["DATE"]="";
	}
	
	$notice["MOTCLE"]=_make_export_branch_thesaurus($rn, "MOTCLE");
	if(($rn->typdoc == "p")){//Titre de périodique le champs est vide
		$notice["MOTCLE"]="";
	}
	
	$notice["NOMP"]=_make_export_branch_thesaurus($rn, "NOMP");
	if(($rn->typdoc == "p")){//Titre de périodique le champs est vide
		$notice["NOMP"]="";
	}
	
	if ($rn->n_gen) {
		$notice["NOTES"]=$rn->n_gen;
	}else{
		$notice["NOTES"]="";
	}
	if($rn->typdoc == "t"){//Texte officiel le champs est vide
		$notice["NOTES"]="";
	}
	
	$notice["PRODFICH"]=_make_export_cp_prodfich($rn);
	
	$notice["LOC"]=_make_export_cp_loc($rn);
	if((($rn->typdoc == "a") || ($rn->typdoc == "h") || ($rn->typdoc == "o")|| ($rn->typdoc == "r")) && (!$notice["LOC"])){// Ouvrage Congrès, Thèse et mémoire le champs ne doit pas être vide
		$notice["LOC"]="[vide]";
	}elseif(($rn->typdoc == "s") || ($rn->typdoc == "p") || ($rn->typdoc == "t") || ($rn->typdoc == "q")){//Article, Titre de périodique, Texte officiel et Rapport le champs est vide
		$notice["LOC"]="";
	}
	
	if ($rn->coll_id && (($rn->typdoc == "a") || ($rn->typdoc == "h") || ($rn->typdoc == "q"))) {//N'est renseigné que pour Ouvrage, Congrès et Rapport
		$requete="select collection_name from collections where collection_id=".$rn->coll_id;
		$resultat=pmb_mysql_query($requete);
		$notice["COL"]=pmb_mysql_result($resultat,0,0);
		if ($rn->nocoll) $notice["COL"].=" ;".$rn->nocoll;
	}else{
		$notice["COL"]="";
	}
	
	$notice["THEME"]=_make_export_branch_thesaurus($rn, "THEME");
	if(($rn->typdoc == "p")){//Titre de périodique le champs est vide
		$notice["THEME"]="";
	}
	
	if ($rn->n_resume) {
		$notice["RESU"]=$rn->n_resume;
	}else{
		$notice["RESU"]="";
	}
	if($rn->typdoc == "p"){//Titre de périodique le champs est vide
		$notice["RESU"]="";
	}
	
	$notice["SUPPORT"]="";
	if($rn->typdoc == "m"){//Document multimédia
		$notice["SUPPORT"]=_make_export_cp_support($rn);
	}
	
	$notice["SUPPORTPERIO"]="";
	if($rn->typdoc == "p"){// Titre de périodique
		if($params["ascodoc_supportperio"]){
			$notice["SUPPORTPERIO"]=$params["ascodoc_supportperio"];
		}else{
			$requete = "SELECT DISTINCT archtype_libelle FROM collections_state JOIN arch_type ON archtype_id=collstate_type WHERE id_serial=".$rn->notice_id;
			$resultat = pmb_mysql_query($requete);
			if ($resultat && pmb_mysql_num_rows($resultat) > 0) {
				if(pmb_mysql_num_rows($resultat) == 1){
					$notice["SUPPORTPERIO"]=pmb_mysql_result($resultat,0,0);
				}else{
					//On traite le 1er
					$perionum = pmb_mysql_fetch_object($resultat);
					$notice["SUPPORTPERIO"]=$perionum->archtype_libelle;
					//Puis les autres
					while($perionum = pmb_mysql_fetch_object($resultat)){
						$params["ascodoc_supportperio"]=$perionum->archtype_libelle;
						$notice_suppl.=_export_($id,$keep_expl,$params);
					}
				}
			}else{
				$notice["SUPPORTPERIO"]="papier";
			}
		}
		
	}
	
	$notice["LIEN"]="";
	if ($rn->lien) {
		if (_check_url_($rn->lien)) {
			$notice["LIEN"]=$rn->lien;
		}
	}else{
		$notice["LIEN"]=_make_export_cp_lien($rn);
	}
	
	$notice["VOL"]="";
	if((($rn->typdoc == "s") || ($rn->typdoc == "q")) && ($rn->niveau_biblio == "m")){// Article ou Rapport
		$notice["VOL"]=$rn->tnvol;
	}elseif((($rn->typdoc == "s") || ($rn->typdoc == "q")) && ($rn->niveau_biblio == "a")){
		$notice["VOL"]=_make_export_numero_bull($rn,2);
	}
	
	$notice["CANDES"]=_make_export_branch_thesaurus($rn, "CANDES");
	if($rn->typdoc == "p"){// Titre de périodique
		$notice["CANDES"]="";
	}
	
	//CONGRTIT, CONGRLIE, CONGRDAT, CONGRNUM
	$notice["CONGRTIT"]="";
	$notice["CONGRLIE"]="";
	$notice["CONGRDAT"]="";
	$notice["CONGRNUM"]="";
	if(($rn->typdoc == "a") || ($rn->typdoc == "h")){// Ouvrage ou Congrès
		//Congrès
		$requete = "SELECT author_name, author_rejete, author_numero, author_lieu, author_date ";
		$requete .= "FROM authors, responsability where responsability_notice=".$rn->notice_id." and responsability_author=author_id ";
		$requete .= "and author_type='72' ";
		$requete .= "ORDER BY responsability_type, responsability_ordre, author_type, responsability_fonction LIMIT 1";
		$resultat=pmb_mysql_query($requete);
		$tmp_array = array();
		if (pmb_mysql_num_rows($resultat)) {
			for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$notice["CONGRTIT"]=pmb_mysql_result($resultat,$i, 0);
				$notice["CONGRLIE"]=pmb_mysql_result($resultat,$i, 3);
				$notice["CONGRDAT"]=pmb_mysql_result($resultat,$i, 4);
				$notice["CONGRNUM"]=pmb_mysql_result($resultat,$i, 2);
			}
		}
	}
	
	$notice["ISBNISSN"]="";
	if(($rn->typdoc == "a") || ($rn->typdoc == "h") || ($rn->typdoc == "p")){//Ouvrage ou Congrès ou Titre de périodique
		if ($rn->code) {
			$notice["ISBNISSN"]=$rn->code;
		}else{
			$notice["ISBNISSN"]="0000-0000";
		}
	}elseif($rn->typdoc == "q"){//Rapport
		$notice["ISBNISSN"]=$rn->code;
	}
	
	$notice["REED"]="";
	if(($rn->typdoc == "a") || ($rn->typdoc == "h") || ($rn->typdoc == "q")){//Ouvrage ou Congrès ou Rapport
		$notice["REED"]=$rn->mention_edition;
	}
	
	$notice["DIPSPE"]="";
	if(($rn->typdoc == "o") || ($rn->typdoc == "r")){
		$notice["DIPSPE"]=_make_export_cp_dipspe($rn);//Thèse ou mémoire
	}
	
	$notice["REV"]="";
	if(($rn->typdoc == "s") || ($rn->typdoc == "t") || ($rn->typdoc == "q")){//Article ou Titre de périodique ou Texte officiel ou Document en ligne = Rapport
		$notice["REV"]=_make_export_title_rev($rn);
	}elseif($rn->typdoc == "p"){//Titre de périodique
		$notice["REV"]=_make_export_title($rn);
	}
	
	$notice["VIEPERIO"]="";
	if($rn->typdoc == "p"){//Titre de périodique
		if ($rn->year) {
			$notice["VIEPERIO"]=$rn->year;
		}else{
			$notice["VIEPERIO"]= "[s.d.]";
		}
	}
	
	$notice["ETATCOL"]="";
	if($rn->typdoc == "p"){//Titre de périodique
		$val_tmp="";
		$requete = "select collections_state.*, archempla_libelle from collections_state JOIN arch_type ON archtype_id=collstate_type JOIN arch_emplacement ON archempla_id=collstate_emplacement where id_serial=".$rn->notice_id." AND archtype_libelle='".addslashes($notice["SUPPORTPERIO"])."'";
		$resultat = pmb_mysql_query($requete);
		if ($resultat && pmb_mysql_num_rows($resultat) > 0) {
			while($etatcoll = pmb_mysql_fetch_object($resultat)){
				$matches_coll=array();
				if($val_tmp) $val_tmp.="/";
				if(preg_match("/asco[0]{0,1}([0-9]+)/i",$etatcoll->archempla_libelle,$matches_coll)){
					$val_tmp2=(($etatcoll->collstate_lacune != "")?$etatcoll->collstate_lacune." ; ":"").(($etatcoll->collstate_origine != "")?$etatcoll->collstate_origine." ; ":"").$etatcoll->state_collections;
					if(trim($val_tmp2)){
						$val_tmp.=$matches_coll[1]." : ".$val_tmp2;
					}else{
						$val_tmp.=$matches_coll[1];
					}
					
				}elseif(preg_match("/(.*) -([^-])*/i",$etatcoll->archempla_libelle,$matches_coll)){
					$val_tmp2=(($etatcoll->collstate_lacune != "")?$etatcoll->collstate_lacune." ; ":"").(($etatcoll->collstate_origine != "")?$etatcoll->collstate_origine." ; ":"").$etatcoll->state_collections;
					if(trim($val_tmp2)){
						$val_tmp.=$matches_coll[1]." : ".$val_tmp2;
					}else{
						$val_tmp.=$matches_coll[1];
					}
				}else{
					$val_tmp2=(($etatcoll->collstate_lacune != "")?$etatcoll->collstate_lacune." ; ":"").(($etatcoll->collstate_origine != "")?$etatcoll->collstate_origine." ; ":"").$etatcoll->state_collections;
					if(trim($val_tmp2)){
						$val_tmp.=$etatcoll->archempla_libelle." : ".$val_tmp2;
					}else{
						$val_tmp.=$etatcoll->archempla_libelle;
					}
				}
			}
		}else{
			$tmp_array=array();
			$requete="select ncv.notices_custom_text from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_etatcol' ";
			$resultat=pmb_mysql_query($requete);
			if ($resultat && pmb_mysql_num_rows($resultat)) {
				for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
					$tmp_array[] = str_replace("\t","    ",pmb_mysql_result($resultat,$i));
				}
				$val_tmp.= implode("/", $tmp_array);
			}
		}
		if(!$val_tmp)$val_tmp="[vide]";
		$notice["ETATCOL"]=$val_tmp;
	}
	
	$notice["NUM"]="";
	if($rn->typdoc == "s"){//Article
		$notice["NUM"]=_make_export_numero_bull($rn,1);
		if(!$notice["NUM"])$notice["NUM"]="[vide]";
	}elseif($rn->typdoc == "q"){//Document en ligne = Rapport
		$notice["NUM"]=_make_export_numero_bull($rn,1);
	}elseif($rn->typdoc == "t"){//Texte officiel
		$notice["NUM"]=_make_export_numero_bull($rn,0);
	}
	
	$notice["PDPF"]="";
	if($rn->typdoc == "s"){//Article
		$notice["PDPF"]=$rn->npages;
		if(!$notice["PDPF"])$notice["PDPF"]="[s.p.]";
	}
	
	$notice["NATTEXT"]="";
	if($rn->typdoc == "t"){//Texte officiel
		$notice["NATTEXT"]=_make_export_cp_nattext($rn);
	}
	
	$notice["DATETEXT"]="";
	if($rn->typdoc == "t"){//Texte officiel
		$notice["DATETEXT"]=_make_export_cp_datetext($rn);
	}
	
	$notice["DATEPUB"]="";
	if($rn->typdoc == "t"){//Texte officiel
		$notice["DATEPUB"]=$rn->date_parution;
	}
	
	$notice["NUMTEXOF"]="";
	if($rn->typdoc == "t"){//Texte officiel
		$notice["NUMTEXOF"]=_make_export_cp_numtexof($rn);
	}
	
	$notice["DATEVALI"]="";
	if($rn->typdoc == "t"){//Texte officiel
		if($tmp =_make_export_cp_datevali($rn)){
			$notice["DATEVALI"]=$tmp;
		}elseif ($rn->update_date) {
			$notice["DATEVALI"]=substr($rn->update_date,0,10);
		}
	}
	
	$notice["ANNEXE"]="";
	if($rn->typdoc == "t"){//Texte officiel
		$notice["ANNEXE"]=_make_export_cp_annexe($rn);
	}
	
	$notice["LIENANNE"]="";
	if($rn->typdoc == "t"){//Texte officiel
		$notice["LIENANNE"]= _make_export_cp_lienanne($rn);
	}
	
	$notice["DATESAIS"]="";
	if(($rn->typdoc == "t") || ($rn->typdoc == "q")){//Texte officiel ou Document en ligne = Rapport
		$notice["DATESAIS"]=$rn->create_date;
	}
	
	$notice_str = implode("\t",array_map("nettoye_chaine",$notice))."\r\n";
	if($notice_suppl){
	      $notice_str.=$notice_suppl;
	}
	return $notice_str;
}


function nettoye_chaine($text){
	return str_replace(array("\t","\r\n","\n"),array(" "," "," "), $text);
}

function _make_export_authors($rn) {
	global $authors_function;
	
	$notice = "";
	
	//Auteurs (sauf congrès : exportés dans la fonction _make_export_congres)
	$requete = "SELECT author_name, author_rejete, author_type, responsability_fonction, responsability_type ";
	$requete .= "FROM authors, responsability where responsability_notice=".$rn->notice_id." and responsability_author=author_id ";
	$requete .= "and author_type<>'72' ";
	$requete .= "ORDER BY responsability_type, responsability_ordre, author_type, responsability_fonction";
	$resultat=pmb_mysql_query($requete);
	if (!$authors_function) {
		/*$authors_function=array("205"=>"Collab.","901"=>"Coord.","651"=>"Dir.","340"=>"Ed.",
			"440"=>"Ill.","080"=>"Préf.","730"=>"Trad.","075"=>"Postf.");*/
		$func=new marc_list("function");
		$authors_function=$func->table;
	}
	$tmp_array = array();
	if (pmb_mysql_num_rows($resultat)) {
		for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
			$prenom = pmb_mysql_result($resultat,$i, 1);
			$tmp = "";
			//$tmp.= trim(str_replace("-"," ",pmb_mysql_result($resultat,$i, 0)));
			$tmp.= trim(pmb_mysql_result($resultat,$i, 0));
			if ($prenom) $tmp.= " ".$prenom;
			/*$func_author = pmb_mysql_result($resultat,$i, 3);
			if (array_key_exists($func_author, $authors_function)) {
				$tmp .= " ".$authors_function[$func_author];
			}*/
			$tmp_array[] = $tmp;	
		}
	}
	if (count($tmp_array)) $notice.= implode("/", $tmp_array);
	else $notice.= "[s.n.]";

	return $notice;
}

function _make_export_title($rn) {
	
	$notice = "";
	//Titres
	if ($rn->tit1) {
	    $notice.=str_replace("/", "-", ucfirst($rn->tit1));
		if ($rn->tit2) {
		    $notice.=" : ".$rn->tit2;
		}
		if ($rn->tit4) {
		    $notice.=" : ".$rn->tit4;
		}
	}
	return $notice;
}

function _make_export_title_rev($rn) {
	global $charset;
	$notice = "";
	//Titre du périodique
	if ($rn->niveau_biblio=="a") {
		//Récupération du titre du périodique
		$requete="select tit1 from notices, bulletins, analysis where analysis_notice=".$rn->notice_id." and analysis_bulletin=bulletin_id and bulletin_notice=notice_id";
		$resultat=pmb_mysql_query($requete);
		$r_perio=@pmb_mysql_fetch_object($resultat);
		if (($r_perio)&&($r_perio->tit1)) {
			$notice .= mb_strtoupper($r_perio->tit1,$charset);
		}
	}
	return $notice;
}
//$dem == 0 -> Le champ numéro
//$dem == 1 -> Le numéro si possible sinon le champ numéro
//$dem == 2 -> Le volume si possible sinon rien
function _make_export_numero_bull($rn,$dem=0) {
	
	$notice = "";
	//Numéro de bulletin
	if ($rn->niveau_biblio=="a") {
		//Récupération du numéro de bulletin
		$requete = "select bulletin_numero from notices, bulletins, analysis where analysis_notice=".$rn->notice_id." and analysis_bulletin=bulletin_id and bulletin_notice=notice_id";
		$resultat = pmb_mysql_query($requete);
		$r_bull = @pmb_mysql_fetch_object($resultat);
		if (($r_bull) && ($r_bull->bulletin_numero)) {
			$matches = array();
			if (($dem != 0) && preg_match("/^(.+) vol (.+)$/i", $r_bull->bulletin_numero, $matches)) {
				if ($dem == 1) {//Numéro
					$notice .= trim($matches[1]);
				} else {//Volume
					$notice .= trim($matches[2]);
				}
			} elseif ($dem != 2) {
			      $notice .= $r_bull->bulletin_numero;
			}
		}
	}
	return $notice;
}

function _make_export_publishers_name($rn) {
	
	$notice = "";
	
	//Nom Editeur
	if ($rn->ed1_id || $rn->ed2_id) {
		if ($rn->ed1_id) {
		    $requete="select ed_name from publishers where ed_id=".$rn->ed1_id;
			$resultat=pmb_mysql_query($requete);
			$red=pmb_mysql_fetch_object($resultat);
			$notice.= ucfirst($red->ed_name);
		}
		if ($rn->ed2_id) {
		    $requete="select ed_name from publishers where ed_id=".$rn->ed2_id;
			$resultat=pmb_mysql_query($requete);
			$red=pmb_mysql_fetch_object($resultat);
			if ($rn->ed1_id) $notice.= "/"; 
			$notice.= ucfirst($red->ed_name);
		}
		//CP Editeurs supplémentaires
		$requete="SELECT ncv.notices_custom_integer FROM notices_custom_values ncv
				JOIN notices_custom nc ON nc.idchamp=ncv.notices_custom_champ
				WHERE ncv.notices_custom_origine=".$rn->notice_id." and nc.name='cp_editeur'";
		$resultat=pmb_mysql_query($requete);
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$requete="select ed_name from publishers where ed_id=".pmb_mysql_result($resultat,$i,0);
				$res=pmb_mysql_query($requete);
				if($res && pmb_mysql_num_rows($res)){
					$red=pmb_mysql_fetch_object($res);
					if ($notice) $notice.= "/";
					$notice.= ucfirst($red->ed_name);
				}
			}
		}
	} else {
		$notice.= "[s.n.]";	
	}
	
	return $notice;
}

function _make_export_publishers_lieu($rn) {
	
	$notice = "";
	
	//Lieu Editeur
	if ($rn->ed1_id || $rn->ed2_id) {
		if ($rn->ed1_id) {
		    $requete="select ed_ville from publishers where ed_id=".$rn->ed1_id;
			$resultat=pmb_mysql_query($requete);
			$red=pmb_mysql_fetch_object($resultat);
			if(trim($red->ed_ville)){
				$notice.= ucfirst($red->ed_ville);
			}else{
				$notice.= "[s.l.]";
			}
		}
		if ($rn->ed2_id) {
		    $requete="select ed_ville from publishers where ed_id=".$rn->ed2_id;
			$resultat=pmb_mysql_query($requete);
			$red=pmb_mysql_fetch_object($resultat);
			if ($rn->ed1_id) $notice.= "/"; 
			if(trim($red->ed_ville)){
				$notice.= ucfirst($red->ed_ville);
			}else{
				$notice.= "[s.l.]";
			}
		}
		//CP Editeurs supplémentaires
		$requete="SELECT ncv.notices_custom_integer FROM notices_custom_values ncv
				JOIN notices_custom nc ON nc.idchamp=ncv.notices_custom_champ
				WHERE ncv.notices_custom_origine=".$rn->notice_id." and nc.name='cp_editeur'";
		$resultat=pmb_mysql_query($requete);
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$requete="select ed_ville from publishers where ed_id=".pmb_mysql_result($resultat,$i,0);
				$res=pmb_mysql_query($requete);
				if($res && pmb_mysql_num_rows($res)){
					$red=pmb_mysql_fetch_object($res);
					if ($notice) $notice.= "/";
					if(trim($red->ed_ville)){
						$notice.= ucfirst($red->ed_ville);
					}else{
						$notice.= "[s.l.]";
					}
				}
			}
		}
	} else {
		$notice.= "[s.l.]";
	}
	
	return $notice;
}

function _make_export_cp_prodfich($rn) {
	
	$notice = "";
	$tmp_array = array();
	$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_prodfich' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
	    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
	    	$tmp2_array=explode("-",trim(strtolower(pmb_mysql_result($resultat,$i))));
	    	if($tmp2 = trim($tmp2_array[0])){
				$tmp_array[$tmp2] = $tmp2;
	    	}
	    }
		$notice.= implode("/", $tmp_array);
	}
	if(!$notice)$notice="[vide]";
	return $notice;
}

function _make_export_cp_lien($rn) {

	$notice = "";
	$tmp_array = array();
	$requete="select ncv.notices_custom_text from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_lien' ";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
			$url = pmb_mysql_result($resultat,$i);
			$tmp=explode("|", $url);
			if(count($tmp) == 2){
				$url=$tmp[0];
			}
			if (_check_url_($url)) $tmp_array[] = $url;
		}
		$notice.= implode(";", $tmp_array);
	}

	return $notice;
}

function _make_export_cp_lienanne($rn) {
	
	$notice = "";
	$tmp_array = array();
	$requete="select ncv.notices_custom_small_text, ncv.notices_custom_text from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_lienanne' ";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
	    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
	    	$url = pmb_mysql_result($resultat,$i,0);
	    	if(!$url)$url= pmb_mysql_result($resultat,$i,1);
	    	$tmp=explode("|", $url);
	    	if(count($tmp) == 2){
	    		$url=$tmp[0];
	    	}
	    	if (_check_url_($url)) $tmp_array[] = $url;
		}
		$notice.= implode(";", $tmp_array);
	}
	
	return $notice;
}

function _make_export_cp_annexe($rn) {
	
	$notice = "";
	$tmp_array = array();
	$requete="select ncv.notices_custom_small_text from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_annexe' ";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
	    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
	    	$tmp_array[] = ucfirst(pmb_mysql_result($resultat,$i));
		}
		$notice.= implode(";", $tmp_array);
	}
	
	return $notice;
}

function _make_export_cp_dipspe($rn) {
	
	$notice = "";
	$requete="select ncv.notices_custom_small_text from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_dipspe'";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		$notice.= pmb_mysql_result($resultat, 0);
	}
	if(!$notice)$notice="[s.n.]";
	return $notice;
}

function _make_export_cp_support($rn) {

	$notice = "";
	$tmp_array = array();
	$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and name='cp_support' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
			$tmp_array[] = trim(strtolower(pmb_mysql_result($resultat,$i)));
		}
		$notice.= implode("/", $tmp_array);
	}
	if(!$notice) $notice="[vide]";
	return $notice;
}

function _make_export_cp_loc($rn) {
	
	$notice = "";
	$tmp_array = array();
	$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and name='cp_loc' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
	    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
	    	$tmp=trim(strtolower(pmb_mysql_result($resultat,$i)));
	    	$tmp2_array=explode("-",$tmp);
	    	if($tmp2 = trim($tmp2_array[0])){
				$tmp_array[$tmp2] = $tmp2;
	    	}
	    }
	}
	//On regarde si il y a des exemplaires
	$req="";
	if($rn->niveau_biblio == "m" && $rn->niveau_hierar == "0"){
		$req="SELECT locdoc_codage_import FROM exemplaires JOIN docs_location ON expl_location=idlocation WHERE expl_notice=".$rn->notice_id." AND expl_bulletin=0";
	}elseif($rn->niveau_biblio == "a" && $rn->niveau_hierar == "2"){
		$req="SELECT locdoc_codage_import FROM analysis JOIN exemplaires ON expl_bulletin=analysis_bulletin AND expl_notice=0 JOIN docs_location ON expl_location=idlocation WHERE analysis_notice=".$rn->notice_id."";
	}
	if($req){
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$tmp=trim(strtolower(pmb_mysql_result($resultat,$i)));
	    		$tmp_array[$tmp] = $tmp;
			}
		}
	}
	
	if(count($tmp_array)){
		$notice.= implode("/", $tmp_array);
	}

	return $notice;
}

function _make_export_cp_nattext($rn) {
	global $charset;
	$notice = "";
	$tmp_array = array();
	$requete="select ncv.notices_custom_small_text from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_nattext'";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat) && trim(pmb_mysql_result($resultat, 0))) {
		$notice.= mb_strtoupper(pmb_mysql_result($resultat, 0),$charset);
	}else{
		$requete="select ncl.notices_custom_list_lib from notices_custom_lists ncl, notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and name='cp_nattext' and ncv.notices_custom_champ=ncl.notices_custom_champ and ncv.notices_custom_integer=ncl.notices_custom_list_value";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
				$tmp_array[] = trim(mb_strtoupper((pmb_mysql_result($resultat,$i)),$charset));
			}
			$notice.= implode("/", $tmp_array);
		}
	}
	if(!$notice)$notice="[vide]";
	return $notice;
}

function _make_export_cp_datetext($rn) {
	$notice = "";
	$requete="select ncv.notices_custom_date from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_datetext'";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		$notice.= strtoupper(pmb_mysql_result($resultat, 0));
	}
	
	return $notice;
}

function _make_export_cp_datevali($rn) {
	$notice = "";
	$requete="select ncv.notices_custom_date from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_datevali'";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		$notice.= strtoupper(pmb_mysql_result($resultat, 0));
	}

	return $notice;
}

function _make_export_cp_numtexof($rn) {
	$notice = "";
	$requete="select ncv.notices_custom_small_text from notices_custom_values ncv, notices_custom nc where ncv.notices_custom_origine=".$rn->notice_id." and ncv.notices_custom_champ=nc.idchamp and nc.name='cp_numtexof'";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		$notice.= strtoupper(pmb_mysql_result($resultat, 0));
	}
	
	return $notice;
}

function _make_export_branch_thesaurus($rn, $name) {
	global $charset;
	//Ancien fonctionnement
	$tmp_array=array();
	$notice = "";
	$requete = "SELECT libelle_categorie FROM categories, notices_categories, noeuds ";
	$requete .= "WHERE notcateg_notice=".$rn->notice_id." AND categories.num_noeud = notices_categories.num_noeud ";
	$requete .= "AND notices_categories.num_noeud = noeuds.id_noeud ";
	$requete .= "AND noeuds.num_parent IN (select num_noeud from categories where libelle_categorie='".$name."') ";
	$requete .= "ORDER BY ordre_categorie, libelle_categorie ";
	$resultat=pmb_mysql_query($requete);
	
	if (pmb_mysql_num_rows($resultat)) {
	    for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
	    	$tmp_array[] = trim(pmb_mysql_result($resultat,$i));
		}
	}
	
	//Nouveau fonctionnement
	switch ($name){
		case "MOTCLE"://thésaurus santéspy
			$req="SELECT id_thesaurus FROM thesaurus WHERE libelle_thesaurus LIKE '%SANTEPSY%'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 1)){
				$id_thes=pmb_mysql_result($res, 0,0);
				$restrict="";
				/*$req="SELECT noeuds.id_noeud FROM noeuds
					JOIN categories ON noeuds.id_noeud=categories.num_noeud
					WHERE noeuds.num_thesaurus=".$id_thes." AND libelle_categorie='CANDES'";*/
				$req="SELECT noeuds.id_noeud FROM noeuds
					WHERE noeuds.num_thesaurus=".$id_thes." AND autorite='CANDES'";
				$res=pmb_mysql_query($req);
				if($res && (pmb_mysql_num_rows($res) == 1)){
					$restrict=" AND num_parent != ".pmb_mysql_result($res, 0,0);
				}
				
				$requete="SELECT libelle_categorie FROM notices_categories
						JOIN categories ON notices_categories.num_noeud=categories.num_noeud
						JOIN noeuds ON notices_categories.num_noeud=noeuds.id_noeud
						WHERE notcateg_notice=".$rn->notice_id." AND categories.num_thesaurus=".$id_thes." ".$restrict."
						ORDER BY ordre_categorie, libelle_categorie ";
				$resultat=pmb_mysql_query($requete);
				
				if ($resultat && pmb_mysql_num_rows($resultat)) {
					for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
						$tmp_array[] = trim(pmb_mysql_result($resultat,$i));
					}
				}
			}
			break;
		case "THEME":
			$req="SELECT id_thesaurus FROM thesaurus WHERE libelle_thesaurus LIKE '%THEMES%'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 1)){
				$id_thes=pmb_mysql_result($res, 0,0);
				
				$requete="SELECT libelle_categorie FROM notices_categories
						JOIN categories ON notices_categories.num_noeud=categories.num_noeud
						WHERE notcateg_notice=".$rn->notice_id." AND categories.num_thesaurus=".$id_thes." 
						ORDER BY ordre_categorie, libelle_categorie ";
				$resultat=pmb_mysql_query($requete);
				
				if ($resultat && pmb_mysql_num_rows($resultat)) {
					for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
						$tmp_array[] = trim(pmb_mysql_result($resultat,$i));
					}
				}
			}
			break;
		case "NOMP":
			$requete = "SELECT libelle_categorie FROM categories, notices_categories, noeuds ";
			$requete .= "WHERE notcateg_notice=".$rn->notice_id." AND categories.num_noeud = notices_categories.num_noeud ";
			$requete .= "AND notices_categories.num_noeud = noeuds.id_noeud ";
			$requete .= "AND noeuds.num_parent IN (select num_noeud from categories where libelle_categorie='LISTE NOMS PROPRES') ";
			$requete .= "ORDER BY ordre_categorie, libelle_categorie ";
			$resultat=pmb_mysql_query($requete);
			
			if (pmb_mysql_num_rows($resultat)) {
				for ($i=0; $i<pmb_mysql_num_rows($resultat); $i++) {
					$tmp_array[] = trim(pmb_mysql_result($resultat,$i));
				}
			}
			break;
		default:
			break;
	}
	
	if(count($tmp_array)){
		$notice.= mb_strtoupper(implode("/", $tmp_array),$charset);
	}
	return $notice;
}

function _check_url_($url) {
	if ((substr(trim($url), 0, 7) == "http://") || (substr(trim($url), 0, 8) == "https://")) return true;
	else return false; 
}
