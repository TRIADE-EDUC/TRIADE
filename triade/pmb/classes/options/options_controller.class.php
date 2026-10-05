<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_controller.class.php,v 1.5 2024/01/19 11:11:02 dgoron Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" )) die ( "no access" );

global $include_path;
require_once ("$include_path/parser.inc.php");
require_once ("$include_path/fields_empr.inc.php");

class options_controller {
	
	protected static $model_class_name = '';
	
	protected static $display_type = '';
	
	public static function proceed() {
		global $options, $first, $msg, $charset;
		global $name, $idchamp;
		
		$instance = new static::$model_class_name();
		$instance->set_display_type(static::$display_type);
		$instance->set_name($name);
		$instance->set_idchamp($idchamp);
		$options = stripslashes($options);
		if ($first == 2) {
		    //Appel pour la classe options_list + option_text_i18n
		    $instance->init_additional_parameters();
			print "<h3>" . $msg['procs_options_param'].$instance->get_name()."</h3><hr />";
			$instance->set_parameters_from_form();
			// Formulaire
			print $instance->get_form();
		} elseif ($first == 1) { // Si enregistrer
			$instance->set_parameters_from_form();
			$parameters = $instance->get_parameters();
			//Rétro-compatibilité : un CDATA était posé sur le libellé du choix vide
			if(!empty($parameters['UNSELECT_ITEM'][0]['value']) && strpos($parameters['UNSELECT_ITEM'][0]['value'], '![CDATA[') === false) {
			    $parameters['UNSELECT_ITEM'][0]['value']="<![CDATA[".$parameters['UNSELECT_ITEM'][0]['value']."]]>";
			}
			$options = array_to_xml($parameters, "OPTIONS");

			print "<script>
        	   opener.document.formulaire.".$instance->get_name()."_options.value='" . str_replace ( "\n", "\\n", addslashes($options)). "';
        		opener.document.formulaire.".$instance->get_name()."_for.value='".$instance->get_type()."';
        	   self.close();
        	</script>";
		} else {
			print "<h3>" . $msg['procs_options_param'].$instance->get_name()."</h3><hr />";
			if (empty($options)) {
				$options = "<OPTIONS></OPTIONS>";
			}
			$param = _parser_text_no_function_( "<?xml version='1.0' encoding='".$charset."'?>\n" . $options, "OPTIONS");
			$instance->set_parameters($param);
			if (! isset ( $param ["FOR"] ) || $param ["FOR"] != $instance->get_type()) {
				$instance->init_default_parameters();
			}
			//Appel pour la classe options_list + option_text_i18n
			$instance->init_additional_parameters();
			// Formulaire
			print $instance->get_form();
		}
	}
	
	public static function set_model_class_name($model_class_name) {
		static::$model_class_name = $model_class_name;
	}
	
	public static function set_display_type($display_type) {
		static::$display_type = $display_type;
	}
}
?>