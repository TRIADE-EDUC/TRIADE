<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external.inc.php,v 1.21 2022/02/03 11:22:20 dgoron Exp $

//Fonctions pour les recherches externes

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/iso2709.class.php");

function entrepot_to_unimarc($recid) {
	global $charset;
	
	$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($recid).";";
	$myQuery = pmb_mysql_query($requete);
	$source_id = pmb_mysql_result($myQuery, 0, 0);
	
	$requete="select * from entrepot_source_$source_id where recid='".addslashes($recid)."' group by ufield,usubfield,field_order,subfield_order,value order by field_order,subfield_order";
	$resultat = pmb_mysql_query($requete);
	$unimarc=new iso2709_record("",USER_UPDATE);
	if($charset == "utf-8"){//Si on est en UTF-8 alors ce qui sort de la base est en UTF-8 et donc la notice sera en utf-8
		$unimarc->is_utf8 = true;
	}
	$field_order=-1;
	$sfields=array();
	$field = '';
	$field_ind = '';
	
	while ($r=pmb_mysql_fetch_object($resultat)) {
		switch ($r->ufield) {
			case "rs":
				$unimarc->set_rs($r->value);
				break;
			case "dt":
				$unimarc->set_dt($r->value);
				break;
			case "bl":
				$unimarc->set_bl($r->value);
				break;
			case "hl":
				$unimarc->set_hl($r->value);
				break;
			case "el":
				$unimarc->set_el($r->value);
				break;
			case "ru":
				$unimarc->set_ru($r->value);
				break;
			case "ser":
			case "aut":
			case "col":
			case "ind":
				$unimarc->add_field($r->ufield, (isset($r->field_ind) ? $r->field_ind : ''));
			    break;
			case "001":
				$unimarc->add_field("001",'  ',$r->value);
				break;
			default:
				if ($field_order!=$r->field_order) {
					if (count($sfields)) {
						$unimarc->add_field($field,$field_ind,$sfields);
					}
					$field=$r->ufield;
					$field_ind= (isset($r->field_ind) ? $r->field_ind : '');
					while(strlen($field_ind)<2){
						$field_ind.= ' ';
					}
					$sfields=array();
					$field_order=$r->field_order;
				}
				if (!$r->usubfield) 
					$unimarc->add_field($r->ufield,'',$r->value);
				else {
				    $sfields[][0] = (is_numeric($r->usubfield) ? $r->usubfield : strtolower($r->usubfield));
					$sfields[count($sfields)-1][1]=$r->value;
				}
				break;
		}
	}
	if (count($sfields)) {
		$unimarc->add_field($field,$field_ind,$sfields);
	}
	$unimarc->update();
	return array('notice' => $unimarc->full_record, 'source_id' => $source_id);
}

function suppr_item_to_entrepot($item){
	$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($item).";";
	$myQuery = pmb_mysql_query($requete);
	//on évite les mauvaises surprises (genre rechargement de page...)
	if(pmb_mysql_num_rows($myQuery)){
		$source_id = pmb_mysql_result($myQuery, 0, 0);
		if($source_id){
			//on supprime les documents numériques intégrés en tant que fichiers
			$q = "select value as file_name from entrepot_source_$source_id where recid='".addslashes($item)."' and ufield='897' and usubfield='a' and value like '/%' ";
			$r = pmb_mysql_query($q);
			if (pmb_mysql_num_rows($r)) {
				while ($row = pmb_mysql_fetch_object($r)) {
					@unlink($row->file_name);
				}
			}
			//on supprime les infos
			$requete="delete from entrepot_source_$source_id where recid='".addslashes($item)."'";
			pmb_mysql_query($requete);
			//on supprime la référence
			$requete = "delete from external_count WHERE rid=".addslashes($item).";";
			$myQuery = pmb_mysql_query($requete);
			
		}
	}
}