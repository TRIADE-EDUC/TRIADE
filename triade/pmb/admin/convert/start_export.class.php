<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: start_export.class.php,v 1.22.10.1 2025/03/13 16:27:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ("$include_path/parser.inc.php");
require_once ($base_path."/admin/convert/start_import.class.php");
require_once ($base_path."/admin/convert/start_export.class.php");

//Récupération du chemin du fichier de paramétrage de l'import
function _item_start_export_($param) {
	global $export_type;
	global $i;
	global $param_path;
	global $export_type_l;

	if ($i == $export_type) {
		$param_path = $param['PATH'];
		$export_type_l = $param['NAME'];
	}
	$i ++;
}

function _item_export_list_($param) {
	global $export_list;
	global $i, $iall;

	if (isset($param["EXPORT"]) && ($param["EXPORT"]=="yes")) {
		$t=array();
		$t["NAME"]=$param["EXPORTNAME"];
		$t["PATH"]=$param["PATH"];
		$t["ID"]=$i;
		$t["IDALL"]=$iall;
		$export_list[]=$t;
		$i++;
	}
	$iall++;
}

//Récupération du paramètre d'import
function _output_start_export_($param) {
	global $output;
	global $output_type;
	global $output_params;

	$output = $param['IMPORTABLE'];
	$output_type = $param['TYPE'];
	$output_params = $param;
}

function _input_start_export_($param) {
	global $specialexport;
	global $input_type;
	global $input_params;

	$input_type = $param['TYPE'];
	$input_params = $param;

	if (isset($param["SPECIALEXPORT"]) && $param["SPECIALEXPORT"]=="yes") {
		$specialexport=true;
	} else $specialexport=false;
}

//Récupération des étapes de conversion
function _step_start_export_($param) {
	global $step;

	$step[] = $param;
}

//Récupération du nom de l'import
function _import_name_start_export_($param) {
	global $import_name;

	$import_name = $param['value'];
}

class start_export {

	public $export_type;
	public $id_notice;
	public $prepared_notice;
	public $output_notice;
    public $message_convert;
    public $error;
    public $n_errors;
    public $param_export=array();

    public function __construct($id_notice,$type_export,$param_export) {
		global $i;
		global $param_path;
		global $specialexport;
		global $output_type;
		global $output_params;
		global $step;
		global $export_type;
		global $base_path;
		global $class_path;
		global $include_path;
		global $msg;

		$step = array();
		$this->id_notice = (int) $id_notice;
		if (!empty($this->id_notice)) {
			$this->export_type = $type_export;
			$export_type = $type_export;
			$this->param_export = $param_export;

			//Récupération du répertoire
			$i = 0;
			$param_path = "";
			if (file_exists("$base_path/admin/convert/imports/catalog_subst.xml")) {
				$fic_catal = "$base_path/admin/convert/imports/catalog_subst.xml";
			} else {
				$fic_catal = "$base_path/admin/convert/imports/catalog.xml";
			}
			_parser_($fic_catal, array("ITEM" => "_item_start_export_"), "CATALOG");

			//Lecture des paramètres

			_parser_("$base_path/admin/convert/imports/$param_path/params.xml", array("IMPORTNAME" => "_import_name_start_export_", "STEP" => "_step_start_export_", "OUTPUT" => "_output_start_export_", "INPUT" => "_input_start_export_"), "PARAMS");

			//Si l'export est spécial, on charge la fonction d'export
			if (!empty($specialexport)) {
	    		if (file_exists("$base_path/admin/convert/imports/$param_path/$param_path.class.php")) {
					require_once "$base_path/admin/convert/imports/$param_path/$param_path.class.php";
				} else {
					require_once "imports/$param_path/export.inc.php";
				}
			}

			//En fonction du type de fichier de sortie, inclusion du script de gestion des sorties
			$output_instance = start_export::get_instance_from_output_type($output_type);

			$e_notice = array();
			if (!empty($_SESSION["param_export"]["notice_exporte"])) {
			    $notice_exporte = $_SESSION["param_export"]["notice_exporte"];
			} else {
			    $notice_exporte = array();
			}
			if (!empty($_SESSION["param_export"]["bulletin_exporte"])) {
			    $bulletin_exporte = $_SESSION["param_export"]["bulletin_exporte"];
			} else {
			    $bulletin_exporte = array();
			}
			if (empty($specialexport)) {
				$param = new export_param(EXP_DSI_CONTEXT, $this->param_export);
				$e = new export(array($this->id_notice), $notice_exporte, $bulletin_exporte);
				do{
					$nn = $e->get_next_notice("", "", "", 0, $param->get_parametres($param->context));
					if (!empty($e->notice)) {
					    $e_notice[] = $e->notice;
					}
				} while (!empty($nn));
				$notice_exporte = $e->notice_exporte;
				$_SESSION["param_export"]["notice_exporte"] = $notice_exporte;
				//Pour les exemplaires de bulletin
				do {
					$nn = $e->get_next_bulletin("", "", "", 0, $param->get_parametres($param->context));
					if (!empty($e->notice)) {
					    $e_notice[] = $e->notice;
					}
				} while ($nn);
				$bulletin_exporte = $e->bulletins_exporte;
				$_SESSION["param_export"]["bulletin_exporte"] = $bulletin_exporte;
			} else {
				if (class_exists($param_path) && method_exists($param_path, '_export_notice_')) {
					$e_notice = $param_path::_export_notice_($this->id_notice);
				} else {
					$e_notice = _export_($this->id_notice);
				}
			}

			if (!is_array($e_notice)) {
				$this->prepared_notice = $e_notice;
				$this->output_notice .= $this->transform();
			} else {
			    $nb_notices = count($e_notice);
			    for ($i = 0; $i < $nb_notices; $i++) {
					$this->prepared_notice = $e_notice[$i];
					$this->output_notice .= $this->transform();
				}
			}
		}
    }

