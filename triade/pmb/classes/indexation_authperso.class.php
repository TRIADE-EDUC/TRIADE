<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_authperso.class.php,v 1.11 2023/04/06 15:28:54 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/indexation_authority.class.php");

//classe de calcul d'indexation des autorités perso...
class indexation_authperso extends indexation_authority {

	protected $id_authperso;
	
	public function __construct($xml_filepath, $table_prefix, $type, $id_authperso) {
		parent::__construct($xml_filepath, $table_prefix, $type);
		$this->id_authperso = intval($id_authperso);
		$this->transform_xml_indexation();
	}
	
	private function transform_xml_indexation(){

		if(is_array(static::$xml_indexation[$this->type]) && count(static::$xml_indexation[$this->type])){
			foreach (static::$xml_indexation[$this->type]['FIELD'] as $i=>$field){
				static::$xml_indexation[$this->type]['FIELD'][$i]['ID'] = str_replace('!!id_authperso!!', $this->id_authperso, $field['ID']);
				if(isset($field['CALLABLE']) && is_array($field['CALLABLE'])){
				    foreach ($field['CALLABLE'] as $j=>$callable){
				        if(!empty($callable['PARAMETERS'])){
				            static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['PARAMETERS'] = str_replace('!!id_authperso!!', $this->id_authperso, $callable['PARAMETERS']);
				        }
					}
				}
				if(isset($field['TABLE']) && is_array($field['TABLE'])){
					foreach ($field['TABLE'] as $j=>$table){
						if(isset($table['LINK']) && is_array($table['LINK'])){
							foreach ($table['LINK'] as $k=>$link){
								static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][$j]['LINK'][$k]['REFERENCEFIELD'][0]['value'] = str_replace('!!id_authperso!!', $this->id_authperso, $link['REFERENCEFIELD'][0]['value']);
								static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][$j]['LINK'][$k]['EXTERNALFIELD'][0]['value'] = str_replace('!!id_authperso!!', $this->id_authperso, $link['EXTERNALFIELD'][0]['value']);
							}
						}
					}	
				}
			}
		}
	}
	
	protected function get_tab_field_insert($object_id, $infos, $order_fields, $isbd, $lang = '', $autorite = 0) {
		$authority = static::get_authority_instance($object_id, $this->type);
		return "(".$authority->get_id().", ".AUT_TABLE_AUTHPERSO.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$order_fields.", '".addslashes(trim($isbd))."', '".addslashes(trim($lang))."', ".$infos["pond"].", ".(intval($autorite)).")";
	}
	
	protected function get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos) {
		$authority = static::get_authority_instance($object_id, $this->type);
		return "(".$authority->get_id().", ".AUT_TABLE_AUTHPERSO.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$num_word.", ".$infos["pond"].", ".$order_fields.", ".$pos.")";
	}
	
	protected static function get_authority_instance($object_id, $object_type) {
	    return authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $object_id, 'type_object' => AUT_TABLE_AUTHPERSO]);
	}
}