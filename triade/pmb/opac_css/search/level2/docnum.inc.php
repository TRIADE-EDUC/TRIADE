<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docnum.inc.php,v 1.34 2023/08/08 08:00:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur document numérique
require_once($class_path."/suggest.class.php");
require_once($class_path."/sort.class.php");

global $begin_result_liste, $count, $charset, $typdoc, $limiter, $page;
global $add_cart_link, $gestion_acces_active, $link_to_print_search_result;
global $opac_search_other_function, $opac_nb_max_tri, $opac_stemming_active, $opac_notices_depliable, $opac_search_allow_refinement, $opac_visionneuse_allow;
global $opac_allow_external_search, $opac_search_cache_duration, $opac_photo_filtre_mimetype, $pmb_logs_activate;

//Enregistrement des stats
if ($pmb_logs_activate) {
	global $nb_results_tab;
	$nb_results_tab['docnum'] = $count;
}

print "	<div id=\"resultatrech\"><h3>".$msg['resultat_recherche']."</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
";

// requête de recherche sur les titres
print pmb_bidi("<h3 class='searchResult-search' id='searchResult-search' ><span class='searchResult-equation'>$count ".$msg['docnum_found']." '".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'");

//calcul restriction
if ($opac_search_other_function) {
	require_once($include_path."/".$opac_search_other_function);
	print pmb_bidi(" ".search_other_function_human_query($_SESSION["last_query"]));
}
print "</span>";
print activation_surlignage();
print "</h3>";

$restrict_opac_view='';
if (isset($_SESSION["opac_view"]) && isset($_SESSION["opac_view_query"])) {
	$restrict_opac_view=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
}

$restrict_typdoc = '';
if ($typdoc) {
	$restrict_typdoc = "typdoc='".addslashes($typdoc)."'";
}

$aq=new analyse_query(stripslashes($user_query),0,0,1,0,$opac_stemming_active);
$members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_id",'',0,0,true);
if($user_query=='*') {
	$new_clause = '1';
	$pert = "100 as pert";
} else {
$new_clause = $members["where"];
}
if (!isset($pert)) {
	$pert=$members["select"]." as pert";
}

//droits d'acces emprunteur/notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_2= $ac->setDomain(2);
	$acces_j= $dom_2->getJoin($_SESSION['id_empr_session'],16,'notice_id');
}

if ($acces_j) {

	$q_restrict = "select notice_id from notices ".$acces_j." where ".(($restrict_typdoc)?$restrict_typdoc:'1 ');
	$q_restrict.= (($restrict_opac_view)?'and '.$restrict_opac_view:'');

	//Pour rester compatible avec l'ancienne version
	$statut_j='';

} else {

	$q_restrict = "select notice_id from notices where ".(($restrict_typdoc)?$restrict_typdoc:'1 ');
	$q_restrict.= (($restrict_opac_view)?'and '.$restrict_opac_view:'');
	$q_restrict.= "and statut in (select id_notice_statut from notice_statut where (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0))".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"")."))";

	//Pour rester compatible avec l'ancienne version
	$statut_j=',notice_statut';

}

if ($opac_search_other_function) {
	$add_notice = search_other_function_clause();
	if ($add_notice) {
		$q_restrict.= ' and notice_id in ('.$add_notice.')';

		//Pour rester compatible avec l'ancienne version
		$old_clause.= ' and notice_id in ('.$add_notice.')';
		$old_clause_bull.= ' and notice_id in ('.$add_notice.')';
		$old_clause_bull_num_notice.= ' and notice_id in ('.$add_notice.')';
	}
}

//creation table tempo search_result_notices_ contenant les ids des notices visibles pour le lecteur courant.
$tx = substr(preg_replace("/[^a-z0-9]/i","", session_id()),0, 32);
$table_tempo_notices = "search_result_notices_".$tx;
pmb_mysql_query("drop table if exists $table_tempo_notices");
$q_table_tempo_notices = "create temporary table ".$table_tempo_notices." engine=memory ".$q_restrict;
$res_table_tempo_notices = pmb_mysql_query($q_table_tempo_notices);

//ajout index
$q_index_tempo_notices = "alter table ".$table_tempo_notices." add index i_id(notice_id)";
pmb_mysql_query($q_index_tempo_notices);

