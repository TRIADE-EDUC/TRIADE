<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universes_controller.class.php,v 1.21 2018-09-26 09:08:33 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/search_universes/search_universe.class.php');
require_once($class_path.'/search_universes/search_segment.class.php');
require_once($class_path.'/facettes_controller.class.php');
require_once($class_path.'/search_persopac.class.php');
require_once($include_path.'/templates/search_universes/search_universe.tpl.php');

class search_universes_controller {
    
	protected $id;
    
    public function __construct($id = 0) {
        $this->id = $id+0;
    }

	public function proceed() {
    	$this->get_tree_interface();   
    }
    
    public function proceed_universe() {
        global $action;
        switch($action) {
            case 'edit':
                $search_universe = new search_universe($this->id);
                print $search_universe->get_form();
                break;
            case 'save':
                $search_universe= new search_universe($this->id);
                $search_universe->set_properties_from_form();
                $search_universe->save();
                if ($search_universe->get_id()) {
                    print search_universe::get_list();
                } else {
                    print $search_universe->get_form();
                }
                break;
            case 'delete':
                $search_universe= new search_universe($this->id);
                $search_universe->delete();
                print search_universe::get_list();
                break;
            default:
                print $this->get_tree_interface();
                break;
        }
    }
    
    public function get_tree_interface(){
    	global $search_universe_tree_interface;
    	$data = array();
    	$search_universe_tree_interface = str_replace('!!parameters!!', encoding_normalize::json_encode($this->get_data()), $search_universe_tree_interface);
    	return $search_universe_tree_interface;
    }
    
    public function proceed_segment() {
        global $action;
        
        switch($action) {
            case 'edit':
                $search_segment = new search_segment($this->id);
                print $search_segment->get_form();
                break;
            case 'save':
                $search_segment= new search_segment($this->id);
                $search_segment->set_properties_from_form();
                $search_segment->save();
                if(!$id){
                    print $search_segment->get_form();
                }else{
                    $universe_id = $search_segment->get_num_universe();
                    $universe = new search_universe($universe_id);
                    print $universe->get_form();
                }
        
                break;
            case 'delete':
                $search_segment= new search_segment($this->id);
                $universe_id = $search_segment->get_num_universe();
                $universe = new search_universe($universe_id);
                $search_segment->delete();
                print $universe->get_form();
                break;
            default:
                break;
        }
    }
    
