<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections_state.inc.php,v 1.16 2022/02/11 09:39:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $object_type, $fname;

require_once($class_path.'/caddie.class.php');
require_once($class_path.'/encoding_normalize.class.php');

function ajax_calculate_collections_state() {
	global $id_location,$id_serial, $bulletins;
	
	if($bulletins != '') {
		$rqt="select bulletin_id,bulletin_numero,mention_date from bulletins where bulletin_id IN (".$bulletins.") order by date_date";
	} else {
		$rqt="select bulletin_id,bulletin_numero,mention_date from bulletins where bulletin_notice=$id_serial order by date_date";
	}
	
	$execute_query=pmb_mysql_query($rqt);
	$compt=pmb_mysql_num_rows($execute_query);
	if(!$compt) return '';
	$temp="";
	$i=0;
	$debut="";
	$t=array();
	
	//est-ce que l'état des collections est localisé
	if ($id_location){
		$restrict_location=" and expl_location=$id_location";
	}else{
		$restrict_location="";
	}
	
	//parcours des bulletins de la notice de périodique
	while ($r=pmb_mysql_fetch_object($execute_query)) {
		
		$rqt1="select expl_id from exemplaires where expl_bulletin=".$r->bulletin_id.$restrict_location;
		$compt1=pmb_mysql_num_rows(pmb_mysql_query($rqt1));
		$temp=pmb_mysql_error();
		//remplissage d'un tableau avec des trous si le bulletin n'a aucun exemplaire associé
		if ($compt1==0) {
			$t[]="";
		} else {
			$item=$r->bulletin_numero;
			if ($r->mention_date) $item.=" (".$r->mention_date.")";
			$t[]=$item;
			//détermination du premier bulletin de la liste qui a des exemplaires associés 
			if ($debut === "") $debut=count($t)-1;
			//comptage des bulletins avec des exemplaires associés
			$i++;
		}
	}
	//si tous les bulletins ont des exemplaires associés, on prend l'intégralité de la liste
	
	if ($i==$compt) {
		$all="";
		$all.=$t[$debut];
		$all.=" - ";
		$j=count($t)-1;
		$all.=$t[$j];
		$temp=$all;
	} else {
		$tableau_final=array();
		//parcours du tableau final
		for ($j=0;$j<count($t);$j++) {
			//si l'élément n'est pas un trou
			if ($t[$j]!="") {
				$temp1=$t[$j];
				$bool=false;
				//parcours du tableau à partir de l'élément jusqu'au premier trou existant
				for ($x=$j;$x<count($t);$x++) {
					if ($t[$x]=="") {
						if ($t[$x-1]!=$t[$j]) $temp1.=" - ".$t[$x-1];
						$j=$x;
						$x=count($t);
						$bool=true;					
					}	
				}
				//si aucun trou jusqu'à la fin n'est trouvé, on finit la borne par le dernier
				//numéro et on quitte la boucle de parcours
				if ($bool==false) {
					$temp1.=" - ".$t[count($t)-1];
					$j=count($t);
				}
				//on remplit un tableau avec les intervalles trouvés
				$tableau_final[]=$temp1;
			} else {
				//on remplit un tableau avec l'élément trouvé
				if ($t[$j-1]!="") $tableau_final[]=$t[$j-1];
			}
		}
		$temp=implode(";",$tableau_final);
	}
	ajax_http_send_response($temp,"text/text");
	
	return;
}

function ajax_modify_collections_state() {
	global $id_serial,$id_location,$texte_coll_state;
	
	if ($id_location) $restrict_location=" and location_id=$id_location";
	$rqt1="select state_collections from collections_state where id_serial=$id_serial $restrict_location";
	$execute_query1=pmb_mysql_query($rqt1);
	if (pmb_mysql_num_rows($execute_query1)) $rqt2="update collections_state set state_collections='".$texte_coll_state."' where id_serial=$id_serial $restrict_location";	
		else $rqt2="insert into collections_state (id_serial,location_id,state_collections) values ('$id_serial','$id_location','".$texte_coll_state."')";
	@pmb_mysql_query($rqt2);
	if (pmb_mysql_error()) $texte_coll_state=pmb_mysql_error();
	
	ajax_http_send_response($texte_coll_state,"text/text");
	return;
}

