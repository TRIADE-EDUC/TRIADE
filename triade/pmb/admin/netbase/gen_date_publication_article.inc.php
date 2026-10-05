<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_date_publication_article.inc.php,v 1.10 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $v_state, $spec;

$req="select date_date,analysis_notice from analysis,bulletins where analysis_bulletin=bulletin_id";	
$res=pmb_mysql_query($req);	
if(pmb_mysql_num_rows($res))while (($row = pmb_mysql_fetch_object($res))) {
	$year=substr($row->date_date,0,4);
	if($year) {
		$req="UPDATE notices SET year='$year', update_date=update_date where notice_id=".$row->analysis_notice;
		pmb_mysql_query($req);
	}		
} 

$spec = $spec - GEN_DATE_PUBLICATION_ARTICLE;

$v_state=urldecode($v_state);
$v_state .= netbase::get_display_progress_v_state($msg["gen_date_publication_article_end"]);

print netbase::get_process_state_form($v_state, $spec);