//creation table tempo search_result_explnum_ contenant les ids des documents numériques et les ids de notices pour monographies/articles.
$table_tempo_explnum = "search_result_explnum_".$tx;
pmb_mysql_query("drop table if exists $table_tempo_explnum");

//droits d'acces emprunteur/document numérique
$acces_j='';
$q_restrict = '';
if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_3= $ac->setDomain(3);
	$acces_j= $dom_3->getJoin($_SESSION['id_empr_session'],16,'explnum_id');
} else {
	$q_restrict= "and explnum_docnum_statut in (select id_explnum_statut from explnum_statut where (explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
}

$q_table_tempo_explnum = "create temporary table $table_tempo_explnum engine=memory select explnum_id, explnum_notice as notice_id, explnum_mimetype, 1.00 as pert from explnum join $table_tempo_notices on explnum_notice=notice_id $acces_j where explnum_notice!=0 $q_restrict ";
$res_table_tempo_explnum = pmb_mysql_query($q_table_tempo_explnum);

//ajout index
$q_index_tempo_explnum = "alter table ".$table_tempo_explnum." add primary key i_id(explnum_id)";
pmb_mysql_query($q_index_tempo_explnum);

//ajout dans la table tempo search_result_explnum_ des ids des documents numériques et des ids de notices pour les notices de periodique des bulletins.
$q_in_tempo_explnum = "insert ignore into $table_tempo_explnum select explnum_id, bulletin_notice as notice_id, explnum_mimetype, 1.00 as pert from explnum join bulletins on explnum_bulletin=bulletin_id $acces_j where num_notice=0 and bulletin_notice in (select notice_id from $table_tempo_notices) $q_restrict";
$res_in_tempo_explnum = pmb_mysql_query($q_in_tempo_explnum);

//ajout dans la table tempo search_result_explnum_ des ids des documents numériques et des ids de notices pour les notices de bulletins.
$q_in_tempo_explnum = "insert ignore into $table_tempo_explnum select explnum_id, num_notice as notice_id, explnum_mimetype, 1.00 as pert from explnum join bulletins on explnum_bulletin=bulletin_id $acces_j where num_notice in (select notice_id from $table_tempo_notices) $q_restrict";
$res_in_tempo_explnum = pmb_mysql_query($q_in_tempo_explnum);

$search_terms = $aq->get_positive_terms($aq->tree);
// On enlève le dernier terme car il s'agit de la recherche booléenne complète
if (!empty($search_terms)) {
    unset($search_terms[count($search_terms)-1]);
}

if(!isset($tri)) {
	$tri="order by pert desc, index_serie, tnvol, index_sew";
}
$nb_result_docnum = 0;


if($new_clause) {

	$s_explnum = 0;
	$nb_explnum = 0;

	//suppression des recherches obsoletes en cache
	$q_cache_del= "delete from search_cache where delete_on_date < NOW()";
	pmb_mysql_query($q_cache_del);

	//recuperation signature recherche
	$str_to_hash = "type_search=explnum";
	$str_to_hash.= $new_clause;
	$sign = md5($str_to_hash);

	//la recherche brute est elle en cache ?
	$q_cache_read = "select value from search_cache where object_id='".addslashes($sign)."'";
	$r_cache_read = pmb_mysql_query($q_cache_read);

	//si oui, recuperation
	if (pmb_mysql_num_rows($r_cache_read)) {
		$o = pmb_mysql_fetch_object($r_cache_read);
		$t_explnum = unserialize($o->value);
		if(count($t_explnum)) {
			$s_explnum = implode(',',array_keys($t_explnum));
		}

		//si non, re-calcul	et mise en cache
	} else {
		// Recherche des documents numeriques correspondants a la recherche.
		$q_explnum = "select distinct(explnum_id), $pert from explnum where $new_clause";
		$r_explnum = pmb_mysql_query($q_explnum);
		$nb_explnum = pmb_mysql_num_rows($r_explnum);
		$t_explnum = array();
		$s_explnum = '';
		if ($nb_explnum) {
			while ($o=pmb_mysql_fetch_object($r_explnum)) {
				$t_explnum[$o->explnum_id]=$o->pert;
			}
		}
		if(count($t_explnum)) {
			$s_explnum = implode(',',array_keys($t_explnum));
		}

		//mise en cache des resultats de la recherche
		$str_to_cache = serialize($t_explnum);
		$q_cache_insert = "insert into search_cache set object_id ='".addslashes($sign)."', value ='".addslashes($str_to_cache)."', delete_on_date = now() + interval ".$opac_search_cache_duration." second";
		pmb_mysql_query($q_cache_insert);

	}

	if (count($t_explnum)) {
		//restriction des resultats
		$q_result_docnum = "delete from $table_tempo_explnum where explnum_id not in (".$s_explnum.") " ;
		$r_result_docnum = pmb_mysql_query($q_result_docnum);

		//Ajout pertinence dans $table_tempo_explnum
		$t_pert = array();
		foreach($t_explnum as $t_id=>$t_pert) {
			$q_pert = "update $table_tempo_explnum set pert=$t_pert where explnum_id=$t_id";
			$r_pert =  pmb_mysql_query($q_pert);
		}
	}

}