    public static function get_exports() {
    	global $export_list;
    	global $i, $iall;
    	global $base_path;
    	$i=0;
    	$iall=0;
		if (file_exists("$base_path/admin/convert/imports/catalog_subst.xml"))
			$fic_catal = "$base_path/admin/convert/imports/catalog_subst.xml";
		else
			$fic_catal = "$base_path/admin/convert/imports/catalog.xml";
		_parser_($fic_catal, array("ITEM" => "_item_export_list_"), "CATALOG");
    	return $export_list;
    }

	public function get_header() {
    	global $output_params;
    	global $output_type;

    	if(isset($output_params['SCRIPT'])) {
    		$class_name = str_replace('.class.php', '', $output_params['SCRIPT']);
    		if(class_exists($class_name)) {
    			$instance = new $class_name();
    			return $instance->_get_header_($output_params);
    		}
    	}
    	$output_instance = start_export::get_instance_from_output_type($output_type);
    	if(is_object($output_instance)) {
    		return $output_instance->_get_header_($output_params);
    	} elseif (function_exists("_get_header_")) {
    		return _get_header_($output_params);
    	}
    }

    public function get_footer() {
    	global $output_params;
    	global $output_type;

    	if(isset($output_params['SCRIPT'])) {
    		$class_name = str_replace('.class.php', '', $output_params['SCRIPT']);
    		if(class_exists($class_name)) {
    			$instance = new $class_name();
    			return $instance->_get_footer_($output_params);
    		}
    	}
    	$output_instance = start_export::get_instance_from_output_type($output_type);
    	if(is_object($output_instance)) {
    		return $output_instance->_get_footer_($output_params);
    	} elseif (function_exists("_get_footer_")) {
    		return _get_footer_($output_params);
    	}
    }

    public function transform() {
   		global $step;
		global $param_path;
		global $n_errors;
		global $message_convert;
    	global $input_type;
    	global $base_path;
    	global $include_path;
    	global $class_path;
    	global $input_params;
    	global $msg;

    	$notice=$this->prepared_notice;

    	//Inclusion des librairies éventuelles
		if (isset($step) && is_countable($step)) {
			for ($i = 0; $i < count($step); $i ++) {
				if ($step[$i]['TYPE'] == "custom") {
					//echo "imports/".$param_path."/".$step[$i][SCRIPT][0][value];
					require_once ("imports/".$param_path."/".$step[$i]['SCRIPT'][0]['value']);
				}
			}
		}

		require_once ("xmltransform.php");

		//En fonction du type de fichier d'entrée, inclusion du script de gestion des entrées
		$input_instance = start_import::get_instance_from_input_type($input_type);

		if (isset($step) && is_countable($step)) {
			for ($i = 0; $i < count($step); $i ++) {
				$s = $step[$i];
				$islast=($i==count($step)-1);
				$isfirst=($i==0);
				switch ($s['TYPE']) {
						case "xmltransform" :
							$r = perform_xslt($notice, $s, $islast, $isfirst, $param_path);
							break;
						case "toiso" :
							$r = toiso($notice, $s, $islast, $isfirst, $param_path);
							break;
						case "isotoxml" :
							$r = isotoxml($notice, $s, $islast, $isfirst, $param_path);
							break;
						case "texttoxml":
							$r = texttoxml($notice, $s, $islast, $isfirst, $param_path);
							break;
						case "custom" :
							eval("\$r=".$s['CALLBACK'][0]['value']."(\$notice, \$s, \$islast, \$isfirst, \$param_path);");
							break;
				}
				if (!$r['VALID']) {
					$this->n_errors=true;
					$this->message_convert= $r['ERROR'];
					$notice = "";
					break;
				} else {
					$notice = $r['DATA'];
					if(isset($r['WARNING']) && $r['WARNING']){
						$this->message_convert= $r['WARNING'];
					}
				}
			}
		}
		return $notice;
    }

	// Récupération de l'id à partir du nom de l'export
	public static function get_id_by_path($path) {
	   	global $export_list;
	   	if (empty($export_list)) start_export::get_exports() ;
		for ($i=0;$i<count($export_list);$i++) {
			if ($export_list[$i]["PATH"]==$path) return $export_list[$i]["IDALL"] ;
		}
	}

	public static function get_instance_from_output_type($output_type) {
		global $base_path, $msg;
		global $param_path;
		global $output_params;

		switch ($output_type) {
			case "xml" :
				require_once ("$base_path/admin/convert/imports/output_xml.class.php");
				return new output_xml();
			break;
			case "iso_2709" :
				require_once ("$base_path/admin/convert/imports/output_iso_2709.class.php");
				return new output_iso_2709();
			break;
			case "custom" :
				require_once ("$base_path/admin/convert/imports/".$param_path."/".$output_params['SCRIPT']);
				$output_classname = str_replace('.class.php', '', $output_params['SCRIPT']);
				if(class_exists($output_classname)) {
					return new $output_classname();
				} else {
					return;
				}
			break;
			case "txt":
				require_once ("$base_path/admin/convert/imports/output_txt.class.php");
				return new output_txt();
			break;
			default :
				die($msg["export_cant_find_output_type"]);
		}
	}

}

?>