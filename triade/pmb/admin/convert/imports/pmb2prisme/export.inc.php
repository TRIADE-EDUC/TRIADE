<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export.inc.php,v 1.16 2018-07-10 06:42:45 dgoron Exp $

require_once("$class_path/marc_table.class.php");
require_once("$class_path/category.class.php");

function find_custom_field($nom) {
	$rqt = "SELECT idchamp FROM notices_custom WHERE name='" . addslashes($nom) . "'";
	$res = pmb_mysql_query($rqt);
	if (pmb_mysql_num_rows($res)>0)
		return pmb_mysql_result($res,0);
	else
		return 0;
}

//trouve le thesaurus avec le code et renvoi son id
function find_thesaurus($code) {
	$rqt = "SELECT num_thesaurus FROM noeuds WHERE autorite='" . $code . "'";
	$res = pmb_mysql_query($rqt);

	if (pmb_mysql_num_rows($res)>0)
		return pmb_mysql_result($res,0);
		else
			return 0;
}

function _export_($id,$keep_expl) {
	global $ty,$charset;
	global $tab_functions;
	global $mois, $mois_enrichis;
	
	if (!$ty) $ty=array_flip(array("REVUE"=>"v","LIVRE"=>"a","MEMOIRE"=>"b","DOCUMENT AUDIOVISUEL"=>"g","CDROM"=>"m","DOCUMENT EN LIGNE"=>"l"));
	if (!$tab_functions) $tab_functions=new marc_list('function');

	if (!$mois) {
		$mois=array(
			0=>"",
			1=>"janvier",
			2=>"fevrier",
			3=>"mars",
			4=>"avril",
			5=>"mai",
			6=>"juin",
			7=>"juillet",
			8=>"aout",
			9=>"septembre",
			10=>"octobre",
			11=>"novembre",
			12=>"decembre"
		);
		$mois_enrichis=array(
			0=>"",
			1=>"janvier",
			2=>"février",
			3=>"mars",
			4=>"avril",
			5=>"mai",
			6=>"juin",
			7=>"juillet",
			8=>"aout",
			9=>"septembre",
			10=>"octobre",
			11=>"novembre",
			12=>"décembre"
		);
	}
	
	if (!$m_thess) {
		$rqt = "SELECT count(1) FROM thesaurus WHERE active=1";
	 	$m_thess = pmb_mysql_result(pmb_mysql_query($rqt),0,0);
	}
	
	$notice="<notice>\n";
	$requete="SELECT * FROM notices WHERE notice_id=$id";
	$resultat=pmb_mysql_query($requete);
	
	$rn=pmb_mysql_fetch_object($resultat);
	
	//Référence
	$notice.="  <REF>".htmlspecialchars($id,ENT_QUOTES,$charset)."</REF>\n";
	
	//Organisme (OP)
	$no_champ = find_custom_field("op");
	if ($no_champ>0) {
		$requete=	"SELECT notices_custom_list_lib ".
					"FROM notices_custom_lists, notices_custom_values ".
					"WHERE notices_custom_lists.notices_custom_champ=$no_champ ". 
						"AND notices_custom_values.notices_custom_champ=$no_champ ".
						"AND notices_custom_integer=notices_custom_list_value ".
						"AND notices_custom_origine=$id";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$op=pmb_mysql_result($resultat,0,0);
			$notice.="  <OP>".htmlspecialchars($op,ENT_QUOTES,$charset)."</OP>\n";
		}
	}
	
	//Date saisie (DS)
	$no_champ = find_custom_field("ds");
	if ($no_champ>0) {
		$requete="SELECT notices_custom_date FROM notices_custom_values WHERE notices_custom_champ=$no_champ AND notices_custom_origine=$id";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat))
			$date=pmb_mysql_result($resultat,0,0);
		else 
			$date=date("Y")."-".date("m")."-".date("d");
		$notice.="<DS>".$date."</DS>\n";
	}
		
	//Type document (TY)
	if (($rn->niveau_biblio!='a')&&($rn->niveau_biblio!='s'))
		$tyd=$ty[$rn->typdoc];
	else
		if ($rn->niveau_biblio=='a')
			$tyd="REVUE";
		else
			$tyd="CHAPEAU";
			
	if ($tyd=="") $tyd="LIVRE";
	$notice.="<TY>".htmlspecialchars($tyd,ENT_QUOTES,$charset)."</TY>\n";
	
	//Genre (GEN)
	$no_champ = find_custom_field("gen");
	if ($no_champ>0) {
		$requete = 	"SELECT notices_custom_list_lib ".
					"FROM notices_custom_lists, notices_custom_values ".
					"WHERE notices_custom_lists.notices_custom_champ=$no_champ ".
						"AND notices_custom_values.notices_custom_champ=$no_champ ".
						"AND notices_custom_integer=notices_custom_list_value ".
						"AND notices_custom_origine=$id";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$notice.="<GEN>".htmlspecialchars(pmb_mysql_result($resultat,0,0),ENT_QUOTES,$charset)."</GEN>\n";
		}
	}
		
	//Auteurs
	$requete=	"SELECT author_name, author_rejete, author_type, responsability_fonction, responsability_type ". 
				"FROM authors, responsability ".
				"WHERE responsability_notice=$id AND responsability_author=author_id ".
				"ORDER BY author_type, responsability_type, responsability_ordre";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		$au=array();
		$auco=array();
		$as=array();
	
		while ($ra=pmb_mysql_fetch_object($resultat)) {
			$a=$ra->author_type=='70'?strtoupper($ra->author_name):$ra->author_name;
			if ($ra->author_rejete) $a.=" (".$ra->author_rejete.")";
			if ($ra->author_type=='70') {
				//C'est une personne, est-ce un auteur principal ou secondaire ?
				if ($ra->responsability_type==2) {
					if ($ra->responsability_fonction>=900) {
						$a.=" ".$tab_functions->table[$ra->responsability_fonction];
					}
					$as[]=$a; 
				} else $au[]=$a;
			} else {
				//C'est un auteur collectif
				$auco[]=$a;
			}
		}
		//Auteurs / Réalisateurs (AU)
		$au_=implode(", ",$au);
		if ($au_) {
			$notice.="<AU>".htmlspecialchars($au_,ENT_QUOTES,$charset)."</AU>\n";
		}
		//Auteurs collectifs (AUCO)
		$auco_=implode(", ",$auco);
		if ($auco_) {
			$notice.="<AUCO>".htmlspecialchars($auco_,ENT_QUOTES,$charset)."</AUCO>\n";
		}
		//Auteurs secondaires (AS)
		$as_=implode(", ",$as);
		if ($as_) {
			$notice.="<AS>".htmlspecialchars($as_,ENT_QUOTES,$charset)."</AS>\n";
		}
	}
	
	//Distributeur (DIST)
	if ($rn->ed2_id) {
		$requete="SELECT ed_ville,ed_name FROM publishers WHERE ed_id=".$rn->ed2_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$re=pmb_mysql_fetch_object($resultat);
			$ed="";
			if ($re->ed_ville) $ed=$re->ed_ville.":";
			$ed.=$re->ed_name;
			$notice.="<DIST>".htmlspecialchars($ed,ENT_QUOTES,$charset)."</DIST>\n";
		}
	}
	
	//Titre (TI)
	$serie="";
	if ($rn->tparent_id) {
		$requete="SELECT serie_name FROM series WHERE serie_id=".$rn->tparent_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) $serie=pmb_mysql_result($resultat,0,0);
	}
	if ($rn->tnvol) $serie.=($serie?" ":"").$rn->tnvol;
	if ($serie) $serie.=". ";
	// ajout GM 15/12/2006 pour export sous-titre dans TI
	if ($rn->tit4!="") $soustitre=" : ".$rn->tit4;
	// fin ajout GM
	// modif GM 15/12/2006 ajout du sous-titre pour l'export
	// $notice.="  <TI>".htmlspecialchars(strtoupper($serie.$rn->tit1))."</TI>\n";
	$notice.="  <TI>".htmlspecialchars($serie.$rn->tit1.$soustitre,ENT_QUOTES,$charset)."</TI>\n";
		
	//Si c'est un article
	if ($rn->niveau_biblio=='a') {
		//Recherche des informations du bulletin
		$requete="SELECT * FROM bulletins, analysis WHERE bulletin_id=analysis_bulletin AND analysis_notice=$id";
		$resultat=pmb_mysql_query($requete);
		$rb=pmb_mysql_fetch_object($resultat);
	}
	
	//Titre du numéro (TN)
	if (($rb->bulletin_titre)&&(substr($rb->bulletin_titre,0,9)!="Bulletin ")) {
		$notice.="<TN>".htmlspecialchars($rb->bulletin_titre,ENT_QUOTES,$charset)."</TN>\n";
	}
	
	//Colloques (COL)
	if ($tyd!="MEMOIRE") {
		if ($rn->tit3) $notice.="<COL>".htmlspecialchars($rn->tit3,ENT_QUOTES,$charset)."</COL>\n";
	}
	
	//Titre de revue (TP)
	if ($rb) {
		$requete="SELECT tit1 FROM notices WHERE notice_id=".$rb->bulletin_notice;
		$resultat=pmb_mysql_query($requete);
		$notice.="<TP>".htmlspecialchars(pmb_mysql_result($resultat,0,0),ENT_QUOTES,$charset)."</TP>\n";
	}
	
	//Souces (SO)
	if ($rb) {
		$so="";
		if ($rb->bulletin_numero) $so=$rb->bulletin_numero;
		if ($rb->mention_date) {
			if ($so) $so.=", ";
			$so.=$rb->mention_date;
		}
	} else
		$so = $rn->n_gen; 
	$notice.="<SO>".htmlspecialchars($so,ENT_QUOTES,$charset)."</SO>";
	
	//Editeur / Collection (ED)
	if ($rn->ed1_id) {
		$requete="SELECT ed_ville,ed_name FROM publishers WHERE ed_id=".$rn->ed1_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$red=pmb_mysql_fetch_object($resultat);
			$ed="";
			if ($red->ed_ville) $ed=$red->ed_ville.":";
			$ed.=$red->ed_name;
		}
		//Collection
		if ($rn->coll_id) {
			$requete="SELECT collection_name FROM collections WHERE collection_id=".$rn->coll_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$coll_name=pmb_mysql_result($resultat,0,0);
				$ed.=" (".$coll_name.")";
			}
		}
		$notice.="<ED>".htmlspecialchars($ed,ENT_QUOTES,$charset)."</ED>\n";
	}
	
	//Date de publication (DP)
	$annee="";
	if (($rn->year)&&($rn->niveau_biblio!='a')) {
		$annee=$rn->year;
	} else if ($rn->niveau_biblio=='a') {
		$req_mention_date="SELECT YEAR(date_date) FROM bulletins, analysis WHERE bulletin_id=analysis_bulletin AND analysis_notice=$id";
		$res_mention_date=pmb_mysql_query($req_mention_date);
		if ($res_mention_date) {
			$annee=pmb_mysql_result($res_mention_date,0,0);
		} else if ($rn->year) {
			$annee=$rn->year;
		}
	}
	if ($annee!="") {
		//on essaie d'enlever les mois
		for($bcl_an=1;$bcl_an<13;$bcl_an++) {
			$annee = str_replace($mois[$bcl_an],"",strtolower($annee));
			$annee = str_replace($mois_enrichis[$bcl_an],"",strtolower($annee));
		}
		$annee = str_replace("-","",$annee);
		$annee = str_replace(",","",$annee);
		$annee = substr($annee,0,4);
		$notice.="<DP>".htmlspecialchars(trim($annee),ENT_QUOTES,$charset)."</DP>\n";
	}
	
	//Diplome (ND)
	if (($tyd=="MEMOIRE")&&($rn->tit3)) {
		$notice.="<ND>".htmlspecialchars($rn->tit3,ENT_QUOTES,$charset)."</ND>\n";
	}
	//Notes (NO)
	if ($tyd=="REVUE")
		$no=$rn->npages;
	else
		$no=$rn->n_contenu;

	if ($no)
		$notice.="<NO>".htmlspecialchars($no,ENT_QUOTES,$charset)."</NO>\n";
	
	$requete="SELECT num_noeud FROM notices_categories WHERE notcateg_notice=$id ORDER BY ordre_categorie";
	$resultat=pmb_mysql_query($requete);
	$go=array();
	$hi=array();
	$denp=array();
	$de=array();
	$cd=array();
	
	if ($m_thess>1) {
		while (list($categ_id)=pmb_mysql_fetch_row($resultat)) {
			$categ=new category($categ_id);
			if (find_thesaurus("GO")==$categ->thes->id_thesaurus) {
				$go[]=$categ->libelle;
			} elseif (find_thesaurus("HI")==$categ->thes->id_thesaurus) {
				$hi[]=$categ->libelle;
			} elseif (find_thesaurus("DENP")==$categ->thes->id_thesaurus) {
				$denp[]=$categ->libelle;
			} elseif (find_thesaurus("DE")==$categ->thes->id_thesaurus) {
				$de[]=$categ->libelle;
			} elseif (find_thesaurus("CD")==$categ->thes->id_thesaurus) {
				$cd[]=$categ->libelle;
			}
		}
	} else {
		
		while (list($categ_id)=pmb_mysql_fetch_row($resultat)) {
			$categ=new categories($categ_id,'fr_FR');
			$list_categ=categories::listAncestors($categ_id,'fr_FR');
			reset($list_categ);
			list($id,$libelle)=each($list_categ);
			switch ($libelle["autorite"]) {
				case "GO":
					$go[]=$categ->libelle_categorie;
					break;
				case "HI":
					$hi[]=$categ->libelle_categorie;
					break;
				case "DENP":
					$denp[]=$categ->libelle_categorie;
					break;
				case "DE":
					$de[]=$categ->libelle_categorie;
					break;
				case "CD":
					$cd[]=$categ->libelle_categorie;
					break;
			}
		}
	}
	
	//Zone (GO)
	if (count($go)) {
		//sort($go);
		$notice.="<GO>".htmlspecialchars(strtoupper(implode(", ",$go)),ENT_QUOTES,$charset)."</GO>\n";
	}
	
	//Période historique (HI)
	if (count($hi)) {
		//sort($hi);
		$notice.="<HI>".htmlspecialchars(strtoupper(implode(", ",$hi)),ENT_QUOTES,$charset)."</HI>\n";
	}
	
	//Descripteurs noms propres (DENP)
	if (count($denp)) {
		//sort($denp);
		$notice.="<DENP>".htmlspecialchars(strtoupper(implode(", ",$denp)),ENT_QUOTES,$charset)."</DENP>\n";
	}
	
	//Descripteurs (DE)
	if (count($de)) {
		//sort($de);
		$notice.="<DE>".htmlspecialchars(strtoupper(implode(", ",$de)),ENT_QUOTES,$charset)."</DE>\n";
	}
	
	//Candidats descripteurs (CD)
	if (count($cd)) {
		//sort($cd);
		$notice.="<CD>".htmlspecialchars(strtoupper(implode(", ",$cd)),ENT_QUOTES,$charset)."</CD>\n";
	}

	//Resumé (RESU)
	if ($rn->n_resume) {
		$notice.="<RESU>".htmlspecialchars($rn->n_resume,ENT_QUOTES,$charset)."</RESU>\n";
	}
	
	//date de tri (DATRI)
	if ($rb->date_date) {
		$notice.="<DATRI>".htmlspecialchars($rb->date_date,ENT_QUOTES,$charset)."</DATRI>\n";
	}
	
	//url (URL)
	if ($rn->lien) {
		$notice.="<URL>".htmlspecialchars($rn->lien,ENT_QUOTES,$charset)."</URL>\n";
	}
	
	//isbn (ISBN)
	if ($rn->code) {
		$notice.="<ISBN>".htmlspecialchars(str_replace("-","",$rn->code),ENT_QUOTES,$charset)."</ISBN>\n";
	}
	
	$notice.="</notice>";
	
	return $notice;
}

?>
