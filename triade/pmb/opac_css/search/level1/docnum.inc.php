<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docnum.inc.php,v 1.40 2023/08/18 13:18:20 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $gestion_acces_active, $gestion_acces_empr_notice;
global $gestion_acces_empr_docnum;
global $opac_stemming_active;
global $opac_search_cache_duration;

// premier niveau de recherche OPAC sur les documents numériques

// inclusion classe pour affichage notices (level 1)
require_once($base_path.'/includes/templates/notice.tpl.php');
require_once($base_path.'/classes/notice.class.php');

$restrict_opac_view='';
if (isset($_SESSION["opac_view"]) && isset($_SESSION["opac_view_query"])) {
	$restrict_opac_view=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
}
$restrict_typdoc = '';
if ($typdoc) {
	$restrict_typdoc = "typdoc='".addslashes($typdoc)."'";
}

//droits d'acces emprunteur/notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_2= $ac->setDomain(2);
	$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],16,'notice_id');
}

//Pour rester compatible avec l'ancienne version
$old_clause = '';
$old_clause_bull = '';
$old_clause_bull_num_notice = '';

$aq=new analyse_query(stripslashes($user_query),0,0,1,0,$opac_stemming_active);
$members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_id",'',0,0,true);
if($user_query=='*') {
	$new_clause='1';
	$pert = "100 as pert";
} else {
$new_clause = $members["where"];
$pert=$members["select"]." as pert";
}


if ($acces_j) {

	$q_restrict = "select notice_id from notices ".$acces_j." where ".(($restrict_typdoc)?$restrict_typdoc:'1 ');
	$q_restrict.= (($restrict_opac_view)?'and '.$restrict_opac_view:'');

	//Pour rester compatible avec l'ancienne version
	$old_members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice"," explnum_notice=notice_id and explnum_bulletin=0",0,0,true);
	$old_clause="where ".$old_members["where"]." and (".$old_members["restrict"].")";
	$old_members_bull=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin"," explnum_bulletin=bulletin_id and explnum_notice=0 and num_notice=0 and bulletin_notice=notice_id",0,0,true);
	$old_clause_bull="where ".$old_members_bull["where"]." and (".$old_members_bull["restrict"].")";
	$old_members_bull_num_notice=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin"," explnum_bulletin=bulletin_id and num_notice=notice_id",0,0,true);
	$old_clause_bull_num_notice="where ".$old_members_bull_num_notice["where"]." and (".$old_members_bull_num_notice["restrict"].")";
	$statut_j='';

} else {

	$q_restrict = "select notice_id from notices where ".(($restrict_typdoc)?$restrict_typdoc:'1 ');
	$q_restrict.= (($restrict_opac_view)?'and '.$restrict_opac_view:'');
	$q_restrict.= "and statut in (select id_notice_statut from notice_statut where (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0))".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"")."))";

	//Pour rester compatible avec l'ancienne version
	$old_members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice" ," explnum_notice=notice_id and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
	$old_clause="where ".$old_members["where"]." and (".$old_members["restrict"].")";
	$old_members_bull=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin" ," explnum_bulletin=bulletin_id and bulletin_notice=notice_id and num_notice=0 and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
	$old_clause_bull="where ".$old_members_bull["where"]." and (".$old_members_bull["restrict"].")";
	$old_members_bull_num_notice=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin" ," explnum_bulletin=bulletin_id and num_notice=notice_id and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
	$old_clause_bull_num_notice="where ".$old_members_bull_num_notice["where"]." and (".$old_members_bull_num_notice["restrict"].")";
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

//Pour rester compatible avec l'ancienne version
if ($restrict_typdoc) {
	$old_clause.=" and ".$restrict_typdoc;
	$old_clause_bull.=" and ".$restrict_typdoc;
	$old_clause_bull_num_notice.=" and ".$restrict_typdoc;
}
if($restrict_opac_view)  $old_clause.=" and ".$restrict_opac_view;


//creation table tempo search_result_notices_ contenant les ids des notices visibles pour le lecteur courant.
$tx = substr(preg_replace("/[^a-z0-9]/i","", session_id()),0, 32);
$table_tempo_notices = "search_result_notices_".$tx;
pmb_mysql_query("drop table if exists $table_tempo_notices");
$q_table_tempo_notices = "create temporary table ".$table_tempo_notices." engine=memory ".$q_restrict ;
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

$q_table_tempo_explnum = "create temporary table $table_tempo_explnum engine=memory select explnum_id, explnum_notice as notice_id from explnum join $table_tempo_notices on explnum_notice=notice_id $acces_j where explnum_notice!=0 $q_restrict";
$res_table_tempo_explnum = pmb_mysql_query($q_table_tempo_explnum);

