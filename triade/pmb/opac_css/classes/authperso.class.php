<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.class.php,v 1.37.4.1 2025/01/31 09:18:09 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/custom_parametres_perso.class.php");
require_once($include_path."/h2o/h2o.php");
require_once("$class_path/aut_link.class.php");


class authperso {
	public $id=0; // id de authperso
	public $info=array();
	public $elt_id=0;
	public static $antiloop = [];
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
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
			    'comment' => $r->authperso_comment,
			    'event' => $r->authperso_oeuvre_event,
			    'responsability_authperso' => $r->authperso_responsability_authperso,
			    'onglet_name' => ''
			);
			$req="SELECT * FROM notice_onglet where id_onglet=".$r->authperso_notice_onglet_num;
			$resultat=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($resultat)) {
				$r_onglet=pmb_mysql_fetch_object($resultat);	
				$this->info['onglet_name']= $r_onglet->onglet_name;						
			}	
		}		
		$req = "select * from authperso_custom where num_type=". $this->id." order by ordre";		
		$resultat = pmb_mysql_query($req);	
		$i = 0;
		if (pmb_mysql_num_rows($resultat)) {
			while ($r = pmb_mysql_fetch_object($resultat)) {
			    $this->info['fields'][$i]= array();
			    $this->info['fields'][$i]['id'] = (int) $r->idchamp;
				$this->info['fields'][$i]['custom_prefixe'] = $r->custom_prefixe;
				$this->info['fields'][$i]['name'] = $r->name;
				$this->info['fields'][$i]['label'] = $r->titre;
				$this->info['fields'][$i]['type'] = $r->type;
				$this->info['fields'][$i]['datatype'] = $r->datatype;
				$this->info['fields'][$i]['ordre'] = (int) $r->ordre;
				$this->info['fields'][$i]['search'] = (int) $r->search;
				$this->info['fields'][$i]['pond'] = (int) $r->pond;
				$this->info['fields'][$i]['obligatoire'] = (int) $r->obligatoire;
				$this->info['fields'][$i]['export'] = (int) $r->export;
				$this->info['fields'][$i]['multiple'] = (int) $r->multiple;
				$this->info['fields'][$i]['opac_sort'] = (int) $r->opac_sort;
				$this->info['fields'][$i]['code_champ'] = $this->id;
				$this->info['fields'][$i]['code_ss_champ'] = (int) $r->idchamp;
				$this->info['fields'][$i]['data'] = array();		
				$this->info['fields'][$i]['OPTIONS'] = [_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS")];
				$i++;
			}
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
				$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,"./autorites.php?categ=authperso&sub=update&id_authperso=".$this->id);
				$p_perso->get_out_values($id);
				$authperso_fields=$p_perso->values;
			}
		}
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
		return $info;
	}
	
	public function fetch_data_auth($id) {
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);
		$p_perso->get_out_values($id);
		
		$this->info['data_auth'][$id]=$p_perso->values;
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
		return $p_perso->values;
	}
	
	// Génération de l'isbd de l'autorité
	public static function get_isbd($id){
	    global $base_path;
	    
	    $id = intval($id);
	    if(!$id) return '';
 	    $isbd = '';
	    $req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
	    $res = pmb_mysql_query($req);
	    if(($r=pmb_mysql_fetch_object($res))) {
	        $p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,"./autorites.php?categ=authperso&sub=update&id_authperso=".$id);
	        $p_perso->get_out_values($id);
	        $authperso_fields=$p_perso->values;
	        if($r->authperso_isbd_script){
	            $index_concept = new index_concept($id, TYPE_AUTHPERSO);
	            $authperso_fields['index_concepts'] = $index_concept->get_data();
	            if($r->authperso_responsability_authperso) {
	            	$authperso_fields["responsability_authperso"] = static::get_responsability_authperso_data($id);
	            }
	            $template_path = $base_path.'/temp/'.LOCATION.'_authperso_isbd_'.$r->authperso_authority_authperso_num;
	            if(!file_exists($template_path) || (md5($r->authperso_isbd_script) != md5_file($template_path))){
	                file_put_contents($template_path, $r->authperso_isbd_script);
	            }
	            $h2o = H2o_collection::get_instance($template_path);
	            $isbd = $h2o->render($authperso_fields);
	            
	        }else{
	            $first = true;
	            foreach ($authperso_fields as $field){
	                if (!$first){
	                    $isbd.=". ";
	                }
	                $isbd.=$field['values'][0]['format_value'];
	                $first=false;
	            }
	        }
	    }
	    $isbd =  str_replace(array("\n", "\t", "\r"), '', strip_tags($isbd));
	    return $isbd;
	}
	
	// Génération de la notice d'autorité
	public function get_view($id){
	    global $base_path;
	    
	    $id = intval($id);
		$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
		$res = pmb_mysql_query($req);
		if(($r=pmb_mysql_fetch_object($res))) {
			$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,"./autorites.php?categ=authperso&sub=update&id_authperso=".$id);
			$p_perso->get_out_values($id);
			$authperso_fields=$p_perso->values;
			if (empty(static::$antiloop[$id])) {
			    static::$antiloop[$id] = true;
    			$aut_link= new aut_link($r->authperso_authority_authperso_num + 1000,$id);
    			$authperso_fields['authorities_link']=$aut_link->get_data();
			}
			if($r->authperso_view_script){
			    $template_path = $base_path.'/temp/'.LOCATION.'_authperso_isbd_'.$r->authperso_authority_authperso_num;
			    if(!file_exists($template_path)  || (md5($r->authperso_view_script) != md5_file($template_path))){
			        file_put_contents($template_path, $r->authperso_view_script);
				}
				$h2o = H2o_collection::get_instance($template_path);
				$view = $h2o->render($authperso_fields);
			}else{
				$view='';
				foreach ($authperso_fields as $field){					
				    $view.= (!empty($field['values'][0]['format_value']) ? $field['values'][0]['format_value'].".  " : "");
				}
			}
		}
		return $view;
	}
	
	public function get_ajax_list($user_input){
		$values=array();
		$search_word = str_replace('*','%',$user_input);
		$req = "select * from authperso_authorities where ( authperso_infos_global like '".addslashes($search_word)."%' or authperso_index_infos_global like ' ".addslashes($user_input)."%' ) and  authperso_authority_authperso_num= ".$this->id;
		$req .= " order by authperso_index_infos_global limit 20";
		$res = pmb_mysql_query($req);
		while(($r=pmb_mysql_fetch_object($res))) {
		    $values[$r->id_authperso_authority]=strip_tags(static::get_isbd($r->id_authperso_authority));
		}
		return($values);
	}
	
	public function get_name() {
		return $this->info['name'];
	}

	public function get_list_selector($id_to_view=0,$url = '',$nb_per_page=10) {
		global $charset;
		global $user_input, $f_user_input, $page, $nb_per_page_search, $nbr_lignes,$last_param;
		global $callback;
		global $caller;
		global $base_url;
	
		if(!$url){
			$url = $base_url;
		}
	
		if($id_to_view){
		    $isbd=strip_tags(static::get_isbd($id_to_view));
			$authority = new authority(0, $id_to_view, AUT_TABLE_AUTHPERSO);
			return "<br />".$authority->get_display_statut_class_html()."<a href='#' onclick=\"set_parent('".$caller."', '".$id_to_view."', '".htmlentities(addslashes(str_replace("\r"," ",str_replace("\n"," ",$isbd))),ENT_QUOTES, $charset)."','".$callback."')\">".
					htmlentities($isbd,ENT_QUOTES, $charset)."</a><br />";
		}
		$auth_lines='';
		if(!$page) $page=1;
		$debut =($page-1)*$nb_per_page;
	
		if (!$user_input && $f_user_input) {
			$user_input = $f_user_input;
		}
	
		$search_word = str_replace('*','%',$user_input);
		if(!($nb_per_page*1)){
			$nb_per_page=$nb_per_page_search;
		}
		if(!$page) $page=1;
		if(!$last_param){
			$debut =($page-1)*$nb_per_page;
			$requete = "SELECT count(1) FROM authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and authperso_authority_authperso_num= ".$this->id;
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			$nbepages = ceil($nbr_lignes/$nb_per_page);
			if($page>$nbepages){
				$debut=0;
				$page=1;
			}
			$req = "select * from authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by authperso_index_infos_global LIMIT ".$debut.",".$nb_per_page." ";
		}else{ // les derniers créés
			$req = "select * from authperso_authorities where  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by id_authperso_authority DESC LIMIT $nb_per_page";
		}
		$res = pmb_mysql_query($req);
		while(($r=pmb_mysql_fetch_object($res))) {
			$id=$r->id_authperso_authority;
			$isbd=strip_tags(static::get_isbd($id));
			$authority = new authority(0, $id, AUT_TABLE_AUTHPERSO);
			$auth_lines.=$authority->get_display_statut_class_html()."<a href='#' onclick=\"set_parent('".$caller."', '".$id."', '".htmlentities(addslashes(str_replace("\r"," ",str_replace("\n"," ",$isbd))),ENT_QUOTES, $charset)."','".$callback."')\">".
					htmlentities($isbd,ENT_QUOTES, $charset)."</a><br />";
		}
	
		//$url_base = $base_url."&user_input=".rawurlencode(stripslashes($user_input));
		$nav_bar = aff_pagination ($url, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
	
		$authperso_list_tpl= "
		<br />
		$auth_lines
		<div class='row'>&nbsp;<hr /></div><div class='center'>
		$nav_bar
		</div>
		";
	
		return $authperso_list_tpl;
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
	public $simple_seach_list_checked=0;
	
	public static function get_name($id_authperso){
		$id_authperso = intval($id_authperso);
		$query = "select authperso_name from authperso where id_authperso = ".$id_authperso;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			return pmb_mysql_result($result, 0);
		}
	}

	public function __construct() {
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->info=array();
		$i=0;
		$req="select * from authperso order by authperso_name";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$authperso= new authperso($r->id_authperso);
				$this->info[$r->id_authperso]=$authperso->get_data();
				$i++;
			}
		}
	}
	
	public function get_data(){
		return($this->info);
	}
	
	public function get_simple_seach_list_tpl() {
		global $look_FIRSTACCESS ; // si 0 alors premier Acces : la rech par defaut est cochee
		global $get_query;
		
		$ou_chercher_tab=array();
		foreach($this->info as $authperso){			
			
			if (!$authperso['opac_search']) continue;
			
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global ${$look_name};
			$look=${$look_name};
			
			if (!$look_FIRSTACCESS && !$get_query ) {
				if ($authperso['opac_search']==2) $look = 1 ;
			}			
			if($look){
				$checked_AUTHPERSO= " checked='' " ; 
				$this->simple_seach_list_checked=1;
			}else $checked_AUTHPERSO="";
			
			$ou_chercher_tab[]= "\n<span style='width: 30%; float: left;'><input type='checkbox' name='$look_name' id='$look_name' value='1' $checked_AUTHPERSO/><label for='$look_name'> ".$authperso['name']." </label></span>";
			
		}
		return $ou_chercher_tab;
	}
	
	public function get_simple_seach_list_tpl_hiden() {
		$tpl="";
		foreach($this->info as $authperso){				
			if (!$authperso['opac_search']) continue;
							
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global ${$look_name};
			$look=${$look_name};
			if($look)$tpl.="<input type='hidden' name='$look_name' id='$look_name' value='1' />";				
		}
		return $tpl;
	}

	public function make_search_test() {
		$tpl="";
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
				
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global ${$look_name};
			$look=${$look_name};
			if($look)$tpl.="<input type='hidden' name='$look_name' id='$look_name' value='1' />";
		}
		return $tpl;
	}
	
	public function get_field_text($id) {
	    // BT : fonction probablement plus/pas utilisée
		$auth = new authperso_data($id);
		return array('valeur_champ' => $auth->get_isbd(), "look_AUTHPERSO_typ_search" => $auth->get_authperso_num());
	}
	
	public function search_authperso($user_query) {
    	global $opac_search_other_function,$typdoc,$charset;
    	global $opac_stemming_active;
    	$total_results=0;
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
				
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global ${$look_name};
			$look=${$look_name};
			if(!$look) continue;
			
			$clause = '';
			$add_notice = '';
			
			$aq=new analyse_query(stripslashes($user_query),0,0,1,1,$opac_stemming_active);
			$members=$aq->get_query_members("authperso_authorities","authperso_infos_global","authperso_index_infos_global","id_authperso_authority");
			$clause.= "where ".$members["where"] ." and authperso_authority_authperso_num=".$authperso['id'];
			
			if ($opac_search_other_function) $add_notice=search_other_function_clause();
			if ($typdoc || $add_notice) $clause = ', notices, notices_authperso '.$clause;
			if ($typdoc) $clause.=" and notice_authperso_notice_num=notice_id and typdoc='".$typdoc."' ";
			if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')';
					
			$tri = 'order by pert desc, authperso_index_infos_global';
			$pert=$members["select"]." as pert";
			
			$auth_res = pmb_mysql_query("SELECT COUNT(distinct id_authperso_authority) FROM authperso_authorities $clause");
			$nb_result = pmb_mysql_result($auth_res, 0 , 0);
			if ($nb_result) {
				$total_results+=$nb_result;
				//définition du formulaire
				$form = "<div class='search_result'><form id='search_authperso_".$authperso['id']."' name=\"search_authperso_".$authperso['id']."\" action=\"./index.php?lvl=more_results\" method=\"post\">";
				$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
				if (function_exists("search_other_function_post_values")){
					$form .=search_other_function_post_values();
				}
				$form .= "<input type=\"hidden\" name=\"mode\" value=\"authperso_".$authperso['id']."\">\n";
				$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
				$form .= "<input type=\"hidden\" name=\"id_authperso\" value=\"".$authperso['id']."\">\n";
				$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result ."\">\n";
				$form .= "<input type=\"hidden\" name=\"name\" value=\"".$authperso["name"] ."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">";
				$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";
				$form .= "</div>";
				 
				$_SESSION["level1"]["authperso_".$authperso['id']]["form"]=$form;
				$_SESSION["level1"]["authperso_".$authperso['id']]["count"]=$nb_result;
				$_SESSION["level1"]["authperso_".$authperso['id']]["name"]=$authperso["name"];
			}
		}		
	    	
    	return $total_results;
	}	
	
	public function rec_history($n) {
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
	
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global ${$look_name};
			$look=${$look_name};
			if($look)$_SESSION[$look_name.$n]=$look;
		}
	}
	
	public function get_history($n) {
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
	
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global ${$look_name};
			${$look_name}=(isset($_SESSION[$look_name.$n]) ? $_SESSION[$look_name.$n] : '');
		}
	}
	public function get_human_query($n) {
		$r1 = '';
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
	
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global ${$look_name};
			if (isset($_SESSION["$look_name".$n]) && $_SESSION["$look_name".$n]) $r1.=$authperso['name']." ";
		}
		return $r1;
	}
	
	public static function get_instance() {
		if(!isset(static::$instance)) {
			static::$instance = new authpersos();
		}
		return static::$instance;
	}
	
	public function is_responsability_authperso() {
	    $req = "select authperso_responsability_authperso from authperso where id_authperso=". $this->id;
	    $res = pmb_mysql_query($req);
	    if(($r=pmb_mysql_fetch_object($res))) {
	        return $r->authperso_responsability_authperso;
	    }
	    return 0;
	}
	
} // authpersos class end
	
