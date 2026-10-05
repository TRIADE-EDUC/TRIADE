<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_perso.class.php,v 1.29.4.1 2025/04/25 14:16:44 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des recherches personnalisées

// inclusions principales
require_once("$include_path/templates/search_perso.tpl.php");
require_once("$class_path/search.class.php");
require_once("$class_path/searcher_tabs.class.php");
require_once("$class_path/users.class.php");
require_once($class_path."/list/configuration/search_perso/list_configuration_search_perso_ui.class.php");

class search_perso {

	public $id;
	public $duplicate_from_id;
	public $type;
	public $name;
	public $shortname;
	public $comment;
	public $query;
	public $human;
	public $directlink;
	public $autorisations;
	public $search_perso_user;
	public $directlink_user;
	public $order;
	protected $my_search;
	protected $uses;
	protected $error_message;
	public $messages;

	// constructeur
	public function __construct($id=0, $type='RECORDS') {
		$this->id = $id;
		$this->type = $type;
		$this->fetch_data();
	}

	// récupération des infos en base
	protected function fetch_data() {
		global $PMBuserid;

		$this->name='';
		$this->shortname='';
		$this->comment='';
		$this->query='';
		$this->human='';
		$this->directlink='';
		$this->autorisations=$PMBuserid;
		$this->order = 0;
		if($this->id) {
			$query = "SELECT * FROM search_perso WHERE search_id='".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->type=$row->search_type;
				$this->name=$row->search_name;
				$this->shortname=$row->search_shortname;
				$this->comment=$row->search_comment;
				$this->query=$row->search_query;
				$this->human=$row->search_human;
				$this->directlink=$row->search_directlink;
				$this->autorisations=$row->autorisations;
				$this->order = $row->search_order;
			}
		}
		//On récupère également ses recherches prédéfinies
		$this->fetch_search_perso_user();
	}

	protected function fetch_search_perso_user() {
		global $PMBuserid;

		$query = "SELECT * FROM search_perso WHERE search_type = '".$this->type."'";
		if ($PMBuserid!=1) $query .= " AND (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		$query .= " order by search_order, search_name ";
		$result = pmb_mysql_query($query);
		$this->search_perso_user=array();
		$link="";
		if(pmb_mysql_num_rows($result)){
			$i=0;
			while($row = pmb_mysql_fetch_object($result)) {
				if($row->search_directlink) {
					if($row->search_shortname)$libelle=$row->search_shortname;
					else $libelle=$row->search_name;
					if($row->search_directlink == 2) {
						$js_launch_search= "document.forms['search_form".$row->search_id."'].action += '&sub=launch';";
					} else {
						$js_launch_search= "";
					}
					$link.="
						<span>
							<a href=\"javascript:".$js_launch_search."document.forms['search_form".$row->search_id."'].submit();\" data-search-perso-id='".$row->search_id."'>$libelle</a>
						</span>
					";
				}
				$this->search_perso_user[$i]= new stdClass();
				$this->search_perso_user[$i]->id=$row->search_id;
				$this->search_perso_user[$i]->type=$row->search_type;
				$this->search_perso_user[$i]->name=$row->search_name;
				$this->search_perso_user[$i]->comment=($row->search_comment?"<br />(".$row->search_comment.")":"");
				$this->search_perso_user[$i]->shortname=$row->search_shortname;
				$this->search_perso_user[$i]->query=$row->search_query;
				$this->search_perso_user[$i]->human=$row->search_human;
				$this->search_perso_user[$i]->directlink=$row->search_directlink;
				$this->search_perso_user[$i]->order=$row->search_order;
				$i++;
			}
		}
		$this->directlink_user=$link;
	}

	public function proceed() {
		global $msg, $sub;

		switch($sub) {
			case "form":
				print $this->do_form();
				break;
			case "edit":
				$this->set_query();
				print $this->do_form();
				break;
			case "save":
				// sauvegarde issu du formulaire
				$this->set_properties_form_form();
				$this->save();
				print $this->do_list();
				break;
			case "duplicate":
				$this->duplicate_from_id = $this->id;
				$this->id = 0;
				print $this->do_form();
				break;
			case "delete":
				$deleted = $this->delete();
				if($deleted) {
				    print $this->do_list();
				} else {
				    print $this->get_display_header_list();
				    error_message(	$msg[294], implode('<br />', $this->messages), 1, $this->get_url_base());
				}
				break;
			case "launch":
				// accès direct à une recherche personalisée
				print $this->launch();
				break;
			default :
				// affiche liste des recherches prédéfinies
				print $this->do_list();
				break;
		}
	}

	public function proceed_ajax() {
		global $action;
		global $class_path;
		global $object_type;

		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'configuration/search_perso');
				break;
		}
	}

	public function set_properties_form_form() {
		global $name, $shortname, $query, $human, $directlink, $directlink_auto_submit, $autorisations, $comment;

		$this->name = stripslashes($name);
		$this->shortname = stripslashes($shortname);
		$this->comment = stripslashes($comment);
		$this->query = stripslashes($query);
		$this->human=stripslashes($human);
		$this->directlink=($directlink ? 1 : 0);
		if($this->directlink && $directlink_auto_submit) {
			$this->directlink += 1;
		}
		if (is_array($autorisations)) {
			$this->autorisations = implode(" ",$autorisations);
		}else {
			$this->autorisations = "1";
		}
	}

	public function set_order($order=0) {
		$order = intval($order);
		if(!$order) {
			$query = "select max(search_order) as max_order from search_perso";
			$result = pmb_mysql_query($query);
			$order = pmb_mysql_result($result, 0)+1;
		}
		$this->order = $order;
	}

	public function set_query() {
		$this->get_instance_search();
		$this->query = $this->my_search->serialize_search();
		$this->my_search->unserialize_search($this->query);
		$this->human = $this->my_search->make_human_query();
	}

	public function save() {
		global $msg;

		if($this->id) {
			$query = 'update search_perso set ';
			$where = 'where search_id = '.$this->id;
		} else {
			$query = 'insert into search_perso set ';
			$where = '';
			$this->set_order(0);
		}
		$query .= '
				search_type = "'.$this->type.'",
				search_name = "'.addslashes($this->name).'",
				search_shortname = "'.addslashes($this->shortname).'",
				search_comment = "'.addslashes($this->comment).'",
				search_query = "'.addslashes($this->query).'",
				search_human = "'.addslashes($this->human).'",
				search_directlink = "'.$this->directlink.'",
				autorisations = "'.$this->autorisations.'",
				search_order = "'.$this->order.'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			$indice = 0;
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			$this->fetch_search_perso_user();
			return true;
		} else {
			if($this->id) {
				error_message($msg["search_perso_form_edit"], $msg["search_perso_form_add_error"],1);
			} else {
				error_message($msg["search_perso_form_add"], $msg["search_perso_form_add_error"],1);
			}
			return false;
		}
	}

	// fonction générant le form de saisie
	public function do_form() {
		global $msg,$tpl_search_perso_form,$charset;

		// titre formulaire
		if($this->id) {
			$libelle=$msg["search_perso_form_edit"];
			$link_duplicate="<input type='button' class='bouton' value='".$msg['duplicate']."' onClick=\"document.location='".$this->get_url_base()."&sub=duplicate&id=".$this->id."'\" />";
			$link_delete="<input type='button' class='bouton' value='".$msg[63]."' onClick=\"confirm_delete();\" />";
			$button_modif_requete = "";
			$form_modif_requete = "";
		} else {
			$libelle=$msg["search_perso_form_add"];
			$link_duplicate="";
			$link_delete="";
			$button_modif_requete = "";
			$form_modif_requete = "";
			if(!$this->duplicate_from_id) {
				$this->get_instance_search();
				$this->query=$this->my_search->serialize_search();
				$this->human = $this->my_search->make_human_query();
			}
		}
		// Champ éditable
		$tpl_search_perso_form = str_replace('!!id!!', htmlentities($this->id,ENT_QUOTES,$charset), $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!name!!', htmlentities($this->name,ENT_QUOTES,$charset), $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!shortname!!', htmlentities($this->shortname,ENT_QUOTES,$charset), $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!comment!!', htmlentities($this->comment,ENT_QUOTES,$charset), $tpl_search_perso_form);
		if($this->directlink) $checked= " checked='checked' ";
		else $checked= "";
		$tpl_search_perso_form = str_replace('!!directlink!!', $checked, $tpl_search_perso_form);
		if($this->directlink == 2) $checked= " checked='checked' ";
		else $checked= "";
		$tpl_search_perso_form = str_replace('!!directlink_auto_submit!!', $checked, $tpl_search_perso_form);

		if ($this->id) {
			$tpl_search_perso_form = str_replace('!!autorisations_users!!', users::get_form_autorisations($this->autorisations,0), $tpl_search_perso_form);
		} else {
			$tpl_search_perso_form = str_replace('!!autorisations_users!!', users::get_form_autorisations($this->autorisations,1), $tpl_search_perso_form);
		}

		$tpl_search_perso_form = str_replace('!!query!!', htmlentities($this->query,ENT_QUOTES,$charset), $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!human!!', htmlentities($this->human,ENT_QUOTES,$charset), $tpl_search_perso_form);

		$tpl_search_perso_form = str_replace('!!requete!!', htmlentities($this->query,ENT_QUOTES, $charset), $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!requete_human!!', $this->human, $tpl_search_perso_form);

		$tpl_search_perso_form = str_replace('!!bouton_modif_requete!!', $button_modif_requete,  $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!form_modif_requete!!', $form_modif_requete,  $tpl_search_perso_form);

		$tpl_search_perso_form = str_replace('!!duplicate!!', $link_duplicate, $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!delete!!', $link_delete, $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!libelle!!',htmlentities($libelle,ENT_QUOTES,$charset) , $tpl_search_perso_form);

		$link_annul = "onClick=\"unload_off();history.go(-1);\"";
		$tpl_search_perso_form = str_replace('!!annul!!', $link_annul, $tpl_search_perso_form);
		$tpl_search_perso_form = str_replace('!!url_base!!', $this->get_url_base(), $tpl_search_perso_form);

		return $tpl_search_perso_form;
	}

	protected function get_list_title() {
	    global $msg;
	    switch ($this->type) {
	        case 'EXPL':
	            return $msg["search_perso_expl_title"];
	        default:
	            return $msg["search_perso_title"];
	    }
	}

	public function get_display_header_list() {
	    global $base_path;
	    global $msg;

	    $display = "
		<script type='text/javascript' src='".$base_path."/javascript/search_perso_drop.js'></script>
		<h1>".$this->get_list_title()."</h1>
        <div class='hmenu'>
			<span><a href='".$this->get_url_base()."'>".$msg["search_perso_list_title"]."</a></span>".$this->directlink_user."
		</div>
		<hr />
		<h3>".$msg["search_perso_list"]."</h3>";
	    return $display;
	}

	// fonction générant le form de saisie
	public function do_list() {
		global $action;

		$display = $this->get_display_header_list();
		switch ($action) {
			case 'up':
			case 'down':
				$instance = list_configuration_search_perso_ui::get_instance(array('type' => $this->type), array(), array('by' => 'search_order', 'asc_desc' => 'asc'));
				break;
			case 'save_order':
				$instance = list_configuration_search_perso_ui::get_instance(array('type' => $this->type));
				$instance->run_action_save_order();
				break;
			default:
				$instance = list_configuration_search_perso_ui::get_instance(array('type' => $this->type));
				break;
		}
		$display .= $instance->get_display_list();
		return $display;
	}

	public function get_forms_list() {

		if($this->type == 'AUTHORITIES') {
			$searcher_tabs = new searcher_tabs();
			$this->my_search=new search_authorities(true, 'search_fields_authorities');
		} else {
			$this->my_search=new search();
		}
		$forms_search='';
		$links='';
		for($i=0;$i<count($this->search_perso_user);$i++) {
			$target_url = $this->get_target_url($this->search_perso_user[$i]->id);
			//composer le formulaire de la recherche
			$this->my_search->unserialize_search($this->search_perso_user[$i]->query);
			$forms_search.= $this->my_search->make_hidden_search_form($target_url,"search_form".$this->search_perso_user[$i]->id);
			$libelle= $this->search_perso_user[$i]->name;
			if($this->search_perso_user[$i]->directlink == 2) {
				$js_launch_search= "document.forms['search_form".$this->search_perso_user[$i]->id."'].action += '&sub=launch';";
			} else {
				$js_launch_search= "";
			}
			$links.="
				<span>
					<a href=\"javascript:".$js_launch_search."document.forms['search_form".$this->search_perso_user[$i]->id."'].submit();\" data-search-perso-id='".$this->search_perso_user[$i]->id."'>$libelle</a>
				</span><br/>";
		}
		return $forms_search.$links;
	}

	protected function get_uses_from_query($query) {
	    $details = array();
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)) {
	        while($row = pmb_mysql_fetch_object($result)) {
	            $search_query = unserialize($row->query);
	            if(is_array($search_query)) {
	                $criteria = $search_query['SEARCH'];
	                if(is_array($criteria) && in_array('s_108', $criteria)) {
	                    foreach ($criteria as $key=>$field) {
	                        if($field == 's_1' && ($search_query[$key]['FIELD'][0] == $this->id)) {
	                            $details[] = $row;
	                        }
	                    }
	                }
	            }
	        }
	    }
	    return $details;
	}

	protected function is_used() {
	    global $msg;

	    $this->uses = array();
	    $this->messages = array();

	    //DSI - Equations
	    $query = "SELECT id_equation as id, nom_equation as label, requete as query FROM equations";
	    $this->uses['equations'] = $this->get_uses_from_query($query);
	    if(count($this->uses['equations'])) {
	        $this->messages[] = $msg['search_perso_delete_used_by_equations'];
	    }

	    //Autre recherche prédéfinie
	    $query = "SELECT search_id as id, search_name as label, search_query as query FROM search_perso WHERE search_id <> ".$this->id;
	    $this->uses['search_perso'] = $this->get_uses_from_query($query);
	    if(count($this->uses['search_perso'])) {
	        $this->messages[] = $msg['search_perso_delete_used_by_search_perso'];
	    }

	    //Vues OPAC
	    $query = "SELECT opac_view_id as id, opac_view_name as label, opac_view_query as query FROM opac_views";
	    $this->uses['opac_views'] = $this->get_uses_from_query($query);
	    if(count($this->uses['opac_views'])) {
	        $this->messages[] = $msg['search_perso_delete_used_by_opac_views'];
	    }

	    if(count($this->uses['equations']) || count($this->uses['search_perso']) || count($this->uses['opac_views'])) {
	        return true;
	    }
	    return false;
	}

	// suppression d'une collection ou de toute les collections d'un périodique
	public function delete() {
	    if($this->id && !$this->is_used()) {
	        pmb_mysql_query("DELETE from search_perso WHERE search_id='".$this->id."' ");
	        $this->fetch_search_perso_user();
	        return true;
		}
		return false;
	}

	// fonction permettant d'accéder directement à une recherche prédéfinie
	public function launch() {
		if($this->id) {
			$this->my_search=new search();
			$this->my_search->unserialize_search($this->query);
			print $this->my_search->make_hidden_search_form("./catalog.php?categ=search&mode=6","search_form".$this->id);
			print "<script type='text/javascript'>document.forms['search_form".$this->id."'].submit();</script>";
		} else {
			print $this->do_list();
		}
	}

	public function get_instance_search() {
	    if (!empty($this->my_search)) {
	        return $this->my_search;
	    }
		switch ($this->type) {
			case 'AUTHORITIES':
				$this->my_search=new search_authorities(true, 'search_fields_authorities');
				break;
			case 'EMPR':
				$this->my_search=new search(true, 'search_fields_empr');
				break;
			case 'EXPL':
			    $this->my_search=new search(true, 'search_fields_expl');
			    break;
			default:
				$this->my_search=new search();
				break;
		}
		return $this->my_search;
	}

	protected function get_target_url($id_predefined_search=0) {
	    global $option_show_notice_fille, $option_show_expl;

		switch ($this->type) {
			case 'AUTHORITIES':
				$searcher_tabs = new searcher_tabs();
				$target_url = "./autorites.php?categ=search&mode=".$searcher_tabs->get_mode_multi_search_criteria($id_predefined_search);
				break;
			case 'EMPR':
				$target_url = "./circ.php?categ=search";
				break;
			case 'EXPL':
			    $target_url = "./catalog.php?categ=search&mode=8&option_show_notice_fille=$option_show_notice_fille&option_show_expl=$option_show_expl";
			    break;
			default:
				$target_url = "./catalog.php?categ=search&mode=6";
				break;
		}
		if($id_predefined_search) {
			$target_url .= "&id_predefined_search=".$id_predefined_search;
		}
		return $target_url;
	}

	protected function get_url_base() {
	    global $base_path, $current_module, $type;
	    return $base_path.'/'.$current_module.'.php?categ=search_perso'.($type ? '&type='.$type : '');
	}

} // fin définition classe