$requete = "select explnum_id, uni.notice_id,explnum_mimetype, pert from $table_tempo_explnum as uni join notices n on uni.notice_id=n.notice_id" ;

$nbexplnum = 0;
if($opac_photo_filtre_mimetype){
	$requete_nbexplnum = "select count(*) from $table_tempo_explnum as uni where explnum_mimetype in ($opac_photo_filtre_mimetype)";
	$res_nbexplnum = pmb_mysql_query($requete_nbexplnum);
	if(pmb_mysql_num_rows($res_nbexplnum)) {
		$nbexplnum = pmb_mysql_result($res_nbexplnum,0,0);
	}
}

//gestion du tri
if (isset($_GET["sort"])) {
	$_SESSION["last_sortnotices"]=$_GET["sort"];
}
if ($count > $opac_nb_max_tri) {
	$_SESSION["last_sortnotices"]="";
}
if (!empty($_SESSION["last_sortnotices"])) {
	$sort=new sort('notices','session');
	$requete=$sort->appliquer_tri($_SESSION["last_sortnotices"],$requete,"notice_id",$debut,$opac_search_results_per_page);
} else {
	$requete .= " ".$tri;
	$requete .= " ".$limiter;
}
//fin gestion du tri

$found = pmb_mysql_query($requete);

print "	</div>\n
		<div id=\"resultatrech_liste\">";

if ($opac_notices_depliable) print $begin_result_liste;

//impression
print "<span class='print_search_result'>".$link_to_print_search_result."</span>";

//gestion du tri
print sort::show_tris_in_result_list($count);

print $add_cart_link;
if($opac_visionneuse_allow && $nbexplnum){
    global $link_to_visionneuse, $sendToVisionneuseByPost;
	print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
	print $sendToVisionneuseByPost;
}

//affinage
//enregistrement de l'endroit actuel dans la session
if ($_SESSION["last_query"]) {	$n=$_SESSION["last_query"]; } else { $n=$_SESSION["nb_queries"]; }

$_SESSION["notice_view".$n]["search_mod"]="docnum";
$_SESSION["notice_view".$n]["search_page"]=$page;

//affichage
if($opac_search_allow_refinement){
	print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_simple_search' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
}

//fin affinage

//Etendre
if ($opac_allow_external_search) print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
//fin etendre

print suggest::get_add_link();

require_once("$class_path/explnum_affichage.class.php");
// Ancienne version
$list_explnum = array();
$list_notices_associated = array();
while(($mesNotices = pmb_mysql_fetch_array($found))){
 	$list_explnum[] = $mesNotices["explnum_id"];
 	if (!in_array($mesNotices["notice_id"], $list_notices_associated)) {
 		$list_notices_associated[] = $mesNotices["notice_id"];
 	}
}

if (count($list_notices_associated)) {
	$_SESSION['tab_result_current_page'] = implode(",", $list_notices_associated);
} else {
	$_SESSION['tab_result_current_page'] = "";
}

//$terms = unserialize(stripslashes($search_terms));
$terms = stripslashes_array($search_terms);

$explnum = new explnum_affichage($list_explnum,DOCNUM_NOTI,$terms);
$explnum->show_explnum();

print " </div>\n
		</div>
		</div>";

?>