    public function get_data() {
    	global $base_url;
    	global $base_path;
    	
    	$data = array();
    	$query = "SELECT id_search_universe FROM search_universes";
    	$result = pmb_mysql_query($query);
    	$data['universes'] = array();
    	while ($row = pmb_mysql_fetch_assoc($result)) {
    		$universe = new search_universe($row["id_search_universe"]);
    		$data_segments = array();
    		$segments = $universe->get_segments();
    		if (count($segments)) {
    			foreach ($segments as $segment) {
    				$search_perso = new search_segment_search_perso($segment["id_search_segment"]);
    				$facets = new search_segment_facets($segment["id_search_segment"]);
    				$data_segments[$segment["id_search_segment"]] = array(
    						'real_id'	=> $segment["id_search_segment"],
    						'name'	=> $segment["search_segment_label"],
    						'description' => $segment['search_segment_description'],
    						'template_directory' => $segment['search_segment_template_directory'],
    						'type' => $segment['search_segment_type'],
    						'set' => $segment['search_segment_set'],
    						'logo' => $segment['search_segment_logo'],
    						'search_perso' => $search_perso->get_search_perso(),
    						'facet' => $facets->get_facets(),
    						'link_edit' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=segment&action=edit&id='.$segment["id_search_segment"],
    						'link_save' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=segment&action=save&id='.$segment["id_search_segment"],
    						'link_delete' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=segment&action=delete&id='.$segment["id_search_segment"],
    						'entity_type' => 'segment',
    				);
    			}
    		}
    		$data['universes'][$universe->get_id()] = array(
    				'real_id'	=> $universe->get_id(),
    				'name'	=> $universe->get_label(),
    				'description'	=> $universe->get_description(),
    				'template_directory'	=> $universe->get_template_directory(),
    				'opac_views' => $universe->get_opac_views(),
    				'segments' => $data_segments,
    				'link_edit' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=universe&action=edit&id='.$row["id_search_universe"],
    				'link_save' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=universe&action=save&id='.$row["id_search_universe"],
    				'link_delete' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=universe&action=delete&id='.$row["id_search_universe"],
    		        'entity_type' => 'universe',
    		);
    	}
    	$query = "SELECT * FROM search_persopac ORDER BY search_type, search_order, search_name";
    	$result = pmb_mysql_query($query);
    	$data['search_perso'] = array();
    	while ($row = pmb_mysql_fetch_assoc($result)) {
    		$data['search_perso'][$row['search_id']] = array(
    				'real_id' => $row['search_id'],
    				'name' => $row['search_name'],
    				'shortname' => $row['search_shortname'],
    				'human_query' => $row['search_human'],
    				'link_edit' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=search&action=edit&id='.$row['search_id'],
    				'search_type' => [$row['search_type']],
    		        'entity_type' => 'search_perso',
    		);
    	}
    	$query = "SELECT * FROM facettes ORDER BY facette_order, facette_name";
    	$result = pmb_mysql_query($query);
    	$data['facet'] = array();
    	while ($row = pmb_mysql_fetch_assoc($result)) {
    		$data['facet'][$row['id_facette']] = array(
    				'real_id' => $row['id_facette'],
    				'name' => $row['facette_name'],
    				'type' => $row['facette_type'],
    				'link_edit' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=facet&action=edit&id='.$row['id_facette'],
    				'link_save' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=facet&action=save&id='.$row['id_facette'],
    		        'entity_type' => 'facet',
    		);
    	}
    	 
    	$query = "SELECT opac_view_id, opac_view_name FROM opac_views order by opac_view_name";
    	$result = pmb_mysql_query($query);
    	$data['opac_view'] = array();
    	while ($row = pmb_mysql_fetch_assoc($result)) {
    		$data['opac_view'][$row['opac_view_id']] = array(
    				'real_id' => $row['opac_view_id'],
    				'name' => $row['opac_view_name'],
    				'link_edit' => $base_path."/admin.php?categ=opac&sub=opac_view&section=list&action=form&opac_view_id=".$row['opac_view_id'],
    		        'entity_type' => 'opac_view',
    		);
    	}
    	$data['creation_links'] = array(
    			'universe' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=universe&action=edit&id=0',
    			'segment' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=segment&action=edit&id=0',
    			'facet' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=facet&action=edit&id=0',
    			'search_perso' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=search&action=edit&id=0'
    	);
    	$data['save_links'] = array(
    			'universe' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=universe&action=save&id=!!id!!',
    			'segment' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=segment&action=save&id=!!id!!',
    			'facet' => $base_path.'/ajax.php?module=admin&categ=search_universes&sub=facet&action=save&id=!!id!!',
    			'search_perso' => $base_path.'/ajax.php?categ=opac&sub=search_persopac&section=liste&action=save&id=!!id!!'
    	);
    	
    	
    	/**
    	 * TODO: Renvoyer la liste des icones de types d'entités ( Notices et autorités)
    	 */
    	
    	
    	return $data;
    }
    
    public function proceed_ajax(){
    	global $sub;
    	
    	switch($sub){
    		case 'universe':
    			$this->proceed_universe_ajax();
    			break;
    		case 'facet':
    			$this->proceed_facet_ajax();
    			break;
    		case 'search':
    			$this->proceed_search_ajax();
    			break;
    		case 'segment':
    			$this->proceed_segment_ajax();
    			break;
    	}
    }
    