//ajout dans la table tempo search_result_explnum_ des ids des documents numériques et des ids de notices pour les notices de periodique des bulletins.
$q_in_tempo_explnum = "insert ignore into $table_tempo_explnum select explnum_id, bulletin_notice as notice_id from explnum join bulletins on explnum_bulletin=bulletin_id $acces_j where num_notice=0 and bulletin_notice in (select notice_id from $table_tempo_notices) $q_restrict";
$res_in_tempo_explnum = pmb_mysql_query($q_in_tempo_explnum);

//ajout dans la table tempo search_result_explnum_ des ids des documents numériques et des ids de notices pour les notices de bulletins.
$q_in_tempo_explnum = "insert ignore into $table_tempo_explnum select explnum_id, num_notice as notice_id from explnum join bulletins on explnum_bulletin=bulletin_id $acces_j where num_notice in (select notice_id from $table_tempo_notices) $q_restrict";
$res_in_tempo_explnum = pmb_mysql_query($q_in_tempo_explnum);

$search_terms = $aq->get_positive_terms($aq->tree);
//On enlève le dernier terme car il s'agit de la recherche booléenne complète
if (!empty($search_terms)) {
    unset($search_terms[count($search_terms)-1]);
}

$tri="order by pert desc, index_serie, tnvol, index_sew";

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
			if(count($t_explnum)) {
				$s_explnum = implode(',',array_keys($t_explnum));
			}

			//mise en cache des resultats de la recherche
			$str_to_cache = serialize($t_explnum);
			$q_cache_insert = "insert into search_cache set object_id ='".addslashes($sign)."', value ='".addslashes($str_to_cache)."', delete_on_date = now() + interval ".$opac_search_cache_duration." second";
			pmb_mysql_query($q_cache_insert);
		}
	}

	//restriction des resultats
	$nb_result_docnum=0;
	if($s_explnum) {
		$q_nb_result_docnum = "select count(distinct(explnum_id)) from $table_tempo_explnum where explnum_id in (".$s_explnum.") " ;
		$r_nb_result_docnum = pmb_mysql_query($q_nb_result_docnum);
		if($r_nb_result_docnum && pmb_mysql_num_rows($r_nb_result_docnum)){
			$nb_result_docnum = pmb_mysql_result($r_nb_result_docnum,0,0);
		}
	}

	//recherche des types de documents des notices concernees
	$t_typdoc=array();
	if($s_explnum) {
		$req_typdoc = "select distinct(typdoc) from notices join $table_tempo_explnum on notices.notice_id=$table_tempo_explnum.notice_id where explnum_id in (".$s_explnum.")";
		$res_typdoc = pmb_mysql_query($req_typdoc);
		if($res_typdoc && pmb_mysql_num_rows($res_typdoc)){
			while (($tpd=pmb_mysql_fetch_object($res_typdoc))) {
				$t_typdoc[]=$tpd->typdoc;
			}
		}
	}
	$l_typdoc=implode(',',$t_typdoc);


	if ($nb_result_docnum) {
	    global $opac_rgaa_active, $msg;

	    $template = "%s";
		if ($opac_rgaa_active) {
    	    $template = "<div id='docnum_result'>%s</div>";
		}

		$template_result = '<strong>'.$msg['docnum'].'</strong> '.$nb_result_docnum.' '.$msg['results'].' ';


		// si il y a d'autres résultats, je met le lien 'plus de résultats'
		// Le lien validant le formulaire est inséré avant le formulaire, cela évite les blancs à l'écran
		if ($opac_rgaa_active) {
    		$template_result .= "<button type='button' class='search_result' onclick=\"
    		    document.forms['search_docnum'].submit();
    		    return false;\">
    		    ".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />
            </button>";
		} else {
    		$template_result .= "<a href=\"#\" onclick=\"
    		    document.forms['search_docnum'].submit();
    		    return false;\">
    		    ".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />
            </a>";
		}

		$form = "<div class='search_result'><form name=\"search_docnum\" action=\"./index.php?lvl=more_results\" method=\"post\">";
		$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
		if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
		$form .= "<input type=\"hidden\" name=\"mode\" value=\"docnum\">\n";
		$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_docnum."\">\n";
		$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($old_clause,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"clause_bull\" value=\"".htmlentities($old_clause_bull,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"clause_bull_num_notice\" value=\"".htmlentities($old_clause_bull_num_notice,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"search_terms\" value=\"".htmlentities(serialize($search_terms),ENT_QUOTES,$charset)."\"></form></div>\n";

		print sprintf($template, $template_result.$form);
	}
}

if ($nb_result_docnum) {
	$_SESSION["level1"]["docnum"]["form"]=$form;
	$_SESSION["level1"]["docnum"]["count"]=$nb_result_docnum;
}
