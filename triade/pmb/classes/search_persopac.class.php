<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_persopac.class.php,v 1.48 2019-06-10 08:57:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des recherches personnalisées

// inclusions principales
require_once "$include_path/templates/search_persopac.tpl.php";
require_once "$include_path/misc.inc.php";
require_once "$class_path/search.class.php";
require_once "$class_path/translation.class.php";
require_once "$class_path/XMLlist.class.php";
require_once "$class_path/search_universes/search_segment_search_perso.class.php";
require_once $class_path."/entities.class.php";
require_once $class_path."/list/configuration/opac/list_configuration_opac_search_persopac_ui.class.php";

class search_persopac {
	public $id=0;
	public $name="";
	public $shortname="";
	public $query="";
	public $human="";
	public $directlink="";
	public $limitsearch="";
	public $order;
	public $type;
	public $empr_categ_restrict = array();	

	// constructeur
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
    
	// récupération des infos en base
	public function fetch_data() {
		if($this->id) {
			$result = pmb_mysql_query("SELECT * FROM search_persopac WHERE search_id='".$this->id."'");
			$row = pmb_mysql_fetch_object($result);
			$this->name = $row->search_name;
			$this->shortname = $row->search_shortname;
			$this->query = $row->search_query;
			$this->human = $row->search_human;
			$this->directlink = $row->search_directlink;
			$this->limitsearch = $row->search_limitsearch;
			$this->order = $row->search_order;
			$this->type = $row->search_type;
			
			$this->empr_categ_restrict = array();
			$query  = "select id_categ_empr from search_persopac_empr_categ where id_search_persopac = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($row = pmb_mysql_fetch_object($result)){
					$this->empr_categ_restrict[]=$row->id_categ_empr;
				}
			}
		}
		$this->load_xml();
	}

	protected function set_order_in_database($id, $order) {
		if($id) {
			$query = "update search_persopac set search_order = '".$order."' where search_id = ".$id;
			pmb_mysql_query($query);
		}
	}
	
	public function get_link() {
		$result = pmb_mysql_query("SELECT * FROM search_persopac order by search_order, search_name ");
		$this->search_persopac_list=array();
		if(pmb_mysql_num_rows($result)){
			$i=0;
			while(($row=pmb_mysql_fetch_object($result))) {
				if($row->search_order == ($i+1)) {
					$order = $row->search_order;
				} else {
					$this->set_order_in_database($row->search_id, ($i+1));
					$order = ($i+1);
				}
				$this->search_persopac_list[$i]= new stdClass();
				$this->search_persopac_list[$i]->id=$row->search_id;
				$this->search_persopac_list[$i]->name=$row->search_name;
				$this->search_persopac_list[$i]->shortname=$row->search_shortname;
				$this->search_persopac_list[$i]->query=$row->search_query;
				$this->search_persopac_list[$i]->human=$row->search_human;
				$this->search_persopac_list[$i]->directlink=$row->search_directlink;
				$this->search_persopac_list[$i]->limitsearch=$row->search_limitsearch;
				$this->search_persopac_list[$i]->order=$order;
				$this->search_persopac_list[$i]->type=$row->search_type;
				$i++;			
			}	
		}
		return true;
	}

	public function set_properties_from_form() {
		global $name, $shortname, $query, $human, $directlink, $directlink_auto_submit, $limitsearch;
		global $empr_restrict, $type;
		
		$this->name = stripslashes($name);
		$this->shortname = stripslashes($shortname);
		$this->query = stripslashes($query);
		$this->human = stripslashes($human);
		$this->directlink = $directlink;
		if($this->directlink && $directlink_auto_submit) {
			$this->directlink += 1;
		}
		$this->limitsearch = $limitsearch;
		$this->empr_categ_restrict = $empr_restrict;
		if (!empty($type)) {
		    $this->type = $type;
		}
	}
	
	public function set_order($order=0) {
		$order += 0;
		if(!$order) {
			$query = "select max(search_order) as max_order from search_persopac";
			$result = pmb_mysql_query($query);
			$order = pmb_mysql_result($result, 0)+1;
		}
		$this->order = $order;
	}
	
	public function save() {
		global $msg;
		
		if(!$this->id) {
			$this->set_order(0);
		}
		$fields = "
			search_name = '".addslashes($this->name)."',	
			search_shortname = '".addslashes($this->shortname)."',
			search_query = '".addslashes($this->query)."',
			search_human = '".addslashes($this->human)."',
			search_directlink = '".$this->directlink."',
			search_limitsearch = '".$this->limitsearch."',
			search_order = '".$this->order."',
			search_type = '".$this->type."'
			";
		if($this->id) {
			// modif
			$no_erreur=pmb_mysql_query("UPDATE search_persopac SET $fields WHERE search_id=".$this->id);
			if(!$no_erreur) {
				error_message($msg["search_persopac_form_edit"], $msg["search_persopac_form_add_error"],1);
				exit;
			}
				
		} else {
			// create
			$no_erreur=pmb_mysql_query("INSERT INTO search_persopac SET $fields ");
			$this->id = pmb_mysql_insert_id();
			if(!$no_erreur) {
				error_message($msg["search_persopac_form_add"], $msg["search_persopac_form_add_error"],1);
				exit;
			}
		}
		//on s'occupe maintenant de la restriction par caégories de lecteur
		$query = "delete from search_persopac_empr_categ where id_search_persopac = ".$this->id;
		pmb_mysql_query($query);
		if(count($this->empr_categ_restrict)){
			foreach($this->empr_categ_restrict as $id_categ_empr){
				$query = "insert into search_persopac_empr_categ set id_search_persopac=".$this->id.", id_categ_empr=".$id_categ_empr;
				pmb_mysql_query($query);
			}
		}
		$translation = new translation($this->id,"search_persopac");
		$translation->update("search_name", "name");
		$translation->update("search_shortname", "shortname");
		return $this->id;
	}

	// fonction générant le form de saisie 
	public function do_form() {
		global $msg,$tpl_search_persopac_form,$charset,$base_path;	
		global $id_search_persopac;
		global $search_type;
		
		//search_type
		if (!empty($search_type)) {
		    $this->type = $search_type;
		}else{ //On met sur notice par défaut maintenant qu'il y'a un sélecteur
			$this->type = 'notices';
		}
		// titre formulaire
		$my_search = $this->get_search_from_type();

		if($this->id) {
			$libelle=$msg["search_persopac_form_edit"] . ' ( ' . $this->get_entities_msg($this->type) . ' )';
			$link_delete="<input type='button' class='bouton' value='".$msg[63]."' onClick=\"confirm_delete();\" />";
			$button_modif_requete = "<input type='button' class='bouton' value=\"".$msg["search_perso_modif_requete"]."\" onClick=\"document.modif_requete_form_".$this->id.".submit();\">";
			
			//Mémorisation de recherche prédéfinie en édition
	 		if ($id_search_persopac) {
	 			$this->query=$my_search->serialize_search();
	 			$my_search->unserialize_search($this->query);
	 		} else {
				$my_search->unserialize_search($this->query);
				$this->query=$my_search->serialize_search();
	 		}
	 		$form_modif_requete = $this->make_hidden_search_form();
		} else {
			$libelle=$msg["search_persopac_form_add"] . ' ( ' . $this->get_entities_msg($this->type) . ' )';
			$link_delete="";
			$button_modif_requete = "";
			$form_modif_requete = "";
	
	 		$this->query=$my_search->serialize_search();
		}

	 	$this->human = $my_search->make_human_query();
		
		// Champ éditable
		$tpl_search_persopac_form = str_replace('!!id!!', htmlentities($this->id,ENT_QUOTES,$charset), $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!name!!', $this->name, $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!shortname!!', $this->shortname, $tpl_search_persopac_form);
		$checked='';
		if($this->directlink) $checked= " checked='checked' ";
		$tpl_search_persopac_form = str_replace('!!directlink!!', $checked, $tpl_search_persopac_form);
		$checked='';
		if($this->directlink == 2) $checked= " checked='checked' ";
		$tpl_search_persopac_form = str_replace('!!directlink_auto_submit!!', $checked, $tpl_search_persopac_form);
		$checked='';
		if($this->limitsearch) $checked= " checked='checked' ";
		$tpl_search_persopac_form = str_replace('!!limitsearch!!', $checked, $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!query!!', htmlentities($this->query,ENT_QUOTES,$charset), $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!human!!', htmlentities($this->human,ENT_QUOTES,$charset), $tpl_search_persopac_form);
		
		$action="./admin.php?categ=opac&sub=search_persopac&section=liste&action=collstate_update".(!empty($this->serial_id) ? "&serial_id=".$this->serial_id : "")."&id=".$this->id;
		$tpl_search_persopac_form = str_replace('!!action!!', $action, $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!delete!!', $link_delete, $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!libelle!!',htmlentities($libelle,ENT_QUOTES,$charset) , $tpl_search_persopac_form);
		
		$link_annul = "onClick=\"unload_off();history.go(-1);\"";
		$tpl_search_persopac_form = str_replace('!!annul!!', $link_annul, $tpl_search_persopac_form);
		
		//restriction aux catégories de lecteur
		$requete = "SELECT id_categ_empr, libelle FROM empr_categ ORDER BY libelle ";
		$res = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)>0){
			$categ = "
			<label for='empr_restrict'>".htmlentities($msg['search_perso_form_user_restrict'],ENT_QUOTES,$charset)."</label><br />
			<select id='empr_restrict' name='empr_restrict[]' multiple>";
			while($obj = pmb_mysql_fetch_object($res)){
				$categ.="
				<option value='".$obj->id_categ_empr."' ".(in_array($obj->id_categ_empr,$this->empr_categ_restrict) ? "selected=selected" : "") .">".htmlentities($obj->libelle,ENT_QUOTES,$charset)."</option>";
			}
			$categ.="
			</select>";
		}else $categ = "";
		$tpl_search_persopac_form = str_replace('!!categorie!!', $categ, $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!type!!', "<input type='hidden' id='type' name='type' value='" . $this->type . "' >", $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!requete!!', htmlentities($this->query,ENT_QUOTES, $charset), $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!requete_human!!', $this->human, $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!bouton_modif_requete!!', $button_modif_requete,  $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!form_modif_requete!!', $form_modif_requete,  $tpl_search_persopac_form);
		
		$translation = new translation($this->id, 'search_persopac');
		$tpl_search_persopac_form .= $translation->connect('search_persopac_form');
		return $tpl_search_persopac_form;	
	}


	public function do_list() {
		global $action;
		
		// pour toute les recherche de l'utilisateur
		$this->get_link();
		switch ($action) {
			case 'up':
			case 'down':
				$instance = list_configuration_opac_search_persopac_ui::get_instance(array(), array(), array('by' => 'search_order', 'asc_desc' => 'asc'));
				break;
			case 'save_order':
				$instance = list_configuration_opac_search_persopac_ui::get_instance();
				$instance->run_action_save_order();
				break;
			default:
				$instance = list_configuration_opac_search_persopac_ui::get_instance();
				break;
		}
		return $instance->get_display_list();	
	}

	public function delete() {		
		if($this->id) {
			pmb_mysql_query("DELETE from search_persopac WHERE search_id='".$this->id."' ");
			pmb_mysql_query("delete from search_persopac_empr_categ where id_search_persopac = ".$this->id);
			search_segment_search_perso::on_delete_search_perso($this->id);
		}	
	}

	public function up() {
		$query = "select search_order from search_persopac where search_id=".$this->id;
		$result = pmb_mysql_query($query);
		$order = pmb_mysql_result($result, 0, 0);
		$query = "select max(search_order) as order_max from search_persopac where search_order < $order";
		$result=pmb_mysql_query($query);
		$order_max=@pmb_mysql_result($result, 0, 0);
		if ($order_max) {
			$query="select search_id from search_persopac where search_order=$order_max limit 1";
			$result=pmb_mysql_query($query);
			$id_search_up=pmb_mysql_result($result,0,0);
			$query="update search_persopac set search_order='".$order_max."' where search_id=".$this->id;
			pmb_mysql_query($query);
			$query="update search_persopac set search_order='".$order."' where search_id=".$id_search_up;
			pmb_mysql_query($query);
		}
	}
	
	public function down() {
		$query = "select search_order from search_persopac where search_id=".$this->id;
		$result = pmb_mysql_query($query);
		$order = pmb_mysql_result($result, 0, 0);
		$query = "select min(search_order) as order_min from search_persopac where search_order > $order";
		$result=pmb_mysql_query($query);
		$order_min=@pmb_mysql_result($result, 0, 0);
		if ($order_min) {
			$query="select search_id from search_persopac where search_order=$order_min limit 1";
			$result=pmb_mysql_query($query);
			$id_search_down=pmb_mysql_result($result,0,0);
			$query="update search_persopac set search_order='".$order_min."' where search_id=".$this->id;
			pmb_mysql_query($query);
			$query="update search_persopac set search_order='".$order."' where search_id=".$id_search_down;
			pmb_mysql_query($query);
		}
	}
	
	public function add_search(){
	    global $msg, $search_type, $charset, $filter_group;
	    
	    if (!empty($search_type)) {
	        $this->type = $search_type;
	    }else{ //On met sur notice par défaut maintenant qu'il y'a un sélecteur
			$this->type = 'notices';
		}
		$this->init_filter_group();
	    $onchange = 'onchange="document.location=\'./admin.php?categ=opac&sub=search_persopac&section=liste&action=add&search_type=\'+this.value+\'&id='.$this->id.'\'"';
		$form = '<h3>'.htmlentities($msg['admin_contribution_area_equation_type'], ENT_QUOTES, $charset).'</h3>';
		$form .= $this->get_entities_selector($onchange);
		
		$my_search = $this->get_search_from_type();
		$form.= $my_search->show_form("./admin.php?categ=opac&sub=search_persopac&section=liste&action=build",
			"","","./admin.php?categ=opac&sub=search_persopac&section=liste&action=form".($this->id ? "&id=".$this->id : ""));
		print $form;
	}

	public function continu_search(){
		global $msg,$base_path;

		$my_search=new search(false,"search_fields_opac");
		$form= $my_search->show_form("./admin.php?categ=opac&sub=search_persopac&section=liste&action=build",
			"","","./admin.php?categ=opac&sub=search_persopac&section=liste&action=form");
		print $form;
	}

		
	// pour maj de requete de recherche prédéfinie
	public function make_hidden_search_form($url="") {
		global $search;
		global $charset;
	 	
		$url = "./admin.php?categ=opac&sub=search_persopac&section=liste&action=add" ;
	
		$r="<form name='modif_requete_form_$this->id' action='$url' style='display:none' method='post'>";
	
		for ($i=0; $i<count($search); $i++) {
			$inter="inter_".$i."_".$search[$i];
			global ${$inter};
			$op="op_".$i."_".$search[$i];
			global ${$op};
			$field_="field_".$i."_".$search[$i];
			global ${$field_};
			$field=${$field_};
			//Récupération des variables auxiliaires
			$fieldvar_="fieldvar_".$i."_".$search[$i];
			global ${$fieldvar_};
			$fieldvar=${$fieldvar_};
			if (!is_array($fieldvar)) $fieldvar=array();
	
			$r.="<input type='hidden' name='search[]' value='".htmlentities($search[$i],ENT_QUOTES,$charset)."'/>";
			$r.="<input type='hidden' name='".$inter."' value='".htmlentities(${$inter},ENT_QUOTES,$charset)."'/>";
			$r.="<input type='hidden' name='".$op."' value='".htmlentities(${$op},ENT_QUOTES,$charset)."'/>";
			for ($j=0; $j<count($field); $j++) {
				$r.="<input type='hidden' name='".$field_."[]' value='".htmlentities($field[$j],ENT_QUOTES,$charset)."'/>";
			}
			reset($fieldvar);
			foreach ($fieldvar as $var_name => $var_value) {
				for ($j=0; $j<count($var_value); $j++) {
					$r.="<input type='hidden' name='".$fieldvar_."[".$var_name."][]' value='".htmlentities($var_value[$j],ENT_QUOTES,$charset)."'/>";
				}
			}
		}
	 	$r.="<input type='hidden' name='id_search_persopac' value='$this->id'/>";
		$r.="</form>";
		return $r;
	}
	
	public function load_xml() {
		global $pmb_opac_url,$lang,$base_path;
		
		// Recherche du fichier lang de l'opac
		$url = $pmb_opac_url."includes/messages/$lang.xml";
		$fichier_xml = $base_path."/temp/opac_lang.xml";
		curl_load_opac_file($url,$fichier_xml);
		
		$url = $pmb_opac_url."includes/search_queries/search_fields.xml";
		$fichier_xml="$base_path/temp/search_fields_opac.xml";
		curl_load_opac_file($url,$fichier_xml);
	}
	
	protected function get_search_from_type() {
	    global $base_path;
	    switch ($this->type) {
	        case 'notices' :
	            return new search(false,"search_fields_opac","$base_path/temp/");
	        default:
	            return new search_authorities(false,"search_fields_authorities");
	    }
	}
	
	protected function get_entities_selector($onchange = '') {
        global $msg, $charset, $search_type;

        $entities = $this->get_entities_msg();        
       	$html = '';
       	foreach ($entities as $value => $label) {
       		$html .= '<option value="'.$value.'" '.($value == $search_type ? 'selected="selected"' : '').'>'.htmlentities($label, ENT_QUOTES, $charset).'</option>';
       	}        
	    return '
		    <select name="type" id="type" '.$onchange.'>
                '.$html.'
		    </select>
	    ';
	}
	
	protected function get_entities_msg($entitie = '') {
        global $msg, $charset;
        
        $authpersos=authpersos::get_instance();
        $authperso_infos = $authpersos->get_data();
        $authperso_values = array();
        if(count($authperso_infos)){
        	foreach($authperso_infos as $authperso_info){
        		$authperso_values[$authperso_info['id']] =  $authperso_info['name'];
        	}
        }        
		$entities = array(
				'notices' => $msg['288'],
				'authors' => $msg['isbd_author'],
				'categories' => $msg['isbd_categories'],
				'concepts' => $msg['search_concept_title'],
				'collections' => $msg['isbd_collection'],
				'indexint' => $msg['isbd_indexint'],
				'publishers' => $msg['isbd_editeur'],
				'series' => $msg['isbd_serie'],
				'subcollections' => $msg['isbd_subcollection'],
				'titres_uniformes' => $msg['isbd_titre_uniforme'],
		);
        $entities = $entities + $authperso_values;
        if($entitie) return $entities[$entitie];
        else return $entities;
	}
	
	protected function init_filter_group(){
		global $filter_group;
		
		switch ($this->type) {
			case 'authors':
				$filter_group = 1;
				break;
			case 'categories':
				$filter_group = 2;
				break;
			case 'concepts':
				$filter_group = 11;
				break;
			case 'collections':
				$filter_group = 4;
				break;
			case 'indexint':
				$filter_group = 8;
				break;
			case 'publishers':
				$filter_group = 3;
				break;
			case 'series':
				$filter_group = 6;
				break;
			case 'subcollections':
				$filter_group = 5;
				break;
			case 'titres_uniformes':
				$filter_group = 7;
				break;
			case is_numeric($this->type):
				$filter_group = 1000 + $this->type; 
				break;
		}
	}
	

} // fin définition classe