    public function proceed_segment_ajax(){
    	global $action, $id;
    	
    	switch($action) {
    		case 'edit':
    			$search_segment = new search_segment($id);
    			print $search_segment->get_form(true); //true == form_ajax
    			break;
    		case 'save':
    			$search_segment= new search_segment($id);
    			$search_segment->set_properties_from_form();
    			$search_segment->save();
    			$response = array(
    			    'entity' => array(
    			        'type' => 'segment',
    			        'id' => $search_segment->get_id()
    			    )
    			);
    			$response['tree_data'] = $this->get_data();
    			print encoding_normalize::json_encode($response);
    			break;
    		case 'update_set':
    		    $response = array();
    			$segment_set = new search_segment_set($id);
    			$segment_set->set_properties_from_form();
    			$response['status'] = $segment_set->update();
    			$response['human_query'] = $segment_set->get_human_query();
    			$response['set'] = $segment_set->get_data_set();
                print encoding_normalize::json_encode($response);
  			    break;
    		case 'delete':
    			$search_segment= new search_segment($id);
    			$num_universe = $search_segment->get_num_universe();
    			$response = array(
    					'entity' => array(
    							'type' => 'universe',
    							'id' => $num_universe
    					)
    			);
    			$response['status'] = search_segment::delete($id);
    			$response['tree_data'] = $this->get_data();
    			print encoding_normalize::json_encode($response);
    			break;
    		default:
    			break;
    	}
    }
    
    public function proceed_universe_ajax(){
    	global $action, $id;
    	 
    	switch($action) {
    		case 'edit':
    			$search_universe = new search_universe($id);
    			print $search_universe->get_form(true);
    			break;
    		case 'save':
    			$search_universe= new search_universe($id);
    			$search_universe->set_properties_from_form();
    			$search_universe->save();
    			$response = array(
    					'entity' => array(
    							'type' => 'universe',
    							'id' => $search_universe->get_id()
    					)
    			);
    			$response['tree_data'] = $this->get_data();
    			print encoding_normalize::json_encode($response);
    			break;
    		case 'delete':
    			$response = array(
    					'entity' => array(
    							'type' => 'universe',
    							'id' => 0
    					)
    			);
    			$response['status'] = search_universe::delete($id);
    			$response['tree_data'] = $this->get_data();
    			print encoding_normalize::json_encode($response);
    			break;
    	}
    }
    
    public function proceed_facet_ajax() {
    	global $id, $segment_type, $segment_id;
    	$facettes_controller = new facettes_controller($id, entities::get_string_from_const_type($segment_type));
    	$ajax_return = $facettes_controller->proceed_ajax();
	    $entity_type = 'facet';
	    $entity_id = $ajax_return;

    	if (!empty($segment_id)) {
    	    $segment_facets = new search_segment_facets($segment_id);
    	    $segment_facets->set_segment_type($segment_type);
    	    $segment_facets->add_facet($ajax_return);
    	    $segment_facets->save();
    	    $entity_type = 'segment';
    	    $entity_id = $segment_id;
    	}
    	
    	if($ajax_return){
    		$response = array(
    			'entity' => array(
    				'type' => $entity_type,
    				'id' => $entity_id
    			)
    		);
    		$response['tree_data'] = $this->get_data();
    		print encoding_normalize::json_encode($response); 
    	}
    	
    }
    
	public function proceed_search_ajax() {
    	global $action, $id, $segment_id;
    	switch ($action) {
    	    case 'add' :
    	        $segment_search = new search_segment_search_perso($segment_id);
    	        print $segment_search->get_search_form();    	        
    	        break;
    		case 'edit' :
    		    $search_persopac = new search_persopac($id);
    			print $search_persopac->do_form();
    			break;
    		case 'save' :
    		    $search_persopac = new search_persopac($id);
    		    $search_persopac->set_properties_from_form();
    			$id_search_persopac = $search_persopac->save();
    			if($id_search_persopac && !empty($segment_id)){    			    
    			    $segment_search = new search_segment_search_perso($segment_id);
    			    $segment_search->add_search_perso($id_search_persopac);
    			    $segment_search->save();
    			    $response = array(
		  	       'entity' => array(
    				    'type' => 'segment',
    				    'id' => $segment_id
    			     )
    		      );
		          $response['tree_data'] = $this->get_data();
			      print encoding_normalize::json_encode($response);
    			}
    			break;
    	}
    }
}
