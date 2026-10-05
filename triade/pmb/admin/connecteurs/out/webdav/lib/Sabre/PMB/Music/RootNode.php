<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootNode.php,v 1.4 2016/03/22 08:12:16 vtouchard Exp $
namespace Sabre\PMB\Music;

class RootNode extends Collection {
	
	public function __construct($config){
		parent::__construct($config);
		$this->type = "rootNode";
	}

	public function getName() {
		return "";	
	}
	
	public function getChildren(){
		$children = array();
		$query = 'select authperso_authorities.id_authperso_authority 
				from authperso_authorities 
				join authperso on authperso.id_authperso = authperso_authorities.authperso_authority_authperso_num and authperso.authperso_oeuvre_event = 1 
				join tu_oeuvres_events on tu_oeuvres_events.oeuvre_event_authperso_authority_num = authperso_authorities.id_authperso_authority';		
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$child = $this->getChild("(K".$row->id_authperso_authority.")");
				if ($this->check_write_permission()) {
					$children[] = $child;
				} else {
					$child->set_parent($this);
					$notices = $child->getNotices();
					if (count($notices) && ($notices[0] != "'ensemble_vide'")) {
						$children[] = $child;
					}
				}
			}
		}
		return $children;
	}
	

}