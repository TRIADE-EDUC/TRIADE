<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webdav.class.php,v 1.37.4.1 2025/03/14 08:07:35 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path, $base_path;
require_once($class_path."/connecteurs_out.class.php");
require_once($class_path."/connecteurs_out_sets.class.php");
require_once($include_path."/misc.inc.php");
require_once($include_path."/isbn.inc.php");
//on inclut les dépendances...
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/explnum.class.php");
require_once("$class_path/acces.class.php");
require_once("$class_path/notice.class.php");
require_once("$class_path/notice_doublon.class.php");
require_once($class_path."/epubData.class.php");
require_once($class_path.'/scan_request/scan_request.class.php');
require_once($class_path.'/scan_request/scan_request_status.class.php');
require_once($class_path.'/scan_request/scan_request_priority.class.php');
require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/vedette/vedette_composee.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');
require_once($class_path.'/nomenclature/nomenclature_record_formations.class.php');
require_once($class_path.'/nomenclature/nomenclature_record_formation.class.php');
require_once($class_path.'/nomenclature/nomenclature_nomenclature.class.php');
require_once($class_path.'/nomenclature/nomenclature_musicstand.class.php');
require_once($class_path.'/authperso_authority.class.php');
require_once($class_path."/concept.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path.'/nomenclature/nomenclature_voices.class.php');
require_once($class_path.'/nomenclature/nomenclature_voice.class.php');
require_once($class_path.'/nomenclature/nomenclature_workshop.class.php');
require_once($class_path.'/notice_relations.class.php');

// on teste si des répertoires de stockages sont paramétrés
if (pmb_mysql_num_rows(pmb_mysql_query("select * from upload_repertoire "))==0) {
	$pmb_docnum_in_directory_allow = 0;
} else {
	$pmb_docnum_in_directory_allow = 1;
}

function debug($elem,$new_file=true){
	global $base_path;
	global $source_id;
	if(is_string($elem)){
		if(!$new_file){
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",$elem,FILE_APPEND);
		}else{
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",$elem);
		}
	}else{
	if(!$new_file){
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",print_r($elem,true),FILE_APPEND);
		}else{
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",print_r($elem,true));
		}
	}
}

function sortChildren($a,$b){
	return strcmp(strtolower(convert_diacrit($a->getName())), strtolower(convert_diacrit($b->getName())));
}


class webdav extends connecteur_out {

	public function get_config_form() {
	    $result = $this->msg["webdav_no_configuration_required"];
	    return $result;
	}

	public function update_config_from_form() {
		return;
	}

	public function instantiate_source_class($source_id) {
		return new webdav_source($this, $source_id, $this->msg);
	}

	public function process($source_id, $pmb_user_id) {
		global $class_path;
		global $webdav_current_user_id,$webdav_current_user_name;

		$source_object = $this->instantiate_source_class($source_id);
		$webdav_current_user_id = 0;
		$webdav_current_user_name = "Anonymous";
		switch ($source_object->config['group_tree']) {
			case 'scan_request' :
				$rootDir = new Sabre\PMB\ScanRequest\Tree($source_object->config);
				break;
			case 'music' :
				$rootDir = new Sabre\PMB\Music\Tree($source_object->config);
				break;
			case 'standard' :
			default :
				$rootDir = new Sabre\PMB\Tree($source_object->config);
				break;
		}
		$server = new Sabre\DAV\Server($rootDir);

		if($source_object->config['allow_web']){
			$web = new Sabre\PMB\BrowserPlugin();
			$server->addPlugin($web);
		}

		if($source_object->config['authentication'] != "anonymous"){
			$auth = new Sabre\PMB\Auth($source_object->config['authentication']);
			$authPlugin = new Sabre\DAV\Auth\Plugin($auth);
			// Adding the plugin to the server
			$server->addPlugin($authPlugin);
		}

		// We're required to set the base uri, it is recommended to put your webdav server on a root of a domain
		$server->setBaseUri($source_object->config['base_uri']);
		// And off we go!

		$server->exec();
	}
}

class webdav_source extends connecteur_out_source {
	public $onglets = array();
	public $groups_collections = array();
	public $included_sets = array();
	public $categ;

	public function __construct($connector, $id, $msg) {

		parent::__construct($connector, $id, $msg);
		$this->included_sets = isset($this->config["included_sets"]) ? $this->config["included_sets"] : array();
	}

	protected function get_msg_to_display($message) {
		global $msg;

		if (substr($message, 0, 4) == "msg:") {
			if(isset($this->msg[substr($message, 4)])){
				return $this->msg[substr($message, 4)];
			}
		}
		return $message;
	}

	protected function parse_file_collections() {
		global $base_path;

		//Liste des collections possibles
		if (file_exists("$base_path/admin/connecteurs/out/webdav/collections_subst.xml"))
			$filename = "$base_path/admin/connecteurs/out/webdav/collections_subst.xml";
		else
			$filename = "$base_path/admin/connecteurs/out/webdav/collections.xml";

		$xml=file_get_contents($filename);
		$param=_parser_text_no_function_($xml,"COLLECTIONS");
		foreach ($param['GROUPS'][0]['GROUP'] as $group) {
			$group_collections = array();
			if(!empty($group['COLLECTION'])){
				foreach ($group['COLLECTION'] as $collection) {
					$group_collections[$collection['CODE']] = $this->get_msg_to_display($collection['value']);
				}
			}
			$this->groups_collections[$group['NAME']] = array(
					'label' => $this->get_msg_to_display($group['VALUE']),
					'collections' => $group_collections,
					'class' => $group['CLASS']
			);
		}
	}

	public function get_groups_collections() {
		if(!count($this->groups_collections)){
			$this->parse_file_collections();
		}
		return $this->groups_collections;
	}

	public function get_group_collections($name) {
		if(!count($this->groups_collections)){
			$this->parse_file_collections();
		}
		return $this->groups_collections[$name]['collections'];
	}

	public function get_config_form() {
		global $charset, $msg;
		global $base_path, $class_path;

		if(empty($this->config['base_uri'])){
			$this->config['base_uri'] = "/";
		}
		if(empty($this->config['group_tree'])){
			$this->config['group_tree'] = 'standard';
		}
		if(empty($this->config['restricted_empr_write_permission'])){
			$this->config['restricted_empr_write_permission'] = array();
		}
		if(empty($this->config['restricted_user_write_permission'])){
			$this->config['restricted_user_write_permission'] = array();
		}
		if(empty($this->config['metasMapper_class'])){
			$this->config['metasMapper_class'] = "";
		}
		if(empty($this->config['allow_web'])){
		    $this->config['allow_web'] = 0;
		}
		if(empty($this->config['authentication'])){
		    $this->config['authentication'] = "opac";
		}
		if(empty($this->config['write_permission'])){
		    $this->config['write_permission'] = 0;
		}
		if(empty($this->config['upload_rep'])){
			global $PMBuserid;
			$query = "select deflt_upload_repertoire from users where userid = ".$PMBuserid;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->config['upload_rep'] = pmb_mysql_result($result,0,0);
			}else{
				$this->config['upload_rep'] = 0;
			}
		}
		$result = parent::get_config_form();

		//Included sets
		$result.= "
			<script src='./javascript/ajax.js' type='text/javascript'></script>
			<div class='row'>
				<label for='base_uri'>".htmlentities($this->msg['webdav_base_uri'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<input type='text' name='base_uri' value='".htmlentities($this->config['base_uri'],ENT_QUOTES,$charset)."'/>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='base_uri'>".htmlentities($this->msg['webdav_allow_web'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				".htmlentities($this->msg['webdav_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='allow_web' value='1' ".($this->config['allow_web'] == 1 ? "checked='checked'" : "")."/>&nbsp;
				".htmlentities($this->msg['webdav_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='allow_web' value='0' ".($this->config['allow_web'] == 0 ? "checked='checked'" : "")."/>
						</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='authentication'>".htmlentities($this->msg['webdav_authentication'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select name='authentication'>
					<option value='anonymous' ".($this->config['authentication'] == "anonymous" ? "selected='selected'" : "").">".htmlentities($this->msg['webdav_anonymous'],ENT_QUOTES,$charset)."</option>
					<option value='gestion' ".($this->config['authentication'] == "gestion" ? "selected='selected'" : "").">".htmlentities($this->msg['webdav_authenticate_gest'],ENT_QUOTES,$charset)."</option>
					<option value='opac' ".($this->config['authentication'] == "opac" ? "selected='selected'" : "").">".htmlentities($this->msg['webdav_authenticate_opac'],ENT_QUOTES,$charset)."</option>
				</select>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='write_permission'>".htmlentities($this->msg['webdav_write_permission'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				".htmlentities($this->msg['webdav_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='write_permission' value='1' ".($this->config['write_permission'] == 1 ? "checked='checked'" : "")."/>&nbsp;
				".htmlentities($this->msg['webdav_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='write_permission' value='0' ".($this->config['write_permission'] == 0 ? "checked='checked'" : "")."/>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='restricted_write_permission'>".htmlentities($this->msg['webdav_restricted_write_permission'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>";
		//groupes d'utilisateurs
		$result.= "
				<div class='colonne2'>
					<label for='restricted_write_permission'>".htmlentities($this->msg['webdav_restricted_user_write_permission'],ENT_QUOTES,$charset)."</label><br />";
		$query = "SELECT grp_id, grp_name FROM users_groups ORDER BY grp_name ";
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)>0){
			$result .= "
				<select id='restricted_user_write_permission' name='restricted_user_write_permission[]' multiple>";
			while($obj = pmb_mysql_fetch_object($res)){
					$result.="
					<option value='".$obj->grp_id."' ".(in_array($obj->grp_id,$this->config['restricted_user_write_permission']) ? "selected=selected" : "") .">".htmlentities($obj->grp_name,ENT_QUOTES,$charset)."</option>";
			}
			$result.=" or id_noeud in (select id_noeud from noeuds where num_parent=".$this->categ->id."))
					</select>";
		}
		$result.= "
				</div>";

		$result.= "
				<div class='colonne-suite'>
					<label for='restricted_write_permission'>".htmlentities($this->msg['webdav_restricted_empr_write_permission'],ENT_QUOTES,$charset)."</label><br />";
		//catégories de lecteurs
		$requete = "SELECT id_categ_empr, libelle FROM empr_categ ORDER BY libelle ";
		$res = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)>0){
			$result .= "
				<select id='restricted_empr_write_permission' name='restricted_empr_write_permission[]' multiple>";
			while($obj = pmb_mysql_fetch_object($res)){
					$result.="
					<option value='".$obj->id_categ_empr."' ".(in_array($obj->id_categ_empr,$this->config['restricted_empr_write_permission']) ? "selected=selected" : "") .">".htmlentities($obj->libelle,ENT_QUOTES,$charset)."</option>";
			}
			$result.="
					</select>";
		}
			$result.= "
				</div>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='included_sets'>".htmlentities($this->msg['webdav_restricted_sets'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select MULTIPLE name='included_sets[]'>";
		$sets = new connector_out_sets();
		foreach ($sets->sets as &$aset) {
			$result.= "
					<option ".(in_array($aset->id, $this->included_sets) ? "selected" : "")." value='".$aset->id."'>".htmlentities($aset->caption ,ENT_QUOTES, $charset)."</option>";
		}
		$result.= "</select>
			</div>";

		$result.="
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='tree'>".htmlentities($this->msg['webdav_collections_group_tree'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select name='group_tree_elem' id='select_group_tree_elem' onchange='load_group_config_form(this.value)'>";
		foreach ($this->get_groups_collections() as $name=>$group_collection) {
			$result.="<option value='".$name."' ".($this->config['group_tree'] == $name ? 'selected="selected"' : '').">".$group_collection['label']."</option>";
		}
		$result.="</select>
			</div>";

		$result.= '<script type="text/javascript">
				function load_group_config_form(group_name){
					var request = new http_request();
					request.request("./ajax.php?module=admin&categ=webdav&sub=config_form",1, "&connector_id='.$this->connector_id.'&source_id='.$this->id.'&group_name="+group_name, 0, replace_group_config_form);
				}
				function replace_group_config_form(data){
					data = JSON.parse(data);
					document.getElementById("group_config_form").innerHTML = data.form;
					var script = document.createElement("script");
					script.innerHTML = data.script;
					document.getElementById("group_config_form").appendChild(script);
				}

		</script>';

		require_once($base_path.'/admin/connecteurs/out/webdav/groups/'.$this->groups_collections[$this->config['group_tree']]['class'].'.class.php');
		$webdav_group = new $this->groups_collections[$this->config['group_tree']]['class']($this->config, $this->get_group_collections($this->config['group_tree']), $this->msg);

		$result.= '<div id="group_config_form">';
		$result.= $webdav_group->get_config_form();
		if ($config_form_script = $webdav_group->get_config_form_script()) {
			$result.='<script type="text/javascript">'.$config_form_script.'</script>';
		}
		$result.='</div>';

		$result.="
			<div class='row'>
				<label for='default_statut'>".htmlentities($this->msg['webdav_metasMapper_class'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<input type='text' name='metasMapper_class' value='".htmlentities($this->config['metasMapper_class'],ENT_QUOTES,$charset)."'/>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='default_statut'>".htmlentities($this->msg['webdav_default_statut'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>";
		$query = "select id_notice_statut, gestion_libelle from notice_statut order by gestion_libelle";
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$result .="
				<select name='default_statut'>";
			while($row=pmb_mysql_fetch_object($res)){
				$result.="
					<option value='".$row->id_notice_statut."'".(!empty($this->config['default_statut']) && $row->id_notice_statut == $this->config['default_statut'] ? " selected='selected' " : "").">".htmlentities($row->gestion_libelle,ENT_QUOTES,$charset)."</option>";
			}
			$result.="
				</select>";
		}
		$result.="
			</div>
			<div class='row'>&nbsp;</div>
			<script src=\"./javascript/select.js\" type='text/javascript'></script>";

		//statut docunum
		$result.="
			<div class='row'>
				<label for='default_docnum_statut'>".htmlentities($this->msg['webdav_default_docnum_statut'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>";
		$query = "select id_explnum_statut, gestion_libelle from explnum_statut order by gestion_libelle";
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$result .="
				<select name='default_docnum_statut'>";
			while($row = pmb_mysql_fetch_object($res)){
				$result.="
					<option value='".$row->id_explnum_statut."'".(!empty($this->config['default_docnum_statut']) && $row->id_explnum_statut == $this->config['default_docnum_statut'] ? " selected='selected' " : "").">".htmlentities($row->gestion_libelle,ENT_QUOTES,$charset)."</option>";
			}
			$result .="
				</select>";
		}
		$result.="
			</div>";


		global $pmb_docnum_in_database_allow,$pmb_docnum_in_directory_allow;

				$result.= "<div class='row'>";

		if ($pmb_docnum_in_database_allow) {
			$result .= "<input type='radio' name='up_place' id='base' value='0' !!check_base!! /> <label for='base'>".$msg['upload_repertoire_sql']."</label>";
		}

		if ($pmb_docnum_in_directory_allow) {
			$result .= "<input type='radio' name='up_place' id='upload' value='1' !!check_up!! /> <label for='upload'>".$msg['upload_repertoire_server']."</label>";
				$req="select repertoire_id, repertoire_nom from upload_repertoire order by repertoire_nom";
				$res = pmb_mysql_query($req);
				if(pmb_mysql_num_rows($res)){
					$result.="
						<select name='id_rep'>";
					while ($row = pmb_mysql_fetch_object($res)){
						$result.="
							<option value='".$row->repertoire_id."' ".($row->repertoire_id == $this->config['upload_rep'] ? " selected='selected' " : "").">".htmlentities($row->repertoire_nom,ENT_QUOTES,$charset)."</option>";
					}
					$result.="
						</select>";
				}
		}

		if($pmb_docnum_in_directory_allow && !empty($this->config['up_place'])){
					$result = str_replace('!!check_base!!','', $result);
			$result = str_replace('!!check_up!!',"checked='checked'", $result);
		} else if($pmb_docnum_in_database_allow) {
			$result = str_replace('!!check_up!!','', $result);
			$result = str_replace('!!check_base!!',"checked='checked'", $result);
				}

		$result .= "</div>";

		return $result;
	}

	public function update_config_from_form() {
		global $included_sets;
		global $group_tree_elem;
		global $authentication;
		global $write_permission;
		global $restricted_empr_write_permission,$restricted_user_write_permission;
		global $default_statut;
		global $base_uri;
		global $id_rep;
		global $up_place;
		global $allow_web;
		global $default_docnum_statut;
		global $metasMapper_class;
		global $base_path;
		global $class_path;

		parent::update_config_from_form();
		$this->config['included_sets'] = $included_sets;
		$this->config['group_tree'] = $group_tree_elem;
		$this->config['authentication']= $authentication;
		$this->config['write_permission']= $write_permission;
		$this->config['restricted_empr_write_permission'] = $restricted_empr_write_permission;
		$this->config['restricted_user_write_permission'] = $restricted_user_write_permission;
		$this->config['default_statut'] = $default_statut;
		$this->config['base_uri'] = $base_uri;
		$this->config['upload_rep'] = $id_rep;
		$this->config['up_place'] = $up_place;
		$this->config['allow_web'] = $allow_web;
		$this->config['default_docnum_statut'] = $default_docnum_statut;
		$this->config['metasMapper_class'] = $metasMapper_class;

		if ($this->config['group_tree']) {
			$this->get_groups_collections();
			$group_class = $this->groups_collections[$this->config['group_tree']]['class'];
			require_once($base_path.'/admin/connecteurs/out/webdav/groups/'.$group_class.'.class.php');
			$this->config = array_merge($this->config, $group_class::update_config_from_form());
		}
		return;
	}

}