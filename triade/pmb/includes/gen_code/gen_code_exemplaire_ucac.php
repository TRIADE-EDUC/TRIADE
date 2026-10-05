<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_code_exemplaire_ucac.php,v 1.6 2019/12/17 07:54:30 dbellamy Exp $

function init_gen_code_exemplaire($notice_id,$bull_id)
{
	if ($notice_id) {
		$requete="select max(expl_cb) from exemplaires where expl_pnb_flag=0 and expl_cb=expl_cb*1 and expl_notice!=0 ";
		$code_exemplaire = pmb_mysql_result(pmb_mysql_query($requete),0,0);
	} else if ($bull_id) {
		$requete="select max(substr(expl_cb,1)) from exemplaires where expl_pnb_flag=0 and expl_bulletin!=0 and expl_cb like 'P%'";
		$code_exemplaire = pmb_mysql_result(pmb_mysql_query($requete),0,0);
	}
	return $code_exemplaire;  	   						
}

function gen_code_exemplaire($notice_id,$bull_id,$code_exemplaire)
{
	$code_exemplaire++;
	if ($notice_id)
		return str_pad($code_exemplaire,6,"0",STR_PAD_LEFT);
	else if ($code_exemplaire[0]!="P")
		return "P".str_pad($code_exemplaire,6,"0",STR_PAD_LEFT);
	else return $code_exemplaire;
}