function ajax_add_expl_to_carts() {
	global $id_caddie_bull, $id_caddie_expl, $cb_expl;
	
	$id_caddie_bull = intval($id_caddie_bull);
	$id_caddie_expl = intval($id_caddie_expl);
	
	if (!$cb_expl || !$id_caddie_bull || !$id_caddie_expl) {
		return 0;
	}
	if(!caddie::is_reachable($id_caddie_bull) || !caddie::is_reachable($id_caddie_expl)) {
		return 0;
	}
	$query = 'select expl_id from exemplaires where expl_cb = "'.$cb_expl.'"';
	$result = pmb_mysql_query($query);
	if (pmb_mysql_num_rows($result)) {
		$expl_id = pmb_mysql_result($result, 0, 0);
		$caddie_bull = new caddie($id_caddie_bull);
		$return = $caddie_bull->add_item($expl_id, 'EXPL');
		if ($return != CADDIE_ITEM_OK) {
			return 0;
		}
		$caddie_expl = new caddie($id_caddie_expl);
		$return = $caddie_expl->add_item($expl_id, 'EXPL');
		if ($return != CADDIE_ITEM_OK) {
			return 0;
		}
		return 1;
	}
	return 0;
}

function ajax_get_data_expl_list() {
	global $id_caddie_expl, $id_caddie_bull, $id_location;
	
	$id_caddie_bull = intval($id_caddie_bull);
	$id_caddie_expl = intval($id_caddie_expl);
	$id_location = intval($id_location);
	
	$data = array(
		'caddie_bull' => array(),
		'caddie_expl' => array(),
		'nb_expl' => 0,
		'expl_list' => array(),
		'bulletins' => array(),
		'cote' => ''
	);
	if ($id_caddie_expl && $id_caddie_bull) {
		if(caddie::is_reachable($id_caddie_bull) && caddie::is_reachable($id_caddie_expl)) {
			$data['caddie_bull'] = caddie::get_data_from_id($id_caddie_bull);
			$data['caddie_expl'] = caddie::get_data_from_id($id_caddie_expl);
			$query = 'select count(object_id) from caddie_content join exemplaires on expl_id = object_id and expl_bulletin <> 0 ';
			if($id_location) {
				$query .= 'and expl_location = '.$id_location.' ';
			}
			$query .= 'left join bulletins on expl_bulletin = bulletin_id
					where caddie_id = '.$id_caddie_expl.' and flag is null and expl_bulletin in (select object_id from caddie_content where caddie_id = '.$id_caddie_bull.' and flag is null)';
			$result = pmb_mysql_query($query);
			$data['nb_expl'] = pmb_mysql_result($result, 0, 0);
			if($data['nb_expl']) {
				$query = 'select object_id, expl_notice, expl_bulletin, expl_cote from caddie_content join exemplaires on expl_id = object_id and expl_bulletin <> 0 ';
				if($id_location) {
					$query .= 'and expl_location = '.$id_location.' ';
				}
				$query .= 'left join bulletins on expl_bulletin = bulletin_id 
						where caddie_id = '.$id_caddie_expl.' and flag is null and expl_bulletin in (select object_id from caddie_content where caddie_id = '.$id_caddie_bull.' and flag is null)
						order by date_date limit 100';
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						if($row->expl_bulletin) {
							$expl = new mono_display_expl(0, $row->object_id, 0, './catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id='.$row->expl_bulletin.'&expl_id='.$row->object_id);
						} else {
							$expl = new mono_display_expl(0, $row->object_id, 0, './catalog.php?categ=edit_expl&id='.$row->expl_notice.'&expl_id='.$row->object_id);
						}
						$data['expl_list'][] = $expl->isbd;
						$data['bulletins'][] = $row->expl_bulletin;
						if (!$data['cote']) {
							$data['cote'] = $row->expl_cote;
						}
					}
					$data['bulletins'] = array_unique($data['bulletins']);
				}
			}
		}
	}
	return $data;
}

switch($action) {
	case 'list':
		lists_controller::proceed_ajax($object_type);
		break;
	default:
		switch ($fname) {
			case "calculate_collections_state":
				ajax_calculate_collections_state();
				break;
			case "modify_collections_state":
				ajax_modify_collections_state();
				break;
			case "add_expl":
				print ajax_add_expl_to_carts();
				break;
			case "get_data_expl_list":
				print json_encode(encoding_normalize::utf8_normalize(ajax_get_data_expl_list()));
				break;
			default:
				ajax_http_send_error("404 Not Found","Invalid command : ".$fname);
				break;
		}
		break;
}

?>
