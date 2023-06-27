<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_date_publication_article.inc.php,v 1.6 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$req="select date_date,analysis_notice from analysis,bulletins where analysis_bulletin=bulletin_id";	
$res=pmb_mysql_query($req,$dbh);	
if(pmb_mysql_num_rows($res))while (($row = pmb_mysql_fetch_object($res))) {
	$year=substr($row->date_date,0,4);
	if($year) {
		$req="UPDATE notices SET year='$year' where notice_id=".$row->analysis_notice;
		pmb_mysql_query($req,$dbh);
	}		
} 

$spec = $spec - GEN_DATE_PUBLICATION_ARTICLE;

$v_state=urldecode($v_state);
$v_state.= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["gen_date_publication_article_end"], ENT_QUOTES, $charset).".";

print netbase::get_process_state_form($v_state, $spec);
