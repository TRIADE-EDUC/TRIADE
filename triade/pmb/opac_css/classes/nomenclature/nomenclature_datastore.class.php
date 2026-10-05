<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_datastore.class.php,v 1.2 2022/09/12 09:19:11 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/encoding_normalize.class.php');

class nomenclature_datastore {
		
	protected static function get_datas(){
		$datas = array();
		$datas_formations = array();
		$query = "select id_formation, formation_name, formation_nature, formation_order from nomenclature_formations order by formation_order";
		$result = pmb_mysql_query($query);
		if($result){
			while($row = pmb_mysql_fetch_object($result)){
				$data = array();
				$data["id"] = $row->id_formation;
				$data["name"] = $row->formation_name;
				$data["nature"] = $row->formation_nature;
				$data["order"] = $row->formation_order;
				$data["types"]=array();
				//récupération des types
				$query_types = "select id_type, type_name, type_formation_num, type_order from nomenclature_types where type_formation_num = ".$row->id_formation." order by type_order asc";
				$result_types = pmb_mysql_query($query_types);
				if($result_types){
					while($row_types = pmb_mysql_fetch_object($result_types)){
						$data_types = array();
						$data_types["id"] = $row_types->id_type;
						$data_types["name"] = $row_types->type_name;
						$data_types["formation_num"] = $row_types->type_formation_num;
						$data_types["order"] = $row_types->type_order;
						$data["types"][] = $data_types;
					}
				}
				$datas_formations[] = $data;
			}
		}
		
		$datas_families = array();
		$query = "select id_family, family_name from nomenclature_families order by family_order asc";
		$result = pmb_mysql_query($query);
		if($result){
			while($row = pmb_mysql_fetch_object($result)){
				$data = array();
				$data["id"] = $row->id_family;
				$data["name"] = $row->family_name;
				//récupération des pupitres
				$data["musicstands"]=array();
				$query_musicstands = "select id_musicstand, musicstand_name, musicstand_division, musicstand_workshop, id_instrument, instrument_code, instrument_name from nomenclature_musicstands left join nomenclature_instruments on nomenclature_musicstands.id_musicstand = nomenclature_instruments.instrument_musicstand_num and instrument_standard = 1 where musicstand_famille_num = ".$row->id_family." order by musicstand_order asc";
				$result_musicstands = pmb_mysql_query($query_musicstands);
				if($result_musicstands){
					while($row_musicstands = pmb_mysql_fetch_object($result_musicstands)){
						$data_musicstand = array();
						$data_musicstand["id"] = $row_musicstands->id_musicstand;
						$data_musicstand["name"] = $row_musicstands->musicstand_name;
						$data_musicstand["divisable"] = ($row_musicstands->musicstand_division ? true : false);
						$data_musicstand["used_by_workshops"] = ($row_musicstands->musicstand_workshop ? true : false);
						$data_musicstand["std_instrument"] = array(
							"id" => $row_musicstands->id_instrument,
							"code" => $row_musicstands->instrument_code,
							"name" => $row_musicstands->instrument_name
						);
						$data["musicstands"][] = $data_musicstand;
					}
				}
				$datas_families[] = $data;
			}
		}
		
		$datas_instruments = array();
		$query = "select id_instrument, instrument_code, instrument_name, instrument_musicstand_num, instrument_standard from nomenclature_instruments order by instrument_name";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$data = array();
				$data["id"] = $row->id_instrument;
				$data["code"] = $row->instrument_code;
				$data["name"] = $row->instrument_name;
				$data["musicstand_num"] = $row->instrument_musicstand_num;
				$data["standard"] = $row->instrument_standard;
				$datas_instruments[] = $data;
			}
		}
		$datas_voices = array();
		$query = "select * from nomenclature_voices order by voice_order, voice_code, voice_name";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$data = array();
				$data["id"] = $row->id_voice;
				$data["code"] = $row->voice_code;
				$data["name"] = $row->voice_name;
				$data["order"] = $row->voice_order;
				$datas_voices[] = $data;
			}
		}
		$datas["formations_datastore"] = $datas_formations;
		$datas["families_datastore"] = $datas_families;
		$datas["instruments_datastore"] = $datas_instruments;
		$datas["voices_datastore"] = $datas_voices;
		return $datas;
	}

	protected static function get_messages(){
		global $msg;
		
		$messages = array();
		foreach ($msg as $code=>$value) {
			if(substr($code,0,15) == "nomenclature_js") {
				$messages[$code] = $value;
			}
		}
		return $messages;
	}
	
	public static function get_form(){
		global $pmb_nomenclature_record_children_link;
		
		$datas = nomenclature_datastore::get_datas();
		$messages = nomenclature_datastore::get_messages();

		$formations_json_datastore = json_encode(encoding_normalize::utf8_normalize($datas["formations_datastore"]),JSON_HEX_APOS | JSON_HEX_QUOT);
		$families_json_datastore = json_encode(encoding_normalize::utf8_normalize($datas["families_datastore"]),JSON_HEX_APOS | JSON_HEX_QUOT);
		$instruments_json_datastore = json_encode(encoding_normalize::utf8_normalize($datas["instruments_datastore"]),JSON_HEX_APOS | JSON_HEX_QUOT);
		$voices_json_datastore = json_encode(encoding_normalize::utf8_normalize($datas["voices_datastore"]),JSON_HEX_APOS | JSON_HEX_QUOT);
		$messages_json_datastore = json_encode(encoding_normalize::utf8_normalize($messages),JSON_HEX_APOS | JSON_HEX_QUOT);
		
		$div="
  		<div id='nomenclature_datastore' data-dojo-type='apps/nomenclature/nomenclature_datastore' data-dojo-props= 'relation_code:\"".$pmb_nomenclature_record_children_link."\",formations_datastore:\"".addslashes($formations_json_datastore)."\" ,families_datastore:\"".addslashes($families_json_datastore)."\" , instruments_datastore:\"".addslashes($instruments_json_datastore)."\" , voices_datastore:\"".addslashes($voices_json_datastore)."\", messages_datastore:\"".addslashes($messages_json_datastore)."\"'/></div>";
		return $div;
	}	
}