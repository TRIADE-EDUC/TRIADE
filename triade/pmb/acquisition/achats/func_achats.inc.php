<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_achats.inc.php,v 1.29 2023/08/02 07:36:48 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $acquisition_custom_calc_numero, $base_path;

if(!isset($acquisition_custom_calc_numero)) $acquisition_custom_calc_numero = '';
if ($acquisition_custom_calc_numero && file_exists($base_path."/acquisition/achats/".$acquisition_custom_calc_numero)) {
	require_once($base_path."/acquisition/achats/".$acquisition_custom_calc_numero);
} else {

	//Calcul du numero d'acte
	function calcNumero($id_entite, $type_acte) {

		global $acquisition_format;

		$p = array();
		$p = explode(",",$acquisition_format);
		$prefix = $p[$type_acte+1];

		//recuperation du dernier numero pour le type d'acte concerné et l'entité en cours
		$q = "select max(substring(numero,".(strlen($prefix)+1).")*1) from actes where type_acte = '".$type_acte."' ";
		$q.= "and num_entite = '".$id_entite."' ";
		$r = pmb_mysql_query($q);

		$res = pmb_mysql_result($r,0,0);
		$res = empty($res) ? 0 : intval($res);

		//creation du numéro avec prefixe et padding
		$res++;
		$res = (string) $res;
		$numero = $prefix;
		if ($p[0] != 0 ) {
			$numero = str_pad($numero, $p[0]-strlen($res)+strlen($prefix), '0').$res;
		} else {
			$numero = $numero.$res;
		}
		return $numero;
	}
}


//Calcule les montants ht, ttc et tva
//a partir d'un tableau
//[index]['q']=qte
//[index]['p']=prix
//[index]['r']=remise %
//[index]['t']=tva %
//
//et retourne un tableau
//['ht']=montant ht
//['ttc']=montant ttc
//['tva']=montant tva
//
//precision = nb decimales
function calc($tab, $precision=0) {

	global $acquisition_gestion_tva;

	$mnt_ht=0;
	$mnt_tva=0;
	$mnt_ttc=0;

	foreach($tab as $v) {
		switch($acquisition_gestion_tva) {
			case '1' :	//saisie des prix ht
				$mnt_ht=$mnt_ht+($v['q']*$v['p']*((100-$v['r'])/100));
				$mnt_tva=$mnt_tva+($v['q']*$v['p']*((100-$v['r'])/100)*($v['t']/100));
				if(isset($v['debit_tva']) && $v['debit_tva']==2){ // on ajoute le montant de la TVA
					$mnt_ht+=($v['q']*$v['p']*((100-$v['r'])/100)*($v['t']/100));
				}
				break;
			case '2' :	//saisie des prix ttc
				$mnt_ttc=$mnt_ttc+($v['q']*$v['p']*((100-$v['r'])/100));
				$mnt_ht=$mnt_ht+(($v['q']*$v['p']*((100-$v['r'])/100))/(1+($v['t']/100))) ;
				if (isset($v['debit_tva']) && $v['debit_tva'] == 1) { // on enlève le montant de la TVA
					$mnt_ttc-=($v['q']*$v['p']*((100-$v['r'])/100)) - (($v['q']*$v['p']*((100-$v['r'])/100))/(1+($v['t']/100)));
				}
				break;
			default:	//pas de gestion de tva
				$mnt_ttc=$mnt_ttc+($v['q']*$v['p']*((100-$v['r'])/100));
				break;
		}
	}
	$tot_ht = 0;
	$tot_tva = 0;
	switch($acquisition_gestion_tva) {
		case '1' :
			$tot_ht=$mnt_ht;
			$tot_tva=$mnt_tva;
			$tot_ttc=($mnt_ht+$mnt_tva);
			break;
		case '2' :
			$tot_ht=$mnt_ht;
			$tot_tva=($mnt_ttc-$mnt_ht);
			$tot_ttc=$mnt_ttc;
			break;
		default :
			$tot_ttc=$mnt_ttc;
			break;
	}
	$tot = array();
	if ($precision) {
		$tot['ttc']=round($tot_ttc,$precision);
		$tot['ht']=round($tot_ht,$precision);
		$tot['tva']=round($tot_tva,$precision);
	} else {
		$tot['ttc']=$tot_ttc;
		$tot['ht']=$tot_ht;
		$tot['tva']=$tot_tva;
	}
	return $tot;
}
?>