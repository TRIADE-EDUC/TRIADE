<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_mails_configuration_domains_ui.class.php,v 1.3 2023/12/26 14:41:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_mails_configuration_domains_ui extends list_mails_configuration_ui {
	
    protected $domains_name = [];
    
	protected function _get_query_base() {
		$query = 'SELECT DISTINCT SUBSTRING(mail_address, POSITION("@" IN mail_address)+1) AS mail_domain FROM (
				SELECT distinct user_email AS mail_address FROM users where user_email != "" 
				UNION
				SELECT distinct email AS mail_address FROM docs_location where email != ""
				UNION
				SELECT SUBSTRING(valeur_param, 1, IF(POSITION(";" IN valeur_param), POSITION(";" IN valeur_param)-1, LENGTH(valeur_param))) AS mail_address FROM parametres WHERE type_param="pmb" AND sstype_param = "mail_adresse_from" and valeur_param != ""
				UNION
				SELECT SUBSTRING(valeur_param, 1, IF(POSITION(";" IN valeur_param), POSITION(";" IN valeur_param)-1, LENGTH(valeur_param))) AS mail_address FROM parametres WHERE type_param="opac" AND sstype_param = "mail_adresse_from" and valeur_param != ""
				UNION
				SELECT valeur_param AS mail_address FROM parametres WHERE type_param = "opac" AND sstype_param = "biblio_email" and valeur_param != ""
				UNION
				SELECT distinct email AS mail_address FROM coordonnees JOIN entites ON entites.id_entite = coordonnees.num_entite WHERE type_entite = 1 and email != ""
				) AS mails LEFT JOIN mails_configuration ON mails_configuration.name_mail_configuration = mails.mail_address AND mails_configuration.mail_configuration_type = "address"';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new mail_configuration($row->mail_domain);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
	}
	
	public function initialization() {
	    foreach ($this->objects as $object) {
            $this->domains_name[] = $object->get_name();
			if(!$object->is_in_database()) {
				$object->initialization();
			}
		}
		//Purge anciens domaines
		if(!empty($this->domains_name) && $this->pager['all_on_page']) {
		    $query = "SELECT name_mail_configuration 
                FROM mails_configuration 
                WHERE mail_configuration_type = 'domain' 
                AND name_mail_configuration NOT IN ('".implode("','", addslashes_array($this->domains_name))."')";
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)) {
    		    while($row = pmb_mysql_fetch_object($result)) {
    		        mail_configuration::delete($row->name_mail_configuration);
    		    }
		    }
		}
	}
}