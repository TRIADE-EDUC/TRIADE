<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_code_exemplaire_monarch.php,v 1.4 2022/03/10 14:06:00 dgoron Exp $

function init_gen_code_exemplaire($notice_id,$bull_id){
	$requete="select max(expl_cb)as cb from exemplaires WHERE expl_pnb_flag=0 and expl_cb like 'GEN%'";
	$query = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($query)) {	
    	if(($cb = pmb_mysql_fetch_object($query)))
			$code_exemplaire= $cb->cb;
		else $code_exemplaire = "GEN000000"; 	
	} else $code_exemplaire = "GEN000000"; 
	return $code_exemplaire;  	   						
}

function gen_code_exemplaire($notice_id,$bull_id,$code_exemplaire){
	$code_exemplaire++;
	return $code_exemplaire;
}

/*
 * Fonction de calcul de la clé MONARCH
 * Non utilisé pour l'instant
 */
function monarch_key($barreCode){
	if(strlen($barreCode) == 13){
		$n4=0;
		foreach(preg_split('//', $barreCode,null,PREG_SPLIT_NO_EMPTY) as $index=>$char){
			if($index&1){
				$n4=$n4+intval($char);
			}else{
				foreach(preg_split('//', intval($char)*2,null,PREG_SPLIT_NO_EMPTY) as $nb){
					$n4=$n4+intval($nb);
				}
			}
		}
		return (10-($n4%10));
	}
}