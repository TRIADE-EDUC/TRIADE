<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.class.php,v 1.15 2021/04/20 13:35:25 gneveu Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/contribution_area/contribution_area_forms_controller.class.php');
require_once($class_path.'/contribution_area/contribution_area_store.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');
//require_once ($include_path . '/templates/contribution_area/contribution_area.tpl.php');
//require_once($class_path.'/onto_parametres_perso.class.php');


/**
 * class contribution_area
 * Représente un espace de contribution
 */
class contribution_area {
	
	/**
	 * Nom de l'espace de contribution
	 * 
	 * @access protected
	 */
	protected $title;
	
	/**
	 * Id de l'espace de contribution
	 * 
	 * @access protected
	 */
	protected $id;
	
	/**
	 * Scénarios de départ
	 * 
	 * @access protected
	 */
	protected $start_scenarios;
	
	/**
	 * Commentaire
	 * @var string $comment
	 */
	protected $comment;
	
	/**
	 * Couleur
	 * @var string
	 */
	protected $color;
	
	/**
	 * Ordre
	 * @var int $order
	 */
	protected $order;
	
	/**
	 * Répertoire de template d'autorités
	 * @var string $repo_template_authorities
	 */
	protected $repo_template_authorities;
	
	/**
	 * Répertoire de template de notices
	 * @var string $repo_template
	 */
	protected $repo_template_records;
	
	/**
	 * Espace utilisé pour la modification d'entité
	 * @var int $repo_template
	 */
	protected $editing_entity;
	
	/**
	 * parametre de visibilité de l'espace à l'opac
	 * @var string $opac_visibility
	 */
	protected $opac_visibility;
	
	public function __construct($area_id = 0) {
		if ($area_id) {
			$this->id = intval($area_id);
			$this->fetch_datas();
		}
	} // end of member function __construct
	
	public function fetch_datas() {
		if ($this->id) {
			$query = "select * from contribution_area_areas where id_area = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$result = pmb_mysql_fetch_object($result);
				$this->id = $result->id_area;
				$this->title = $result->area_title;
				$this->comment = $result->area_comment;
				$this->color = $result->area_color;
				$this->order = $result->area_order;
				$this->repo_template_authorities = $result->area_repo_template_authorities;
				$this->repo_template_records = $result->area_repo_template_records;
				$this->editing_entity = $result->area_editing_entity;
				$this->opac_visibility = $result->area_opac_visibility;
			}
		}
	}

	public function get_title() {
		return $this->title;
	}	

	/**
	 * Parcours les enregistrement en base et renvoi la liste (ou un message indiquant que nous n'en avons pas)
	 */
	public static function get_list() {
		$areas = array();
		$query = 'select id_area as id,	area_title as title, area_comment as comment, area_color as color, area_order, area_opac_visibility, area_logo from contribution_area_areas order by area_order';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			global $gestion_acces_active, $gestion_acces_empr_contribution_area;
			if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_area == 1)) {
				$ac = new acces();
				$dom_4 = $ac->setDomain(4);
			}
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$visible = true;
				
				if (isset($dom_4) && !$dom_4->getRights($_SESSION['id_empr_session'],$row['id'], 4)) {
					$visible = false;
				}
				if (!$row['area_opac_visibility']) {
				    $visible = false;
				}
				if ($visible) {
				    if ($row['area_logo']) {
				        $row['area_logo'] = "data:image/png;base64,".base64_encode(file_get_contents($row['area_logo']));
				    }
					$areas[] = $row;
				}
			}
		}
		return $areas;
	}
	
	
	public function render () {
		global $include_path;
		$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_area.tpl.html');
		return $h2o->render(array('contribution_area' => $this));
	}
	
	public function get_start_scenarios () {
		if (isset($this->start_scenarios)) {
			return $this->start_scenarios;
		}
		$contribution_area_store  = new contribution_area_store();
		$graph_store_datas = $contribution_area_store->get_attachment_detail($this->get_area_uri(), $this->get_area_uri(),'','startScenario');
				
		$this->start_scenarios = array();
		//gestion des droits
		global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
			$ac = new acces();
			$dom_5 = $ac->setDomain(5);
		}
		
		for ($i = 0 ; $i < count($graph_store_datas); $i++) {
			if (isset($graph_store_datas[$i]['startScenario'])) {
				$visible = true;
				if (isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($graph_store_datas[$i]['uri']), 4)) {
					$visible = false;
				}
				if ($visible) {
					$this->start_scenarios[] = $graph_store_datas[$i];
				}
			}
		}
		return $this->start_scenarios;
	}	
	
	public function get_area_uri(){
		return "http://www.pmbservices.fr/ca/Area#".$this->id;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_color() {
		return $this->color;
	}
		
	public function get_repo_template_authorities() {
	    return $this->repo_template_authorities;
	}
	
	public function get_repo_template_records() {
	    return $this->repo_template_records;
	}
	
	public function get_editing_entity() {
	    return $this->editing_entity;
	}
	
	public static function get_editing_entity_area_id() {
	    // On récupère l'espace de contribution pour la modification d'entité
	    $query = "SELECT id_area FROM contribution_area_areas WHERE area_editing_entity = 1";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
    	    $row = pmb_mysql_fetch_assoc($result);
    	    return $row['id_area'];
	    }
	    return 0;
	}
	
	public function get_acces_editing_entity() {
	    global $msg;
	    
	    return [
	        0 => $msg['contribution_area_is_default_area_not_use'],
	        1 => $msg['contribution_area_is_default_area']
	    ];
	}
	
	public function get_normalized_item(){
	    $retour = array(
	        "id" => $this->id,
	        "title" => $this->title,
	        "comment" => (!empty($this->comment) ? $this->comment : ''),
	        "color" => (!empty($this->color) ? $this->color : ''),
	        "order" => (!empty($this->order) ? $this->order : ''),
	        "status" => (!empty($this->status) ? $this->status : ''),
	        "opac_visibility" => $this->opac_visibility,
	        "repo_template_authorities" => (!empty($this->repo_template_authorities) ? $this->repo_template_authorities : ''),
	        "repo_template_records" => (!empty($this->repo_template_records) ? $this->repo_template_records : ''),
	        "area_logo" => (!empty($this->area_logo) ? $this->area_logo : '')
	    );
	    
	    return $retour;
	}
} // end of contribution_area
