<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_code_exemplaire_num.php,v 1.2 2019/12/17 07:49:27 dbellamy Exp $

function init_gen_code_exemplaire($notice_id,$bull_id) {
	$query="select max(expl_cb)as cb from exemplaires WHERE expl_pnb_flag=0 and expl_cb REGEXP '^[0-9]*$'";
	$result = pmb_mysql_query($query);
	$code_exemplaire = pmb_mysql_result($result, 0, 0);
	if(!$code_exemplaire) {
		$code_exemplaire = "0";
	}
	return $code_exemplaire;  	   						
}

function gen_code_exemplaire($notice_id,$bull_id,$code_exemplaire) {
	if(preg_match("/(\D*)([0-9]*)/",$code_exemplaire,$matches)){
		$len = strlen($matches[2]);
		$matches[2]++;
		$code_exemplaire=$matches[1].str_pad($matches[2],$len,"0",STR_PAD_LEFT);
	} else{
		$code_exemplaire++;
	}
	return $code_exemplaire;
}