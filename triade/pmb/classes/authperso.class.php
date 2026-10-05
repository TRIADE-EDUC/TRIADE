<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.class.php,v 1.125.2.2.2.2 2025/03/07 15:59:18 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkEntityPmb;

global $javascript_path; // pas compris pourquoi, sinon fait planter connector_out
global $class_path, $include_path,$base_path;

require_once($javascript_path."/misc.inc.php");
require_once($include_path."/templates/authperso.tpl.php");
require_once($include_path."/templates/parametres_perso.tpl.php");
require_once($class_path."/custom_parametres_perso.class.php");
require_once("$class_path/aut_link.class.php");
require_once($class_path."/index_concept.class.php");
require_once("$class_path/audit.class.php");
require_once($include_path."/h2o/h2o.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/indexation_authperso.class.php");
require_once($class_path."/authority.class.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once "$class_path/event/events/event_authperso.class.php";
require_once ($class_path.'/interface/entity/interface_entity_authperso_form.class.php');
require_once ($class_path.'/netbase/netbase.class.php');
require_once ($base_path.'/admin/netbase/params.inc.php');

class authperso {
	public $id=0;
	public $info=array();
	public $elt_id=0;
	public $cp_error_message;
	public $editable_fields;
	public $display = "";

	protected $searcher_instance;
	protected static $controller;

	protected static $prefixes = array(
	        'author',
	        'authperso',
	        'categ',
	        'cms_editorial',
	        'collection',
	        'indexint',
	        'notices',
	        'publisher',
	        'serie',
	        'subcollection',
	        'tu',
	        'empr',
	        'skos',
	        'collstate',
	        'demandes',
	        'expl',
	        'explnum',
	        'pret',
	        'gestfic0',
	);

	protected static $custom_pperso_instances = [];

	public function __construct($id=0,$id_auth=0) {
		$id = intval($id);
		$id_auth = intval($id_auth);
		if(!$id && $id_auth) {
			$query = "SELECT authperso_authority_authperso_num FROM authperso_authorities, authperso WHERE id_authperso=authperso_authority_authperso_num AND id_authperso_authority={$id_auth} LIMIT 1";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
			    $row = pmb_mysql_fetch_object($result);
			    $id = $row->authperso_authority_authperso_num;
			    pmb_mysql_free_result($result);
			}
		}
		$this->id=$id;
		$this->fetch_data();
	}
	public function get_view($id){
		global $base_path;

		$id = (int) $id;
		$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
		$res = pmb_mysql_query($req);
		if(($r=pmb_mysql_fetch_object($res))) {
			$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,static::format_url("&sub=update"));
			$p_perso->get_out_values($id);
			$authperso_fields=$p_perso->values;
			$aut_link= new aut_link($r->authperso_authority_authperso_num + 1000,$id);
			if($r->authperso_view_script){
				if(!file_exists($base_path.'/temp/'.LOCATION.'_authperso_view_'.$r->authperso_authority_authperso_num)){
					file_put_contents($base_path.'/temp/'.LOCATION.'_authperso_view_'.$r->authperso_authority_authperso_num, $r->authperso_view_script);
				}
				$h2o = H2o_collection::get_instance($base_path.'/temp/'.LOCATION.'_authperso_view_'.$r->authperso_authority_authperso_num);
				$view = $h2o->render($authperso_fields);
			}else{
				$view = "";
				foreach ($authperso_fields as $field){
					$view.=$field['values'][0]['format_value'].".  ";
				}
			}
		}
		return $view;
	}


	public function fetch_data() {
	    global $charset;
		$this->info=array();
		$this->info['fields']=array();
		if(!$this->id) return;

		$req="select * from authperso where id_authperso=". $this->id." order by authperso_name";
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			$this->info=array(
			    'id' => $r->id_authperso,
			    'name' => translation::get_translated_text($r->id_authperso, 'authperso', 'authperso_name', $r->authperso_name),
			    'onglet_num' => $r->authperso_notice_onglet_num,
			    'isbd_script' => $r->authperso_isbd_script,
			    'opac_search' => $r->authperso_opac_search,
			    'opac_multi_search' => $r->authperso_opac_multi_search,
			    'gestion_search' => $r->authperso_gestion_search,
			    'gestion_multi_search' => $r->authperso_gestion_multi_search,
			    'comment' => $r->authperso_comment,
			    'event' => $r->authperso_oeuvre_event,
			    'responsability_authperso' => $r->authperso_responsability_authperso,
			    'onglet_name' => ''
			);
			pmb_mysql_free_result($resultat);

			$req="SELECT onglet_name FROM notice_onglet where id_onglet=".$r->authperso_notice_onglet_num;
			$resultat=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($resultat)) {
				$r_onglet=pmb_mysql_fetch_object($resultat);
				$this->info['onglet_name']= $r_onglet->onglet_name;
				pmb_mysql_free_result($resultat);
			}
		}
		$req="select * from authperso_custom where num_type=". $this->id." order by ordre";
		$resultat=pmb_mysql_query($req);
		$i=0;
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
			    $this->info['fields'][$i]= array();
			    $this->info['fields'][$i]['id']= $r->idchamp;
				$this->info['fields'][$i]['name']= $r->name;
				$this->info['fields'][$i]['label']= $r->titre;
				$this->info['fields'][$i]['type']= $r->type ;
				$this->info['fields'][$i]['datatype']= $r->datatype;
				$this->info['fields'][$i]['ordre']= $r->ordre ;
				$this->info['fields'][$i]['search']=$r->search;
				$this->info['fields'][$i]['pond']=$r->pond;
				$this->info['fields'][$i]['obligatoire']=$r->obligatoire;
				$this->info['fields'][$i]['export']=$r->export;
				$this->info['fields'][$i]['multiple']=$r->multiple;
				$this->info['fields'][$i]['opac_sort']=$r->opac_sort;
				$this->info['fields'][$i]['code_champ']=$this->id;
				$this->info['fields'][$i]['code_ss_champ']=$r->idchamp;
				$this->info['fields'][$i]['data']= array();
				$this->info['fields'][$i]['OPTIONS'] = [_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS")];
				$i++;
			}
			pmb_mysql_free_result($resultat);
		}
	}

	public function get_data(){
		return $this->info;
	}

	public function get_info_fields($id=0){
		$info= array();
		$id = intval($id);
		if($id){
			$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
			$res = pmb_mysql_query($req);
			if(($r=pmb_mysql_fetch_object($res))) {
				$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,static::format_url("&sub=update"));
				$p_perso->get_out_values($id);
				$authperso_fields=$p_perso->values;
			}
		}
		if(isset($this->info['fields']) && is_array($this->info['fields'])) {
    		foreach($this->info['fields'] as $field){
    		    $info[$field['id']]= array();
    		    $info[$field['id']]['id']= $field['id'];
    			$info[$field['id']]['name']= $field['name'];
    			$info[$field['id']]['label']= $field['label'];
    			$info[$field['id']]['type']= $field['type'];
    			$info[$field['id']]['ordre']= $field['ordre'];
    			$info[$field['id']]['search']=$field['search'];
    			$info[$field['id']]['pond']=$field['pond'];
    			$info[$field['id']]['obligatoire']=$field['obligatoire'];
    			$info[$field['id']]['export']=$field['export'];
    			$info[$field['id']]['multiple']=$field['multiple'];
    			$info[$field['id']]['opac_sort']=$field['opac_sort'];
    			$info[$field['id']]['code_champ']=$this->id;
    			$info[$field['id']]['code_ss_champ']=$field['id'];
    			$info[$field['id']]['values']= (isset($authperso_fields[$field['name']]['values']) ? $authperso_fields[$field['name']]['values'] : '');
    			$info[$field['id']]['all_format_values']= (isset($authperso_fields[$field['name']]['all_format_values']) ? $authperso_fields[$field['name']]['all_format_values'] : '');
    		}
		}
		return $info;
	}

	public function get_data_auth($id) {
	    $p_perso=self::get_custom_pperso_instance($this->id);
	    $p_perso->get_out_values($id);
	    return $p_perso->values;
	}

	public function fetch_data_auth($id) {
	    $id = intval($id);
	    $values = $this->get_data_auth($id);
	    $this->info['data_auth'][$id]=$values;
		//pour ne pas louper les champs vides...
		foreach($this->info['fields'] as $i =>$field){
			if(isset($this->info['data_auth'][$id][$field['name']])) {
				$this->info['fields'][$i]['data'][$id]=$this->info['data_auth'][$id][$field['name']];
			} else {
				$this->info['fields'][$i]['data'][$id]=array('values' =>
					array(
						array(
							'value' => '',
							'format_value' => ''
						)
					)
				);
			}
		}
		return $values;
	}

	public function get_search_list($tpl_auth,$restriction){
		global $charset;

		$auth_lines = '';
		$req = "select * from authperso_authorities where  authperso_authority_authperso_num= ".$this->id;
		$req .= " order by id_authperso_authority DESC $restriction";
		$res = pmb_mysql_query($req);
		while(($r=pmb_mysql_fetch_object($res))) {
			$id=$r->id_authperso_authority;
			$isbd=static::get_isbd($id);
			$tpl=$tpl_auth;
			$tpl = str_replace ('!!isbd_addslashes!!', htmlentities(addslashes($isbd),ENT_QUOTES, $charset), $tpl);
			$tpl = str_replace ('!!isbd!!', htmlentities($isbd, ENT_QUOTES, $charset), $tpl);
			$tpl = str_replace ('!!auth_id!!', $id, $tpl);
			$auth_lines.=$tpl;
		}
		return $auth_lines;
	}

	public function get_list($form_only = false) {
		global $msg, $charset;
		global $user_query, $user_input, $page, $nbr_lignes, $sub;
		global $url_base;
		global $authority_statut;
		global $nb_per_page_gestion;
		global $pmb_perso_sep;

		$sorted_authperso = array();
		if(!$form_only) {
			if(!$user_input){
				$user_input = '*';
			}
			$nb_per_page_gestion = (int) $nb_per_page_gestion;
			if(!$page){
				$page = 1;
			}
			$debut = ($page-1) * $nb_per_page_gestion;

			if($sub == 'authperso_last') { // les derniers créés
				$req = "select SQL_CALC_FOUND_ROWS num_object, id_authority from authorities left join authperso_authorities on num_object=id_authperso_authority and type_object=9 where authperso_authority_authperso_num= ".$this->id;
				$req .= " order by id_authperso_authority DESC LIMIT $debut,  $nb_per_page_gestion";
				$res = pmb_mysql_query($req);
				if ( pmb_mysql_num_rows($res)) {
					while(($r = pmb_mysql_fetch_object($res))) {
						$sorted_authperso[] = $r->id_authority;
					}
					$nbr_lignes = pmb_mysql_result(pmb_mysql_query('select FOUND_ROWS()'), 0, 0);
				}
			}else {
				$this->get_searcher_instance();
				$nbr_lignes = $this->searcher_instance->get_nb_results();
				$sorted_authperso = $this->searcher_instance->get_sorted_result('default', $debut, $nb_per_page_gestion);
			}
		}
		$user_query = str_replace ('!!user_query_title!!', $msg["authperso_search_title"], $user_query);
		$user_query = str_replace ('!!action!!', static::format_url("&sub=reach&id_authperso=".$this->id."&id="), $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg["authperso_search_add"] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', static::format_url('&sub=authperso_form&id_authperso='.$this->id), $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='".static::format_url("&sub=authperso_last&last_param=authperso_last")."'>".$msg["authperso_search_last"]."</a>", $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		$user_query = str_replace('!!user_input_url!!',	rawurlencode(stripslashes($user_input)),$user_query);

		$user_query = str_replace("<!-- sel_authority_statuts -->", authorities_statuts::get_form_for($this->id +1000, $authority_statut, true), $user_query);

		if($form_only) {
			// Pas de recherche a effectuer
			return $user_query;
		}

		if ($nbr_lignes) {
			$auth_lines = '';
			$parity = 1;
			foreach($sorted_authperso as $id_authperso) {
				if ($parity % 2) $pair_impair = "even"; else $pair_impair = "odd";
				$parity += 1;
				$authority = new authority($id_authperso);
				$authority_instance = $authority->get_object_instance();
				$id = $authority->get_num_object();
				$this->fetch_data_auth($id);
				$auth_line = "<td style='text-align:center; width:25px;'>
	        					<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=authperso&id=".$id."'>
	        						<i class='fa fa-eye'></i>
	        					</a>
	        		    	  </td>";
				//$this->info['fields'][$i]['data'][$id][$field['name']]
				$statut_class_html = $authority->get_display_statut_class_html();
				foreach($this->info['fields'] as $field){
					$data_labels = array();
					if(is_array($field['data'][$id]['values']) && is_countable($field['data'][$id]['values'])) {
						foreach ($field['data'][$id]['values'] as $data_value) {
							$data_labels[] = $data_value['format_value'];
						}
					}
					$data_label = implode($pmb_perso_sep, $data_labels);
					$auth_line.= "<td sorttable_customkey='".strip_tags($data_label)."' onmousedown=\"document.location='".static::format_url("&sub=authperso_form&id=".$id."&amp;user_input=!!user_input_url!!&amp;nbr_lignes=".$nbr_lignes."&amp;page=".$page)."';\" title='' style='vertical-align:top'>";
					//$auth_line.= "<td onmousedown=\"document.location='./autorites.php?categ=see&sub=authperso&id=$id';\" title='' valign='top'>";
					$auth_line.= $statut_class_html.$data_label."</td>";
					$statut_class_html = '';
					$auth_line = str_replace('!!user_input_url!!',	rawurlencode(stripslashes($user_input)), $auth_line);
				}
				// usage
				$auth_line.= "
					<td onmousedown=\"document.location='./catalog.php?categ=search&mode=".($this->id +1000)."&etat=aut_search&aut_type=authperso&authperso_id=".$this->id."&aut_id=$id';\" title='' style='vertical-align:top'>".
						$this->get_count_notice($id)
					."</td>";

				$auth_lines.= "
				<tr class='" . $pair_impair . "' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='" . $pair_impair . "'\" style=\"cursor: pointer\">
					$auth_line
				</tr>
				";
			}
			$authperso_list_tpl = $user_query."
			<br />
			<br />
			<div class='row'>
				<h3><! --!!nb_autorite_found!!-- >".$msg["authperso_search_found"]." !!cle!! </h3>".
				entities_authperso_controller::get_sort_link($nbr_lignes, $this->get_sort_entity_type())."
				</div>
				<script type='text/javascript' src='./javascript/sorttable.js'></script>
				<table class='sortable'>
					<tr>
						<th></th>
					!!th_fields!!
					</tr>
					!!list!!
				</table>
			<div class='row'>
				!!nav_bar!!
			</div>
			";

			$th_fields = '';
			foreach($this->info['fields'] as $field){
				$th_fields.= "<th>".htmlentities($field['label'], ENT_QUOTES, $charset)."</th>";
			}
			$th_fields.= "<th>".htmlentities($msg['authperso_usage'], ENT_QUOTES, $charset)."</th>";

			$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;

			$authperso_list_tpl = str_replace( "<! --!!nb_autorite_found!!-- >", $nbr_lignes.' ', $authperso_list_tpl);
			$authperso_list_tpl = str_replace("!!th_fields!!", $th_fields, $authperso_list_tpl);
			$authperso_list_tpl = str_replace("!!cle!!", htmlentities(stripslashes($user_input), ENT_QUOTES, $charset), $authperso_list_tpl);
			$authperso_list_tpl = str_replace("!!list!!", $auth_lines, $authperso_list_tpl);
			$authperso_list_tpl = str_replace("!!nav_bar!!", $nav_bar, $authperso_list_tpl);
		} else {
			$authperso_list_tpl = $user_query;
			$error_message = str_replace(
				'!!user_input!!',
				htmlentities(stripslashes($user_input), ENT_QUOTES, $charset),
				$msg['authority_authperso_no_authority_found_with_key']
			);
			$error_message = str_replace('!!authperso_name!!', $this->info['name'], $error_message);
			$authperso_list_tpl.= return_error_message($msg[211], $error_message);
		}

		return $authperso_list_tpl;
	}

	public function get_list_selector($id_to_view = 0, $url = '', $nb_per_page = 10) {
		global $charset;
		global $nb_per_page_search, $user_input, $f_user_input, $page, $nbr_lignes, $last_param;
		global $callback;
		global $caller;
		global $base_url;

		if(!$url){
			$url = $base_url;
		}

		if($id_to_view){
			$isbd = strip_tags(static::get_isbd($id_to_view));
			$authority = new authority(0, $id_to_view, AUT_TABLE_AUTHPERSO);
			return "<br />".$authority->get_display_statut_class_html()."<a href='#' onclick=\"set_parent('".$caller."', '".$id_to_view."', '".htmlentities(addslashes(str_replace("\r"," ", str_replace("\n"," ",$isbd))), ENT_QUOTES, $charset)."','".$callback."')\">".
					htmlentities($isbd, ENT_QUOTES, $charset)."</a><br />";
		}
		$auth_lines = '';
		if(!$page) $page = 1;
		$debut = ($page-1)*$nb_per_page;

		if (!$user_input && $f_user_input) {
			$user_input = $f_user_input;
		}

		$search_word = str_replace('*', '%', $user_input);
		if(!($nb_per_page*1)){
			$nb_per_page = $nb_per_page_search;
		}
		if(!$page) $page = 1;
		if(!$last_param){
			$debut = ($page-1)*$nb_per_page;
			$requete = "SELECT count(1) FROM authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and authperso_authority_authperso_num= ".$this->id;
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			$nbepages = ceil($nbr_lignes/$nb_per_page);
			if($page>$nbepages){
				$debut = 0;
				$page = 1;
			}
			$req = "select * from authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by authperso_index_infos_global LIMIT ".$debut.",".$nb_per_page." ";
		}else{ // les derniers créés
			$req = "select * from authperso_authorities where  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by id_authperso_authority DESC LIMIT $nb_per_page";
		}
		$res = pmb_mysql_query($req);
		while(($r = pmb_mysql_fetch_object($res))) {
			$id = $r->id_authperso_authority;
			$isbd = strip_tags(static::get_isbd($id));
			$authority = new authority(0, $id, AUT_TABLE_AUTHPERSO);
			$auth_lines.= $authority->get_display_statut_class_html()."<a href='#' onclick=\"set_parent('".$caller."', '".$id."', '".htmlentities(addslashes(str_replace("\r", " ", str_replace("\n"," ", $isbd))), ENT_QUOTES, $charset)."','".$callback."')\">".
					htmlentities($isbd, ENT_QUOTES, $charset)."</a><br />";
		}

		//$url_base = $base_url."&user_input=".rawurlencode(stripslashes($user_input));
		$nav_bar = aff_pagination ($url, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;

		$authperso_list_tpl = "
			<br />
				$auth_lines
			<div class='row'>&nbsp;<hr /></div><div class='center'>
				$nav_bar
			</div>
		";

		return $authperso_list_tpl;
	}

	protected static function get_custom_pperso_instance($authperso_num){
	    if(!empty(self::$custom_pperso_instances[$authperso_num])){
			return self::$custom_pperso_instances[$authperso_num];
	    }
	    self::$custom_pperso_instances[$authperso_num] = new custom_parametres_perso("authperso", "authperso", $authperso_num);
	    return self::$custom_pperso_instances[$authperso_num];
	}

	public static function get_isbd($id) {
		global $base_path;

		$id = intval($id);
		if (!$id) {
			return '';
		}

		$isbd = '';
		$req = "select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
		$res = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($res)) {
			$r = pmb_mysql_fetch_object($res);
			pmb_mysql_free_result($res);

			$p_perso = self::get_custom_pperso_instance($r->authperso_authority_authperso_num);
			$p_perso->get_out_values($id);
			$authperso_fields = $p_perso->values;
			if ($r->authperso_isbd_script) {
				$index_concept = new index_concept($id, TYPE_AUTHPERSO);
				$authperso_fields['index_concepts'] = $index_concept->get_data();
				if($r->authperso_responsability_authperso) {
					$authperso_fields["responsability_authperso"] = static::get_responsability_authperso_data($id);
				}
				if (!file_exists($base_path.'/temp/'.LOCATION.'_authperso_isbd_'.$r->authperso_authority_authperso_num)) {
					file_put_contents($base_path.'/temp/'.LOCATION.'_authperso_isbd_'.$r->authperso_authority_authperso_num, $r->authperso_isbd_script);
				}

				$h2o = H2o_collection::get_instance($base_path.'/temp/'.LOCATION.'_authperso_isbd_'.$r->authperso_authority_authperso_num, [
				        'id_authperso_authority' => $r->id_authperso_authority
				    ]
				);
				$isbd = $h2o->render($authperso_fields);
			} else {
			    if (!empty($authperso_fields)) {
			        $format_values = array();
    				foreach ($authperso_fields as $field) {
    				    $format_values[] = $field['values'][0]['format_value'];
    				}

    				$isbd .= implode(".  ", $format_values);
			    }
			}
		}
		return trim(preg_replace('/\s+/', ' ', $isbd));
	}

	public function get_count_notice($id){
		$req = "select count(1) from notices_authperso where notice_authperso_authority_num=". $id;
		return pmb_mysql_result(pmb_mysql_query($req), 0, 0);
	}

	public function get_notices($id){
		$list = array();
		$req = "select notice_authperso_notice_num from notices_authperso where notice_authperso_authority_num=". $id;
		$res = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($res)) {
		    while ($r = pmb_mysql_fetch_object($res)) {
		        $list[] = $r->notice_authperso_notice_num;
		    }
		}
		return $list;
	}

	public function is_event() {

    	$req = "select authperso_oeuvre_event from authperso where id_authperso=". $this->id;
    	$res = pmb_mysql_query($req);
    	if(($r=pmb_mysql_fetch_object($res))) {
    	    return $r->authperso_oeuvre_event;
    	}
    	return 0;
	}

	public function get_oeuvres($id) {

	    $values = array();
	    $req = "select * from tu_oeuvres_events where oeuvre_event_authperso_authority_num=". $id . " order by oeuvre_event_order";
	    $res = pmb_mysql_query($req);
	    if (pmb_mysql_num_rows($res)) {
	        while (($r = pmb_mysql_fetch_object($res))) {
    	        $tu = new titre_uniforme($r->oeuvre_event_tu_num);
    	        $values[] = array(
    	           'id' => $r->oeuvre_event_tu_num,
    	           'label' => $tu->get_isbd()
    	        );
	        }
	    }
	    return $values;
	}

	public function get_editable_fields($id) {
		if(!isset($this->editable_fields[$id])) {
			$p_perso = new custom_parametres_perso("authperso", "authperso", $this->id, static::format_url("&sub=update"));
			$this->editable_fields[$id] = $p_perso->show_editable_fields($id);
		}
		return $this->editable_fields[$id];
	}

	protected function get_content_form($id) {
		global $msg, $charset, $thesaurus_concepts_active;
		global $authperso_content_form;
		global $_custom_prefixe_;

		$_custom_prefixe_ = "authperso";
		$id = (int) $id;
		$authperso_fields = $this->get_editable_fields($id);

		$content_form = $authperso_content_form;
		$content_form = str_replace('!!id!!', $id, $content_form);

		$authperso_field_tpl="
		<div id='!!node_id!!' movable='yes' title=\"".htmlentities('!!titre!!', ENT_QUOTES, $charset)."\">
		<div class='row'>
				<label class='etiquette'>!!titre!! </label>!!comment!!
		</div>
		<div class='row'>
			!!aff!!
			</div>
		</div>";
		$tpl = '';
		if ( isset($authperso_fields['FIELDS']) && is_array($authperso_fields['FIELDS']) ) {
			foreach($authperso_fields['FIELDS'] as $field){
				$field_tpl=$authperso_field_tpl;
				$field_tpl = str_replace("!!node_id!!", $field['NAME'], $field_tpl);
				$field_tpl = str_replace("!!titre!!", $field['TITRE'], $field_tpl);
				$field_tpl = str_replace("!!aff!!", $field['AFF'], $field_tpl);
				$field_tpl = str_replace("!!comment!!", $field['COMMENT_DISPLAY'], $field_tpl);
				$tpl.=$field_tpl;
			}
		}

		$aut_link= new aut_link($this->id+1000,$id);
		$content_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_authperso') , $content_form);

		if ($this->is_event($id)) {
			// Oeuvres de l'évenement
			$form_oeuvres = "
        	<div id='el8Child_0' class='row' movable='yes' title=\"".htmlentities($msg['catal_onglet_titre_uniforme'],ENT_QUOTES, $charset)."\">
        		!!oeuvres_contens!!
        	</div>";
			$oeuvres  = tu_notice::gen_input_selection($msg["authperso_oeuvres_event"],'saisie_authperso', "titre_uniforme",
					$this->get_oeuvres($id),"titre_uniforme","saisie-80emr", 0);
			$form_oeuvres = str_replace('!!oeuvres_contens!!', $oeuvres, $form_oeuvres);
			$content_form = str_replace('<!-- tu_link -->', $form_oeuvres, $content_form);
		}

		if ($this->is_responsability_authperso()) {
			$content_form = str_replace('<!-- rep_aut_link -->', $this->gen_template_responsability($id), $content_form);
		}
		// Indexation concept
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($id, TYPE_AUTHPERSO);
			$content_form = str_replace('<!-- index_concept_form -->', $index_concept->get_form('saisie_authperso'), $content_form);
		}
		$authority = new authority(0, $id, AUT_TABLE_AUTHPERSO);
		$content_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $content_form);
		$content_form = str_replace("!!list_field!!", $tpl, $content_form);

		return $content_form;
	}

	public function get_form($id, $duplicate = false) {
		global $msg;
		global $user_input, $nbr_lignes, $page;

		if($id){
			$authority = new authority(0, $id, AUT_TABLE_AUTHPERSO);
			$statut = $authority->get_num_statut();
		} else {
			$statut=1;
		}

		$interface_form = new interface_entity_authperso_form('saisie_authperso');
		if(isset(static::$controller) && is_object(static::$controller)) {
			$interface_form->set_controller(static::$controller);
		}
		$interface_form->set_enctype('multipart/form-data');
		if($id && !$duplicate) {
			$interface_form->set_label($msg['authperso_form_titre_edit']);
			$interface_form->set_document_title(strip_tags(self::get_isbd($id)).' - '.$msg['authperso_form_titre_edit']);
			$interface_form->set_object_id($id);
		} else {
			$interface_form->set_label($msg['authperso_form_titre_new']);
			$interface_form->set_document_title($msg['authperso_form_titre_new']);
		}
		$interface_form->set_id_authperso($this->id)
		->set_num_statut($statut)
		->set_content_form($this->get_content_form($id))
		->set_table_name('authperso_authorities')
		->set_url_base(static::format_url());

		$interface_form->set_page($page)
		->set_nbr_lignes($nbr_lignes)
		->set_user_input($user_input);

		$display = $interface_form->get_display();
		$authperso_fields = $this->get_editable_fields($id);
		$display .= $authperso_fields['CHECK_SCRIPTS'];
		return $display;
	}

	public function get_form_select($id,$base_url) {
		global $msg,$charset,$authperso_form_select;
		global $user_query, $user_input,$page,$nbr_lignes;
		global $pmb_type_audit;
		global $thesaurus_concepts_active;

		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id,static::format_url("&sub=update"));
		$authperso_fields=$p_perso->show_editable_fields($id);

		$authperso_field_tpl="
		<div class='row'>
			<label class='etiquette'>!!titre!! </label>!!comment!!
		</div>
		<div class='row'>
			!!aff!!
		</div>";
		$tpl = '';
		if(is_array($authperso_fields['FIELDS'])) {
			foreach($authperso_fields['FIELDS'] as $field){
				//printr($field);
				$field_tpl=$authperso_field_tpl;
				$field_tpl = str_replace("!!titre!!", $field['TITRE'], $field_tpl);
				$field_tpl = str_replace("!!aff!!", $field['AFF'], $field_tpl);
				$field_tpl = str_replace("!!comment!!", $field['COMMENT_DISPLAY'], $field_tpl);
				$tpl.=$field_tpl;
			}
		}
		$button_remplace = "<input type='button' class='bouton' value='$msg[158]' onclick='unload_off();document.location=\"".static::format_url("&sub=replace&id=".$id)."\"'>";
		$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=".($this->id + 1000)."&etat=aut_search&aut_type=authperso&aut_id=$id\"'>";

		if ($pmb_type_audit && $id)
			$bouton_audit= audit::get_dialog_button($id, ($this->id + 1000));

		$aut_link= new aut_link($this->id+1000,$id);
		$authperso_form_select = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_authperso') , $authperso_form_select);

		// Indexation concept
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($id, TYPE_AUTHPERSO);
			$authperso_form_select = str_replace('<!-- index_concept_form -->', $index_concept->get_form('saisie_authperso'), $authperso_form_select);
		}

		$authperso_form_select = str_replace("!!libelle!!", $msg['authperso_form_titre_new'], $authperso_form_select);
		$authperso_form_select = str_replace("!!list_field!!", $tpl, $authperso_form_select);

		$authperso_form_select = str_replace("!!retour!!", "$base_url&action=", $authperso_form_select);
		$authperso_form_select = str_replace("!!action!!", "$base_url&action=update", $authperso_form_select);
		$authperso_form_select = str_replace("!!id_authperso!!", $this->id, $authperso_form_select);
		$authperso_form_select = str_replace("!!id!!", $id, $authperso_form_select);
		return $authperso_form_select;
	}

	public function update_from_form($id=0) {
		global $thesaurus_concepts_active;
		global $authority_statut;
		global $authority_thumbnail_url;
		global $msg, $max_titre_uniforme;
		global $responsabilities;

		$id = (int) $id;

		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);

		if($p_perso->check_submited_fields()){ //Des erreurs de types dans les champs perso postés
			$this->cp_error_message = $p_perso->error_message;
			return;
		}

		$error_list = $p_perso->check_mandatory_fields_value();
		if(count($error_list)){
			$this->cp_error_message=sprintf($msg['parperso_field_is_needed'],$error_list[0]['field']['TITRE']);
			return;
		}

		if(!$id){
			$requete="insert into authperso_authorities set authperso_authority_authperso_num=".$this->id;
			pmb_mysql_query($requete);
			$id = pmb_mysql_insert_id();
			audit::insert_creation ($this->id+1000,$id);
		}else{
			audit::insert_modif ($this->id+1000,$id);
		}
		if(!$id) return;

		//update authority informations
		$authority = new authority(0, $id, AUT_TABLE_AUTHPERSO);
		$authority->set_num_statut($authority_statut);
		$authority->set_thumbnail_url($authority_thumbnail_url);
		$authority->update();

		$p_perso->rec_fields_perso($id);

		$aut_link= new aut_link($this->id+1000,$id);
		$aut_link->save_form();

		// Oeuvres associées à l'évennement
		$query = "DELETE FROM tu_oeuvres_events WHERE oeuvre_event_authperso_authority_num=" . $id;
		pmb_mysql_query($query);
		$order = 0;
		$max_titre_uniforme = intval($max_titre_uniforme);
	    for ($i = 0; $i < $max_titre_uniforme ; $i++) {
	        $var_tu_id = 'f_titre_uniforme_code' . $i;
	        global ${$var_tu_id};
	        if (($tu_id = intval(${$var_tu_id}))) {
	            $query = "INSERT INTO tu_oeuvres_events SET
                    oeuvre_event_authperso_authority_num =" . $id . ",
                    oeuvre_event_tu_num =" . $tu_id . ",
                    oeuvre_event_order =".$order++;
                pmb_mysql_query($query);
	        }
	    }

	    // Responsabilities
	    if ($responsabilities) {
	        $responsabilities_instance = new responsabilities();
	        $responsabilities_instance->save_authperso($id, $responsabilities);
	    }

		// Indexation concepts
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($id, TYPE_AUTHPERSO);
			$index_concept->save();
		}

		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($id, TYPE_AUTHPERSO);

		$this->update_global_index($id);

		// Publication d'un event
		$evt_handler = events_handler::get_instance();
		$event = new event_authperso("authperso", "update");
		$event->set_id_authperso($id);
		$evt_handler->send($event);

		return $id;
	}

	public function update_global_index($id){
		global $include_path;

		$id = (int) $id;
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);
		$mots_perso=$p_perso->get_fields_recherche($id);
		if($mots_perso) {
			$infos_global = $mots_perso.' ';
			$infos_global_index = strip_empty_words($mots_perso).' ';
		} else {
			$infos_global = '';
			$infos_global_index = '';
		}
		$req = "update authperso_authorities set authperso_infos_global='".addslashes($infos_global)."', authperso_index_infos_global='".addslashes(' '.$infos_global_index)."' where id_authperso_authority=$id";
		pmb_mysql_query($req);

		indexation_stack::push($id, TYPE_AUTHPERSO);
	}

	public function reindex_all(){
		$req = "select id_authperso_authority from authperso_authorities";
		$res = pmb_mysql_query($req);
		while($fiche = pmb_mysql_fetch_object($res)){
			$this->update_global_index($fiche->id_authperso_authority);
		}
	}

	static public function update_all_global_index($id_authperso){
	    global $start, $count;
	    global $msg, $charset;
	    global $include_path;

	    if (!isset($start)) {
	        $start = 0;
	    }
	    $lot = REINDEX_PAQUET_SIZE;
	    $id_authperso = intval($id_authperso);
	    if (!isset($count) || !$count) {
	        $elts = pmb_mysql_query("SELECT count(1) FROM authperso_authorities");
	        $count = pmb_mysql_result($elts, 0, 0);
	        //On controle qu'il n'y a pas d'autorité à enlever
	        $req="SELECT id_authority FROM authorities LEFT JOIN authperso_authorities ON num_object=id_authperso_authority WHERE type_object ='".AUT_TABLE_AUTHPERSO."' AND id_authperso_authority IS NULL";
	        $res = pmb_mysql_query($req);
	        if($res && pmb_mysql_num_rows($res)){
	            while($aut = pmb_mysql_fetch_row($res)){
	                $authority = new authority($aut[0]);
	                $authority->delete();
	            }
	        }
	    }

	    $query = pmb_mysql_query("
                SELECT id_authperso_authority as id, authperso_authority_authperso_num
                FROM authperso_authorities
                WHERE authperso_authority_authperso_num = " . $id_authperso . "
                ORDER BY authperso_authority_authperso_num
                LIMIT $start, $lot");
	    if (pmb_mysql_num_rows($query)) {
    	    print "<div class='row left'>";
    	    print netbase::get_display_progress_title($msg["nettoyage_reindex_authperso"]);
	        print netbase::get_display_progress($start, $count);
            $indexation_authperso = new indexation_authperso($include_path."/indexation/authorities/authperso/champs_base.xml", "authorities", (1000+$id_authperso), $id_authperso);
            $indexation_authperso->set_deleted_index(true);
	        while($row = pmb_mysql_fetch_object($query)) {
                $indexation_authperso->maj($row->id);
	        }
	        pmb_mysql_free_result($query);
	        $next = $start + $lot;
            $script = "
                <script type='text/javascript'>
                    document.location = './admin.php?categ=authorities&sub=authperso&auth_action=update_global_index&id_authperso=$id_authperso&count=$count&start=$next';
                </script>
            ";
            print $script;
            print "</div>";
	    } else {
	        print "<span class='erreur'>".$msg['admin_authperso_update_global_index_end']."</span>";
	    }

	    return ;
	}

	public function delete($id) {
		$id = (int) $id;

		$is_used = $this->check_uses($id);
		if (empty($is_used)) {
		    $evt_handler = events_handler::get_instance();
		    $event = new event_authperso("authperso", "delete");
		    $event->set_id_authperso($id);
		    $evt_handler->send($event);

    		// Lien événement
    		$query = "delete from tu_oeuvres_events where oeuvre_event_authperso_authority_num=$id";
    		pmb_mysql_query($query);

    		// Liens entre autorités
    		$req = "select authperso_authority_authperso_num from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=$id";
    		$res = pmb_mysql_query($req);
    		if ($r = pmb_mysql_fetch_object($res)) {
    		    $query = "delete from aut_link where aut_link_from=" . ($r->authperso_authority_authperso_num + 1000) . " and aut_link_from_num=$id";
    		    pmb_mysql_query($query);
    		    $query = "delete from aut_link where aut_link_to=" . ($r->authperso_authority_authperso_num + 1000) . " and aut_link_to_num=$id";
    		    pmb_mysql_query($query);
    		}

    		// On supprime l'indexation de l'autorité perso avant de supprimer son identifiant unique d'autorité
    		indexation_authperso::delete_all_index($id, "authorities", "id_authority", AUT_TABLE_AUTHPERSO);

    		// Effacement de l'identifiant unique d'autorité
    		$authority = new authority(0, $id, AUT_TABLE_AUTHPERSO);
    		$authority->delete();

    		// Suppression des valeurs des champs perso
    		$p_perso = new custom_parametres_perso("authperso", "authperso", $this->id);
    		$p_perso->delete_values($id);

    		// Nettoyage indexation concepts
    		$index_concept = new index_concept($id, TYPE_AUTHPERSO);
    		$index_concept->delete();

    		$responsabilities = new responsabilities();
    		$responsabilities->delete_authperso($id);

    		$req = "DELETE FROM authperso_authorities where id_authperso_authority=$id";
    		pmb_mysql_query($req);

    		audit::delete_audit($this->id + 1000, $id);

    		return false;
		}
		return $is_used;
	}

	public function replace_form($id) {
		global $authperso_replace_content_form;
		global $msg;
		global $include_path;

		if(!$id ) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, static::format_url('&sub=&id='));
			return;
		}

		$content_form = $authperso_replace_content_form;
		$content_form = str_replace('!!id!!', $id, $content_form);
		$content_form = str_replace('!!id_authperso!!', $this->id, $content_form);

		$interface_form = new interface_autorites_replace_form('authperso_replace');
		$interface_form->set_object_id($id)
		->set_label($msg["159"]." ".strip_tags(static::get_isbd($id)))
		->set_content_form($content_form)
		->set_table_name('authperso')
		->set_field_focus('authperso_libelle')
		->set_url_base(static::format_url());
		return $interface_form->get_display();
	}

	public function replace($id,$by,$link_save=0) {
		global $msg;
		global $pmb_ark_activate;

		$id = (int) $id;
		$by = (int) $by;

		if (($id == $by) || (!$id) || (!$by)) {
		    return $msg[223];
		}

		$evt_handler = events_handler::get_instance();
		$event = new event_authperso("authperso", "replace");
		$event->set_id_authperso($id);
		$event->set_replacement_id($by);
		$evt_handler->send($event);

		$aut_link= new aut_link($this->id+1000,$id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to($this->id +1000,$by);
		}
		$aut_link->delete();

		// remplacement dans les notices
		$requete = "UPDATE notices_authperso SET notice_authperso_authority_num='$by' WHERE notice_authperso_authority_num='$id' ";
		@pmb_mysql_query($requete);

		vedette_composee::replace(TYPE_AUTHPERSO, $id, $by);

		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_AUTHPERSO, $id, $by);

		if ($pmb_ark_activate) {
		    $idReplaced = authority::get_authority_id_from_entity($id, AUT_TABLE_AUTHPERSO);
		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_AUTHPERSO);
		    if ($idReplaced && $idReplacing) {
		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
		        $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
		    }
		}
		$this->delete($id);
		$this->update_global_index($by);
	}

	public static function import($data) {
		// to do
	}

	/**
	 * Retourne la requête permettant de filtrer les autorites en fonction du statut
	 *
	 * @return string
	 */
	protected static function get_join_status_filter()
	{
		global $module_from;

		$join_statuts = '';
		if (strpos($module_from, 'catalog') !== false) {
			$field = 'authorities_statuts_searcher_autority';
			if (strpos($module_from, 'autocomplete') !== false) {
				$field = 'authorities_statuts_autocomplete';
			}
			$join_statuts = "JOIN authorities_statuts ON id_authorities_statut = num_statut AND {$field} = 1";
		}

		return $join_statuts;
	}

	public function get_ajax_list($user_input) {
	    global $autexclude;

		$values = array();
		$search_word = str_replace('*', '%', $user_input);

		$join_statuts = self::get_join_status_filter();
		$req = "SELECT id_authperso_authority FROM authperso_authorities
			JOIN authorities ON num_object = id_authperso_authority AND type_object = '".AUT_TABLE_AUTHPERSO."'
			{$join_statuts}
			WHERE
				( authperso_infos_global LIKE '".addslashes($search_word)."%' OR authperso_index_infos_global LIKE '".addslashes($user_input)."%' )
				AND authperso_authority_authperso_num = ".$this->id;

		if (isset($autexclude) && !empty($autexclude)) {
		    $req .= " AND id_authperso_authority != " . intval($autexclude);
		}
		$req .= " ORDER BY authperso_index_infos_global LIMIT 20";

		$res = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($res)) {
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$values[$row['id_authperso_authority']] = strip_tags(static::get_isbd($row['id_authperso_authority']));
			}
			pmb_mysql_free_result($res);
		}

		return $values;
	}

	public static function get_ajax_list_oeuvre_events($user_input, $oeuvre_event_type = 3) {
		$values = array();

		$search_word = str_replace('*', '%', $user_input);


		$join_statuts = self::get_join_status_filter();
		$req = "SELECT id_authperso_authority FROM authperso_authorities
		        JOIN authperso ON authperso_authorities.authperso_authority_authperso_num = authperso.id_authperso
				{$join_statuts}
		        WHERE (authperso_infos_global LIKE '".addslashes($search_word)."%' OR authperso_index_infos_global LIKE ' ".addslashes($user_input)."%')
		        AND authperso.authperso_oeuvre_event = 1";

		if(!empty($oeuvre_event_type)) {
			$req .= " AND authperso_authority_authperso_num = $oeuvre_event_type";
		}
		$req .= " ORDER BY authperso_index_infos_global limit 20";
		$res = pmb_mysql_query($req);

		if (pmb_mysql_num_rows($res)) {
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$values[$row['id_authperso_authority']] = strip_tags(static::get_isbd($row['id_authperso_authority']));
			}
			pmb_mysql_free_result($res);
		}
		return $values;
	}

	public function get_cp_error_message(){
		return $this->cp_error_message;
	}

	public static function set_controller($controller) {
		static::$controller = $controller;
	}

	protected static function format_url($url='') {
		global $base_path;

		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_url_base().$url;
		} else {
			return $base_path.'/autorites.php?categ=authperso'.$url;
		}
	}

	protected static function format_back_url() {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_back_url();
		} else {
			return "history.go(-1)";
		}
	}

	protected static function format_delete_url($url='') {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_delete_url();
		} else {
			return static::format_url("&sub=delete".$url);
		}
	}

	public function get_searcher_instance() {
		if (!isset($this->searcher_instance)) {
			global $user_input;
			$this->searcher_instance = searcher_factory::get_searcher('authperso', '', ($user_input ? $user_input : '*'), $this->id);
		}
		return $this->searcher_instance;
	}

	public function get_custom_fields_using_this_authority() {
	    $number = intval($this->id) + 1000;
	    $query = "";
	    $custom_fields = [];
	    foreach (self::$prefixes as $prefix) {
	        if ($query) {
	            $query .= " UNION ";
	        }
	        $query .= "SELECT idchamp, type, datatype, options, '".$prefix."' AS prefix
                    FROM ".$prefix."_custom
                    WHERE type = 'query_auth'
                    AND options LIKE '%<DATA_TYPE>".$number."</DATA_TYPE>%'";
	    }
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_assoc($result)) {
	            if (!isset($custom_fields[$row["prefix"]])) {
	                $custom_fields[$row["prefix"]] = [];
	            }
                $custom_fields[$row["prefix"]][] = $row["idchamp"];
	        }
	    }
	    return $custom_fields;
	}

	protected function check_uses($id) {
	    global $msg;

	    $display_messages = '';
	    $messages = [];

        // Publication d'un event
	    $evt_handler = events_handler::get_instance();
	    $event = new event_authperso("authperso", "authperso_check_uses");
	    $event->set_id_authperso($id);
	    $evt_handler->send($event);

	    if (empty($event->get_error_message())) {
    	    if ($usage = aut_pperso::delete_pperso(AUT_TABLE_AUTHPERSO, $id, 0)) {
    	        // Cette autorité est utilisée dans des champs perso, impossible de supprimer
    	        $messages[] = $msg['autority_delete_error'] . '<br /><br />' . $usage['display'];
    	    }

    	    $attached_vedettes = vedette_composee::get_vedettes_built_with_element($id, TYPE_AUTHPERSO);
    	    if (!empty($attached_vedettes)) {
    	        if (isset($event->get_elements()['concept'])) {
    	            if (count(array_diff($event->get_elements()['concept'], $attached_vedettes))) {
            	        // Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
            	        $messages[] = $msg["vedette_dont_del_autority"] . '<br/>' . vedette_composee::get_vedettes_display($attached_vedettes);
    	            }
    	        } else {
        	        $messages[] = $msg["vedette_dont_del_autority"] . '<br/>' . vedette_composee::get_vedettes_display($attached_vedettes);
    	        }
    	    }

    	    if (!empty($messages)) {
    	        foreach ($messages as $message) {
    	            $display_messages .= "<strong>$this->display</strong><br />$message";
    	        }
    	    }
    	    return $display_messages;
	    }
	    return $event->get_error_message();
	}

	public function is_responsability_authperso() {
	    $req = "select authperso_responsability_authperso from authperso where id_authperso=". $this->id;
	    $res = pmb_mysql_query($req);
	    if(($r=pmb_mysql_fetch_object($res))) {
	        return $r->authperso_responsability_authperso;
	    }
	    return 0;
	}

	public function gen_template_responsability(int $id) {
	    $responsabilities = new responsabilities();
	    return $responsabilities->get_form(TYPE_AUTHPERSO_RESPONSABILITY, $id);
	}

	public function get_sort_entity_type() {
	    return "authperso";
	}

	/**
	 * Récupération des données des responsabilités sous forme d'un tableau ["isbd" => "", "function" => ""]
	 * @param int $id_authperso
	 * @return array
	 */
	public static function get_responsability_authperso_data($id_authperso) {
		$result = array();

		$responsabilities = new responsabilities();
		$responsability_list = $responsabilities->get_responsabilities_authperso($id_authperso);

		foreach($responsability_list as $responsability) {
			$result[] = array(
				"isbd" => $responsability["isbd"] ?? "",
				"fonction_name" => $responsability["fonction_name"] ?? "",
				"fonction_id" => $responsability["fonction_id"] ?? "",
				"qualification" => $responsability["qualification"] ?? ""
			);
		}

		return $result;
	}
} //authperso class end


