<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_selector.php,v 1.119 2019-06-10 08:57:12 btafforeau Exp $

$base_path=".";
$base_noheader=1;
$base_nobody=1;
//$base_nocheck=1;

require_once("includes/init.inc.php");
require_once("$class_path/marc_table.class.php");
require_once("$class_path/analyse_query.class.php");

header("Content-Type: text/html; charset=$charset");
$start=stripslashes($datas);
$start = str_replace("*","%",$start);
$insert_between_separator = "";
$taille_search = "";
if($att_id_filter == 'null'){
    $att_id_filter = null;
}
switch($completion):
	case 'categories':
		/* Pas utilisé en gestion Matthieu 02/08/2012 */
		$array_selector=array();
		require_once($class_path."/thesaurus.class.php");
		require_once($class_path."/categories.class.php");
		if ($thesaurus_mode_pmb==1) $id_thes=-1;
		else $id_thes=$thesaurus_defaut;

		$aq=new analyse_query($start);

		$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
		$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
		
		$requete_langue="select catlg.num_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, catlg.langue as langue, 
		catlg.libelle_categorie as categ_libelle,catlg.index_categorie as index_categorie, catlg.note_application as categ_comment, 
		(".$members_catlg["select"].") as pert from thesaurus left join noeuds on  thesaurus.id_thesaurus = noeuds.num_thesaurus left join categories as catlg on noeuds.id_noeud = catlg.num_noeud 
		and catlg.langue = '".$lang."' where catlg.libelle_categorie like '".addslashes($start)."%' and catlg.libelle_categorie not like '~%'";
		
		$requete_defaut="select catdef.num_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, catdef.langue as langue, 
		catdef.libelle_categorie as categ_libelle,catdef.index_categorie as index_categorie, catdef.note_application as categ_comment, 
		(".$members_catdef["select"].") as pert from thesaurus left join noeuds on  thesaurus.id_thesaurus = noeuds.num_thesaurus left join categories as catdef on noeuds.id_noeud = catdef.num_noeud 
		and catdef.langue = thesaurus.langue_defaut where catdef.libelle_categorie like '".addslashes($start)."%' and catdef.libelle_categorie not like '~%'";
		
		$requete="select * from (".$requete_langue." union ".$requete_defaut.") as sub1 group by categ_id order by pert desc,num_thesaurus, categ_libelle limit 20";
		
		$res = @pmb_mysql_query($requete, $dbh) or die(pmb_mysql_error()."<br />$requete");
		while(($categ=pmb_mysql_fetch_object($res))) {
			$display_temp = "" ;
			$lib_simple="";
			$tab_lib_categ=array();
			$temp = new categories($categ->categ_id, $categ->langue);
			if ($id_thes == -1) {
				$thes = new thesaurus($categ->num_thesaurus);
				$display_temp = htmlentities('['.$thes->libelle_thesaurus.'] ',ENT_QUOTES, $charset);
			}
			$id_categ_retenue = $categ->categ_id ;	
			if($categ->categ_see) {
				$id_categ_retenue = $categ->categ_see ;
				$temp = new categories($categ->categ_see, $categ->langue);
				$display_temp.= $categ->categ_libelle." -> ";
				$lib_simple = $temp->libelle_categorie;
				if ($thesaurus_categories_show_only_last) $display_temp.= $temp->libelle_categorie;
					else $display_temp.= categories::listAncestorNames($categ->categ_see, $categ->langue);				
				$display_temp.= "@";
			} else {
				$lib_simple = $categ->categ_libelle;
				if ($thesaurus_categories_show_only_last) $display_temp.= $categ->categ_libelle;
				else $display_temp.= categories::listAncestorNames($categ->categ_id, $categ->langue); 			
			}		
			
			$tab_lib_categ[$display_temp] = $lib_simple; 
			$array_selector[$id_categ_retenue] = $display_temp;
		} // fin while		
		$origine = "ARRAY" ;
		break;
	case 'categories_mul':
		$array_selector=array();
		$array_prefix=array();
		require_once("$class_path/thesaurus.class.php");
		require_once("$class_path/categories.class.php");
		if ($thesaurus_mode_pmb==1){
			$id_thes=-1;	
		}else{
			$id_thes=$thesaurus_defaut;
		}
		if($att_id_filter!=0){ //forcage sur un thésaurus en particulié
			$id_thes=$att_id_filter;
			$linkfield=$att_id_filter; 
		}
		
		if(preg_match("#^f_categ_id#",$autfield)){//Permet de savoir si l'on vient du formulaire de notice ou de recherche
			$from="notice";//Affichage complet du chemin de la catégorie
		}else{
			$from="search";//Affichage que de la catégorie
		}
		$aq=new analyse_query($start);
		$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
		$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");

		$thesaurus_requette='';
		$thes_unique=0;
		if($thesaurus_mode_pmb==0){
			$thesaurus_requette= " id_thesaurus='$thesaurus_defaut' and ";
			$thes_unique=$thesaurus_defaut;
		}elseif($linkfield){
			if(!preg_match("#,#i",$linkfield)){
				$thesaurus_requette= " id_thesaurus='$linkfield' and ";
				$thes_unique=$linkfield;
			}else{
				$thesaurus_requette= " id_thesaurus in ($linkfield) and ";
			}
		}		
		 
		$requete_langue="select catlg.num_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, catlg.langue as langue, 
		catlg.libelle_categorie as categ_libelle,catlg.index_categorie as index_categorie, catlg.note_application as categ_comment, noeuds.not_use_in_indexation as not_use_in_indexation, 
		(".$members_catlg["select"].") as pert from thesaurus left join noeuds on  thesaurus.id_thesaurus = noeuds.num_thesaurus left join categories as catlg on noeuds.id_noeud = catlg.num_noeud 
		and catlg.langue = '".$lang."' where $thesaurus_requette catlg.libelle_categorie like '".addslashes($start)."%' and catlg.libelle_categorie not like '~%'";
		
		$requete_defaut="select catdef.num_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, catdef.langue as langue, 
		catdef.libelle_categorie as categ_libelle,catdef.index_categorie as index_categorie, catdef.note_application as categ_comment, noeuds.not_use_in_indexation as not_use_in_indexation, 
		(".$members_catdef["select"].") as pert from thesaurus left join noeuds on  thesaurus.id_thesaurus = noeuds.num_thesaurus left join categories as catdef on noeuds.id_noeud = catdef.num_noeud 
		and catdef.langue = thesaurus.langue_defaut where $thesaurus_requette catdef.libelle_categorie like '".addslashes($start)."%' and catdef.libelle_categorie not like '~%'";
		
		$requete="select * from (".$requete_langue." union ".$requete_defaut.") as sub1 group by categ_id order by pert desc,num_thesaurus, index_categorie limit 20";
		
		$res = @pmb_mysql_query($requete, $dbh) or die(pmb_mysql_error()."<br />$requete");
		while(($categ=pmb_mysql_fetch_object($res))) {
			$display_temp = "" ;
			$lib_simple="";
			$tab_lib_categ=array();
			$temp = new categories($categ->categ_id, $categ->langue);
			if ($id_thes == -1) {//Si mode multi-thésaurus
				$thes = new thesaurus($categ->num_thesaurus);
				if($from == "notice"){//Si saisi de notice
					$lib_simple = htmlentities('['.$thes->libelle_thesaurus.'] ',ENT_QUOTES, $charset);
				}
			}
			
			$id_categ_retenue = $categ->categ_id ;
			//Catégorie à ne pas utiliser en indexation
			$not_use_in_indexation=$categ->not_use_in_indexation;
			if($categ->categ_see) {
				$id_categ_retenue = $categ->categ_see ;
				//Catégorie à ne pas utiliser en indexation
				$category=new category($id_categ_retenue);
				$not_use_in_indexation=$category->not_use_in_indexation;
				
				$temp = new categories($categ->categ_see, $categ->langue);
				$display_temp= $categ->categ_libelle." -> ";
				$chemin=categories::listAncestorNames($categ->categ_see, $categ->langue);
				if ($thesaurus_categories_show_only_last){
					$display_temp.= $temp->libelle_categorie;
					$lib_simple.= $temp->libelle_categorie;
				}else{
					$display_temp.=$chemin;
					if($from == "notice"){
						$lib_simple.= $chemin;
					}else{
						$lib_simple.= $temp->libelle_categorie;
					}
				}
				$display_temp.= "@";
			} else {
				$chemin=categories::listAncestorNames($categ->categ_id, $categ->langue);
				if ($thesaurus_categories_show_only_last){
					$display_temp.= $categ->categ_libelle;
					$lib_simple.= $categ->categ_libelle;
				}else{
					$display_temp.= $chemin;
					if($from == "notice"){
						$lib_simple.= $chemin;
					}else{
						$lib_simple.= $categ->categ_libelle;
					}
				}			
			}
			
			if(!$not_use_in_indexation && !preg_match("#:~|^~#i",$chemin)){
				$tab_lib_categ[$display_temp] = $lib_simple; 
				$array_selector[$id_categ_retenue] = $tab_lib_categ;
				if(!$thes_unique){
					$array_prefix[$id_categ_retenue] = array(
						'id' => $categ->num_thesaurus,
						'libelle' => htmlentities('['.$thes->libelle_thesaurus.'] ',ENT_QUOTES, $charset)
					);
				}else{
					$array_prefix[$id_categ_retenue] = array(
						'id' => $categ->num_thesaurus,
						'libelle' => ""
					);
				}
				
			}
		} // fin while		
		$origine = "ARRAY" ;
		break;
	case 'authors':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		else $restrict = "";
		$query = "select if(author_date!='',concat(if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name),' (',author_date,')'),if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name)) as author,author_id from authors where if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
		    $authority_instance_from_selector = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $row->author_id, 'type_object' => AUT_TABLE_AUTHORS]);
		    $array_selector[$row->author_id] = $authority_instance_from_selector->get_isbd();
        }
        $origine = "ARRAY";
		break;
	case 'authors_person':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		else $restrict = "";
		$requete="select if(author_date!='',concat(if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name),' (',author_date,')'),if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name)) as author,author_id from authors where author_type='70' and if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;
	case 'congres_name':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		else $restrict = "";
		$requete="select distinct author_name from authors where  author_type='72' and author_name like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;	
	case 'collectivite_name':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		else $restrict = "";
		$requete="select distinct author_name from authors where  author_type='71' and author_name like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;	
	case 'publishers':
		if ($autexclude) $restrict = " AND ed_id not in ($autexclude) ";
		else $restrict = "";
		$requete="select concat(
					ed_name,
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),' (',''), 
					if(ed_ville is not null and ed_ville!='',ed_ville,''),
					if(ed_ville is not null and ed_ville!='' and ed_pays is not null and ed_pays!='',' - ',''), 
					if(ed_pays is not null and ed_pays!='',ed_pays,''), 
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),')','')
					) as ed,ed_id from publishers where concat(
					ed_name,
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),' (',''), 
					if(ed_ville is not null and ed_ville!='',ed_ville,''),
					if(ed_ville is not null and ed_ville!='' and ed_pays is not null and ed_pays!='',' - ',''), 
					if(ed_pays is not null and ed_pays!='',ed_pays,''), 
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),')','')
					) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;
	case 'titre_uniforme':
		require_once($class_path.'/authority.class.php');
		if ($autexclude) $restrict = " AND tu_id not in ($autexclude) ";
		else $restrict = "";
		if($att_id_filter){
		    $restrict.= " and tu_oeuvre_nature='".$att_id_filter."' ";
		}
		$query = "select tu_id from titres_uniformes where tu_name like '".addslashes($start)."%' ".$restrict." order by tu_name limit 20"; 
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			$authority_instance_from_selector = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $row->tu_id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
			$array_selector[$row->tu_id] = $authority_instance_from_selector->get_isbd();
		}
		$origine = "ARRAY" ;
		break;		
	case 'collections':
		if ($autexclude) $restrict = " AND collection_id not in ($autexclude) ";
		else $restrict = "";
		if ($linkfield) $restrict .= " AND collection_parent ='$linkfield' ";
		$requete="select if(collection_issn is not null and collection_issn!='',concat(collection_name,', ',collection_issn),collection_name) as coll,collection_id from collections where if(collection_issn is not null and collection_issn!='',concat(collection_name,', ',collection_issn),collection_name) like '".addslashes($start)."%' $restrict order by index_coll limit 20";
		$origine = "SQL" ;
		break;
	case 'subcollections':
		if ($autexclude) $restrict = " AND sub_coll_id not in ($autexclude) ";
		else $restrict = "";
		if ($linkfield) $restrict .= " AND sub_coll_parent ='$linkfield' ";
		$requete="select if(sub_coll_issn is not null and sub_coll_issn!='',concat(sub_coll_name,', ',sub_coll_issn),sub_coll_name) as subcoll,sub_coll_id from sub_collections where if(sub_coll_issn is not null and sub_coll_issn!='',concat(sub_coll_name,', ',sub_coll_issn),sub_coll_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;
	case 'indexint':
		if ($autexclude) $restrict = " AND indexint_id not in ($autexclude) ";
		else $restrict = "";
		if ($thesaurus_classement_mode_pmb != 0) { //classement indexation décimale autorisé en parametrage
			if($thesaurus_classement_location && $deflt_docs_location) {
				$restrict_location = " AND (locations like '".$deflt_docs_location."' or locations like '".$deflt_docs_location.",%' or locations like '%,".$deflt_docs_location."' or locations like '%,".$deflt_docs_location.",%')";
			} else {
				$restrict_location = "";
			}
			$requete="select if(indexint_comment is not null and indexint_comment!='',concat('[',name_pclass,'] ',indexint_name,' - ',indexint_comment),
			concat('[',name_pclass,'] ',indexint_name)) as indexint,indexint_id
			from indexint,pclassement
			where if(name_pclass is not null and indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) like '".addslashes($start)."%' $restrict
			and id_pclass = num_pclass
			and typedoc like '%$typdoc%'
			".$restrict_location."
			order by indexint_name, name_pclass limit 20";	
		}else {
			$requete="select if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) as indexint,indexint_id from indexint 
			where if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) like '".addslashes($start)."%' $restrict and num_pclass = '$thesaurus_classement_defaut' order by 1 limit 20";
		}
		$origine = "SQL" ;
		break;
	case 'indexint_mul':
		if ($autexclude) $restrict = " AND indexint_id not in ($autexclude) ";
		else $restrict = "";
		if ($thesaurus_classement_mode_pmb != 0) { //classement indexation décimale autorisé en parametrage
			if($thesaurus_classement_location && $deflt_docs_location) {
				$restrict_location = " AND (locations like '".$deflt_docs_location."' or locations like '".$deflt_docs_location.",%' or locations like '%,".$deflt_docs_location."' or locations like '%,".$deflt_docs_location.",%')";
			} else {
				$restrict_location = "";
			}
			$requete="select if(indexint_comment is not null and indexint_comment!='',concat('[',name_pclass,'] ',indexint_name,' - ',indexint_comment),
			concat('[',name_pclass,'] ',indexint_name)) as indexint,indexint_id, concat( indexint_name,' ',indexint_comment) as indexsimple
			from indexint,pclassement
			where if(name_pclass is not null and indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) like '".addslashes($start)."%' $restrict
			and id_pclass = num_pclass
			and typedoc like '%$typdoc%'
			".$restrict_location."
			order by indexint_name, name_pclass limit 20";	
		} else {
			$requete="select if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) as indexint,indexint_id, concat( indexint_name,' ',indexint_comment) as indexsimple from indexint 
			where if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) like '".addslashes($start)."%' $restrict and num_pclass = '$thesaurus_classement_defaut' order by 1 limit 20";
		}
		$origine = "SQL" ;
		break;
	case 'notice':
	case 'tu_notices':
		require_once('./includes/isbn.inc.php');
		if ($autexclude) $restrict = " AND notice_id not in ($autexclude) ";
		else $restrict = "";
		$requete = "select if(serie_name is not null,if(tnvol is not null,concat(serie_name,', ',tnvol,'. ',tit1),concat(serie_name,'. ',tit1)),tit1), notice_id from notices left join series on serie_id=tparent_id where (index_sew like ' ".addslashes(strip_empty_words($start))."%' or TRIM(index_wew) like '".addslashes($start)."%' or tit1 like '".addslashes($start)."%' or (code like '".traite_code_isbn(addslashes($start))."'";
		if (isISBN(traite_code_isbn($start))) {
			if (strlen(traite_code_isbn($start))==13)
				$requete.=" or code like '".formatISBN(traite_code_isbn($start),13)."'";
			else $requete.=" or code like '".formatISBN(traite_code_isbn($start),10)."'";
		}
		$requete.=")) $restrict order by index_serie, tnvol, index_sew , code limit 20 ";
		$origine = "SQL" ;
		break;
	case 'serie':
		if ($autexclude) $restrict = " AND serie_id not in ($autexclude) ";
		else $restrict = "";
		$requete="select serie_name,serie_id from series where serie_name like '".addslashes($start)."%' $restrict order by 1 limit 20";
		$origine = "SQL" ;
		break;
	case 'fonction':
		// récupération des codes de fonction
		if (!isset($s_func )) {
			$s_func = new marc_list('function');
		}
		$origine = "TABLEAU" ;
		break;
	case 'langue':
	case 'lang':
		// récupération des codes de langue
		if (!isset($s_func )) {
			$s_func = new marc_list('lang');
		}
		$origine = "TABLEAU" ;
		break;	
	case 'country':
		// récupération des codes de langue
		if (!isset($s_func )) {
			$s_func = new marc_list('country');
		}
		$origine = "TABLEAU" ;
		break;
	case 'synonyms':
		$array_selector=array();
		//recherche des mots
		$rqt="select id_mot, mot from mots left join linked_mots on (num_mot=id_mot) where mot like '".addslashes($start)."%' and id_mot not in (select num_mot from linked_mots where linked_mots.num_linked_mot=0) group by id_mot";
		$execute_query=pmb_mysql_query($rqt);
		while ($r=pmb_mysql_fetch_object($execute_query)) {
			$array_selector[$r->id_mot]=$r->mot;
		}
		pmb_mysql_free_result($execute_query);
		if (count($array_selector)) {
			//dédoublonnage du tableau final
			$array_selector=array_unique($array_selector);
			//tri alphanumérique du tableau
			asort($array_selector);
		}
		$origine = "ARRAY" ;
		break;
	case 'tags':
		require_once("$class_path/tags.class.php");
		$tags = new tags();
		$array_selector = $tags->get_array($start,$pos_cursor);
		$taille_search = $tags->get_taille_search();
		$origine = "ARRAY";
		break;
	case 'perio':
		$requete = "select tit1, notice_id from notices where niveau_biblio='s' and niveau_hierar='1' and tit1 like '".addslashes($start)."%' order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'bull':
		if($linkfield) $link_bull = " and bulletin_notice ='".$linkfield."'";
		$requete = "select if(bulletin_titre is not null and bulletin_titre!='',concat(bulletin_titre,' - ',bulletin_numero),bulletin_numero) as bulletin_numero, bulletin_id from bulletins where (bulletin_numero like '".addslashes($start)."%' or bulletin_titre like '".addslashes($start)."%')  $link_bull order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'bull_num':
		$id_notice = substr($id,13);
		$requete = "select bulletin_numero, date_date from bulletins where bulletin_notice='$id_notice' and bulletin_numero like '%".addslashes($start)."%' order by 1 limit 20";
		$origine = "SQL"; 
		break;		
	case 'expl_cote':	
		if($pmb_prefill_cote_ajax){
			include("./catalog/expl/ajax/$pmb_prefill_cote_ajax");
			$array_selector = calculer_cote($start);	
			$origine = "ARRAY";		
		}
		break;	
	case 'fournisseur':
		$requete = "select raison_sociale as lib,id_entite as id from entites where type_entite='0' ";
		if ($linkfield) $requete.= "and num_bibli='".$linkfield."' ";	
		$requete.= "and raison_sociale like '".addslashes($start)."%' order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'origine':
		$requete = "select concat(nom,' ',prenom) as lib, concat(userid,',0') as id from users where nom like '".addslashes($start)."%' ";
		$requete.= "union select concat(empr_nom,' ',empr_prenom), concat(id_empr,',1') from empr where empr_nom like '".addslashes($start)."%' ";
		$requete.= "order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'empr':		
		$requete = "select concat(empr_nom,' ',empr_prenom), id_empr as id from empr where empr_nom like '".addslashes($start)."%' ";
		$requete.= "order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'rubrique':
		$requete = "select concat('[',budgets.libelle,']',rubriques.libelle) as lib, id_rubrique as id, rubriques.libelle as lib2 from rubriques join budgets on num_budget=id_budget where autorisations like ' %".SESSuserid."% ' ";
		if ($linkfield) $requete.= "and num_exercice='".$linkfield."' ";
		$requete.= "and rubriques.libelle like '".addslashes($start)."%' ";
		$requete.= "order by lib limit 20";
		$origine="SQL";
		break;
	case 'rubriques':
		// $param1 : id_entite
		// $param2 : id_exercice
		require_once($class_path.'/rubriques.class.php');
		require_once($class_path.'/entites.class.php');
		
		//on cherche toutes les rubriques correspondant à la recherche
		$q = "select rubriques.id_rubrique from budgets, rubriques ";
		$q.= "where budgets.statut = '1' and budgets.num_entite = '".$param1."'  and budgets.num_exercice = '".$param2."' and rubriques.num_budget = budgets.id_budget ";
		$q.= "and rubriques.libelle like '".addslashes($start)."%' ";
		$r = pmb_mysql_query($q, $dbh);
		
		//on liste toutes les rubriques finales correspondantes
		$array_rubriques_finales = array();
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$rub_finales = entites::findRubriquesFinales($row->id_rubrique);
				if (count($rub_finales)) {
					foreach ($rub_finales as $id_rub_finale) {
						if ((!count($array_rubriques_finales)) || (!in_array($id_rub_finale,$array_rubriques_finales))) {
							$array_rubriques_finales[] = $id_rub_finale;
						}
					}
				}
			}
		}
		
		//on retourne le recordset des rubriques finales
		$requete = "select budgets.libelle as lib_bud, rubriques.* from budgets, rubriques left join rubriques as rubriques2 on rubriques.id_rubrique=rubriques2.num_parent ";
		$requete.= "where budgets.statut = '1' and budgets.num_entite = '".$param1."'  and budgets.num_exercice = '".$param2."' and rubriques.num_budget = budgets.id_budget and rubriques2.num_parent is NULL ";
		if (count($array_rubriques_finales)) {
			$requete.= "and rubriques.id_rubrique in (".implode(",",$array_rubriques_finales).") ";
		} else {
			$requete.= "and rubriques.id_rubrique = 0 "; //Pas de rubrique trouvée
		}
		$requete.= "and rubriques.autorisations like(' %".SESSuserid."% ') ";
		$requete.= "order by budgets.libelle, rubriques.id_rubrique ";
		$res=pmb_mysql_query($requete);
		while($row = pmb_mysql_fetch_object($res)) {
			$tab_rub = rubriques::listAncetres($row->id_rubrique, true);
			$lib_rub = '';
			foreach ($tab_rub as $dummykey=>$value) {
				$lib_rub.= htmlentities($value[1], ENT_QUOTES, $charset);
				if($value[0] != $row->id_rubrique) $lib_rub.= ":";
			}
			$array_selector[$row->id_rubrique]=htmlentities($row->lib_bud, ENT_QUOTES, $charset).":".$lib_rub;
		}
		$origine="ARRAY";
		break;
	case 'types_produits':
		// $param1 : id_fournisseur
		$array_selector=array();
		require_once($class_path.'/types_produits.class.php');
		require_once($class_path.'/tva_achats.class.php');
		require_once($class_path.'/offres_remises.class.php');
		$q = types_produits::listTypes();
		$res = pmb_mysql_query($q, $dbh);
		while($row=pmb_mysql_fetch_object($res)) {
			$typ = $row->id_produit;
			$lib_typ = $row->libelle;
			$taux_tva = new tva_achats($row->num_tva_achat);
			$lib_tva = htmlentities($taux_tva->taux_tva, ENT_QUOTES, $charset);
			$offre = new offres_remises($linkfield, $row->id_produit);
			if ($offre->remise) {
				$lib_rem = htmlentities($offre->remise, ENT_QUOTES, $charset);
			} else $lib_rem = '0';
			$array_selector[$typ.','.$lib_rem.','.$lib_tva]=htmlentities($lib_typ, ENT_QUOTES, $charset);
		}
		$origine="ARRAY";
		break;
	case 'fournisseurs':
		// $param1 : id_bibli
		$array_selector=array();
		require_once($class_path.'/entites.class.php');
		$requete = "select raison_sociale, id_entite from entites where type_entite='0' ";
		$requete.= "and num_bibli='".$param1."' ";
		$requete.= "and raison_sociale like '".addslashes($start)."%' order by 1";
		$res = pmb_mysql_query($requete, $dbh);
		while(($row=pmb_mysql_fetch_object($res))) {
			$adresse="";
			$idAdresse=0;
			$coord = entites::get_coordonnees($row->id_entite, '1');
			if (pmb_mysql_num_rows($coord) != 0) {
				$coord = pmb_mysql_fetch_object($coord);
				$idAdresse=$coord->id_contact;
				if($coord->libelle != '') $adresse = htmlentities($coord->libelle, ENT_QUOTES, $charset)."\n";
				if($coord->contact !='') $adresse.=  htmlentities($coord->contact, ENT_QUOTES, $charset)."\n";
				if($coord->adr1 != '') $adresse.= htmlentities($coord->adr1, ENT_QUOTES, $charset)."\n";
				if($coord->adr2 != '') $adresse.= htmlentities($coord->adr2, ENT_QUOTES, $charset)."\n";
				if($coord->cp !='') $adresse.= htmlentities($coord->cp, ENT_QUOTES, $charset).' ';
				if($coord->ville != '') $adresse.= htmlentities($coord->ville, ENT_QUOTES, $charset);
			}
			$array_selector[$row->id_entite.','.$idAdresse.','.$adresse]=htmlentities($row->raison_sociale, ENT_QUOTES, $charset);
		}
		$origine="ARRAY";
		break;
	case 'onto':
		if(!isset($autoloader) || !is_object($autoloader)){
			require_once($class_path."/autoloader.class.php");	
			$autoloader = new autoloader();
		}
		$autoloader->add_register("onto_class",true);
		$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		$tab_namespaces=array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		if($linkfield && !$param1) {
			$param1 = $linkfield;
		}
		$params=new onto_param(
			array(
				'autexclude' => "",
				'linkfield' => "",
				'autfield' => "",
				'typdoc' => "",
				'att_id_filter' => "",
				'listfield' => "",
				'callback' => "",
				'datas' => "",
				'concept_scheme' => explode(",",$param1),
				'return_concept_id' => $param2,
				'action'=>'ajax_selector'
			)
		);
		//HACK pas hyper hyper générique, mais ca fait le job!
		if(isset($param1) && $param1){
			global $concept_scheme;
			if($param1 == -1 || $param1 > 0) {
			    $concept_scheme = explode(",",$param1);
			}else {
				$concept_scheme = (($params->concept_scheme !== '') ? $params->concept_scheme : []);
			}
			$params->return_concept_id = true;
		}else{
		    $concept_scheme = [];
		}
		$onto_ui = new onto_ui($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel',$params);
		$list_results = $onto_ui->proceed();
		$array_prefix = (isset($list_results['prefix']) ? $list_results['prefix'] : '');
		$array_selector = (isset($list_results['elements']) ? $list_results['elements'] : '');
		$origine='ONTO_ARRAY';
		break;
		
	case 'instruments':
		// $param1 : id du pupitre préféré. si 0 on retourne tous les instruments
		// $param1 = workshop: Signifie qu'il faut aller chercher le(s) pupitre(s) associés aux ateliers
		// $param2 = 0: Instruments du pupitre préféré seulement
		// $param2 = 1: Instruments du pupitre préféré en premier, puis les autres
		if ($autexclude) $restrict = " AND id_instrument not in ($autexclude) ";
		else $restrict = "";
		if(strlen($start)==$pos_cursor){
			$liste_mots=explode("/",$start);
			$start = array_pop($liste_mots);
		} else {
			$liste_mots = explode("/",substr($start,0,$pos_cursor));
			$start = array_pop($liste_mots);
		}
		
		$origine = "SQL" ;
		$musicstands = array();
		if($param1 == 'workshop') {
			$query = "select id_musicstand from nomenclature_musicstands where musicstand_workshop = 1";
			$result = pmb_mysql_query($query);
			if($result) {
				while($row = pmb_mysql_fetch_object($result)) {
					$musicstands[] = $row->id_musicstand;
				}
			}
		} elseif($param1) {
			$musicstands[] = $param1;
		}
		if (count($musicstands) && !$param2){ // que ceux du pupitre
			$restrict .= " AND instrument_musicstand_num IN (".implode(',', $musicstands).") ";
			
			$requete="
			select if(instrument_name is not null and instrument_name!='',concat(instrument_code,' - ',instrument_name),instrument_code) as instrument_lib, id_instrument, instrument_code from nomenclature_instruments 
			where ( instrument_code like '".addslashes($start)."%' or instrument_name like '".addslashes($start)."%' ) $restrict order by 1 limit 20";
		
		}elseif(count($musicstands) && $param2){//  que ceux du pupitre en premier, puis les autres
			$restrict1 = $restrict." AND instrument_musicstand_num IN (".implode(',', $musicstands).") ";
			$restrict2 = $restrict." AND instrument_musicstand_num NOT IN (".implode(',', $musicstands).") ";
			$requetes = array();
			$requetes[] = "select if(instrument_name is not null and instrument_name!='',concat(instrument_code,' - ',instrument_name),instrument_code) as instrument_lib, id_instrument, instrument_code from nomenclature_instruments 
			 	where ( instrument_code like '".addslashes($start)."%' or instrument_name like '".addslashes($start)."%' ) $restrict1 order by 1";
			$requetes[] = "select if(instrument_name is not null and instrument_name!='',concat(instrument_code,' - ',instrument_name),instrument_code) as instrument_lib, id_instrument, instrument_code from nomenclature_instruments 
				where ( instrument_code like '".addslashes($start)."%' or instrument_name like '".addslashes($start)."%' ) $restrict2 order by 1 limit 20";
			$origine = "SQL_GROUP" ;
		}else{ // tous les instruments
			$requete="
			select if(instrument_name is not null and instrument_name!='',concat(instrument_code,' - ',instrument_name),instrument_code) as instrument_lib, id_instrument, instrument_code from nomenclature_instruments
			where ( instrument_code like '".addslashes($start)."%' or instrument_name like '".addslashes($start)."%' ) $restrict order by 1 limit 20";
		}
		$insert_between_separator = "/";
		break;
	case 'voices':
		if ($autexclude) $restrict = " AND id_voice not in ($autexclude) ";
		else $restrict = "";
		//On récupère toutes les voix
		$requete="
			select if(voice_name is not null and voice_name!='',concat(voice_code,' - ',voice_name),voice_code) as voice_lib, id_voice, voice_code from nomenclature_voices
			where ( voice_code like '".addslashes($start)."%' or voice_name like '".addslashes($start)."%' ) $restrict order by 1 limit 20";
		
		$insert_between_separator = "/";
		$origine = "SQL" ;
		break;
	case 'music_key':
		// récupération des codes
		if (!isset($s_func )) {
			$s_func = new marc_list('music_key');
		}
		$origine = "TABLEAU" ;
		break;
	case 'music_form':
		// récupération des codes
		if (!isset($s_func )) {
			$s_func = new marc_list('music_form');
		}
		$origine = "TABLEAU" ;
		break;
	case 'oeuvre_event':
		require_once($class_path.'/authperso.class.php');
		$array_selector=authperso::get_ajax_list_oeuvre_events($start);
		$origine='ARRAY';
		break;
	case 'vedette':
	    /**
	     * modification de la requête pour utiliser le label plutot que l'id de la vedette.
	     * cela evite d'avoir des doublons dans l'autocomplétion en RMC
	     * à voir si l'id_vedette est nécessaire dans certains cas
	     */	    
		$requete = "select distinct label, label from vedette where label like '".addslashes($start)."%'";
		if($linkfield) {
			$requete .= " and grammar in ('".implode("','",explode(',',$linkfield))."')";
		}
		$requete .= " order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'concepts':
		$requete="select distinct value, num_concept from index_concept join skos_fields_global_index on num_concept = id_item and code_champ = 1 where value like '".addslashes($start)."%' $restrict group by num_object order by 1 limit 20";
		$origine = "SQL" ;
		break;
	case 'empr_mail':
		$requetes = array();
		$query = "drop table if exists temp_empr_mail";
		pmb_mysql_query($query);
		$query = "drop table if exists temp_empr_name";
		pmb_mysql_query($query);
		$query = "create temporary table temp_empr_mail as select concat(empr_mail, ' (',empr_nom,' ',empr_prenom,')'), id_empr as id
				from empr where empr_mail like '".addslashes($start)."%' and empr_mail !='' order by 1 limit 20";
		pmb_mysql_query($query);
		$requetes[] = "select * from temp_empr_mail";
		$query = "create temporary table temp_empr_name as select concat(empr_mail, ' (',empr_nom,' ',empr_prenom,')'), id_empr as id 
				from empr where empr_nom like '".addslashes($start)."%' and empr_mail !='' and id_empr not in (select id from temp_empr_mail) order by 1 limit 20";
		pmb_mysql_query($query);
		$requetes[] = "select * from temp_empr_name";
		$requetes[] = "select concat(empr_mail, ' (',empr_nom,' ',empr_prenom,')'), id_empr as id
				from empr where empr_prenom like '".addslashes($start)."%' and empr_mail !='' and id_empr not in (select id from temp_empr_name) and id_empr not in (select id from temp_empr_mail) order by 1 limit 20";
		$origine = "SQL_GROUP";
		break;
	case 'query_list':
	case 'list':
	case 'marc_list':
		$array_selector = array();
		$search = new search($param1);
		$p=explode('_', $param2);
		if($p[0] == 'f') {
			$start = str_replace('%', '', $start);
			$array_selector = $search->get_options_list_field($search->fixedfields[$p[1]], $start, 20);
		}
		$origine = "ARRAY" ;
		break;
	case 'profession':
		$requete="select distinct empr_prof, 'dummy' as dummy_id from empr where empr_prof like '".addslashes($start)."%' order by 1 limit 20";
		$origine = "SQL" ;
		break;
	case 'fields_global_index':
		if(isset($param1) && isset($param2)) {
			$code_champ = explode("_", $param2)[1];
			$code_ss_champ = explode("_", $param2)[2];
			$requete="select distinct value from ".$param1."_fields_global_index where value like '".addslashes($start)."%' and code_champ = '".$code_champ."' and code_ss_champ = '".$code_ss_champ."' order by 1 limit 20";
			$origine = "SQL" ;
		}
		break;
	case 'custom_classements':
		if(isset($param1)) {
			$requete = "select distinct custom_classement from ".$param1."_custom where custom_classement like '".addslashes($start)."%'";
			$origine = "SQL" ;
		}
		break;
	case 'campaigns_descriptors':
		$requete = "select libelle_categorie, categories.num_noeud from categories JOIN campaigns_descriptors ON campaigns_descriptors.num_noeud = categories.num_noeud where libelle_categorie like '".addslashes($start)."%' AND langue = '".$lang."' ";
		$requete.= "order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'campaigns_tags':
		$requete = "select tags.libelle, tags.id_tag from tags join campaigns_tags ON campaigns_tags.num_tag = tags.id_tag where tags.libelle like '".addslashes($start)."%' ";
		$requete.= "order by 1 limit 20";
		$origine = "SQL";
		break;
	case 'groups':
		$requete="select distinct libelle_groupe, id_groupe from groupe where libelle_groupe like '".addslashes($start)."%' order by 1 limit 20";
		$origine = "SQL" ;
		break;
	case 'connectors':
		$array_selector = array();
		$start = str_replace('%', '', $start);
		$start_length = strlen($start);
		$source_id = intval($att_id_filter);
		require_once($class_path."/connecteurs.class.php");
		$conn = connecteurs::get_connector_instance_from_source_id($source_id);
		if ($conn) {
			$source_params = $conn->get_source_params($source_id);
			$parameters = unserialize($source_params["PARAMETERS"]);
			switch ($conn->get_id()) {
				case 'oai':
					//Intérogation du serveur
					$oai_p=new oai20($parameters['url'],$charset, $conn->timeout);
					if (!$oai_p->error) {
						if ($oai_p->has_feature("SETS")) {
							foreach ($oai_p->sets as $code=>$set) {
								if(!$start || (substr(strtolower($set['name']),0,$start_length) == strtolower($start))) {
									$array_selector[$code] = $set['name'].($set['description'] ? " (".$set['description'].")" : "");
								}
							}
						}
					}
					break;
			}
		}
		$origine = "ARRAY" ;
		break;
	default: 
		$p=explode('_', $completion);
		if(count ($p)){
			switch ($p[0]){
				case 'authperso':
					require_once($class_path.'/authperso.class.php');
					$authperso = new authperso($p[1]);
					$array_selector=$authperso->get_ajax_list($start);
					$origine='ARRAY';
					break;
				case 'perso':
					switch ($p[1]){
						case 'cms':
 							require_once($class_path.'/cms/cms_editorial_parametres_perso.class.php');
							$cms_type = cms_editorial_parametres_perso::get_num_type_from_name($persofield);
 							$p_perso = new cms_editorial_parametres_perso($cms_type);
							$array_selector=$p_perso->get_ajax_list($persofield,$start);
							$origine='ARRAY';
							break;
						case 'pret':
							require_once($class_path.'/pret_parametres_perso.class.php');
							$p_perso = new pret_parametres_perso($p[1]);
							$array_selector=$p_perso->get_ajax_list($persofield,$start);
							$origine='ARRAY';
							break;
						default:
							require_once($class_path.'/parametres_perso.class.php');
							$p_perso = new parametres_perso($p[1]);
							$array_selector=$p_perso->get_ajax_list($persofield,$start);
							$origine='ARRAY';
							break;
					}
					break;
			}
		}
		break;
endswitch;

if (empty($origine)) $origine = '';
switch ($origine):
	case 'SQL':
		$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error()."<br />$requete") ;
		$i=1;
		while($r=@pmb_mysql_fetch_array($resultat)) {
			if(isset($r[2]) && $r[2])
				echo "<div id='"."c".$id."_".$i."' style='display:none'>$r[2]</div>";
			echo "<div id='l".$id."_".$i."'";
			if ($autfield) echo " autid='".$r[1]."'";
			echo " class='ajax_selector_normal' onmouseover='this.className=\"ajax_selector_surbrillance\";' onmouseout='this.className=\"ajax_selector_normal\";' onClick='if(document.getElementById(\"c".$id."_".$i."\")) ajax_set_datas(\"c".$id."_".$i."\",\"$id\",\"$insert_between_separator\"); else ajax_set_datas(\"l".$id."_".$i."\",\"$id\",\"$insert_between_separator\");'>".$r[0]."</div>";
			$i++;
		}
		break;
	case 'SQL_GROUP':
		$i=1;
		foreach ($requetes as $requete) {
			$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error()."<br />$requete") ;
			if($resultat && pmb_mysql_num_rows($resultat)) {
				if($i > 1) echo "<hr style='margin:1px 0px' />";
				while($r=@pmb_mysql_fetch_array($resultat)) {
					if(isset($r[2]) && $r[2])
						echo "<div id='"."c".$id."_".$i."' style='display:none'>$r[2]</div>";
					echo "<div id='l".$id."_".$i."'";
					if ($autfield) echo " autid='".$r[1]."'";
					echo " class='ajax_selector_normal' onmouseover='this.className=\"ajax_selector_surbrillance\";' onmouseout='this.className=\"ajax_selector_normal\";' onClick='if(document.getElementById(\"c".$id."_".$i."\")) ajax_set_datas(\"c".$id."_".$i."\",\"$id\",\"$insert_between_separator\"); else ajax_set_datas(\"l".$id."_".$i."\",\"$id\",\"$insert_between_separator\");'>".$r[0]."</div>";
					$i++;
				}
			}
		}
		break;
	case 'TABLEAU':
		$i=1;
		$start_converted = convert_diacrit($start);
		foreach ($s_func->table as $index => $value) {
			if (strtolower(substr(convert_diacrit($value),0,strlen($start_converted)))==strtolower($start_converted)) {
				echo "<div id='l".$id."_".$i."'";
				if ($autfield) echo " autid='".$index."'";
				echo " class='ajax_selector_normal' onmouseover='this.className=\"ajax_selector_surbrillance\";' onmouseout='this.className=\"ajax_selector_normal\";' onClick='ajax_set_datas(\"l".$id."_".$i."\",\"$id\")'>".$value."</div>";
				$i++;
			}
		}
		break;
	case 'ARRAY':
		if (is_array($array_selector) && count($array_selector)) {
			$i=1;
			foreach ($array_selector as $index => $value) {
				$lib_liste="";
				if(isset($array_prefix[$index]['libelle'])) {
					$thesaurus_lib = $array_prefix[$index]['libelle'];
				} else {
					$thesaurus_lib = '';
				}
				if(isset($array_prefix[$index]['id'])) {
					$thesaurus_id = $array_prefix[$index]['id'];
				} else {
					$thesaurus_id = 0;
				}
				if(is_array($value)){
					foreach($value as $k=>$v){
						$lib_liste = $k;
						echo "<div id='"."c".$id."_".$i."' style='display:none' nbcar='".$taille_search."'>".$v."</div>";
					}
				} else $lib_liste=$value;
				echo "<div id='l".$id."_".$i."'";
				if ($autfield) echo " autid='".$index."'";
				if ($thesaurus_id) echo " thesid='".$thesaurus_id."'";
				echo " class='ajax_selector_normal' onmouseover='this.className=\"ajax_selector_surbrillance\";' onmouseout='this.className=\"ajax_selector_normal\";' onClick='if(document.getElementById(\"c".$id."_".$i."\")) ajax_set_datas(\"c".$id."_".$i."\",\"$id\"); else ajax_set_datas(\"l".$id."_".$i."\",\"$id\");'>".trim($thesaurus_lib." ".$lib_liste)."</div>";
				$i++;	
			}
		}
		break;
	case 'ONTO_ARRAY':
		if (is_array($array_selector) && count($array_selector)) {
			$i=1;
			foreach ($array_selector as $index => $value) {
				$lib_liste="";
				if(isset($array_prefix[$index]['libelle'])) {
					$type_label = $array_prefix[$index]['libelle'];
				} else {
					$type_label = '';
				}
				if(isset($array_prefix[$index]['id'])) {
					$type_uri = $array_prefix[$index]['id'];
				} else {
					$type_uri = '';
				}
				if(!$type_uri) $type_uri = $att_id_filter;
				if(is_array($value)){
					foreach($value as $k=>$v){
						$lib_liste = $k;
						echo "<div id='"."c".$id."_".$i."' style='display:none' nbcar='".$taille_search."'>".$v."</div>";
					}
				} else $lib_liste=$value;
				echo "<div id='l".$id."_".$i."'";
				if ($autfield) echo " autid='".$index."'";
				if ($type_uri) echo " typeuri='".$type_uri."'";
				echo " class='ajax_selector_normal' onmouseover='this.className=\"ajax_selector_surbrillance\";' onmouseout='this.className=\"ajax_selector_normal\";' onClick='if(document.getElementById(\"c".$id."_".$i."\")) ajax_set_datas(\"c".$id."_".$i."\",\"$id\"); else ajax_set_datas(\"l".$id."_".$i."\",\"$id\");'>".trim($type_label." ".$lib_liste)."</div>";
				$i++;
			}
		}
		break;	
	default: 
		break;
endswitch;

switch($completion){
	case 'instruments':
		if (SESSrights & ADMINISTRATION_AUTH) {
			require_once($class_path."/nomenclature/nomenclature_instrument.class.php");
			echo "<div id='l".$id."_add_instrument' class='ajax_selector_normal ajax_selector_instruments' onmouseover='this.className=\"ajax_selector_surbrillance ajax_selector_instruments\";' onmouseout='this.className=\"ajax_selector_normal ajax_selector_instruments\";' onClick='ajax_insert_element(\"$id\");'>".$msg['nomenclature_js_dialog_add_instrument']."</div>";
		}
		break;
}

