<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_contribution.class.php,v 1.2 2022/01/28 15:29:38 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/shorturl/shorturl_type.class.php");
require_once($class_path.'/contribution_area/contribution_area_form_resolver.class.php');

class shorturl_type_contribution extends shorturl_type{
	
	protected function contribute() {
		global $opac_url_base;
		$context = unserialize($this->context);
		$url = $opac_url_base.'/index.php?lvl='.$context['lvl'].'&sub='.$context['sub'].'&area_id='.$context['area_id'].'&scenario='.$context['scenario'].'&form_id='.$context['form_id'].'&form_uri='.$context['form_uri'];
		if(isset($context['from_record'])){
		    $url .= "&from_record={$context['from_record']}";
		}
		$_SESSION['contribution_default_fields'] = $context['default_fields'];
		//redirection simple
		header('Location: '.$url);
	}
	
	public function generate_hash($action,$context=array()) {
		$hash = '';
		$context['lvl'] = 'contribution_area';
		
		$forms = contribution_area_form_resolver::get_contribution_forms_from_entity_type($context['sub']);
		// Pour l'instant on prend le premier renvoyé
		$context['area_id'] = str_replace('http://www.pmbservices.fr/ca/Area#', '', $forms[0]['area_uri']);
		$context['scenario'] = str_replace('http://www.pmbservices.fr/ca/Scenario#', '', $forms[0]['scenario_uri']);
		$context['form_id'] = $forms[0]['form_id'];
		$context['form_uri'] = str_replace('http://www.pmbservices.fr/ca/Form#', '', $forms[0]['form_uri']);
		if(method_exists($this, $action)){
			$hash = self::create_hash('contribution', $action, $context);
		}
		return $hash;
	}
}