class authpersos {
	public $info=array();
	protected static $instance;

	public function __construct() {
		$this->fetch_data();
	}

	public static function get_name($id_authperso){
	    $id_authperso = (int) $id_authperso;
		$query = "select authperso_name from authperso where id_authperso = ".$id_authperso;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			return pmb_mysql_result($result, 0);
		}
	}

	public function fetch_data() {
		$this->info=array();
		$i=0;
		$req="select * from authperso order by authperso_name";
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				// $this->info[$i]= new authperso($r->id_authperso);
				$authperso= new authperso($r->id_authperso);
				$this->info[$r->id_authperso]=$authperso->get_data();
				$i++;
			}
		}
	}

	public function get_data(){
		return($this->info);
	}

	public function get_all_index_fields(){
		$index_fields=array();
		$req="select id_authperso from authperso order by authperso_name";
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				$index_fields[]=$r->id_authperso;
			}
		}
		return $index_fields;
	}

	public function get_onglet_list() {
		$onglets=array();
		foreach($this->info as $elt){
		//	if($elt['onglet_num'])
			$onglets[$elt['onglet_num']][]=$elt;
		}
		return $onglets;
	}

	public function get_menu() {
	    global $categ;

		$line_tpl="<li ".( $categ == "authperso" ? "class='active'" : "" )."><a href='./autorites.php?categ=authperso&sub=&id_authperso=!!id_authperso!!&id='>!!name!!</a></li>";
		$tpl_list='';
		foreach($this->info as $elt){
			$tpl_elt=$line_tpl;
			$tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
			$tpl_elt=str_replace('!!id_authperso!!',$elt['id'], $tpl_elt);
			$tpl_list.=$tpl_elt;
		}
		return $tpl_list;
	}

	public static function get_authpersos() {
		$authpersos=array();
		$req="select id_authperso, authperso_name from authperso order by authperso_name";
		$resultat=pmb_mysql_query($req);
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				$authpersos[]=array(
						"id" => $r->id_authperso,
				        "name" => translation::get_translated_text($r->id_authperso, 'authperso', 'authperso_name', $r->authperso_name),
				);
			}
		}
		return $authpersos;
	}

    public static function get_oeuvre_event_authpersos() {
    	$authpersos=array();
    	$req="select id_authperso, authperso_name from authperso where authperso_oeuvre_event=1 order by authperso_name";
    	$resultat=pmb_mysql_query($req);
    	if ($resultat && pmb_mysql_num_rows($resultat)) {
    		while($r=pmb_mysql_fetch_object($resultat)){
    			$authpersos[]=array(
    					"id" => $r->id_authperso,
    					"name" => $r->authperso_name,
    			);
    		}
    	}
    	return $authpersos;
    }

    public static function get_instance() {
    	if(!isset(static::$instance)) {
    		static::$instance = new authpersos();
    	}
    	return static::$instance;
    }
} // authpersos class end